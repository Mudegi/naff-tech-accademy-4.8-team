<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\StudentAssignment;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Term;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    /**
     * Display the assessment management page for teachers
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can access this page.');
        }
        
        // Get all video resources that teacher can add notes to
        // If teacher has school_id, show resources from their school or global resources
        // If teacher has no school_id, show all global resources
        $query = Resource::whereNotNull('google_drive_link')
            ->with(['subject', 'classRoom', 'topic', 'teacher', 'creator']);
        
        if ($user->school_id) {
            $query->where(function($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereNull('school_id'); // Include global resources
            });
        } else {
            $query->whereNull('school_id'); // Only global resources for teachers without school
        }
        
        $resources = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('teacher.assessments.index', compact('resources'));
    }
    
    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can access this page.');
        }
        
        // Get all resources created by this teacher
        $resources = Resource::where('teacher_id', $user->id)
            ->with(['subject', 'classRoom', 'topic'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('teacher.assessments.create', compact('resources'));
    }
    
    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return response()->json(['success' => false, 'message' => 'Only teachers can upload assignments.'], 403);
        }
        
        // Validate the request
        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'assignment_type' => 'required|in:assessment,notes',
            'assignment_file' => 'required|file|max:10240'
        ]);
        
        // Check if the resource belongs to this teacher
        $resource = Resource::where('id', $request->resource_id)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        // Validate file type based on assignment type
        if ($request->assignment_type === 'assessment') {
            $request->validate(['assignment_file' => 'mimes:pdf']);
        } else {
            $request->validate(['assignment_file' => 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx']);
        }
        
        try {
            // Handle file upload
            if ($request->hasFile('assignment_file')) {
                $file = $request->file('assignment_file');
                
                if ($request->assignment_type === 'assessment') {
                    $path = $file->store('resources/assessments', 'public');
                    $resource->assessment_tests_path = $path;
                    $resource->assessment_tests_type = $file->getClientOriginalExtension();
                } else {
                    $path = $file->store('resources/notes', 'public');
                    $resource->notes_file_path = $path;
                    $resource->notes_file_type = $file->getClientOriginalExtension();
                }
                
                $resource->save();
                
                return response()->json([
                    'success' => true, 
                    'message' => ucfirst($request->assignment_type) . ' uploaded successfully!'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Show assessment details for a specific resource
     */
    public function show($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can access this page.');
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->with(['subject', 'classRoom', 'topic'])
            ->firstOrFail();
        
        // Get student assignments for this resource
        $assignments = StudentAssignment::where('resource_id', $resource->id)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return view('teacher.assessments.show', compact('resource', 'assignments'));
    }
    
    /**
     * Upload assessment test for a resource
     */
    public function uploadAssessment(Request $request, $resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return response()->json(['success' => false, 'message' => 'Only teachers can upload assessment tests.'], 403);
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        // Validate the request
        $validated = $request->validate([
            'assessment_tests' => 'required|file|mimes:pdf|max:10240'
        ]);
        
        try {
            // Handle file upload
            if ($request->hasFile('assessment_tests')) {
                $file = $request->file('assessment_tests');
                $path = $file->store('resources/assessments', 'public');
                
                // Update the resource
                $resource->assessment_tests_path = $path;
                $resource->assessment_tests_type = $file->getClientOriginalExtension();
                $resource->save();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Assessment test uploaded successfully!'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Upload notes/study materials for a resource
     */
    public function uploadNotes(Request $request, $resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return response()->json(['success' => false, 'message' => 'Only teachers can upload study materials.'], 403);
        }
        
        // Allow teachers to add notes to any video resource (not just their own)
        $resource = Resource::where('id', $resourceId)
            ->whereNotNull('google_drive_link') // Must be a video resource
            ->firstOrFail();
        
        // If resource has a school_id, ensure teacher belongs to that school
        if ($resource->school_id && $user->school_id != $resource->school_id) {
            return response()->json(['success' => false, 'message' => 'You can only add notes to resources from your school.'], 403);
        }
        
        // Validate the request
        $validated = $request->validate([
            'notes_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240'
        ]);
        
        try {
            // Handle file upload
            if ($request->hasFile('notes_file')) {
                $file = $request->file('notes_file');
                $path = $file->store('resources/notes', 'public');
                
                // Update the resource
                $resource->notes_file_path = $path;
                $resource->notes_file_type = $file->getClientOriginalExtension();
                $resource->save();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Study material uploaded successfully!'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Download assessment test
     */
    public function downloadAssessment($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can download assessment tests.');
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        if (!$resource->assessment_tests_path) {
            abort(404, 'Assessment test not found.');
        }
        
        $filePath = storage_path('app/public/' . $resource->assessment_tests_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }
        
        return response()->download($filePath, 'assessment_' . $resource->title . '.' . $resource->assessment_tests_type);
    }
    
    /**
     * Download notes/study materials
     */
    public function downloadNotes($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can download study materials.');
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        if (!$resource->notes_file_path) {
            abort(404, 'Study material not found.');
        }
        
        $filePath = storage_path('app/public/' . $resource->notes_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }
        
        return response()->download($filePath, 'notes_' . $resource->title . '.' . $resource->notes_file_type);
    }
    
    /**
     * Delete assessment test
     */
    public function deleteAssessment($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return response()->json(['success' => false, 'message' => 'Only teachers can delete assessment tests.'], 403);
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        if ($resource->assessment_tests_path) {
            // Delete file from storage
            Storage::disk('public')->delete($resource->assessment_tests_path);
            
            // Clear database fields
            $resource->assessment_tests_path = null;
            $resource->assessment_tests_type = null;
            $resource->save();
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Assessment test deleted successfully!'
        ]);
    }
    
    /**
     * Delete notes/study materials
     */
    public function deleteNotes($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return response()->json(['success' => false, 'message' => 'Only teachers can delete study materials.'], 403);
        }
        
        $resource = Resource::where('id', $resourceId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        
        if ($resource->notes_file_path) {
            // Delete file from storage
            Storage::disk('public')->delete($resource->notes_file_path);
            
            // Clear database fields
            $resource->notes_file_path = null;
            $resource->notes_file_type = null;
            $resource->save();
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Study material deleted successfully!'
        ]);
    }
}
