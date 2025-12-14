<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherResourceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all resources uploaded by this teacher
        $resources = Resource::where('teacher_id', $user->id)
            ->with(['subject', 'topic', 'term', 'classRoom'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('teacher.resources.index', compact('resources'));
    }
    
    public function showUploadForm()
    {
        $user = Auth::user();
        // Get classes assigned to teacher using raw query to bypass global scopes
        $classIds = \DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        if (!empty($classIds)) {
            $classes = SchoolClass::withoutGlobalScope('school')
                ->whereIn('id', $classIds)
                ->get();
        } else {
            // If no classes assigned, get all school classes as fallback
            $classes = SchoolClass::where('school_id', $user->school_id)->get();
        }
        
        // Get subjects assigned to teacher
        $subjectIds = \DB::table('subject_user')
            ->where('user_id', $user->id)
            ->pluck('subject_id')
            ->toArray();
        
        if (!empty($subjectIds)) {
            $subjects = \App\Models\Subject::withoutGlobalScope('school')
                ->whereIn('id', $subjectIds)
                ->get();
        } else {
            // If no subjects assigned, get all system subjects as fallback
            $subjects = \App\Models\Subject::withoutGlobalScope('school')
                ->whereNull('school_id')
                ->where('is_active', true)
                ->get();
        }
        
        // Get all active terms (system-wide terms)
        $terms = \App\Models\Term::where('is_active', true)->get();
        
        return view('teacher.resources.upload', compact('classes', 'subjects', 'terms'));
    }

    public function uploadResource(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'term_id' => 'required|integer|exists:terms,id',
            'topic_name' => 'required|string|max:255',
            'resource_file' => 'required|file|mimes:pdf,png|max:10240', // 10MB max
        ]);

        // Verify teacher teaches this subject
        if (!$user->teachesSubject($request->subject_id)) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'You are not assigned to teach this subject. You can only upload resources for subjects you teach.'])
                ->withInput();
        }
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        $termId = $request->input('term_id');
        $topicName = $request->input('topic_name');
        $file = $request->file('resource_file');
        $path = $file->store('resources', 'public');
        
        // Determine file type
        $extension = strtolower($file->getClientOriginalExtension());
        $fileType = $extension === 'pdf' ? 'pdf' : 'image';
        
        // Get or create topic for this subject and teacher
        $topic = \App\Models\Topic::firstOrCreate(
            [
                'subject_id' => $subjectId,
                'school_id' => $user->school_id,
                'name' => $topicName
            ],
            [
                'slug' => \Illuminate\Support\Str::slug($topicName),
                'description' => 'Topic created by ' . $user->name,
                'created_by' => $user->id,
                'is_active' => true
            ]
        );
        
        $resource = new Resource();
        $resource->teacher_id = $user->id;
        $resource->created_by = $user->id;
        $resource->school_id = $user->school_id;
        $resource->class_id = $classId;
        $resource->subject_id = $subjectId;
        $resource->topic_id = $topic->id;
        $resource->term_id = $termId;
        $resource->notes_file_path = $path;
        $resource->notes_file_type = $fileType;
        $resource->title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $resource->is_active = true;
        $resource->save();
        return redirect()->route('teacher.dashboard')->with('success', 'Resource uploaded successfully.');
    }
}
