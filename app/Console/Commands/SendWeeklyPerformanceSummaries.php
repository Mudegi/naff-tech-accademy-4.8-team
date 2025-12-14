<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AssignmentSubmission;
use App\Models\StudentMark;
use App\Notifications\WeeklyPerformanceSummary;

class SendWeeklyPerformanceSummaries extends Command
{
    protected $signature = 'parent:send-weekly-summaries';
    protected $description = 'Send weekly performance summaries to all parents with notifications enabled';

    public function handle()
    {
        $this->info('Sending weekly performance summaries...');
        
        $links = \DB::table('parent_student')
            ->where('receive_notifications', true)
            ->get();

        $sentCount = 0;

        foreach ($links as $link) {
            $parent = User::find($link->parent_id);
            $student = User::find($link->student_id);

            if (!$parent || !$student) continue;

            $performanceData = $this->calculateWeeklyPerformance($student->id);
            $parent->notify(new WeeklyPerformanceSummary($student, $performanceData));
            $sentCount++;

            $this->line("Sent summary for {$student->name} to {$parent->name}");
        }

        $this->info("✓ Sent {$sentCount} weekly summaries successfully!");
        return 0;
    }

    private function calculateWeeklyPerformance($studentId)
    {
        $weekAgo = now()->subDays(7);

        $assignments = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->where('reviewed_at', '>=', $weekAgo)
            ->with('assignment')
            ->get();

        $assignmentAvg = $assignments->count() > 0
            ? $assignments->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100)
            : 0;

        $exams = StudentMark::where('user_id', $studentId)
            ->where('created_at', '>=', $weekAgo)
            ->get();

        $examAvg = $this->calculateExamAverage($exams);

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

        $pending = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', '!=', 'graded')
            ->count();

        $prevWeekAvg = $this->getPreviousWeekAverage($studentId);
        $trend = 'stable';
        if ($overallAvg > $prevWeekAvg + 2) $trend = 'improving';
        if ($overallAvg < $prevWeekAvg - 2) $trend = 'declining';

        return [
            'overall_average' => round($overallAvg, 1),
            'letter_grade' => $this->getLetterGrade($overallAvg),
            'assignments_count' => $assignments->count(),
            'assignment_avg' => round($assignmentAvg, 1),
            'exams_count' => $exams->count(),
            'exam_avg' => round($examAvg, 1),
            'pending_assignments' => $pending,
            'trend' => $trend
        ];
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
            $gradeMap = ['D1' => 95, 'D2' => 85, 'C3' => 75, 'C4' => 70, 'C5' => 65, 'C6' => 60, 'P7' => 55, 'P8' => 50, 'F9' => 40];
            return $gradeMap[$mark->grade] ?? 0;
        } elseif ($mark->grade_type === 'letter') {
            $gradeMap = ['A' => 90, 'B' => 80, 'C' => 70, 'D' => 60, 'F' => 50];
            return $gradeMap[$mark->grade] ?? 0;
        }
        return 0;
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

    private function getPreviousWeekAverage($studentId)
    {
        $twoWeeksAgo = now()->subDays(14);
        $oneWeekAgo = now()->subDays(7);
        $assignments = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->whereBetween('reviewed_at', [$twoWeeksAgo, $oneWeekAgo])
            ->with('assignment')
            ->get();
        $assignmentAvg = $assignments->count() > 0 ? $assignments->avg(fn($s) => ($s->grade / $s->assignment->total_marks) * 100) : 0;
        $exams = StudentMark::where('user_id', $studentId)->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->get();
        $examAvg = $this->calculateExamAverage($exams);
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
        return $weights > 0 ? $overallAvg / $weights : 0;
    }
}
