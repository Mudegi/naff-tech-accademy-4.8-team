<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AssignmentSubmission;
use App\Models\StudentMark;
use App\Models\GroupSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = Auth::user();
        
        // Get all children linked to this parent
        $children = $parent->children()->with(['student', 'classes', 'school'])->get();
        
        if ($children->isEmpty()) {
            return view('parent.parent-dashboard', [
                'children' => collect(),
                'childrenData' => [],
                'overallSummary' => null
            ]);
        }
        
        // Get performance summary for each child
        $childrenData = [];
        foreach ($children as $child) {
            $performance = $this->getChildPerformance($child->id);
            $recentActivity = $this->getRecentActivity($child->id);
            $alerts = $this->getAlerts($child->id);
            
            $childrenData[] = [
                'student' => $child,
                'performance' => $performance,
                'recent_activity' => $recentActivity,
                'alerts' => $alerts
            ];
        }
        
        // Calculate overall summary across all children
        $overallSummary = $this->calculateOverallSummary($childrenData);
        
        return view('parent.parent-dashboard', compact('childrenData', 'overallSummary', 'children'));
    }

    /**
     * View detailed performance for a specific child.
     */
    public function showChild($studentId)
    {
        $parent = Auth::user();
        
        // Verify this child belongs to this parent
        $child = $parent->children()->where('student_id', $studentId)->first();
        
        if (!$child) {
            abort(403, 'Unauthorized access to student data.');
        }
        
        // Get comprehensive performance data
        $assignmentPerformance = $this->getAssignmentPerformance($studentId);
        $examPerformance = $this->getExamPerformance($studentId);
        $groupWorkPerformance = $this->getGroupWorkPerformance($studentId);
        $subjectBreakdown = $this->getSubjectBreakdown($studentId);
        $recentActivity = $this->getRecentActivity($studentId, 20);
        $performanceTrends = $this->getPerformanceTrends($studentId);
        $overallMetrics = $this->calculateOverallMetrics($assignmentPerformance, $examPerformance, $groupWorkPerformance);
        
        return view('parent.child-performance', compact(
            'child',
            'assignmentPerformance',
            'examPerformance',
            'groupWorkPerformance',
            'subjectBreakdown',
            'recentActivity',
            'performanceTrends',
            'overallMetrics'
        ));
    }

    /**
     * Get child's overall performance summary.
     */
    private function getChildPerformance($studentId)
    {
        // Assignments
        $assignments = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->with('assignment')
            ->get();
        
        $assignmentAvg = $assignments->count() > 0
            ? $assignments->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100)
            : 0;
        
        // Exams
        $exams = StudentMark::where('user_id', $studentId)->get();
        $examAvg = $this->calculateExamAverage($exams);
        
        // Overall average (weighted)
        $overallAvg = 0;
        $weights = 0;
        if ($assignments->count() > 0) {
            $overallAvg += $assignmentAvg * 0.4;
            $weights += 0.4;
        }
        if ($exams->count() > 0) {
            $overallAvg += $examAvg * 0.6;
            $weights += 0.6;
        }
        $overallAvg = $weights > 0 ? $overallAvg / $weights : 0;
        
        return [
            'overall_average' => round($overallAvg, 1),
            'letter_grade' => $this->getLetterGrade($overallAvg),
            'total_assignments' => $assignments->count(),
            'total_exams' => $exams->count(),
            'pending_assignments' => AssignmentSubmission::where('student_id', $studentId)
                ->where('status', '!=', 'graded')
                ->count()
        ];
    }

    /**
     * Get recent activity for a child.
     */
    private function getRecentActivity($studentId, $limit = 5)
    {
        $activities = [];
        
        // Recent assignments
        $recentAssignments = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->with('assignment')
            ->orderByDesc('reviewed_at')
            ->limit($limit)
            ->get();
        
        foreach ($recentAssignments as $submission) {
            $percentage = ($submission->grade / $submission->assignment->total_marks) * 100;
            $activities[] = [
                'type' => 'assignment',
                'title' => $submission->assignment->title,
                'grade' => $submission->grade . '/' . $submission->assignment->total_marks,
                'percentage' => round($percentage, 1),
                'date' => $submission->reviewed_at,
                'status' => $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger')
            ];
        }
        
        // Sort by date
        usort($activities, fn($a, $b) => $b['date'] <=> $a['date']);
        
        return array_slice($activities, 0, $limit);
    }

    /**
     * Get alerts for a child.
     */
    private function getAlerts($studentId)
    {
        $alerts = [];
        
        // Low grades alert
        $failingGrades = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->with('assignment')
            ->get()
            ->filter(function($submission) {
                $percentage = ($submission->grade / $submission->assignment->total_marks) * 100;
                return $percentage < 50;
            });
        
        if ($failingGrades->count() >= 2) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'message' => $failingGrades->count() . ' assignments with grades below 50%',
                'action' => 'Review performance'
            ];
        }
        
        // Pending assignments
        $pending = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'submitted')
            ->count();
        
        if ($pending > 3) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'clock',
                'message' => $pending . ' assignments awaiting grading',
                'action' => 'Check with teacher'
            ];
        }
        
        // Missing exam marks
        $examCount = StudentMark::where('user_id', $studentId)->count();
        if ($examCount == 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'file-alt',
                'message' => 'No exam results uploaded yet',
                'action' => 'Contact school'
            ];
        }
        
        return $alerts;
    }

    /**
     * Calculate overall summary across all children.
     */
    private function calculateOverallSummary($childrenData)
    {
        if (empty($childrenData)) return null;
        
        $totalChildren = count($childrenData);
        $performingWell = 0;
        $needsAttention = 0;
        $totalActivities = 0;
        $totalAlerts = 0;
        
        foreach ($childrenData as $data) {
            $avg = $data['performance']['overall_average'];
            if ($avg >= 70) $performingWell++;
            if ($avg < 50) $needsAttention++;
            
            $totalActivities += $data['performance']['total_assignments'] + $data['performance']['total_exams'];
            $totalAlerts += count($data['alerts']);
        }
        
        return [
            'total_children' => $totalChildren,
            'performing_well' => $performingWell,
            'needs_attention' => $needsAttention,
            'total_activities' => $totalActivities,
            'total_alerts' => $totalAlerts
        ];
    }

    // Helper methods (same as PerformanceController)
    private function getAssignmentPerformance($userId)
    {
        $submissions = AssignmentSubmission::where('student_id', $userId)
            ->where('status', 'graded')
            ->whereNotNull('grade')
            ->with('assignment')
            ->get();

        $totalSubmissions = $submissions->count();
        $averageGrade = $totalSubmissions > 0 
            ? $submissions->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100)
            : 0;

        return [
            'total' => $totalSubmissions,
            'average_grade' => round($averageGrade, 1),
            'graded' => $submissions->count(),
            'pending' => AssignmentSubmission::where('student_id', $userId)
                ->where('status', '!=', 'graded')
                ->count()
        ];
    }

    private function getExamPerformance($userId)
    {
        $marks = StudentMark::where('user_id', $userId)->get();
        $averageGrade = $this->calculateExamAverage($marks);

        return [
            'total' => $marks->count(),
            'average_grade' => round($averageGrade, 1),
            'principal_passes' => $marks->where('is_principal_pass', true)->count()
        ];
    }

    private function getGroupWorkPerformance($userId)
    {
        $groupSubmissions = GroupSubmission::whereHas('group.members', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('grade')
        ->with('assignment')
        ->get();

        $totalSubmissions = $groupSubmissions->count();
        $averageGrade = $totalSubmissions > 0
            ? $groupSubmissions->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100)
            : 0;

        return [
            'total' => $totalSubmissions,
            'average_grade' => round($averageGrade, 1)
        ];
    }

    private function calculateOverallMetrics($assignments, $exams, $groupWork)
    {
        $components = [];
        $weights = [];

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

        return [
            'overall_average' => round($overallAverage, 1),
            'letter_grade' => $this->getLetterGrade($overallAverage),
            'total_activities' => $assignments['total'] + $exams['total'] + $groupWork['total']
        ];
    }

    private function getSubjectBreakdown($userId)
    {
        $breakdown = [];
        $subjects = collect();

        $examSubjects = StudentMark::where('user_id', $userId)
            ->select('subject_name')
            ->distinct()
            ->pluck('subject_name');
        $subjects = $subjects->merge($examSubjects);

        $subjects = $subjects->unique();

        foreach ($subjects as $subject) {
            $examMarks = StudentMark::where('user_id', $userId)
                ->where('subject_name', $subject)
                ->get();

            $examAvg = $this->calculateExamAverage($examMarks);

            if ($examMarks->count() > 0) {
                $breakdown[] = [
                    'subject' => $subject,
                    'average' => round($examAvg, 1),
                    'exam_count' => $examMarks->count(),
                    'letter_grade' => $this->getLetterGrade($examAvg)
                ];
            }
        }

        usort($breakdown, fn($a, $b) => $b['average'] - $a['average']);

        return $breakdown;
    }

    private function getPerformanceTrends($userId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');

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
} 