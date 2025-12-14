<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Project;
use App\Models\StudentAssignment;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(404, 'Student record not found');
        }

        // Get student's school and classes
        $school = $student->school;
        $classes = $user->classes()->get();
        $classIds = $classes->pluck('id')->toArray();

        // Get student's marks (grouped by class and subject)
        $marks = StudentMark::where('student_id', $student->id)
            ->with(['subject', 'class', 'uploadedBy'])
            ->orderBy('class_id')
            ->orderBy('subject_id')
            ->get();

        // Get student's groups
        $groups = GroupMember::where('student_id', $student->id)
            ->with(['group' => function($q) {
                $q->with(['schoolClass', 'teacher', 'members']);
            }])
            ->get()
            ->pluck('group');

        // Get student's projects (through groups)
        $projects = Project::whereIn('group_id', $groups->pluck('id'))
            ->with(['group', 'members'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get student's assignments (from resources in their classes)
        $assignments = StudentAssignment::where('student_id', $student->id)
            ->with(['resource', 'submittedFile'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Count pending assignments (not submitted yet)
        $pendingAssignments = $assignments->filter(fn($a) => !$a->submittedFile)->count();

        // Get resources available in student's classes
        $resources = Resource::whereIn('class_id', $classIds)
            ->where('school_id', $school->id)
            ->with(['teacher', 'schoolClass'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'total_marks' => $marks->count(),
            'total_groups' => $groups->count(),
            'total_projects' => $projects->count(),
            'total_assignments' => $assignments->count(),
            'pending_assignments' => $pendingAssignments,
            'average_percentage' => $this->calculateAveragePercentage($marks),
        ];

        return view('student.dashboard', [
            'student' => $student,
            'school' => $school,
            'classes' => $classes,
            'marks' => $marks,
            'groups' => $groups,
            'projects' => $projects,
            'assignments' => $assignments,
            'resources' => $resources,
            'stats' => $stats,
        ]);
    }

    /**
     * Calculate average percentage for student marks
     */
    private function calculateAveragePercentage($marks)
    {
        if ($marks->isEmpty()) {
            return 0;
        }

        $percentageMarks = $marks->filter(fn($m) => $m->marks_percentage !== null);

        if ($percentageMarks->isEmpty()) {
            return 0;
        }

        return round($percentageMarks->avg('marks_percentage'), 2);
    }
}
