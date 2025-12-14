<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Term;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SampleVideoController extends Controller
{
    /**
     * Display a listing of sample videos.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSchoolStudent = $user->account_type === 'student' && $user->school_id;
        
        $query = Resource::where('visible_as_sample', true)
            ->with(['subject', 'term', 'topic', 'classRoom']);
        
        // Restrict to school if school student
        if ($isSchoolStudent) {
            $query->where('school_id', $user->school_id);
        }

        // Apply filters
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tags', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('subject', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('topic', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('term', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('classRoom', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $resources = $query->latest()->paginate(12);
        $resources->appends($request->query());

        // Get filter options - only for subjects that have sample videos (restricted to school if school student)
        $subjectsQuery = Subject::whereHas('resources', function($q) use ($isSchoolStudent, $user) {
            $q->where('visible_as_sample', true);
            if ($isSchoolStudent) {
                $q->where('school_id', $user->school_id);
            }
        })->where('is_active', true);
        
        if ($isSchoolStudent) {
            $subjectsQuery->where('school_id', $user->school_id);
        }
        
        $subjects = $subjectsQuery->get();

        // Get topics for the selected subject or all topics with sample videos (restricted to school if school student)
        $topicsQuery = Topic::whereHas('resources', function($q) use ($isSchoolStudent, $user) {
            $q->where('visible_as_sample', true);
            if ($isSchoolStudent) {
                $q->where('school_id', $user->school_id);
            }
        })->where('is_active', true);
        
        if ($isSchoolStudent) {
            $topicsQuery->where('school_id', $user->school_id);
        }
        
        if ($request->filled('subject_id')) {
            $topicsQuery->where('subject_id', $request->subject_id);
        }
        $topics = $topicsQuery->get();

        // Get terms that have sample videos (terms are global, no school restriction needed)
        $terms = Term::whereHas('resources', function($q) use ($isSchoolStudent, $user) {
            $q->where('visible_as_sample', true);
            if ($isSchoolStudent) {
                $q->where('school_id', $user->school_id);
            }
        })->where('is_active', true)->get();

        // Get classes that have sample videos (restricted to school if school student)
        $classesQuery = SchoolClass::whereHas('resources', function($q) use ($isSchoolStudent, $user) {
            $q->where('visible_as_sample', true);
            if ($isSchoolStudent) {
                $q->where('school_id', $user->school_id);
            }
        })->where('is_active', true);
        
        if ($isSchoolStudent) {
            $classesQuery->where('school_id', $user->school_id);
        }
        
        $classes = $classesQuery->get();

        // Check if the user has an active subscription
        $hasActiveSubscription = \App\Models\UserSubscription::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->exists();

        return view('student.sample-videos.index', compact('resources', 'subjects', 'topics', 'terms', 'classes', 'hasActiveSubscription'));
    }

    /**
     * Display the specified sample video.
     */
    public function show($hashId)
    {
        $user = Auth::user();
        $isSchoolStudent = $user->account_type === 'student' && $user->school_id;
        
        $resource = Resource::findByHashId($hashId);
        
        if (!$resource->visible_as_sample) {
            abort(404);
        }
        
        // Check if school student is trying to access resource from their school
        if ($isSchoolStudent && $resource->school_id !== $user->school_id) {
            abort(403, 'You do not have access to this resource.');
        }

        $resource->load(['subject', 'term']);

        // Extract video ID if it's a YouTube URL
        $videoId = null;
        if ($resource->video_url) {
            if (preg_match('/youtu\.be\/([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&]v=([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/embed\/([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            }
        }

        // Extract Google Drive file ID
        $driveFileId = null;
        if ($resource->google_drive_link) {
            if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $resource->google_drive_link, $matches)) {
                $driveFileId = $matches[1];
            }
        }

        return view('student.sample-videos.show', compact('resource', 'videoId', 'driveFileId'));
    }
} 