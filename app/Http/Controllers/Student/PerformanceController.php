<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentMark;
use App\Models\StudentAssignment;
use App\Models\GroupSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    /**
     * Display comprehensive student performance dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Get all performance data
        $assignmentPerformance = $this->getAssignmentPerformance($userId);
        $examPerformance = $this->getExamPerformance($userId);
        $groupWorkPerformance = $this->getGroupWorkPerformance($userId);
        
        // Calculate overall metrics
        $overallMetrics = $this->calculateOverallMetrics(
            $assignmentPerformance,
            $examPerformance,
            $groupWorkPerformance
        );
        
        // Get subject breakdown
        $subjectBreakdown = $this->getSubjectBreakdown($userId);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($userId);
        
        // Get performance trends (last 6 months)
        $performanceTrends = $this->getPerformanceTrends($userId);
        
        return view('student.performance.index', compact(
            'assignmentPerformance',
            'examPerformance',
            'groupWorkPerformance',
            'overallMetrics',
            'subjectBreakdown',
            'recentActivity',
            'performanceTrends'
        ));
    }

    /**
     * Get assignment performance (standalone homework).
     */
    private function getAssignmentPerformance($userId)
    {
        $submissions = AssignmentSubmission::where('student_id', $userId)
            ->where('status', 'graded')
            ->whereNotNull('grade')
            ->with('assignment')
            ->get();

        $totalSubmissions = $submissions->count();
        $averageGrade = $totalSubmissions > 0 
            ? $submissions->avg(function ($submission) {
                return ($submission->grade / $submission->assignment->total_marks) * 100;
            })
            : 0;

        $onTimeSubmissions = AssignmentSubmission::where('student_id', $userId)
            ->whereHas('assignment', function($query) {
                $query->whereRaw('assignment_submissions.submitted_at <= assignments.due_date');
            })
            ->count();

        $totalAssignments = Assignment::whereHas('submissions', function($query) use ($userId) {
            $query->where('student_id', $userId);
        })->count();

        $onTimeRate = $totalAssignments > 0 ? ($onTimeSubmissions / $totalAssignments) * 100 : 0;

        return [
            'total' => $totalSubmissions,
            'average_grade' => round($averageGrade, 1),
            'on_time_rate' => round($onTimeRate, 1),
            'graded' => $submissions->count(),
            'pending' => AssignmentSubmission::where('student_id', $userId)
                ->where('status', '!=', 'graded')
                ->count()
        ];
    }

    /**
     * Get exam/test performance.
     */
    private function getExamPerformance($userId)
    {
        $marks = StudentMark::where('user_id', $userId)->get();

        $totalExams = $marks->count();
        
        // Calculate average based on grade type
        $averageGrade = 0;
        $validMarks = 0;
        
        foreach ($marks as $mark) {
            if ($mark->grade_type === 'numeric' && $mark->numeric_mark) {
                $averageGrade += $mark->numeric_mark;
                $validMarks++;
            } elseif ($mark->grade_type === 'distinction_credit_pass') {
                // Convert UNEB grades to percentage
                $gradeMap = [
                    'D1' => 95, 'D2' => 85, 'C3' => 75, 'C4' => 70, 'C5' => 65, 'C6' => 60,
                    'P7' => 55, 'P8' => 50, 'F9' => 40
                ];
                if (isset($gradeMap[$mark->grade])) {
                    $averageGrade += $gradeMap[$mark->grade];
                    $validMarks++;
                }
            } elseif ($mark->grade_type === 'letter') {
                // Convert letter grades to percentage
                $gradeMap = ['A' => 90, 'B' => 80, 'C' => 70, 'D' => 60, 'F' => 50];
                if (isset($gradeMap[$mark->grade])) {
                    $averageGrade += $gradeMap[$mark->grade];
                    $validMarks++;
                }
            }
        }

        $averageGrade = $validMarks > 0 ? $averageGrade / $validMarks : 0;

        return [
            'total' => $totalExams,
            'average_grade' => round($averageGrade, 1),
            'principal_passes' => $marks->where('is_principal_pass', true)->count(),
            'aggregate_points' => $this->calculateAggregatePoints($userId)
        ];
    }

    /**
     * Get group work performance.
     */
    private function getGroupWorkPerformance($userId)
    {
        $groupSubmissions = GroupSubmission::whereHas('group.members', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('status', 'graded')
        ->get();

        $totalSubmissions = $groupSubmissions->count();
        // Note: group_submissions table doesn't have grade column
        // This would need to be implemented or grades stored in a related table
        $averageGrade = 0;

        return [
            'total' => $totalSubmissions,
            'average_grade' => round($averageGrade, 1),
            'groups_active' => DB::table('group_members')
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->count()
        ];
    }

    /**
     * Calculate overall performance metrics.
     */
    private function calculateOverallMetrics($assignments, $exams, $groupWork)
    {
        $components = [];
        $weights = [];

        // Weight: Assignments 30%, Exams 50%, Group Work 20%
        if ($assignments['total'] > 0) {
            $components[] = $assignments['average_grade'] * 0.3;
            $weights[] = 0.3;
        }
        if ($exams['total'] > 0) {
            $components[] = $exams['average_grade'] * 0.5;
            $weights[] = 0.5;
        }
        if ($groupWork['total'] > 0) {
            $components[] = $groupWork['average_grade'] * 0.2;
            $weights[] = 0.2;
        }

        $totalWeight = array_sum($weights);
        $overallAverage = $totalWeight > 0 ? array_sum($components) / $totalWeight : 0;

        // Determine letter grade
        $letterGrade = $this->getLetterGrade($overallAverage);

        // Calculate trend (compare with previous period)
        $previousAverage = $this->getPreviousPeriodAverage(Auth::id());
        $trend = $previousAverage > 0 ? (($overallAverage - $previousAverage) / $previousAverage) * 100 : 0;

        return [
            'overall_average' => round($overallAverage, 1),
            'letter_grade' => $letterGrade,
            'trend' => round($trend, 1),
            'trend_direction' => $trend > 2 ? 'improving' : ($trend < -2 ? 'declining' : 'stable'),
            'total_activities' => $assignments['total'] + $exams['total'] + $groupWork['total']
        ];
    }

    /**
     * Get subject-wise breakdown.
     */
    private function getSubjectBreakdown($userId)
    {
        $breakdown = [];

        // Get all subjects from various sources
        $subjects = collect();

        // From exams
        $examSubjects = StudentMark::where('user_id', $userId)
            ->select('subject_name')
            ->distinct()
            ->pluck('subject_name');
        $subjects = $subjects->merge($examSubjects);

        // From assignments
        $assignmentSubjects = DB::table('assignment_submissions')
            ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')
            ->join('subjects', 'assignments.subject_id', '=', 'subjects.id')
            ->where('assignment_submissions.student_id', $userId)
            ->where('assignment_submissions.status', 'graded')
            ->select('subjects.name as subject_name')
            ->distinct()
            ->pluck('subject_name');
        $subjects = $subjects->merge($assignmentSubjects);

        $subjects = $subjects->unique();

        foreach ($subjects as $subject) {
            // Get exam marks
            $examMarks = StudentMark::where('user_id', $userId)
                ->where('subject_name', $subject)
                ->get();

            // Get assignment grades
            $assignmentGrades = DB::table('assignment_submissions')
                ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')
                ->join('subjects', 'assignments.subject_id', '=', 'subjects.id')
                ->where('assignment_submissions.student_id', $userId)
                ->where('subjects.name', $subject)
                ->where('assignment_submissions.status', 'graded')
                ->select('assignment_submissions.grade', 'assignments.total_marks')
                ->get();

            $examCount = $examMarks->count();
            $assignmentCount = $assignmentGrades->count();
            
            if ($examCount > 0 || $assignmentCount > 0) {
                $examAvg = $this->calculateExamAverage($examMarks);
                $assignmentAvg = $assignmentGrades->count() > 0
                    ? $assignmentGrades->avg(fn($a) => ($a->grade / $a->total_marks) * 100)
                    : 0;

                // Weighted average (50% exams, 50% assignments)
                $weights = [];
                $values = [];
                if ($examCount > 0) {
                    $weights[] = 0.5;
                    $values[] = $examAvg * 0.5;
                }
                if ($assignmentCount > 0) {
                    $weights[] = 0.5;
                    $values[] = $assignmentAvg * 0.5;
                }

                $totalWeight = array_sum($weights);
                $subjectAverage = $totalWeight > 0 ? array_sum($values) / $totalWeight : 0;

                $breakdown[] = [
                    'subject' => $subject,
                    'average' => round($subjectAverage, 1),
                    'exam_count' => $examCount,
                    'assignment_count' => $assignmentCount,
                    'letter_grade' => $this->getLetterGrade($subjectAverage)
                ];
            }
        }

        // Sort by average (highest first)
        usort($breakdown, fn($a, $b) => $b['average'] - $a['average']);

        return $breakdown;
    }

    /**
     * Get recent activity timeline.
     */
    private function getRecentActivity($userId)
    {
        $activities = [];

        // Recent assignment submissions
        $recentAssignments = AssignmentSubmission::where('student_id', $userId)
            ->where('status', 'graded')
            ->with('assignment')
            ->orderByDesc('reviewed_at')
            ->limit(5)
            ->get();

        foreach ($recentAssignments as $submission) {
            $percentage = ($submission->grade / $submission->assignment->total_marks) * 100;
            $activities[] = [
                'type' => 'assignment',
                'title' => $submission->assignment->title,
                'grade' => $submission->grade . '/' . $submission->assignment->total_marks,
                'percentage' => round($percentage, 1),
                'letter_grade' => $this->getLetterGrade($percentage),
                'date' => $submission->reviewed_at,
                'status' => $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger')
            ];
        }

        // Recent exam marks
        $recentExams = StudentMark::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($recentExams as $mark) {
            $percentage = $this->convertMarkToPercentage($mark);
            $activities[] = [
                'type' => 'exam',
                'title' => $mark->subject_name . ($mark->paper_name ? ' - ' . $mark->paper_name : ''),
                'grade' => $mark->grade,
                'percentage' => round($percentage, 1),
                'letter_grade' => $this->getLetterGrade($percentage),
                'date' => $mark->created_at,
                'status' => $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger')
            ];
        }

        // Sort by date (most recent first)
        usort($activities, fn($a, $b) => $b['date'] <=> $a['date']);

        return array_slice($activities, 0, 10);
    }

    /**
     * Get performance trends over time.
     */
    private function getPerformanceTrends($userId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');

            // Get activities for this month
            $assignmentAvg = AssignmentSubmission::where('student_id', $userId)
                ->where('status', 'graded')
                ->whereYear('reviewed_at', $date->year)
                ->whereMonth('reviewed_at', $date->month)
                ->with('assignment')
                ->get()
                ->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100) ?? 0;

            $examAvg = StudentMark::where('user_id', $userId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get()
                ->avg(fn($m) => $this->convertMarkToPercentage($m)) ?? 0;

            $monthlyAvg = ($assignmentAvg + $examAvg) / 2;

            $months[] = [
                'month' => $monthLabel,
                'average' => round($monthlyAvg, 1)
            ];
        }

        return $months;
    }

    /**
     * Helper methods
     */
    private function getLetterGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }

    private function calculateExamAverage($marks)
    {
        if ($marks->isEmpty()) return 0;

        $total = 0;
        $count = 0;

        foreach ($marks as $mark) {
            $percentage = $this->convertMarkToPercentage($mark);
            if ($percentage > 0) {
                $total += $percentage;
                $count++;
            }
        }

        return $count > 0 ? $total / $count : 0;
    }

    private function convertMarkToPercentage($mark)
    {
        if ($mark->grade_type === 'numeric' && $mark->numeric_mark) {
            return $mark->numeric_mark;
        } elseif ($mark->grade_type === 'distinction_credit_pass') {
            $gradeMap = [
                'D1' => 95, 'D2' => 85, 'C3' => 75, 'C4' => 70, 'C5' => 65, 'C6' => 60,
                'P7' => 55, 'P8' => 50, 'F9' => 40
            ];
            return $gradeMap[$mark->grade] ?? 0;
        } elseif ($mark->grade_type === 'letter') {
            $gradeMap = ['A' => 90, 'B' => 80, 'C' => 70, 'D' => 60, 'F' => 50];
            return $gradeMap[$mark->grade] ?? 0;
        }
        return 0;
    }

    private function calculateAggregatePoints($userId)
    {
        $principalPasses = StudentMark::where('user_id', $userId)
            ->where('is_principal_pass', true)
            ->orderByDesc('points')
            ->take(3)
            ->get();

        return $principalPasses->count() >= 2 ? $principalPasses->sum('points') : 0;
    }

    private function getPreviousPeriodAverage($userId)
    {
        // Get average from 2 months ago
        $twoMonthsAgo = now()->subMonths(2);
        
        $assignmentAvg = AssignmentSubmission::where('student_id', $userId)
            ->where('status', 'graded')
            ->where('reviewed_at', '<', $twoMonthsAgo)
            ->with('assignment')
            ->get()
            ->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100) ?? 0;

        $examAvg = StudentMark::where('user_id', $userId)
            ->where('created_at', '<', $twoMonthsAgo)
            ->get()
            ->avg(fn($m) => $this->convertMarkToPercentage($m)) ?? 0;

        return ($assignmentAvg + $examAvg) / 2;
    }
}
