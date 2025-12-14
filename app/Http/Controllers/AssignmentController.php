<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudentAssignment;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display all submitted assignments for the teacher's resources
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can view assignments.');
        }

        // Build query for assignments (only for teacher's resources)
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];

        if (empty($classIds)) {
            $assignments = collect();
            $subjects = \App\Models\Subject::orderBy('name')->get();
            $terms = \App\Models\Term::orderBy('name')->get();
            $topics = \App\Models\Topic::orderBy('name')->get();
            $classes = \App\Models\SchoolClass::orderBy('name')->get();
            $gradeLevels = \App\Models\Resource::where('teacher_id', $user->id)
                ->distinct()
                ->pluck('grade_level')
                ->filter()
                ->sort()
                ->values();

            return view('teacher.assignments.index', compact('assignments', 'subjects', 'terms', 'topics', 'classes', 'gradeLevels'));
        }

        // Build query for assignments filtered to students in teacher's classes
        $query = StudentAssignment::with(['student', 'resource.subject', 'resource.term'])
            ->whereHas('resource', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->whereHas('student', function($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            });

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

        if ($request->filled('topic_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('topic_id', $request->topic_id);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('student_name')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student_name . '%');
            });
        }

        if ($request->filled('grade_range')) {
            switch ($request->grade_range) {
                case 'excellent':
                    $query->where('grade', '>=', 90);
                    break;
                case 'good':
                    $query->whereBetween('grade', [80, 89]);
                    break;
                case 'average':
                    $query->whereBetween('grade', [70, 79]);
                    break;
                case 'below_average':
                    $query->whereBetween('grade', [60, 69]);
                    break;
                case 'poor':
                    $query->where('grade', '<', 60);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Get filtered assignments
        $assignments = $query->orderBy('submitted_at', 'desc')->paginate(20);

        // Get filter options
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $terms = \App\Models\Term::orderBy('name')->get();
        $topics = \App\Models\Topic::orderBy('name')->get();
        $classes = \App\Models\SchoolClass::orderBy('name')->get();
        $gradeLevels = \App\Models\Resource::where('teacher_id', $user->id)
            ->distinct()
            ->pluck('grade_level')
            ->filter()
            ->sort()
            ->values();

        return view('teacher.assignments.index', compact('assignments', 'subjects', 'terms', 'topics', 'classes', 'gradeLevels'));
    }

    /**
     * Display a specific assignment
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can view assignments.');
        }

        $assignment = StudentAssignment::with(['student', 'resource'])
            ->whereHas('resource', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->findOrFail($id);

        // Enforce class-assignment: teacher can only view assignments for students in their classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds) || ! in_array($assignment->student->class_id, $classIds)) {
            abort(403, 'You do not have access to this assignment.');
        }

        return view('teacher.assignments.show', compact('assignment'));
    }

    /**
     * View the assignment file (for preview in iframe)
     */
    public function view($assignment)
    {
        try {
            $user = Auth::user();
            
            // Check if user is a teacher
            if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
                abort(403, 'Access denied. Only teachers can view assignments.');
            }

            // Handle route parameter (could be ID string or model instance from route model binding)
            if (is_object($assignment) && $assignment instanceof StudentAssignment) {
                // Route model binding provided the model directly
                $assignmentModel = $assignment;
            } else {
                // Route parameter is an ID, fetch the model
                $assignmentId = is_numeric($assignment) ? $assignment : $assignment;
                $assignmentModel = StudentAssignment::with('resource')->find($assignmentId);
                
                if (!$assignmentModel) {
                    \Log::warning('Assignment not found', [
                        'assignment_id' => $assignmentId,
                        'user_id' => $user->id
                    ]);
                    abort(404, 'Assignment not found.');
                }
            }
            
            // Load resource relationship if not already loaded
            if (!$assignmentModel->relationLoaded('resource')) {
                $assignmentModel->load('resource');
            }
            
            \Log::info('Viewing assignment', [
                'assignment_id' => $assignmentModel->id,
                'user_id' => $user->id,
                'user_type' => $user->account_type
            ]);
            
            // Check if resource exists
            if (!$assignmentModel->resource) {
                \Log::warning('Assignment resource not found', [
                    'assignment_id' => $assignmentModel->id,
                    'resource_id' => $assignmentModel->resource_id,
                    'user_id' => $user->id
                ]);
                abort(404, 'Assignment resource not found.');
            }
            
            // Check if teacher owns the resource
            if ($assignmentModel->resource->teacher_id !== $user->id) {
                \Log::warning('Teacher does not own assignment resource', [
                    'assignment_id' => $assignmentModel->id,
                    'user_id' => $user->id,
                    'resource_teacher_id' => $assignmentModel->resource->teacher_id
                ]);
                abort(403, 'You do not have permission to view this assignment.');
            }

            // Enforce class-assignment restriction
            $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
            if (empty($classIds) || ! in_array($assignmentModel->student->class_id, $classIds)) {
                \Log::warning('Teacher not assigned to student class', [
                    'assignment_id' => $assignmentModel->id,
                    'user_id' => $user->id,
                    'student_class_id' => $assignmentModel->student->class_id ?? null
                ]);
                abort(403, 'You do not have permission to view this assignment.');
            }
            
            $assignment = $assignmentModel;

            // Check if assignment_file_path exists
            if (empty($assignment->assignment_file_path)) {
                \Log::warning('Assignment file path is empty', [
                    'assignment_id' => $assignment->id,
                    'user_id' => $user->id
                ]);
                abort(404, 'Assignment file path is not set.');
            }

            $filePath = storage_path('app/public/' . $assignment->assignment_file_path);
            
            // Try alternative paths if the file doesn't exist
            if (!file_exists($filePath)) {
                // Try without 'app/public' prefix (in case path already includes it)
                $altPath = storage_path($assignment->assignment_file_path);
                if (file_exists($altPath)) {
                    $filePath = $altPath;
                } else {
                    // Try public storage path
                    $publicPath = public_path('storage/' . $assignment->assignment_file_path);
                    if (file_exists($publicPath)) {
                        $filePath = $publicPath;
                    } else {
                        \Log::error('Assignment file not found', [
                            'assignment_id' => $assignment->id,
                            'file_path' => $assignment->assignment_file_path,
                            'attempted_paths' => [
                                'primary' => storage_path('app/public/' . $assignment->assignment_file_path),
                                'alt1' => storage_path($assignment->assignment_file_path),
                                'alt2' => public_path('storage/' . $assignment->assignment_file_path),
                            ],
                            'storage_exists' => is_dir(storage_path('app/public')),
                            'public_storage_exists' => is_dir(public_path('storage')),
                        ]);
                        abort(404, 'Assignment file not found. Please contact support.');
                    }
                }
            }

            $mimeType = mime_content_type($filePath);
            
            if (!$mimeType) {
                // Fallback MIME type based on file extension
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                ];
                $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
            }
            
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($assignment->assignment_file_path) . '"',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $assignmentId = is_object($assignment) && isset($assignment->id) ? $assignment->id : (is_numeric($assignment) ? $assignment : 'unknown');
            \Log::error('Assignment not found', [
                'assignment_id' => $assignmentId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            abort(404, 'Assignment not found or you do not have permission to view it.');
        } catch (\Exception $e) {
            $assignmentId = is_object($assignment) && isset($assignment->id) ? $assignment->id : (is_numeric($assignment) ? $assignment : 'unknown');
            \Log::error('Error viewing assignment', [
                'assignment_id' => $assignmentId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'An error occurred while viewing the assignment.');
        }
    }

    /**
     * Download the assignment file
     */
    public function download($id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can download assignments.');
        }

        $assignment = StudentAssignment::with('resource')
            ->whereHas('resource', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->findOrFail($id);

        // Enforce class-assignment: teacher can only download assignments for students in their classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds) || ! in_array($assignment->student->class_id, $classIds)) {
            abort(403, 'You do not have access to this assignment.');
        }

        $filePath = storage_path('app/public/' . $assignment->assignment_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Assignment file not found.');
        }

        $fileName = $assignment->student->name . '_' . $assignment->resource->title . '_assignment.' . $assignment->assignment_file_type;
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Update assignment status and feedback
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can update assignments.');
        }

        $assignment = StudentAssignment::with('resource')
            ->whereHas('resource', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->findOrFail($id);

        // Enforce class-assignment: teacher can only update assignments for students in their classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds) || ! in_array($assignment->student->class_id, $classIds)) {
            abort(403, 'You do not have access to update this assignment.');
        }

        $validated = $request->validate([
            'status' => 'required|in:submitted,reviewed,graded',
            'teacher_feedback' => 'nullable|string|max:1000',
            'grade' => 'nullable|integer|min:0|max:100',
        ]);

        // Store original values to detect changes
        $originalFeedback = $assignment->teacher_feedback;
        $originalGrade = $assignment->grade;
        $originalStatus = $assignment->status;

        $assignment->update([
            'status' => $validated['status'],
            'teacher_feedback' => $validated['teacher_feedback'],
            'grade' => $validated['grade'],
            'reviewed_at' => now(),
        ]);

        // If a grade was provided, create/update a StudentMark record
        if (isset($validated['grade'])) {
            try {
                $resource = $assignment->resource;
                $subjectName = $resource && $resource->subject ? $resource->subject->name : ($resource->title ?? 'Assignment');
                $paperName = $resource->title ?? 'Assignment';

                // Remove any existing student marks for this assignment title
                \App\Models\StudentMark::where('user_id', $assignment->student_id)
                    ->where('subject_name', 'Assignment: ' . $paperName)
                    ->delete();

                \App\Models\StudentMark::create([
                    'user_id' => $assignment->student_id,
                    'student_id' => $assignment->student_id,
                    'academic_level' => null,
                    'subject_name' => 'Assignment: ' . $paperName,
                    'paper_name' => $paperName,
                    'numeric_mark' => $validated['grade'],
                    'grade_type' => 'numeric',
                    'remarks' => $validated['teacher_feedback'] ?? null,
                    'school_id' => $resource->school_id ?? null,
                    'uploaded_by' => $user->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create StudentMark from assignment grade', ['error' => $e->getMessage(), 'assignment_id' => $assignment->id]);
            }
        }

        // Create detailed notification based on what was updated
        $notificationTitle = '';
        $notificationMessage = '';
        $notificationType = 'assignment_updated';

        // Determine what was updated and create appropriate notification
        if ($validated['grade'] && $validated['grade'] !== $originalGrade) {
            // Grade was added or updated
            $notificationTitle = 'Assignment Graded';
            $notificationMessage = 'Your assignment for "' . $assignment->resource->title . '" has been graded. You received ' . $validated['grade'] . '%.';
            $notificationType = 'assignment_graded';
            
            if ($validated['teacher_feedback'] && $validated['teacher_feedback'] !== $originalFeedback) {
                $notificationMessage .= ' Your teacher also provided feedback.';
            }
        } elseif ($validated['teacher_feedback'] && $validated['teacher_feedback'] !== $originalFeedback) {
            // Only feedback was added or updated
            $notificationTitle = 'Assignment Feedback';
            $notificationMessage = 'Your teacher has provided feedback on your assignment for "' . $assignment->resource->title . '".';
            $notificationType = 'assignment_feedback';
        } elseif ($validated['status'] !== $originalStatus) {
            // Only status was changed
            $notificationTitle = 'Assignment Status Updated';
            $notificationMessage = 'The status of your assignment for "' . $assignment->resource->title . '" has been updated to ' . ucfirst($validated['status']) . '.';
            $notificationType = 'assignment_status_updated';
        } else {
            // Fallback notification
            $notificationTitle = 'Assignment Updated';
            $notificationMessage = 'Your assignment for "' . $assignment->resource->title . '" has been updated by your teacher.';
        }

        // Create notification for student
        \App\Models\Notification::create([
            'user_id' => $assignment->student_id,
            'resource_id' => $assignment->resource_id,
            'type' => $notificationType,
            'title' => $notificationTitle,
            'message' => $notificationMessage,
        ]);

        // Return JSON for AJAX, otherwise redirect back with a flash message
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Assignment updated successfully!');
    }

    /**
     * Get assignments by resource
     */
    public function byResource($resourceId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can view assignments.');
        }

        $resource = Resource::where('teacher_id', $user->id)->findOrFail($resourceId);

        // Enforce class-assignment: teacher can only view assignments for students in their classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds) && $resource->class_id) {
            // teacher has no classes assigned but resource has a class
            abort(403);
        }
        
        $assignments = StudentAssignment::with(['student'])
            ->where('resource_id', $resourceId)
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('teacher.assignments.by-resource', compact('assignments', 'resource'));
    }

    /**
     * Bulk grading form
     */
    public function bulkForm()
    {
        $user = Auth::user();
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            return redirect()->route('teacher.assignments.index')->with('error', 'You are not assigned to any class.');
        }

        $assignments = StudentAssignment::with(['student', 'resource'])
            ->whereHas('resource', function($q) use ($user) { $q->where('teacher_id', $user->id); })
            ->whereHas('student', function($q) use ($classIds) { $q->whereIn('class_id', $classIds); })
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('teacher.assignments.bulk', compact('assignments'));
    }

    /**
     * Process bulk grading
     */
    public function bulkSubmit(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'assignments' => 'required|array',
            'assignments.*' => 'integer|exists:student_assignments,id',
            'grades' => 'array',
        ]);

        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];

        $updated = 0;
        foreach ($data['assignments'] as $aid) {
            $grade = $request->input('grades.' . $aid);
            if ($grade === null || $grade === '') continue;

            $assignment = StudentAssignment::with('resource', 'student')
                ->whereHas('resource', function($q) use ($user) { $q->where('teacher_id', $user->id); })
                ->find($aid);

            if (! $assignment) continue;
            if (empty($classIds) || ! in_array($assignment->student->class_id, $classIds)) continue;

            $assignment->update([
                'status' => 'graded',
                'grade' => intval($grade),
                'teacher_feedback' => $assignment->teacher_feedback,
                'reviewed_at' => now(),
            ]);

            // Create StudentMark
            try {
                $resource = $assignment->resource;
                $paperName = $resource->title ?? 'Assignment';
                \App\Models\StudentMark::where('user_id', $assignment->student_id)
                    ->where('subject_name', 'Assignment: ' . $paperName)
                    ->delete();

                \App\Models\StudentMark::create([
                    'user_id' => $assignment->student_id,
                    'student_id' => $assignment->student_id,
                    'subject_name' => 'Assignment: ' . $paperName,
                    'paper_name' => $paperName,
                    'numeric_mark' => intval($grade),
                    'grade_type' => 'numeric',
                    'school_id' => $resource->school_id ?? null,
                    'uploaded_by' => $user->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Bulk grading error', ['error' => $e->getMessage(), 'assignment_id' => $assignment->id]);
            }

            $updated++;
        }

        return redirect()->route('teacher.assignments.index')->with('success', "Bulk graded {$updated} assignments.");
    }

    /**
     * Download student scores report
     */
    public function downloadStudentScoresReport(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Access denied. Only teachers can download student scores reports.');
        }

        // Build query for assignments
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403, 'You are not assigned to any class.');
        }

        $query = StudentAssignment::with(['student', 'resource.subject', 'resource.term', 'resource.topic'])
            ->whereHas('resource', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->whereHas('student', function($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            })
            ->whereNotNull('grade'); // Only include graded assignments

        // Apply filters
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

        if ($request->filled('topic_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('topic_id', $request->topic_id);
            });
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        if ($request->filled('grade_range')) {
            switch ($request->grade_range) {
                case 'excellent':
                    $query->where('grade', '>=', 90);
                    break;
                case 'good':
                    $query->whereBetween('grade', [80, 89]);
                    break;
                case 'average':
                    $query->whereBetween('grade', [70, 79]);
                    break;
                case 'below_average':
                    $query->whereBetween('grade', [60, 69]);
                    break;
                case 'poor':
                    $query->where('grade', '<', 60);
                    break;
            }
        }

        $assignments = $query->orderBy('submitted_at', 'desc')->get();

        // Calculate statistics
        $totalAssignments = $assignments->count();
        $averageGrade = $totalAssignments > 0 ? round($assignments->avg('grade'), 2) : 0;
        $highestGrade = $assignments->max('grade');
        $lowestGrade = $assignments->min('grade');

        // Grade distribution
        $gradeDistribution = [
            'excellent' => $assignments->where('grade', '>=', 90)->count(),
            'good' => $assignments->whereBetween('grade', [80, 89])->count(),
            'average' => $assignments->whereBetween('grade', [70, 79])->count(),
            'below_average' => $assignments->whereBetween('grade', [60, 69])->count(),
            'poor' => $assignments->where('grade', '<', 60)->count(),
        ];

        // Generate HTML report
        $html = view('teacher.student-scores-report', compact('assignments', 'user', 'totalAssignments', 'averageGrade', 'highestGrade', 'lowestGrade', 'gradeDistribution'))->render();
        
        $fileName = 'Student_Scores_Report_' . now()->format('Y-m-d') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
