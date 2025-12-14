<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectImplementation;
use App\Models\SchoolSubscription;

class TeacherProjectSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Create demo school
            $school = School::firstOrCreate(
                ['slug' => 'demo-school'],
                [
                    'name' => 'Demo School',
                    'email' => 'demo@school.test',
                    'status' => 'active',
                ]
            );

            // Create a teacher user
            $teacher = User::updateOrCreate(
                ['email' => 'teacher1@example.com'],
                [
                    'name' => 'Demo Teacher',
                    'password' => Hash::make('password'),
                    'account_type' => 'teacher',
                    'school_id' => $school->id,
                    'email_verified_at' => now(),
                ]
            );

            // Create 3 student users and student profiles
            $students = [];
            for ($i = 1; $i <= 3; $i++) {
                $email = "student{$i}@example.com";
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => "Student {$i}",
                        'password' => Hash::make('password'),
                        'account_type' => 'student',
                        'school_id' => $school->id,
                        'email_verified_at' => now(),
                    ]
                );

                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'first_name' => 'Student',
                        'last_name' => (string)$i,
                        'school_id' => $school->id,
                    'level' => 'O Level',
            $group = Group::updateOrCreate(
                ['name' => 'Demo Project Group', 'school_id' => $school->id],
                [
                    'description' => 'Group created for demo teacher project seeder',
                    'created_by' => $teacher->id,
                    'school_id' => $school->id,
                    'status' => 'active',
                    'max_members' => 6,
                ]
            );

            // Attach members to group (approved)
            foreach ($students as $studentUser) {
                $group->members()->syncWithoutDetaching([
                    $studentUser->id => [
                        'role' => 'member',
                        'status' => 'approved',
                        'joined_at' => now(),
                    ]
                ]);
            }

            // Create a project for the group
            $project = Project::updateOrCreate(
                ['title' => 'Demo Group Project', 'group_id' => $group->id],
                [
                    'description' => 'A sample group project for testing grading flows',
                    'group_id' => $group->id,
                    'created_by' => $teacher->id,
                    'school_id' => $school->id,
                    'status' => 'implementation',
                    'start_date' => now()->subWeeks(2),
                    'end_date' => now()->addWeeks(2),
                ]
            );

            // Create a simple implementation record
            ProjectImplementation::updateOrCreate(
                ['project_id' => $project->id],
                [
                    'project_id' => $project->id,
                    'activity_execution' => 'Demo execution notes',
                    'status' => 'submitted',
                ]
            );

            // Create an active school subscription so demo school is considered active
            $existingSub = SchoolSubscription::where('school_id', $school->id)->first();
            if (!$existingSub) {
                SchoolSubscription::create([
                    'school_id' => $school->id,
                    'subscription_package_id' => null,
                    'amount_paid' => 0,
                    'payment_status' => 'completed',
                    'payment_method' => 'manual',
                    'transaction_id' => 'demo-'.now()->timestamp,
                    'start_date' => now()->subDays(1),
                    'end_date' => now()->addYears(1),
                    'is_active' => true,
                ]);
            }

            $this->command->info('TeacherProjectSeeder: demo school, teacher, students, group and project created.');
        });
    }
}
