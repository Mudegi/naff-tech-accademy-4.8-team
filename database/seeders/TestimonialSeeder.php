<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'John Doe',
                'role' => 'Web Development Graduate',
                'testimonial' => 'The hands-on projects and expert instructors helped me land my dream job in tech. Highly recommended!',
                'initials' => 'JD',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Alice Smith',
                'role' => 'Data Science Student',
                'testimonial' => 'The curriculum is up-to-date with industry standards, and the support from instructors is exceptional.',
                'initials' => 'AS',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Robert Johnson',
                'role' => 'Mobile App Developer',
                'testimonial' => 'The project-based learning approach helped me build a strong portfolio that impressed employers.',
                'initials' => 'RJ',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            // Check if a testimonial with the same name already exists
            if (!Testimonial::where('name', $testimonial['name'])->exists()) {
                Testimonial::create($testimonial);
            }
        }
    }
}
