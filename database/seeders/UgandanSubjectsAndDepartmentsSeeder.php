<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UgandanSubjectsAndDepartmentsSeeder extends Seeder
{
    /**
     * Seed Ugandan Secondary School Subjects and create corresponding Departments.
     * Each subject gets its own specific department (e.g., Chemistry -> Chemistry Department)
     */
    public function run(): void
    {
        // Define all 21 Ugandan subjects - each will get its own department
        $subjects = [
            [
                'name' => 'English Language',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Composition, Summary, and Comprehension'],
                    ['name' => 'Paper 2', 'description' => 'Literature in English']
                ],
                'description' => 'English language covering grammar, composition, literature, and communication skills',
            ],
            [
                'name' => 'Kiswahili',
                'level' => 'O Level',
                'paper_count' => 1,
                'papers' => null,
                'description' => 'Kiswahili language and literature',
            ],
            [
                'name' => 'Literature in English',
                'level' => 'A Level',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Drama and Prose'],
                    ['name' => 'Paper 2', 'description' => 'Poetry and Unseen']
                ],
                'description' => 'Study of prose, poetry, drama and literary criticism',
            ],
            [
                'name' => 'Mathematics',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Algebra, Geometry, and Trigonometry'],
                    ['name' => 'Paper 2', 'description' => 'Calculus and Mechanics']
                ],
                'description' => 'Core mathematics for O and A level',
            ],
            [
                'name' => 'Physics',
                'level' => 'Both',
                'paper_count' => 3,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Mechanics and Heat'],
                    ['name' => 'Paper 2', 'description' => 'Light, Waves, and Modern Physics'],
                    ['name' => 'Paper 3', 'description' => 'Practical Examination']
                ],
                'description' => 'Physics covering classical and modern concepts',
            ],
            [
                'name' => 'Chemistry',
                'level' => 'Both',
                'paper_count' => 3,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Physical and General Chemistry'],
                    ['name' => 'Paper 2', 'description' => 'Organic and Inorganic Chemistry'],
                    ['name' => 'Paper 3', 'description' => 'Practical Examination']
                ],
                'description' => 'Chemistry covering organic, inorganic, and physical chemistry',
            ],
            [
                'name' => 'Biology',
                'level' => 'Both',
                'paper_count' => 3,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Botany and Ecology'],
                    ['name' => 'Paper 2', 'description' => 'Zoology and Physiology'],
                    ['name' => 'Paper 3', 'description' => 'Practical Examination']
                ],
                'description' => 'Biology covering plant, animal, and human biology',
            ],
            [
                'name' => 'History',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'History of Uganda'],
                    ['name' => 'Paper 2', 'description' => 'History of Africa']
                ],
                'description' => 'History of Uganda, Africa, and the world',
            ],
            [
                'name' => 'Geography',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Physical Geography'],
                    ['name' => 'Paper 2', 'description' => 'Human and Economic Geography']
                ],
                'description' => 'Physical and human geography',
            ],
            [
                'name' => 'Christian Religious Education',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Old Testament'],
                    ['name' => 'Paper 2', 'description' => 'New Testament']
                ],
                'description' => 'Christian Religious Education (CRE)',
            ],
            [
                'name' => 'Islamic Religious Education',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Quran and Hadith'],
                    ['name' => 'Paper 2', 'description' => 'Islamic History and Practices']
                ],
                'description' => 'Islamic Religious Education (IRE)',
            ],
            [
                'name' => 'Agriculture',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Principles and Practices of Agriculture'],
                    ['name' => 'Paper 2', 'description' => 'Animal Husbandry and Farm Management']
                ],
                'description' => 'Principles and practices of agriculture',
            ],
            [
                'name' => 'Art and Design',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Theory and History of Art'],
                    ['name' => 'Paper 2', 'description' => 'Practical']
                ],
                'description' => 'Fine art, design, and creative expression',
            ],
            [
                'name' => 'Music',
                'level' => 'Both',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Theory and History of Music'],
                    ['name' => 'Paper 2', 'description' => 'Practical Performance']
                ],
                'description' => 'Music theory, history, and performance',
            ],
            [
                'name' => 'Performing Arts',
                'level' => 'O Level',
                'paper_count' => 1,
                'papers' => null,
                'description' => 'Drama, dance, and performance skills',
            ],
            [
                'name' => 'Information and Communication Technology',
                'level' => 'O Level',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Theory'],
                    ['name' => 'Paper 2', 'description' => 'Practical']
                ],
                'description' => 'ICT skills and applications',
            ],
            [
                'name' => 'Nutrition and Food Technology',
                'level' => 'O Level',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Theory of Nutrition and Food Science'],
                    ['name' => 'Paper 2', 'description' => 'Practical Food Preparation']
                ],
                'description' => 'Nutrition, food science, and home economics',
            ],
            [
                'name' => 'Technology and Design',
                'level' => 'O Level',
                'paper_count' => 1,
                'papers' => null,
                'description' => 'Technical drawing, design, and technology concepts',
            ],
            [
                'name' => 'Physical Education',
                'level' => 'O Level',
                'paper_count' => 2,
                'papers' => [
                    ['name' => 'Paper 1', 'description' => 'Theory'],
                    ['name' => 'Paper 2', 'description' => 'Practical']
                ],
                'description' => 'Sports, physical fitness, and health education',
            ],
            [
                'name' => 'Entrepreneurship',
                'level' => 'Both',
                'paper_count' => 1,
                'papers' => null,
                'description' => 'Business skills, entrepreneurship, and enterprise education',
            ],
            [
                'name' => 'General Paper',
                'level' => 'A Level',
                'paper_count' => 1,
                'papers' => null,
                'description' => 'Compulsory general knowledge and current affairs for A-Level',
            ],
        ];

        $this->command->info('Creating system-wide subjects and their departments...');
        $this->command->info('Each subject will have its own specific department.');
        
        $departmentCount = 0;
        $subjectCount = 0;

        foreach ($subjects as $subjectData) {
            $subjectName = $subjectData['name'];
            $departmentName = $subjectName . ' Department';
            
            // Generate department code from subject name
            $deptCode = $this->generateDepartmentCode($subjectName);
            
            // Create the department for this subject (system-wide: school_id = null)
            DB::table('departments')->updateOrInsert(
                ['code' => $deptCode, 'school_id' => null],
                [
                    'name' => $departmentName,
                    'code' => $deptCode,
                    'description' => "The {$departmentName}",
                    'school_id' => null, // System-wide department
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $this->command->info("✓ Created department: {$departmentName} ({$deptCode})");
            $departmentCount++;
            
            // Create the subject (system-wide: school_id = null)
            $slug = Str::slug($subjectName);
            $papers = $subjectData['papers'] ? json_encode($subjectData['papers']) : null;

            DB::table('subjects')->updateOrInsert(
                ['slug' => $slug, 'school_id' => null],
                [
                    'name' => $subjectName,
                    'slug' => $slug,
                    'description' => $subjectData['description'],
                    'level' => $subjectData['level'],
                    'paper_count' => $subjectData['paper_count'],
                    'papers' => $papers,
                    'is_active' => true,
                    'school_id' => null, // System-wide subject
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $this->command->info("  ✓ Created subject: {$subjectName} ({$subjectData['level']})");
            $subjectCount++;
        }

        $this->command->info('');
        $this->command->info('✅ All Ugandan subjects and departments created successfully!');
        $this->command->info("📚 Total subjects: {$subjectCount}");
        $this->command->info("🏢 Total departments: {$departmentCount}");
    }

    /**
     * Generate department code from subject name
     */
    private function generateDepartmentCode(string $name): string
    {
        // Remove common words and take first letters
        $name = str_replace(['and', 'in', 'of', 'the'], '', $name);
        $words = array_filter(explode(' ', $name));
        
        if (count($words) >= 3) {
            // For 3+ words, take first letter of each
            $word1 = array_values($words)[0] ?? '';
            $word2 = array_values($words)[1] ?? '';
            $word3 = array_values($words)[2] ?? '';
            return strtoupper(substr($word1, 0, 1) . substr($word2, 0, 1) . substr($word3, 0, 1));
        } elseif (count($words) === 2) {
            // For 2 words, take 2 letters from each
            $word1 = array_values($words)[0] ?? '';
            $word2 = array_values($words)[1] ?? '';
            return strtoupper(substr($word1, 0, 2) . substr($word2, 0, 2));
        } else {
            // For 1 word, take first 4 letters
            return strtoupper(substr($name, 0, 4));
        }
    }
}
