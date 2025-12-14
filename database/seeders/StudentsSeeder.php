<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\User;

class StudentsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Student Accounts
            [
                'account_type' => 'student',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'James',
                'school_name' => 'Kampala High School',
                'academic_levels' => json_encode(['O Level']),
                'registration_number' => 'STD2024001',
                'email' => 'john.doe@example.com',
                'phone_number' => '0770123456',
                'email_verified' => true,
                'phone_verified' => true,
                'classes' => null,
                'class' => 'S.3',
                'is_referral' => true,
                'referee_name' => 'Sarah Smith',
                'referee_contact' => '0770123457',
                'how_you_know_us' => 'Friend Recommendation',
                'date_of_birth' => '2006-05-15',
                'subscription_package_id' => 2, // Standard Package
                'subscription_start_date' => Carbon::now(),
                'subscription_end_date' => Carbon::now()->addMonths(3),
            ],
            [
                'account_type' => 'student',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'middle_name' => null,
                'school_name' => 'Entebbe Secondary School',
                'academic_levels' => json_encode(['A Level']),
                'registration_number' => 'STD2024002',
                'email' => 'jane.smith@example.com',
                'phone_number' => '0770123458',
                'email_verified' => true,
                'phone_verified' => false,
                'classes' => null,
                'class' => 'S.5',
                'is_referral' => false,
                'referee_name' => null,
                'referee_contact' => null,
                'how_you_know_us' => 'Social Media',
                'date_of_birth' => '2005-08-20',
                'subscription_package_id' => 3, // Premium Package
                'subscription_start_date' => Carbon::now(),
                'subscription_end_date' => Carbon::now()->addMonths(6),
            ],
            // Parent Accounts
            [
                'account_type' => 'parent',
                'first_name' => 'James',
                'last_name' => 'Mukasa',
                'middle_name' => null,
                'school_name' => null,
                'academic_levels' => json_encode(['O Level']),
                'registration_number' => null,
                'email' => 'james.mukasa@example.com',
                'phone_number' => '0770123462',
                'email_verified' => true,
                'phone_verified' => false,
                'classes' => json_encode(['S.1', 'S.2', 'S.3', 'S.4']),
                'class' => null,
                'is_referral' => true,
                'referee_name' => 'Sarah Nalwoga',
                'referee_contact' => '0770123463',
                'how_you_know_us' => 'Friend Referral',
                'date_of_birth' => '1978-11-20',
                'subscription_package_id' => 3, // Premium Package
                'subscription_start_date' => Carbon::now(),
                'subscription_end_date' => Carbon::now()->addMonths(6),
            ],
        ];

        foreach ($accounts as $account) {
            // Check if student with this registration number already exists
            if (!Student::where('registration_number', $account['registration_number'])->exists()) {
                // Create user account
                $user = User::create([
                    'name' => $account['first_name'] . ' ' . $account['last_name'],
                    'email' => $account['email'],
                    'password' => Hash::make('password'),
                    'account_type' => $account['account_type'],
                ]);

                // Create student profile
                Student::create([
                    'user_id' => $user->id,
                    'account_type' => $account['account_type'],
                    'first_name' => $account['first_name'],
                    'last_name' => $account['last_name'],
                    'middle_name' => $account['middle_name'],
                    'school_name' => $account['school_name'],
                    'academic_levels' => $account['academic_levels'],
                    'registration_number' => $account['registration_number'],
                    'phone_number' => $account['phone_number'],
                    'email_verified' => $account['email_verified'],
                    'phone_verified' => $account['phone_verified'],
                    'classes' => $account['classes'],
                    'class' => $account['class'],
                    'is_referral' => $account['is_referral'],
                    'referee_name' => $account['referee_name'],
                    'referee_contact' => $account['referee_contact'],
                    'how_you_know_us' => $account['how_you_know_us'],
                    'date_of_birth' => $account['date_of_birth'],
                    'subscription_package_id' => $account['subscription_package_id'],
                    'subscription_start_date' => $account['subscription_start_date'],
                    'subscription_end_date' => $account['subscription_end_date'],
                ]);
            }
        }
    }
}
