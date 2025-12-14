<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeamMember;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamMembers = [
            [
                'name' => 'John Doe',
                'position' => 'Lead Instructor',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
                'bio' => 'With over 15 years of experience in tech education, John leads our team of instructors.',
                'email' => 'john@example.com',
                'linkedin' => 'https://linkedin.com/in/johndoe',
                'twitter' => 'https://twitter.com/johndoe',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Jane Smith',
                'position' => 'Senior Instructor',
                'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
                'bio' => 'Jane brings her expertise in curriculum development and educational technology.',
                'email' => 'jane@example.com',
                'linkedin' => 'https://linkedin.com/in/janesmith',
                'twitter' => 'https://twitter.com/janesmith',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Mike Johnson',
                'position' => 'Technical Instructor',
                'image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
                'bio' => 'Mike ensures our technical curriculum stays current with industry trends.',
                'email' => 'mike@example.com',
                'linkedin' => 'https://linkedin.com/in/mikejohnson',
                'twitter' => 'https://twitter.com/mikejohnson',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($teamMembers as $member) {
            TeamMember::firstOrCreate(
                ['email' => $member['email']],
                $member
            );
        }
    }
}
