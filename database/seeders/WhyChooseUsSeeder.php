<?php

namespace Database\Seeders;

use App\Models\WhyChooseUs;
use App\Models\WhyChooseUsFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhyChooseUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if WhyChooseUs already exists
        $whyChooseUs = WhyChooseUs::first();
        
        if (!$whyChooseUs) {
            // Create the main WhyChooseUs record
            $whyChooseUs = WhyChooseUs::create([
                'title' => 'Why Choose Us',
                'subtitle' => 'Excellence in Tech Education',
            ]);
            
            // Define features
            $features = [
                [
                    'title' => 'Industry-Relevant Curriculum',
                    'description' => 'Our courses are designed with input from industry experts to ensure you learn the most relevant skills.',
                    'icon' => 'fas fa-laptop-code',
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'title' => 'Expert Instructors',
                    'description' => 'Learn from experienced professionals who are passionate about teaching and technology.',
                    'icon' => 'fas fa-chalkboard-teacher',
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'title' => 'Hands-on Projects',
                    'description' => 'Apply your learning through real-world projects and build a strong portfolio.',
                    'icon' => 'fas fa-project-diagram',
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'title' => 'Career Support',
                    'description' => 'Get guidance on career development, resume building, and job placement assistance.',
                    'icon' => 'fas fa-briefcase',
                    'order' => 4,
                    'is_active' => true,
                ],
            ];
            
            // Create features
            foreach ($features as $feature) {
                WhyChooseUsFeature::create(array_merge($feature, ['why_choose_us_id' => $whyChooseUs->id]));
            }
        }
    }
}
