<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statistics = [
            [
                'key' => 'active_students',
                'value' => '1000+',
                'label' => 'Active Students',
                'icon' => 'fas fa-users',
                'display_order' => 1,
                'is_active' => true
            ],
            [
                'key' => 'expert_instructors',
                'value' => '50+',
                'label' => 'Expert Instructors',
                'icon' => 'fas fa-chalkboard-teacher',
                'display_order' => 2,
                'is_active' => true
            ],
            [
                'key' => 'courses_available',
                'value' => '100+',
                'label' => 'Courses Available',
                'icon' => 'fas fa-book',
                'display_order' => 3,
                'is_active' => true
            ],
            [
                'key' => 'success_rate',
                'value' => '95%',
                'label' => 'Success Rate',
                'icon' => 'fas fa-chart-line',
                'display_order' => 4,
                'is_active' => true
            ]
        ];

        foreach ($statistics as $stat) {
            // Only create if the statistic doesn't exist
            if (!\App\Models\Statistic::where('key', $stat['key'])->exists()) {
                \App\Models\Statistic::create($stat);
            }
        }
    }
}
