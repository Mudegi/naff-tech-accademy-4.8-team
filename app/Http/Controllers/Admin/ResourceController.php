<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Term;
use App\Models\SchoolClass;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Vinkla\Hashids\Facades\Hashids;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        
        // For super admin (no school_id), show all resources unless school context is set
        // For school users, TenantScope will automatically filter by school_id
        $query = Resource::query()->with(['subject', 'topic', 'term', 'classRoom', 'teacher'])
            ->whereNotNull('google_drive_link');
        
        // If user has school_id or super admin has school context, filter by school
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tags', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply subject filter
        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        // Apply topic filter
        if ($request->filled('topic')) {
            $query->where('topic_id', $request->topic);
        }

        // Apply grade level filter
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Apply term filter
        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        }

        // Apply teacher filter
        if ($request->filled('teacher')) {
            $query->where('teacher_id', $request->teacher);
        }

        // Get paginated results
        $perPage = $request->get('per_page', 10);
        $resources = $query->latest()->paginate($perPage);
        $resources->appends($request->query());

        // Get filter options - only for resources that have Google Drive links
        // Respect school boundaries for school users or super admin with school context
        $subjectsQuery = Subject::whereHas('resources', function($q) use ($schoolId) {
            $q->whereNotNull('google_drive_link');
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
        });
        if ($schoolId) {
            $subjectsQuery->where('school_id', $schoolId);
        }
        $subjects = $subjectsQuery->where('is_active', true)->get();

        $topicsQuery = Topic::whereHas('resources', function($q) use ($schoolId) {
            $q->whereNotNull('google_drive_link');
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
        });
        if ($schoolId) {
            $topicsQuery->where('school_id', $schoolId);
        }
        if ($request->filled('subject')) {
            $topicsQuery->where('subject_id', $request->subject);
        }
        $topics = $topicsQuery->where('is_active', true)->get();

        $terms = Term::whereHas('resources', function($q) use ($schoolId) {
            $q->whereNotNull('google_drive_link');
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
        })->where('is_active', true)->get();

        $teachersQuery = \App\Models\User::where('account_type', 'teacher')
            ->whereHas('resources', function($q) use ($schoolId) {
                $q->whereNotNull('google_drive_link');
                if ($schoolId) {
                    $q->where('school_id', $schoolId);
                }
            });
        if ($schoolId) {
            $teachersQuery->where('school_id', $schoolId);
        }
        $teachers = $teachersQuery->orderBy('name')->get();

        $gradeLevels = ['O Level', 'A Level'];

        return view('admin.resources.index', compact('resources', 'subjects', 'topics', 'terms', 'teachers', 'gradeLevels'));
    }

    public function create()
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        
        // For school users or super admin with school context, only show their school's data
        // For super admin without context, show all data
        if ($schoolId) {
            $subjects = Subject::where('school_id', $schoolId)->where('is_active', true)->get();
            $topics = Topic::where('school_id', $schoolId)->where('is_active', true)->get();
            $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->get();
            $teachers = \App\Models\User::where('school_id', $schoolId)
                ->where('account_type', 'teacher')
                ->orderBy('name')
                ->get();
        } else {
            $subjects = Subject::where('is_active', true)->get();
            $topics = Topic::where('is_active', true)->get();
            $classes = SchoolClass::where('is_active', true)->get();
            $teachers = \App\Models\User::where('account_type', 'teacher')->orderBy('name')->get();
        }
        
        $terms = Term::where('is_active', true)->get(); // Terms are global
        $schools = School::orderBy('name')->get();
        
        return view('admin.resources.create', compact('subjects', 'topics', 'terms', 'classes', 'teachers', 'schools'));
    }
    
    /**
     * Get effective school ID (user's school_id or session context for admins)
     */
    private function getEffectiveSchoolId($user)
    {
        // If user has school_id, use it
        if ($user->school_id) {
            return $user->school_id;
        }
        
        // For admins (both super admin and regular admin) without school_id, check session context
        if ($user->account_type === 'admin' && !$user->school_id) {
            return \Illuminate\Support\Facades\Session::get('admin_school_context');
        }
        
        return null;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'term_id' => 'required|exists:terms,id',
            'class_id' => 'required|exists:classes,id',
            'school_id' => 'nullable|exists:schools,id',
            'school_ids' => 'nullable|array',
            'school_ids.*' => 'nullable|exists:schools,id',
            'video_url' => 'nullable|url',
            'google_drive_link' => 'nullable|url',
            'notes_file' => 'nullable|file|mimes:pdf,ppt,pptx,xls,xlsx|max:10240',
            'assessment_tests' => 'nullable|file|mimes:pdf|max:10240',
            'tags' => 'nullable|string',
            'learning_outcomes' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'visible_as_sample' => 'boolean'
        ]);

        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        
        // For school users or super admin with school context, ensure they can only create resources for that school
        // Super admin without context can assign to any school or leave global
        if ($schoolId) {
            // Set school_id to the effective school (user's school or session context)
            $validated['school_id'] = $schoolId;
            
            // Verify subject, topic, and class belong to the school
            $subject = Subject::where('id', $validated['subject_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
            
            $topic = Topic::where('id', $validated['topic_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
            
            if ($validated['class_id']) {
                $class = SchoolClass::where('id', $validated['class_id'])
                    ->where('school_id', $schoolId)
                    ->firstOrFail();
            }
        }
        
        $resource = new Resource();
        $resource->title = $validated['title'];
        $resource->description = $validated['description'];
        $resource->grade_level = $validated['grade_level'];
        $resource->subject_id = $validated['subject_id'];
        $resource->topic_id = $validated['topic_id'];
        $resource->term_id = $validated['term_id'];
        $resource->class_id = $validated['class_id'];
        $resource->school_id = $validated['school_id'] ?? null;
        $resource->video_url = $validated['video_url'];
        $resource->google_drive_link = $validated['google_drive_link'];
        $resource->is_active = $request->has('is_active');
        $resource->visible_as_sample = $request->has('visible_as_sample');
        $resource->created_by = auth()->id();
        $resource->tags = $validated['tags'];
        $resource->teacher_id = $request->input('teacher_id');
        
        // Process learning outcomes
        if ($request->has('learning_outcomes') && !empty(trim($request->learning_outcomes))) {
            $resource->learning_outcomes = trim($request->learning_outcomes);
        }

        if ($request->hasFile('notes_file')) {
            $file = $request->file('notes_file');
            $path = $file->store('resources/notes', 'public');
            $resource->notes_file_path = $path;
            $resource->notes_file_type = $file->getClientOriginalExtension();
        }

        if ($request->hasFile('assessment_tests')) {
            $file = $request->file('assessment_tests');
            $path = $file->store('resources/assessments', 'public');
            $resource->assessment_tests_path = $path;
            $resource->assessment_tests_type = $file->getClientOriginalExtension();
        }

        $resource->save();
        
        // Sync additional schools via pivot table
        if ($request->has('school_ids') && is_array($request->school_ids)) {
            $schoolIds = array_filter($request->school_ids);
            $resource->schools()->sync($schoolIds);
        }
        
        return redirect()->route('admin.resources.index')->with('success', 'Resource created successfully!');
    }

    protected function findResourceByHash($hash_id)
    {
        $resource = Resource::findByHashId($hash_id);
        if (!$resource) {
            abort(404);
        }
        return $resource;
    }

    public function edit($hash_id)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = $this->findResourceByHash($hash_id);
        
        // Load schools relationship
        $resource->load('schools');
        
        // Check if user can edit this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only edit resources from your school.');
        }
        
        // For school users or super admin with school context, only show their school's data
        if ($schoolId) {
            $subjects = Subject::where('school_id', $schoolId)->where('is_active', true)->get();
            $topics = Topic::where('school_id', $schoolId)->where('is_active', true)->get();
            $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->get();
            $teachers = \App\Models\User::where('school_id', $schoolId)
                ->where('account_type', 'teacher')
                ->orderBy('name')
                ->get();
        } else {
            $subjects = Subject::where('is_active', true)->get();
            $topics = Topic::where('is_active', true)->get();
            $classes = SchoolClass::where('is_active', true)->get();
            $teachers = \App\Models\User::where('account_type', 'teacher')->orderBy('name')->get();
        }
        
        $terms = Term::where('is_active', true)->get(); // Terms are global
        $schools = School::orderBy('name')->get();
        
        return view('admin.resources.edit', compact('resource', 'subjects', 'topics', 'terms', 'classes', 'teachers', 'schools'));
    }

    public function update(Request $request, $hash_id)
    {
        $id = Hashids::decode($hash_id)[0] ?? null;
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid resource ID');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'term_id' => 'required|exists:terms,id',
            'class_id' => 'required|exists:classes,id',
            'school_id' => 'nullable|exists:schools,id',
            'school_ids' => 'nullable|array',
            'school_ids.*' => 'nullable|exists:schools,id',
            'video_url' => 'nullable|url',
            'google_drive_link' => 'nullable|url',
            'notes_file' => 'nullable|file|mimes:pdf,ppt,pptx,xls,xlsx|max:10240',
            'assessment_tests' => 'nullable|file|mimes:pdf|max:10240',
            'tags' => 'nullable|string',
            'learning_outcomes' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'visible_as_sample' => 'boolean'
        ]);

        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = Resource::findOrFail($id);
        
        // Check if user can update this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only update resources from your school.');
        }
        
        // For school users or super admin with school context, ensure they can only update to that school
        if ($schoolId) {
            $validated['school_id'] = $schoolId;
            
            // Verify subject, topic, and class belong to the school
            $subject = Subject::where('id', $validated['subject_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
            
            $topic = Topic::where('id', $validated['topic_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
            
            if ($validated['class_id']) {
                $class = SchoolClass::where('id', $validated['class_id'])
                    ->where('school_id', $schoolId)
                    ->firstOrFail();
            }
        }
        
        $resource->title = $validated['title'];
        $resource->description = $validated['description'];
        $resource->grade_level = $validated['grade_level'];
        $resource->subject_id = $validated['subject_id'];
        $resource->topic_id = $validated['topic_id'];
        $resource->term_id = $validated['term_id'];
        $resource->class_id = $validated['class_id'];
        $resource->school_id = $validated['school_id'] ?? null;
        $resource->video_url = $validated['video_url'];
        $resource->google_drive_link = $validated['google_drive_link'];
        $resource->is_active = $request->has('is_active');
        $resource->visible_as_sample = $request->has('visible_as_sample');
        $resource->tags = $validated['tags'];
        $resource->teacher_id = $request->input('teacher_id');
        
        // Process learning outcomes
        if ($request->has('learning_outcomes') && !empty(trim($request->learning_outcomes))) {
            $resource->learning_outcomes = trim($request->learning_outcomes);
        } else {
            $resource->learning_outcomes = null;
        }

        if ($request->hasFile('notes_file')) {
            $file = $request->file('notes_file');
            $path = $file->store('resources/notes', 'public');
            $resource->notes_file_path = $path;
            $resource->notes_file_type = $file->getClientOriginalExtension();
        }

        if ($request->hasFile('assessment_tests')) {
            $file = $request->file('assessment_tests');
            $path = $file->store('resources/assessments', 'public');
            $resource->assessment_tests_path = $path;
            $resource->assessment_tests_type = $file->getClientOriginalExtension();
        }

        $resource->save();
        
        // Sync additional schools via pivot table
        if ($request->has('school_ids') && is_array($request->school_ids)) {
            $schoolIds = array_filter($request->school_ids);
            $resource->schools()->sync($schoolIds);
        } else {
            $resource->schools()->sync([]);
        }
        
        return redirect()->back()->with('success', 'Resource updated successfully!');
    }

    public function destroy($hash_id)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = $this->findResourceByHash($hash_id);
        
        // Check if user can delete this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only delete resources from your school.');
        }
        
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Resource deleted!');
    }

    public function show($hash_id)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = $this->findResourceByHash($hash_id);
        
        // Check if user can view this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only view resources from your school.');
        }
        
        return view('admin.resources.show', compact('resource'));
    }

    public function videoView($hash_id)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = $this->findResourceByHash($hash_id);
        
        // Check if user can view this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only view resources from your school.');
        }
        
        $videoId = null;
        if ($resource && $resource->video_url) {
            // Extract YouTube video ID from URL
            if (preg_match('/youtu\.be\/([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&]v=([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/embed\/([\w-]{11})/', $resource->video_url, $matches)) {
                $videoId = $matches[1];
            }
        }
        if (!$videoId) abort(404);
        return view('admin.resources.video-view', compact('videoId'));
    }

    public function drivePlay($hash_id)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $resource = $this->findResourceByHash($hash_id);
        
        // Check if user can view this resource
        if ($schoolId && $resource->school_id != $schoolId) {
            abort(403, 'Access denied. You can only view resources from your school.');
        }
        
        if (!$resource || !$resource->google_drive_link) {
            abort(404);
        }

        // Extract file ID from Google Drive URL
        $fileId = null;
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $matches)) {
            $fileId = $matches[1];
        } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $matches)) {
            $fileId = $matches[1];
        } elseif (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $matches)) {
            $fileId = $matches[1];
        }

        if (!$fileId) {
            abort(404);
        }

        $driveUrl = "https://drive.google.com/file/d/{$fileId}/preview";
        return view('admin.resources.video-play', compact('driveUrl'));
    }

    public function getTeachersBySubjectAndClass(Request $request)
    {
        $user = Auth::user();
        $schoolId = $this->getEffectiveSchoolId($user);
        $subjectId = $request->input('subject_id');
        $classId = $request->input('class_id');
        $query = \App\Models\User::where('account_type', 'teacher');
        
        // Filter by school if user has school_id or super admin has school context
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        
        if ($subjectId) {
            $query->whereHas('subjects', function($q) use ($subjectId, $schoolId) {
                $q->where('subjects.id', $subjectId);
                if ($schoolId) {
                    $q->where('subjects.school_id', $schoolId);
                }
            });
        }
        if ($classId) {
            $query->whereHas('classes', function($q) use ($classId, $schoolId) {
                $q->where('classes.id', $classId);
                if ($schoolId) {
                    $q->where('classes.school_id', $schoolId);
                }
            });
        }
        $teachers = $query->orderBy('name')->get(['id', 'name']);
        return response()->json($teachers);
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'resource_ids' => 'required|array|min:1',
            'resource_ids.*' => 'required|string',
            'school_ids' => 'required|array|min:1',
            'school_ids.*' => 'required|exists:schools,id',
        ]);

        $user = Auth::user();
        
        // Only super admin can perform bulk assignment
        if ($user->account_type !== 'admin' || $user->school_id) {
            abort(403, 'Access denied. Only super administrators can perform bulk resource assignment.');
        }

        $resourceIds = [];
        foreach ($validated['resource_ids'] as $hashId) {
            $resource = Resource::findByHashId($hashId);
            if ($resource) {
                $resourceIds[] = $resource->id;
            }
        }

        if (empty($resourceIds)) {
            return redirect()->back()->with('error', 'No valid resources found.');
        }

        $assignedCount = 0;
        foreach ($resourceIds as $resourceId) {
            $resource = Resource::find($resourceId);
            if ($resource) {
                // Get current school assignments
                $currentSchoolIds = $resource->schools()->pluck('schools.id')->toArray();
                
                // Add new school assignments (avoid duplicates)
                $newSchoolIds = array_unique(array_merge($currentSchoolIds, $validated['school_ids']));
                
                // Sync schools
                $resource->schools()->sync($newSchoolIds);
                $assignedCount++;
            }
        }

        $schoolNames = School::whereIn('id', $validated['school_ids'])->pluck('name')->join(', ');
        
        return redirect()->back()->with('success', "Successfully assigned {$assignedCount} resource(s) to: {$schoolNames}");
    }
} 