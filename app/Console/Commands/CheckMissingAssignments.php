<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Notifications\MissingAssignmentAlert;

class CheckMissingAssignments extends Command
{
    protected $signature = 'parent:check-missing-assignments';
    protected $description = 'Check for overdue assignments and alert parents';

    public function handle()
    {
        $this->info('Checking for missing assignments...');
        
        // Get all students
        $students = User::where('account_type', 'student')->get();
        $alertCount = 0;

        foreach ($students as $student) {
            // Get overdue assignments for this student
            $overdueAssignments = Assignment::where('due_date', '<', now())
                ->whereDoesntHave('submissions', function($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->orWhereHas('submissions', function($query) use ($student) {
                    $query->where('student_id', $student->id)
                          ->where('status', 'draft');
                })
                ->get();

            if ($overdueAssignments->isEmpty()) continue;

            // Get parents with notifications enabled
            $parents = $student->parents()
                ->wherePivot('receive_notifications', true)
                ->get();

            foreach ($parents as $parent) {
                $parent->notify(new MissingAssignmentAlert($student, $overdueAssignments));
                $alertCount++;
                $this->line("Alert sent to {$parent->name} for {$student->name}'s {$overdueAssignments->count()} missing assignments");
            }
        }

        $this->info("✓ Sent {$alertCount} missing assignment alerts!");
        return 0;
    }
}
