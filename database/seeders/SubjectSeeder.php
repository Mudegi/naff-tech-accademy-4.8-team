<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Mathematics',
                'slug' => 'mathematics',
                'description' => 'Advanced mathematics courses covering algebra, calculus, and statistics',
            ],
            [
                'name' => 'Physics',
                'slug' => 'physics',
                'description' => 'Comprehensive physics courses covering mechanics, thermodynamics, and modern physics',
            ],
            [
                'name' => 'Chemistry',
                'slug' => 'chemistry',
                'description' => 'Detailed chemistry courses covering organic, inorganic, and physical chemistry',
            ],
            [
                'name' => 'Biology',
                'slug' => 'biology',
                'description' => 'In-depth biology courses covering cell biology, genetics, and ecology',
            ],
            [
                'name' => 'English',
                'slug' => 'english',
                'description' => 'English language courses covering grammar, literature, and composition',
            ],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['slug' => $subject['slug']],
                [
                    'name' => $subject['name'],
                    'description' => $subject['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
