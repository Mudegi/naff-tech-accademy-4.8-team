<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Term;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class SchoolResourceController extends Controller
{
    /**
     * Display a listing of school resources
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $query = Resource::where('school_id', $school->id)
            ->with(['subject', 'topic', 'term', 'classRoom', 'teacher', 'creator']);

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
        $perPage = $request->get('per_page', 15);
        $resources = $query->latest()->paginate($perPage);
        $resources->appends($request->query());

        // Get filter options - only for this school
        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $topics = Topic::where('school_id', $school->id)
            ->where('is_active', true);
        
        if ($request->filled('subject')) {
            $topics->where('subject_id', $request->subject);
        }
        $topics = $topics->orderBy('name')->get();

        $terms = Term::where('is_active', true)->orderBy('name')->get();

        // Get school teachers (subject teachers and HODs)
        $teachers = User::where('school_id', $school->id)
            ->whereIn('account_type', ['subject_teacher', 'head_of_department'])
            ->orderBy('name')
            ->get();

        $classes = SchoolClass::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $gradeLevels = ['O Level', 'A Level'];

        return view('admin.school.resources.index', compact('resources', 'subjects', 'topics', 'terms', 'teachers', 'classes', 'gradeLevels'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $topics = Topic::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $terms = Term::where('is_active', true)->orderBy('name')->get();

        $classes = SchoolClass::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get school teachers (subject teachers and HODs)
        $teachers = User::where('school_id', $school->id)
            ->whereIn('account_type', ['subject_teacher', 'head_of_department'])
            ->orderBy('name')
            ->get();

        return view('admin.school.resources.create', compact('subjects', 'topics', 'terms', 'classes', 'teachers'));
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|string|in:O Level,A Level',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'term_id' => 'required|exists:terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'video_url' => 'nullable|url',
            'google_drive_link' => 'nullable|url',
            'notes_file' => 'nullable|file|mimes:pdf,ppt,pptx,xls,xlsx|max:10240',
            'assessment_tests' => 'nullable|file|mimes:pdf|max:10240',
            'tags' => 'nullable|string',
            'learning_outcomes' => 'nullable|string|max:2000',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'visible_as_sample' => 'boolean'
        ]);

        // Verify subject, topic, and class belong to the school
        $subject = Subject::where('id', $validated['subject_id'])
            ->where('school_id', $school->id)
            ->firstOrFail();

        $topic = Topic::where('id', $validated['topic_id'])
            ->where('school_id', $school->id)
            ->firstOrFail();

        if ($request->filled('class_id')) {
            $class = SchoolClass::where('id', $request->class_id)
                ->where('school_id', $school->id)
                ->firstOrFail();
        }

        // Verify teacher belongs to the school if provided
        if ($request->filled('teacher_id')) {
            $teacher = User::where('id', $request->teacher_id)
                ->where('school_id', $school->id)
                ->whereIn('account_type', ['subject_teacher', 'head_of_department'])
                ->firstOrFail();
        }

        $resource = new Resource();
        $resource->title = $validated['title'];
        $resource->description = $validated['description'];
        $resource->grade_level = $validated['grade_level'];
        $resource->subject_id = $validated['subject_id'];
        $resource->topic_id = $validated['topic_id'];
        $resource->term_id = $validated['term_id'];
        $resource->class_id = $request->input('class_id');
        $resource->video_url = $validated['video_url'] ?? null;
        $resource->google_drive_link = $validated['google_drive_link'] ?? null;
        $resource->is_active = $request->has('is_active');
        $resource->visible_as_sample = $request->has('visible_as_sample');
        $resource->created_by = auth()->id();
        $resource->school_id = $school->id;
        $resource->tags = $validated['tags'] ?? null;
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
        
        return redirect()->route('admin.school.resources.index')
            ->with('success', 'Resource created successfully!');
    }

    /**
     * Show the form for editing a resource
     */
    public function edit($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $resource = Resource::where('school_id', $school->id)->findOrFail($id);

        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $topics = Topic::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $terms = Term::where('is_active', true)->orderBy('name')->get();

        $classes = SchoolClass::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get school teachers
        $teachers = User::where('school_id', $school->id)
            ->whereIn('account_type', ['subject_teacher', 'head_of_department'])
            ->orderBy('name')
            ->get();

        return view('admin.school.resources.edit', compact('resource', 'subjects', 'topics', 'terms', 'classes', 'teachers'));
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $resource = Resource::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|string|in:O Level,A Level',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'term_id' => 'required|exists:terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'video_url' => 'nullable|url',
            'google_drive_link' => 'nullable|url',
            'notes_file' => 'nullable|file|mimes:pdf,ppt,pptx,xls,xlsx|max:10240',
            'assessment_tests' => 'nullable|file|mimes:pdf|max:10240',
            'tags' => 'nullable|string',
            'learning_outcomes' => 'nullable|string|max:2000',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'visible_as_sample' => 'boolean'
        ]);

        // Verify subject, topic, and class belong to the school
        $subject = Subject::where('id', $validated['subject_id'])
            ->where('school_id', $school->id)
            ->firstOrFail();

        $topic = Topic::where('id', $validated['topic_id'])
            ->where('school_id', $school->id)
            ->firstOrFail();

        if ($request->filled('class_id')) {
            $class = SchoolClass::where('id', $request->class_id)
                ->where('school_id', $school->id)
                ->firstOrFail();
        }

        // Verify teacher belongs to the school if provided
        if ($request->filled('teacher_id')) {
            $teacher = User::where('id', $request->teacher_id)
                ->where('school_id', $school->id)
                ->whereIn('account_type', ['subject_teacher', 'head_of_department'])
                ->firstOrFail();
        }

        $resource->title = $validated['title'];
        $resource->description = $validated['description'];
        $resource->grade_level = $validated['grade_level'];
        $resource->subject_id = $validated['subject_id'];
        $resource->topic_id = $validated['topic_id'];
        $resource->term_id = $validated['term_id'];
        $resource->class_id = $request->input('class_id');
        $resource->video_url = $validated['video_url'] ?? null;
        $resource->google_drive_link = $validated['google_drive_link'] ?? null;
        $resource->is_active = $request->has('is_active');
        $resource->visible_as_sample = $request->has('visible_as_sample');
        $resource->tags = $validated['tags'] ?? null;
        $resource->teacher_id = $request->input('teacher_id');
        
        // Process learning outcomes
        if ($request->has('learning_outcomes') && !empty(trim($request->learning_outcomes))) {
            $resource->learning_outcomes = trim($request->learning_outcomes);
        } else {
            $resource->learning_outcomes = null;
        }

        if ($request->hasFile('notes_file')) {
            // Delete old file if exists
            if ($resource->notes_file_path) {
                \Storage::disk('public')->delete($resource->notes_file_path);
            }
            $file = $request->file('notes_file');
            $path = $file->store('resources/notes', 'public');
            $resource->notes_file_path = $path;
            $resource->notes_file_type = $file->getClientOriginalExtension();
        }

        if ($request->hasFile('assessment_tests')) {
            // Delete old file if exists
            if ($resource->assessment_tests_path) {
                \Storage::disk('public')->delete($resource->assessment_tests_path);
            }
            $file = $request->file('assessment_tests');
            $path = $file->store('resources/assessments', 'public');
            $resource->assessment_tests_path = $path;
            $resource->assessment_tests_type = $file->getClientOriginalExtension();
        }

        $resource->save();
        
        return redirect()->route('admin.school.resources.index')
            ->with('success', 'Resource updated successfully!');
    }

    /**
     * Remove the specified resource
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $resource = Resource::where('school_id', $school->id)->findOrFail($id);

        // Delete associated files
        if ($resource->notes_file_path) {
            \Storage::disk('public')->delete($resource->notes_file_path);
        }
        if ($resource->assessment_tests_path) {
            \Storage::disk('public')->delete($resource->assessment_tests_path);
        }

        $resource->delete();
        
        return redirect()->route('admin.school.resources.index')
            ->with('success', 'Resource deleted successfully!');
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $resource = Resource::where('school_id', $school->id)
            ->with(['subject', 'topic', 'term', 'classRoom', 'teacher', 'creator'])
            ->findOrFail($id);

        return view('admin.school.resources.show', compact('resource'));
    }
}

