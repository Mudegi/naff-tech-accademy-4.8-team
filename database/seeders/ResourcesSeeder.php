<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user's ID
       // Get the first admin user's ID
       $adminId = DB::table('users')
       ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
       ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
       ->where('roles.name', 'Super Admin')  // Changed from 'admin' to 'Super Admin'
       ->where('model_has_roles.model_type', 'App\\Models\\User')
       ->value('users.id');

        if (!$adminId) {
            throw new \Exception('No admin user found. Please run the RolesAndPermissionsSeeder first.');
        }

        $resources = [
            // Mathematics Resources
            [
                'topic_id' => 1, // Algebra
                'term_id' => 1,
                'subject_id' => 1,
                'title' => 'Introduction to Algebraic Expressions',
                'description' => 'Learn the basics of algebraic expressions and their manipulation',
                'video_url' => 'https://www.youtube.com/embed/MHeirBPOI6w',
                'notes_file_path' => 'resources/algebra/intro_algebraic_expressions.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'algebra,expressions,mathematics,basics',
            ],
            [
                'topic_id' => 2, // Geometry
                'term_id' => 1,
                'subject_id' => 1,
                'title' => 'Understanding Geometric Shapes',
                'description' => 'Comprehensive guide to geometric shapes and their properties',
                'video_url' => 'https://www.youtube.com/embed/9QZQxXxXxXx',
                'notes_file_path' => 'resources/geometry/geometric_shapes.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'geometry,shapes,mathematics',
            ],
            
            // Physics Resources
            [
                'topic_id' => 4, // Mechanics
                'term_id' => 1,
                'subject_id' => 2,
                'title' => 'Forces and Motion',
                'description' => 'Understanding forces and their effects on motion',
                'video_url' => 'https://www.youtube.com/embed/EVcNvjg6Ilg',
                'notes_file_path' => 'resources/physics/forces_motion.pptx',
                'notes_file_type' => 'pptx',
                'grade_level' => 'O Level',
                'tags' => 'physics,forces,motion,mechanics',
            ],
            
            // Chemistry Resources
            [
                'topic_id' => 7, // Atomic Structure
                'term_id' => 1,
                'subject_id' => 3,
                'title' => 'Atomic Theory and Structure',
                'description' => 'Detailed explanation of atomic theory and structure',
                'video_url' => 'https://www.youtube.com/embed/5pW0k-6XyHI',
                'notes_file_path' => 'resources/chemistry/atomic_theory.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'chemistry,atoms,atomic structure',
            ],
            [
                'topic_id' => 8, // Chemical Bonding
                'term_id' => 1,
                'subject_id' => 3,
                'title' => 'Chemical Bonding',
                'description' => 'Understanding how atoms combine to form molecules',
                'video_url' => 'https://www.youtube.com/embed/aKk4QYt4D3U',
                'notes_file_path' => 'resources/chemistry/chemical_bonding.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'chemistry,bonding,molecules',
            ],
            
            // Biology Resources
            [
                'topic_id' => 10, // Cell Biology
                'term_id' => 1,
                'subject_id' => 4,
                'title' => 'Cell Structure and Function',
                'description' => 'Comprehensive guide to cell biology',
                'video_url' => 'https://www.youtube.com/embed/URUJD5NEXC8',
                'notes_file_path' => 'resources/biology/cell_biology.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'biology,cells,organelles',
            ],
            
            // English Resources
            [
                'topic_id' => 13, // Grammar
                'term_id' => 1,
                'subject_id' => 5,
                'title' => 'Parts of Speech',
                'description' => 'Detailed guide to English parts of speech',
                'video_url' => 'https://www.youtube.com/embed/2ISBt7A9WnQ',
                'notes_file_path' => 'resources/english/parts_of_speech.pdf',
                'notes_file_type' => 'pdf',
                'grade_level' => 'O Level',
                'tags' => 'english,grammar,parts of speech',
            ],
        ];

        foreach ($resources as $resource) {
            DB::table('resources')->updateOrInsert(
                [
                    'topic_id' => $resource['topic_id'],
                    'term_id' => $resource['term_id'],
                    'subject_id' => $resource['subject_id'],
                    'title' => $resource['title']
                ],
                [
                    'description' => $resource['description'],
                    'video_url' => $resource['video_url'],
                    'notes_file_path' => $resource['notes_file_path'],
                    'notes_file_type' => $resource['notes_file_type'],
                    'grade_level' => $resource['grade_level'],
                    'tags' => $resource['tags'],
                    'is_active' => true,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
