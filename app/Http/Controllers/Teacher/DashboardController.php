<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\StudentAssignment;
use App\Models\StudentMark;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can access this dashboard.');
        }

        // Get teacher's classes
        $classes = $user->classes()
            ->where('classes.school_id', $user->school_id)
            ->where('classes.is_active', true)
            ->orderBy('classes.name')
            ->get();

        // Get teacher's resources (videos they created)
        $resourcesQuery = Resource::with(['subject', 'term', 'topic', 'classRoom'])
            ->where('teacher_id', $user->id)
            ->whereNotNull('google_drive_link');
        
        if ($user->school_id) {
            $resourcesQuery->where('school_id', $user->school_id);
        }
        
        $totalResources = $resourcesQuery->count();
        $recentResources = $resourcesQuery->latest()->take(5)->get();

        // Get assignments statistics
        $assignmentsQuery = StudentAssignment::whereHas('resource', function($query) use ($user) {
            $query->where('teacher_id', $user->id);
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        });

        $totalAssignments = $assignmentsQuery->count();
        $pendingAssignments = $assignmentsQuery->where('status', 'submitted')->count();
        $gradedAssignments = $assignmentsQuery->where('status', 'graded')->count();
        $recentAssignments = $assignmentsQuery->with(['user', 'resource'])
            ->latest()
            ->take(5)
            ->get();

        // Get marks statistics - only marks uploaded by teachers (not by students themselves)
        $marksQuery = StudentMark::where('school_id', $user->school_id)
            ->where('uploaded_by', $user->id) // Only marks uploaded by this teacher
            ->whereHas('user', function($query) use ($user) {
                $query->where('school_id', $user->school_id);
            });
        
        $totalMarksUploaded = $marksQuery->count();
        $recentMarks = $marksQuery->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Get students count (students in teacher's classes)
        $studentsCount = 0;
        if ($classes->count() > 0 && $user->school_id) {
            $classNames = $classes->pluck('name')->toArray();
            $classIds = $classes->pluck('id')->toArray();
            
            $studentsCount = \App\Models\User::where('account_type', 'student')
                ->where('school_id', $user->school_id)
                ->whereHas('student', function($query) use ($classNames, $classIds) {
                    $query->where(function($q) use ($classNames, $classIds) {
                        foreach ($classNames as $className) {
                            $q->orWhere('class', 'LIKE', '%' . $className . '%')
                              ->orWhereJsonContains('classes', $className);
                        }
                        foreach ($classIds as $classId) {
                            $q->orWhereJsonContains('classes', $classId);
                        }
                    });
                })
                ->count();
        }

        // Get resources with unreplied comments
        $resourcesWithComments = Resource::where('teacher_id', $user->id)
            ->whereHas('comments', function($query) {
                $query->whereNull('parent_id'); // Only top-level comments
            })
            ->withCount(['comments' => function($query) {
                $query->whereNull('parent_id');
            }])
            ->get();

        $unrepliedCommentsCount = 0;
        foreach ($resourcesWithComments as $resource) {
            $unrepliedCommentsCount += $resource->getUnrepliedStudentCommentsCount($user->id);
        }

        return view('teacher.dashboard', compact(
            'classes',
            'totalResources',
            'recentResources',
            'totalAssignments',
            'pendingAssignments',
            'gradedAssignments',
            'recentAssignments',
            'totalMarksUploaded',
            'recentMarks',
            'studentsCount',
            'unrepliedCommentsCount'
        ));
    }

    /**
     * Display assigned videos for the teacher.
     */
    public function assignedVideos(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can access this page.');
        }

        \Log::info('Teacher assigned videos debug', [
            'teacher_id' => $user->id,
            'teacher_name' => $user->name,
            'school_id' => $user->school_id,
            'account_type' => $user->account_type
        ]);

        // Get videos assigned to this teacher (by school, not teacher_id)
        $query = Resource::withoutGlobalScope('school')
            ->with(['subject', 'term', 'topic', 'classRoom', 'schools'])
            ->where(function($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereHas('schools', function($sq) use ($user) {
                      $sq->where('schools.id', $user->school_id);
                  });
            })
            ->where('is_active', true)
            ->whereNotNull('google_drive_link')
            ->where('google_drive_link', '!=', '');

        $totalCount = $query->count();
        \Log::info('Total resources for school', ['school_id' => $user->school_id, 'count' => $totalCount]);

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }
        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $resources = $query->latest()->paginate(12);
        $resources->appends($request->query());

        // Get filter options based on all school resources
        $schoolResourceQuery = Resource::withoutGlobalScope('school')
            ->where(function($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereHas('schools', function($sq) use ($user) {
                      $sq->where('schools.id', $user->school_id);
                  });
            })
            ->where('is_active', true)
            ->whereNotNull('google_drive_link')
            ->where('google_drive_link', '!=', '');

        $subjects = \App\Models\Subject::whereIn('id', 
            $schoolResourceQuery->clone()->pluck('subject_id')->unique()->filter()
        )->get();

        $topics = \App\Models\Topic::whereIn('id',
            $schoolResourceQuery->clone()->pluck('topic_id')->unique()->filter()
        )->get();

        $terms = \App\Models\Term::whereIn('id',
            $schoolResourceQuery->clone()->pluck('term_id')->unique()->filter()
        )->get();

        $classes = collect();
        $isSchoolStudent = false; // For teachers

        return view('student.my-videos', compact('resources', 'classes', 'subjects', 'topics', 'terms', 'isSchoolStudent'));
    }
}

