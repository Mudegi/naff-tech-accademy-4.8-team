<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Department;
use App\Models\Resource;
use App\Models\StudentAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectorOfStudiesController extends Controller
{
    /**
     * Display the Director of Studies dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies
        if (!$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only Directors of Studies can access this dashboard.');
        }

        // Get educational statistics
        $stats = [
            // Department Statistics
            'total_departments' => Department::where('school_id', $school->id)->count(),
            'active_departments' => Department::where('school_id', $school->id)->where('is_active', true)->count(),
            
            // Staff Statistics
            'total_heads_of_department' => User::where('school_id', $school->id)
                ->where('account_type', 'head_of_department')
                ->count(),
            'total_teachers' => User::where('school_id', $school->id)
                ->where('account_type', 'subject_teacher')
                ->count(),
            'total_staff' => User::where('school_id', $school->id)
                ->whereIn('account_type', ['head_of_department', 'subject_teacher'])
                ->count(),
            
            // Student Statistics
            'total_students' => Student::where('school_id', $school->id)->count(),
            'active_students' => Student::where('school_id', $school->id)
                ->whereHas('user', function($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            
            // Academic Statistics
            'total_subjects' => Subject::where('school_id', $school->id)->count(),
            'total_classes' => SchoolClass::where('school_id', $school->id)->count(),
            'total_resources' => Resource::where('school_id', $school->id)->count(),
            'active_resources' => Resource::where('school_id', $school->id)->where('is_active', true)->count(),
            
            // Assignment Statistics
            'total_assignments' => StudentAssignment::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->count(),
            'submitted_assignments' => StudentAssignment::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->where('status', 'submitted')->count(),
            'reviewed_assignments' => StudentAssignment::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->where('status', 'reviewed')->count(),
            'graded_assignments' => StudentAssignment::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->where('status', 'graded')->count(),
            'average_grade' => StudentAssignment::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->whereNotNull('grade')->avg('grade'),
        ];

        // Get departments with their statistics
        $departments = Department::where('school_id', $school->id)
            ->withCount(['teachers'])
            ->with('headOfDepartment')
            ->orderBy('name')
            ->get();

        // Get recent resources
        $recentResources = Resource::where('school_id', $school->id)
            ->with(['subject', 'term', 'teacher'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent assignments
        $recentAssignments = StudentAssignment::whereHas('student', function($query) use ($school) {
            $query->where('school_id', $school->id);
        })
            ->with(['student', 'resource.subject'])
            ->latest()
            ->take(5)
            ->get();

        // Get department performance (average grades by department)
        $departmentPerformance = Department::where('school_id', $school->id)
            ->with(['teachers' => function($query) {
                $query->where('account_type', 'subject_teacher');
            }])
            ->get()
            ->map(function($department) use ($school) {
                $teacherIds = $department->teachers->pluck('id');
                
                if ($teacherIds->isEmpty()) {
                    return [
                        'department' => $department,
                        'average_grade' => 0,
                        'total_assignments' => 0,
                    ];
                }
                
                $averageGrade = StudentAssignment::whereHas('resource', function($query) use ($teacherIds) {
                    $query->whereIn('teacher_id', $teacherIds);
                })
                ->whereHas('student', function($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->whereNotNull('grade')
                ->avg('grade');
                
                $totalAssignments = StudentAssignment::whereHas('resource', function($query) use ($teacherIds) {
                    $query->whereIn('teacher_id', $teacherIds);
                })
                ->whereHas('student', function($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->count();
                
                return [
                    'department' => $department,
                    'average_grade' => round($averageGrade ?? 0, 2),
                    'total_assignments' => $totalAssignments,
                ];
            })
            ->filter(function($performance) {
                return $performance['total_assignments'] > 0; // Only show departments with assignments
            });

        return view('admin.director-of-studies.dashboard', compact(
            'school',
            'stats',
            'departments',
            'recentResources',
            'recentAssignments',
            'departmentPerformance'
        ));
    }

    /**
     * Show form to select a class for downloading assignment reports
     */
    public function showClassAssignmentReport()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies
        if (!$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only Directors of Studies can access this feature.');
        }

        // Get all classes for this school
        $classes = SchoolClass::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.director-of-studies.class-assignment-report', compact('classes', 'school'));
    }

    /**
     * Download best ranked assignments report for a class
     */
    public function downloadClassAssignmentReport(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies
        if (!$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only Directors of Studies can access this feature.');
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'top_count' => 'nullable|integer|min:1|max:10',
        ]);

        $class = SchoolClass::where('school_id', $school->id)->findOrFail($request->class_id);
        $topCount = $request->get('top_count', 3); // Default to top 3

        // Get all students in this class
        // Students can be linked via class_user pivot table or via the class field in students table
        $studentUserIds = DB::table('class_user')
            ->where('class_id', $class->id)
            ->pluck('user_id')
            ->toArray();

        // Also get students by class name from students table
        $studentsByClass = Student::where('school_id', $school->id)
            ->where('class', $class->name)
            ->pluck('user_id')
            ->toArray();

        // Combine both methods
        $allStudentIds = array_unique(array_merge($studentUserIds, $studentsByClass));

        if (empty($allStudentIds)) {
            return redirect()->back()
                ->with('error', 'No students found in this class.');
        }

        // Get students with their best assignments
        $studentsWithAssignments = User::whereIn('id', $allStudentIds)
            ->where('account_type', 'student')
            ->where('school_id', $school->id)
            ->with(['student'])
            ->get()
            ->map(function($studentUser) use ($topCount) {
                // Get best ranked assignments for this student
                $bestAssignments = StudentAssignment::with([
                    'resource.subject',
                    'resource.term',
                    'resource.topic',
                    'resource.teacher'
                ])
                    ->where('student_id', $studentUser->id)
                    ->whereNotNull('grade')
                    ->orderBy('grade', 'desc')
                    ->orderBy('submitted_at', 'desc')
                    ->take($topCount)
                    ->get();

                return [
                    'student' => $studentUser,
                    'assignments' => $bestAssignments,
                    'has_assignments' => $bestAssignments->count() > 0,
                ];
            })
            ->filter(function($item) {
                return $item['has_assignments']; // Only include students with graded assignments
            })
            ->sortByDesc(function($item) {
                // Sort by highest average grade
                if ($item['assignments']->isEmpty()) {
                    return 0;
                }
                return $item['assignments']->avg('grade');
            });

        if ($studentsWithAssignments->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No graded assignments found for students in this class.');
        }

        // Generate PDF
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.director-of-studies.class-assignment-report-pdf', [
                'class' => $class,
                'school' => $school,
                'studentsWithAssignments' => $studentsWithAssignments,
                'topCount' => $topCount,
                'generatedBy' => $user->name,
            ]);

            $fileName = 'Class_Assignments_Report_' . str_replace(' ', '_', $class->name) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}

