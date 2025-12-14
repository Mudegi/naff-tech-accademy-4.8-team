<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'description' => 'Access to basic educational resources',
                'price' => 30000, // 30,000 UGX
                'duration_days' => 30,
                'features' => json_encode([
                    'Access to free videos',
                    'Basic PDF resources',
                    'Email support',
                    'Single subject access',
                    'Basic progress tracking',
                ]),
            ],
            [
                'name' => 'Standard',
                'description' => 'Access to all educational resources',
                'price' => 50000, // 50,000 UGX
                'duration_days' => 30,
                'features' => json_encode([
                    'Access to all videos',
                    'All PDF resources',
                    'PowerPoint presentations',
                    'Priority email support',
                    'Live chat support',
                    'All subjects access',
                    'Detailed progress tracking',
                    'Mock exams',
                ]),
            ],
            [
                'name' => 'Premium',
                'description' => 'Complete learning experience with additional features',
                'price' => 75000, // 75,000 UGX
                'duration_days' => 30,
                'features' => json_encode([
                    'All Standard features',
                    'One-on-one tutoring sessions',
                    'Downloadable content',
                    'Offline access',
                    'Practice questions bank',
                    'Performance analytics',
                    'Parent progress reports',
                ]),
            ],
            [
                'name' => 'Family',
                'description' => 'Access for up to 3 family members',
                'price' => 120000, // 120,000 UGX
                'duration_days' => 30,
                'features' => json_encode([
                    'All Premium features',
                    'Up to 3 family members',
                    'Family progress tracking',
                    'Shared resources',
                    'Family dashboard',
                    'Customized learning paths',
                    'Priority support',
                ]),
            ],
            [
                'name' => 'School Premium',
                'description' => 'Complete educational solution for schools and institutions',
                'price' => 3000000, // 3,000,000 UGX
                'duration_days' => 365, // 1 year
                'subscription_type' => 'school',
                'features' => json_encode([
                    'Unlimited student accounts',
                    'All educational resources',
                    'Teacher management tools',
                    'School-wide progress tracking',
                    'Custom curriculum integration',
                    'Administrative dashboard',
                    'Priority technical support',
                    'Training and onboarding',
                    'Data analytics and reporting',
                    'Offline content access',
                    'Mobile app access',
                    '24/7 support hotline',
                ]),
            ],
        ];

        foreach ($packages as $package) {
            DB::table('subscription_packages')->updateOrInsert(
                ['name' => $package['name']],
                [
                    'description' => $package['description'],
                    'price' => $package['price'],
                    'duration_days' => $package['duration_days'],
                    'features' => $package['features'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
