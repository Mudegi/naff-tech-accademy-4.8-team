<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Database\Seeders\StatisticSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // First run RolesAndPermissionsSeeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Create or update super admin user
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@nafftechacademy.com'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'account_type' => 'admin',
                'remember_token' => \Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Get the super admin user ID
        $superAdminId = DB::table('users')->where('email', 'admin@nafftechacademy.com')->first()->id;

        // Assign super admin role to the super admin user
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $superAdminRole->id,
                    'model_id' => $superAdminId,
                    'model_type' => 'App\\Models\\User',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Run remaining seeders
        $this->call([
            TermsSeeder::class,
            UgandanSubjectsAndDepartmentsSeeder::class, // Ugandan curriculum subjects
            SubjectSeeder::class,
            SubjectDetailsSeeder::class,
            TopicsSeeder::class,
            ClassSeeder::class,
            ResourcesSeeder::class,
            SubscriptionPackageSeeder::class,
            StudentsSeeder::class,
            PartnersSeeder::class,
            FooterContentSeeder::class,
            TestimonialSeeder::class,
            WhyChooseUsSeeder::class,
            StatisticSeeder::class,
            SmsSettingsSeeder::class,
            FlutterwaveSettingsSeeder::class,
            EasypayConfigurationSeeder::class,
            CompanySettingsSeeder::class,
            WelcomeLinkSeeder::class,
            WelcomeLinkMetaSeeder::class,
            TeamMemberSeeder::class,
            TeamSeeder::class,
            ContactPageSeeder::class,
            TeacherProjectSeeder::class,
            GradeScaleSeeder::class, // O-Level and A-Level grading scales
        ]);

        // Add FullRolesAndPermissionsSeeder
        $this->call(FullRolesAndPermissionsSeeder::class);
    }
}
