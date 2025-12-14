<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            // Mathematics Topics
            [
                'subject_id' => 1,
                'name' => 'Algebra',
                'slug' => 'algebra',
                'description' => 'Learn about algebraic expressions, equations, and functions',
                'order' => 1,
            ],
            [
                'subject_id' => 1,
                'name' => 'Geometry',
                'slug' => 'geometry',
                'description' => 'Study of shapes, sizes, and positions of figures',
                'order' => 2,
            ],
            [
                'subject_id' => 1,
                'name' => 'Statistics',
                'slug' => 'statistics',
                'description' => 'Data collection, analysis, and probability',
                'order' => 3,
            ],
            
            // Physics Topics
            [
                'subject_id' => 2,
                'name' => 'Mechanics',
                'slug' => 'mechanics',
                'description' => 'Study of motion, forces, and energy',
                'order' => 1,
            ],
            [
                'subject_id' => 2,
                'name' => 'Electricity',
                'slug' => 'electricity',
                'description' => 'Electric charges, current, and circuits',
                'order' => 2,
            ],
            [
                'subject_id' => 2,
                'name' => 'Light and Waves',
                'slug' => 'light-and-waves',
                'description' => 'Properties of light and wave phenomena',
                'order' => 3,
            ],
            
            // Chemistry Topics
            [
                'subject_id' => 3,
                'name' => 'Atomic Structure',
                'slug' => 'atomic-structure',
                'description' => 'Study of atoms and their components',
                'order' => 1,
            ],
            [
                'subject_id' => 3,
                'name' => 'Chemical Bonding',
                'slug' => 'chemical-bonding',
                'description' => 'How atoms combine to form molecules',
                'order' => 2,
            ],
            [
                'subject_id' => 3,
                'name' => 'Organic Chemistry',
                'slug' => 'organic-chemistry',
                'description' => 'Study of carbon-based compounds',
                'order' => 3,
            ],
            
            // Biology Topics
            [
                'subject_id' => 4,
                'name' => 'Cell Biology',
                'slug' => 'cell-biology',
                'description' => 'Structure and function of cells',
                'order' => 1,
            ],
            [
                'subject_id' => 4,
                'name' => 'Genetics',
                'slug' => 'genetics',
                'description' => 'Study of heredity and variation',
                'order' => 2,
            ],
            [
                'subject_id' => 4,
                'name' => 'Ecology',
                'slug' => 'ecology',
                'description' => 'Interactions between organisms and their environment',
                'order' => 3,
            ],
            
            // English Topics
            [
                'subject_id' => 5,
                'name' => 'Grammar',
                'slug' => 'grammar',
                'description' => 'Rules and structure of language',
                'order' => 1,
            ],
            [
                'subject_id' => 5,
                'name' => 'Literature',
                'slug' => 'literature',
                'description' => 'Study of prose, poetry, and drama',
                'order' => 2,
            ],
            [
                'subject_id' => 5,
                'name' => 'Composition',
                'slug' => 'composition',
                'description' => 'Writing skills and techniques',
                'order' => 3,
            ],
        ];

        foreach ($topics as $topic) {
            DB::table('topics')->updateOrInsert(
                [
                    'subject_id' => $topic['subject_id'],
                    'slug' => $topic['slug']
                ],
                [
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'order' => $topic['order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
