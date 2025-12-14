<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAssignmentController extends Controller
{
    /**
     * Display student's assignments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get student's class IDs
        $classIds = DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        if (empty($classIds)) {
            $assignments = collect();
        } else {
            $query = Assignment::with(['subject', 'classRoom', 'term', 'teacher', 'submissions' => function($q) use ($user) {
                $q->where('student_id', $user->id);
            }])
            ->where('school_id', $user->school_id)
            ->whereIn('class_id', $classIds)
            ->where('is_active', true);
            
            // Apply filters
            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }
            
            if ($request->filled('status')) {
                if ($request->status === 'submitted') {
                    $query->whereHas('submissions', function($q) use ($user) {
                        $q->where('student_id', $user->id);
                    });
                } elseif ($request->status === 'pending') {
                    $query->whereDoesntHave('submissions', function($q) use ($user) {
                        $q->where('student_id', $user->id);
                    });
                }
            }
            
            $assignments = $query->orderBy('due_date', 'asc')->orderBy('created_at', 'desc')->paginate(20);
        }
        
        return view('student.standalone-assignments.index', compact('assignments'));
    }

    /**
     * Show assignment details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Get student's class IDs
        $classIds = DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        $assignment = Assignment::with(['subject', 'classRoom', 'term', 'teacher', 'topic'])
            ->where('school_id', $user->school_id)
            ->whereIn('class_id', $classIds)
            ->where('is_active', true)
            ->findOrFail($id);
        
        // Get student's submission if exists
        $submission = AssignmentSubmission::where('assignment_id', $id)
            ->where('student_id', $user->id)
            ->first();
        
        return view('student.standalone-assignments.show', compact('assignment', 'submission'));
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        
        // Get student's class IDs
        $classIds = DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        $assignment = Assignment::where('school_id', $user->school_id)
            ->whereIn('class_id', $classIds)
            ->where('is_active', true)
            ->findOrFail($id);
        
        // Check if already submitted
        $existingSubmission = AssignmentSubmission::where('assignment_id', $id)
            ->where('student_id', $user->id)
            ->first();
        
        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted this assignment.'
            ], 400);
        }
        
        // Validate request
        $validated = $request->validate([
            'submission_file' => 'required|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:20480',
            'student_comment' => 'nullable|string|max:500',
        ]);
        
        try {
            // Handle file upload
            $file = $request->file('submission_file');
            $path = $file->store('assignment-submissions', 'public');
            
            // Create submission
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $user->id,
                'submission_file_path' => $path,
                'submission_file_type' => $file->getClientOriginalExtension(),
                'student_comment' => $validated['student_comment'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
            
            // Notify teacher
            Notification::create([
                'user_id' => $assignment->teacher_id,
                'type' => 'assignment_submission',
                'title' => 'New Assignment Submission',
                'message' => $user->name . ' submitted assignment: "' . $assignment->title . '"',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assignment submitted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download assignment file
     */
    public function download($id)
    {
        $user = Auth::user();
        
        // Get student's class IDs
        $classIds = DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        $assignment = Assignment::where('school_id', $user->school_id)
            ->whereIn('class_id', $classIds)
            ->findOrFail($id);
        
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
     * Download student's own submission
     */
    public function downloadSubmission($id)
    {
        $user = Auth::user();
        
        $submission = AssignmentSubmission::where('student_id', $user->id)
            ->findOrFail($id);
        
        $filePath = storage_path('app/public/' . $submission->submission_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }
        
        return response()->download($filePath);
    }
}
