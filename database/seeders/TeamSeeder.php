<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Dr. Sarah Johnson',
                'position' => 'Chief Executive Officer',
                'skills' => 'Strategic Planning, Leadership, Educational Technology, Business Development',
                'image_path' => 'team.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Michael Chen',
                'position' => 'Head of Academic Affairs',
                'skills' => 'Curriculum Development, Research, Quality Assurance, Academic Leadership',
                'image_path' => 'team.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'position' => 'Director of Technology',
                'skills' => 'Software Development, System Architecture, AI/ML, Digital Innovation',
                'image_path' => 'team.jpg',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Mr. David Thompson',
                'position' => 'Lead Instructor - Mathematics',
                'skills' => 'Advanced Mathematics, Calculus, Statistics, Problem Solving',
                'image_path' => 'team.jpg',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Lisa Wang',
                'position' => 'Lead Instructor - Sciences',
                'skills' => 'Physics, Chemistry, Biology, Laboratory Management, Research',
                'image_path' => 'team.jpg',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Ms. Jennifer Martinez',
                'position' => 'Student Success Coordinator',
                'skills' => 'Student Counseling, Career Guidance, Mentoring, Communication',
                'image_path' => 'team.jpg',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Mr. Robert Kim',
                'position' => 'Technical Support Manager',
                'skills' => 'IT Support, System Administration, Network Security, User Training',
                'image_path' => 'team.jpg',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Amanda Foster',
                'position' => 'Head of Research & Development',
                'skills' => 'Educational Research, Data Analysis, Innovation, Project Management',
                'image_path' => 'team.jpg',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(
                ['name' => $team['name']], // Check if team member with this name exists
                $team // If not, create with all the data
            );
        }
    }
}