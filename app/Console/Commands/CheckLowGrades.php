<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AssignmentSubmission;
use App\Notifications\LowGradeAlert;

class CheckLowGrades extends Command
{
    protected $signature = 'parent:check-low-grades';
    protected $description = 'Check for low grades and alert parents';

    public function handle()
    {
        $this->info('Checking for low grades...');
        
        // Get recent graded submissions with low grades (< 50%)
        $lowGradeSubmissions = AssignmentSubmission::where('status', 'graded')
            ->whereNotNull('reviewed_at')
            ->where('reviewed_at', '>=', now()->subDays(1)) // Last 24 hours
            ->with(['assignment', 'student'])
            ->get()
            ->filter(function($submission) {
                $percentage = ($submission->grade / $submission->assignment->total_marks) * 100;
                return $percentage < 50;
            });

        $alertCount = 0;

        foreach ($lowGradeSubmissions as $submission) {
            $student = $submission->student;
            if (!$student) continue;

            // Get parents with notifications enabled
            $parents = $student->parents()
                ->wherePivot('receive_notifications', true)
                ->get();

            foreach ($parents as $parent) {
                $parent->notify(new LowGradeAlert(
                    $student,
                    $submission->assignment,
                    $submission->grade,
                    $submission->assignment->total_marks
                ));
                $alertCount++;
                $this->line("Alert sent to {$parent->name} for {$student->name}'s low grade");
            }
        }

        $this->info("✓ Sent {$alertCount} low grade alerts!");
        return 0;
    }
}
