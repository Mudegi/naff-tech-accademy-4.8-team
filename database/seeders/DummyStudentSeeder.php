<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\School;
use App\Models\SchoolClass;

class DummyStudentSeeder extends Seeder
{
    public function run()
    {
        // Create or get a school
        $school = School::firstOrCreate(
            ['name' => 'Test School'],
            [
                'email' => 'admin@testschool.com',
                'phone_number' => '+256700000000',
                'address' => '123 Test Street, Kampala',
                'status' => 'active',
            ]
        );

        // Create or get a class
        $class = SchoolClass::firstOrCreate(
            ['name' => 'Form 3A', 'school_id' => $school->id],
            [
                'school_id' => $school->id,
                'slug' => 'form-3a',
                'grade_level' => 3,
                'level' => 'O Level',
                'term' => 'Term 1',
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
                'is_system_class' => false,
            ]
        );

        // Create a user account for the student
        $user = User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password123'),
                'account_type' => 'student',
                'school_id' => $school->id,
                'email_verified_at' => now(),
            ]
        );

        // Create student profile
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Test',
                'school_id' => $school->id,
                'level' => 'O Level',
                'class' => 'Form 3',
                'registration_number' => 'STU-2024-001',
                'phone_number' => '+256700123456',
                'date_of_birth' => now()->subYears(16),
                'email_verified' => true,
                'phone_verified' => false,
                'is_referral' => false,
            ]
        );

        // Attach student to the class via class_user table (many-to-many)
        $user->classes()->syncWithoutDetaching([$class->id]);

        $this->command->info('Dummy student created: john@test.com / password123');
    }
}
