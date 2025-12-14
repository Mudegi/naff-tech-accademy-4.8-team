<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UniversityCutOff;
use Carbon\Carbon;

class UniversityCutOffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');

        $universities = [
            [
                'name' => 'Makerere University',
                'code' => 'MAK',
                'courses' => [
                    [
                        'name' => 'Bachelor of Medicine and Bachelor of Surgery',
                        'code' => 'MBChB',
                        'faculty' => 'College of Health Sciences',
                        'cut_off' => 20.5,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 17.0,
                        'essential_subjects' => ['Biology', 'Chemistry', 'Physics'],
                        'relevant_subjects' => ['Mathematics'],
                        'duration' => 5,
                    ],
                    [
                        'name' => 'Bachelor of Laws',
                        'code' => 'LLB',
                        'faculty' => 'School of Law',
                        'cut_off' => 18.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 15.0,
                        'essential_subjects' => ['History', 'English'],
                        'relevant_subjects' => ['Literature', 'Divinity'],
                        'duration' => 4,
                    ],
                    [
                        'name' => 'Bachelor of Science in Computer Science',
                        'code' => 'BSc CS',
                        'faculty' => 'College of Computing and Information Sciences',
                        'cut_off' => 16.5,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 14.0,
                        'essential_subjects' => ['Mathematics', 'Physics'],
                        'relevant_subjects' => ['ICT', 'Chemistry'],
                        'duration' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Kyambogo University',
                'code' => 'KYU',
                'courses' => [
                    [
                        'name' => 'Bachelor of Education (Science)',
                        'code' => 'BEd Sc',
                        'faculty' => 'Faculty of Education',
                        'cut_off' => 14.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 12.0,
                        'essential_subjects' => ['Mathematics', 'Physics', 'Chemistry'],
                        'relevant_subjects' => ['Biology'],
                        'duration' => 3,
                    ],
                    [
                        'name' => 'Bachelor of Engineering (Civil)',
                        'code' => 'BEng Civil',
                        'faculty' => 'Faculty of Engineering',
                        'cut_off' => 17.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 15.0,
                        'essential_subjects' => ['Mathematics', 'Physics'],
                        'relevant_subjects' => ['Chemistry'],
                        'duration' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Mbarara University of Science and Technology',
                'code' => 'MUST',
                'courses' => [
                    [
                        'name' => 'Bachelor of Medicine and Bachelor of Surgery',
                        'code' => 'MBChB',
                        'faculty' => 'Faculty of Medicine',
                        'cut_off' => 19.5,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 16.0,
                        'essential_subjects' => ['Biology', 'Chemistry', 'Physics'],
                        'relevant_subjects' => ['Mathematics'],
                        'duration' => 5,
                    ],
                    [
                        'name' => 'Bachelor of Science in Nursing',
                        'code' => 'BSc Nursing',
                        'faculty' => 'Faculty of Medicine',
                        'cut_off' => 15.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 13.0,
                        'essential_subjects' => ['Biology', 'Chemistry'],
                        'relevant_subjects' => ['Physics', 'Mathematics'],
                        'duration' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Uganda Christian University',
                'code' => 'UCU',
                'courses' => [
                    [
                        'name' => 'Bachelor of Business Administration',
                        'code' => 'BBA',
                        'faculty' => 'Faculty of Business Administration',
                        'cut_off' => 14.5,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 12.0,
                        'essential_subjects' => ['Mathematics', 'Economics'],
                        'relevant_subjects' => ['Commerce', 'Accounts'],
                        'duration' => 3,
                    ],
                    [
                        'name' => 'Bachelor of Science in Accounting and Finance',
                        'code' => 'BSc A&F',
                        'faculty' => 'Faculty of Business Administration',
                        'cut_off' => 15.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 13.0,
                        'essential_subjects' => ['Mathematics', 'Economics'],
                        'relevant_subjects' => ['Commerce', 'Accounts'],
                        'duration' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Gulu University',
                'code' => 'GU',
                'courses' => [
                    [
                        'name' => 'Bachelor of Science in Agriculture',
                        'code' => 'BSc Agriculture',
                        'faculty' => 'Faculty of Agriculture',
                        'cut_off' => 13.5,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 11.0,
                        'essential_subjects' => ['Biology', 'Chemistry'],
                        'relevant_subjects' => ['Agriculture', 'Mathematics'],
                        'duration' => 3,
                    ],
                    [
                        'name' => 'Bachelor of Science in Education',
                        'code' => 'BSc Ed',
                        'faculty' => 'Faculty of Education',
                        'cut_off' => 13.0,
                        'min_principal_passes' => 2,
                        'min_aggregate' => 11.0,
                        'essential_subjects' => ['Mathematics'],
                        'relevant_subjects' => ['Physics', 'Chemistry', 'Biology'],
                        'duration' => 3,
                    ],
                ],
            ],
        ];

        foreach ($universities as $university) {
            foreach ($university['courses'] as $course) {
                UniversityCutOff::create([
                    'university_name' => $university['name'],
                    'university_code' => $university['code'],
                    'course_name' => $course['name'],
                    'course_code' => $course['code'],
                    'faculty' => $course['faculty'],
                    'minimum_principal_passes' => $course['min_principal_passes'],
                    'minimum_aggregate_points' => $course['min_aggregate'],
                    'cut_off_points' => $course['cut_off'],
                    'academic_year' => $currentYear,
                    'essential_subjects' => $course['essential_subjects'],
                    'relevant_subjects' => $course['relevant_subjects'] ?? null,
                    'desirable_subjects' => null,
                    'duration_years' => $course['duration'],
                    'degree_type' => 'bachelor',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('University cut-offs seeded successfully!');
    }
}
