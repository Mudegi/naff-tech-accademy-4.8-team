<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAssignment;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Display all assignments for admin review
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can view all assignments.');
        }

        // Build query for assignments
        $query = StudentAssignment::with(['student', 'resource.subject', 'resource.term', 'resource.teacher'])
            ->orderBy('submitted_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->filled('term_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('term_id', $request->term_id);
            });
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
            });
        }

        if ($request->filled('teacher_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        }

        if ($request->filled('student_name')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student_name . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Get filtered assignments
        $assignments = $query->paginate(20);

        // Get filter options
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $terms = \App\Models\Term::orderBy('name')->get();
        $teachers = \App\Models\User::where('account_type', 'teacher')->orderBy('name')->get();
        $gradeLevels = \App\Models\Resource::distinct()
            ->pluck('grade_level')
            ->filter()
            ->sort()
            ->values();

        return view('admin.assignments.index', compact('assignments', 'subjects', 'terms', 'teachers', 'gradeLevels'));
    }

    /**
     * Display a specific assignment
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can view assignments.');
        }

        $assignment = StudentAssignment::with(['student', 'resource.subject', 'resource.term', 'resource.teacher'])
            ->findOrFail($id);

        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Download the assignment file
     */
    public function download($id)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can download assignments.');
        }

        $assignment = StudentAssignment::with(['student', 'resource'])
            ->findOrFail($id);

        $filePath = storage_path('app/public/' . $assignment->assignment_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Assignment file not found.');
        }

        $fileName = $assignment->student->name . '_' . $assignment->resource->title . '_assignment.' . $assignment->assignment_file_type;
        
        return response()->download($filePath, $fileName);
    }

    /**
     * View teacher assignments (resources with assessment files)
     */
    public function teacherAssignments(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can view teacher assignments.');
        }

        // Build query for resources with assessment files
        $query = Resource::with(['subject', 'term', 'teacher'])
            ->whereNotNull('assessment_tests_path')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Get filtered resources
        $resources = $query->paginate(20);

        // Get filter options
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $terms = \App\Models\Term::orderBy('name')->get();
        $teachers = \App\Models\User::where('account_type', 'teacher')->orderBy('name')->get();
        $gradeLevels = \App\Models\Resource::whereNotNull('assessment_tests_path')
            ->distinct()
            ->pluck('grade_level')
            ->filter()
            ->sort()
            ->values();

        return view('admin.assignments.teacher-assignments', compact('resources', 'subjects', 'terms', 'teachers', 'gradeLevels'));
    }

    /**
     * Display a specific teacher assignment
     */
    public function showTeacherAssignment($id)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can view teacher assignments.');
        }

        $resource = Resource::with(['subject', 'term', 'teacher'])
            ->whereNotNull('assessment_tests_path')
            ->findOrFail($id);

        // Get student submissions for this assignment
        $studentSubmissions = \App\Models\StudentAssignment::with(['student'])
            ->where('resource_id', $id)
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('admin.assignments.teacher-assignment-show', compact('resource', 'studentSubmissions'));
    }

    /**
     * Download teacher assignment file
     */
    public function downloadTeacherAssignment($id)
    {
        $user = Auth::user();
        
        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Access denied. Only admins can download teacher assignments.');
        }

        $resource = Resource::whereNotNull('assessment_tests_path')->findOrFail($id);

        $filePath = storage_path('app/public/' . $resource->assessment_tests_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Assignment file not found.');
        }

        $fileName = $resource->title . '_assignment.' . $resource->assessment_tests_type;
        
        return response()->download($filePath, $fileName);
    }
}
