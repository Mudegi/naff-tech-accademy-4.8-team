<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Term;
use App\Models\Topic;
use App\Models\StudentMark;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StandaloneAssignmentController extends Controller
{
    /**
     * Display a listing of assignments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Assignment::with(['subject', 'classRoom', 'term', 'submissions'])
            ->where('teacher_id', $user->id)
            ->where('school_id', $user->school_id);
        
        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get filter options
        $subjects = Subject::where('school_id', $user->school_id)->orWhereNull('school_id')->get();
        $classes = SchoolClass::whereNull('school_id')->get();
        $terms = Term::all();
        
        return view('teacher.standalone-assignments.index', compact('assignments', 'subjects', 'classes', 'terms'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get subjects assigned to teacher
        $subjectIds = DB::table('subject_user')
            ->where('user_id', $user->id)
            ->pluck('subject_id')
            ->toArray();
        
        if (!empty($subjectIds)) {
            $subjects = Subject::withoutGlobalScope('school')
                ->whereIn('id', $subjectIds)
                ->get();
        } else {
            // If no subjects assigned, get all school subjects as fallback
            $subjects = Subject::where('school_id', $user->school_id)->get();
        }
        
        // Get classes assigned to teacher
        $classIds = DB::table('class_user')
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
        
        $terms = Term::all();
        $topics = Topic::where('school_id', $user->school_id)->orWhereNull('school_id')->get();
        
        return view('teacher.standalone-assignments.create', compact('subjects', 'classes', 'terms', 'topics'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $subject = Subject::withoutGlobalScope('school')->find($value);
                    if (!$subject) {
                        $fail('The selected subject is invalid.');
                    }
                },
            ],
            'class_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $class = SchoolClass::withoutGlobalScope('school')->find($value);
                    if (!$class) {
                        $fail('The selected class is invalid.');
                    }
                },
            ],
            'term_id' => 'nullable|exists:terms,id',
            'topic_id' => 'nullable|exists:topics,id',
            'due_date' => 'nullable|date',
            'total_marks' => 'required|integer|min:1|max:1000',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:20480',
        ]);

        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            $file = $request->file('assignment_file');
            $path = $file->store('assignments', 'public');
            $validated['assignment_file_path'] = $path;
            $validated['assignment_file_type'] = $file->getClientOriginalExtension();
        }
        
        $validated['teacher_id'] = $user->id;
        $validated['school_id'] = $user->school_id;
        $validated['is_active'] = true;
        
        try {
            $assignment = Assignment::create($validated);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['general' => 'Failed to create assignment: ' . $e->getMessage()])->withInput();
        }
        
        return redirect()->route('teacher.standalone-assignments.index')
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Display the specified assignment with submissions
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::with([
            'subject',
            'classRoom',
            'term',
            'topic',
            'submissions.student'
        ])
        ->where('teacher_id', $user->id)
        ->findOrFail($id);
        
        return view('teacher.standalone-assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the assignment
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($id);
        
        // Only show subjects that the teacher teaches
        $subjects = $user->subjects()->get();
        
        // Get classes assigned to this teacher
        $classes = $user->classes()->get();
        
        $terms = Term::all();
        $topics = Topic::where('school_id', $user->school_id)->orWhereNull('school_id')->get();
        
        return view('teacher.standalone-assignments.edit', compact('assignment', 'subjects', 'classes', 'terms', 'topics'));
    }

    /**
     * Update the specified assignment
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'topic_id' => 'nullable|exists:topics,id',
            'due_date' => 'nullable|date',
            'total_marks' => 'required|integer|min:1|max:1000',
            'is_active' => 'boolean',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:20480',
        ]);

        // Verify teacher teaches this subject
        if (!$user->teachesSubject($validated['subject_id'])) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'You are not assigned to teach this subject. You can only update assignments for subjects you teach.'])
                ->withInput();
        }
        
        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            // Delete old file if exists
            if ($assignment->assignment_file_path) {
                Storage::disk('public')->delete($assignment->assignment_file_path);
            }
            
            $file = $request->file('assignment_file');
            $path = $file->store('assignments', 'public');
            $validated['assignment_file_path'] = $path;
            $validated['assignment_file_type'] = $file->getClientOriginalExtension();
        }
        
        $assignment->update($validated);
        
        return redirect()->route('teacher.standalone-assignments.show', $assignment->id)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($id);
        
        // Delete file if exists
        if ($assignment->assignment_file_path) {
            Storage::disk('public')->delete($assignment->assignment_file_path);
        }
        
        $assignment->delete();
        
        return redirect()->route('teacher.standalone-assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Download assignment file
     */
    public function download($id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($id);
        
        if (!$assignment->assignment_file_path) {
            abort(404, 'Assignment file not found');
        }
        
        $filePath = storage_path('app/public/' . $assignment->assignment_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }
        
        return response()->download($filePath);
    }

    /**
     * View submissions for an assignment
     */
    public function submissions($id)
    {
        $user = Auth::user();
        
        $assignment = Assignment::with(['subject', 'classRoom', 'term'])
            ->where('teacher_id', $user->id)
            ->findOrFail($id);
        
        $submissions = AssignmentSubmission::with('student')
            ->where('assignment_id', $id)
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        return view('teacher.standalone-assignments.submissions', compact('assignment', 'submissions'));
    }

    /**
     * View a specific submission
     */
    public function viewSubmission($assignmentId, $submissionId)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($assignmentId);
        $submission = AssignmentSubmission::with('student')->findOrFail($submissionId);
        
        return view('teacher.standalone-assignments.view-submission', compact('assignment', 'submission'));
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission(Request $request, $assignmentId, $submissionId)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($assignmentId);
        $submission = AssignmentSubmission::findOrFail($submissionId);
        
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:' . $assignment->total_marks,
            'teacher_feedback' => 'nullable|string|max:1000',
            'status' => 'required|in:submitted,reviewed,graded',
        ]);
        
        $submission->update([
            'grade' => $validated['grade'],
            'teacher_feedback' => $validated['teacher_feedback'],
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ]);
        
        // Create StudentMark record
        StudentMark::updateOrCreate(
            [
                'user_id' => $submission->student_id,
                'subject_name' => 'Assignment: ' . $assignment->title,
                'exam_type' => 'assignment',
            ],
            [
                'numeric_mark' => $validated['grade'],
                'total_marks' => $assignment->total_marks,
                'percentage' => ($validated['grade'] / $assignment->total_marks) * 100,
                'grade' => $this->calculateGrade(($validated['grade'] / $assignment->total_marks) * 100),
                'remarks' => $validated['teacher_feedback'],
                'school_id' => $user->school_id,
            ]
        );
        
        // Send notification to student
        Notification::create([
            'user_id' => $submission->student_id,
            'type' => 'assignment_graded',
            'title' => 'Assignment Graded',
            'message' => 'Your assignment "' . $assignment->title . '" has been graded. You scored ' . $validated['grade'] . '/' . $assignment->total_marks,
        ]);
        
        return back()->with('success', 'Submission graded successfully!');
    }

    /**
     * Download submission file
     */
    public function downloadSubmission($assignmentId, $submissionId)
    {
        $user = Auth::user();
        
        $assignment = Assignment::where('teacher_id', $user->id)->findOrFail($assignmentId);
        $submission = AssignmentSubmission::findOrFail($submissionId);
        
        $filePath = storage_path('app/public/' . $submission->submission_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }
        
        return response()->download($filePath);
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}
