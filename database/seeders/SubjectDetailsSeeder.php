<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'mathematics' => [
                'content' => 'Our mathematics course covers essential topics from basic algebra to advanced calculus. Students will learn through a combination of theoretical concepts and practical applications.',
                'objectives' => json_encode([
                    'Master fundamental mathematical concepts',
                    'Develop problem-solving skills',
                    'Understand mathematical proofs and reasoning',
                    'Apply mathematics to real-world scenarios'
                ]),
                'prerequisites' => json_encode([
                    'Basic arithmetic skills',
                    'Understanding of basic algebra',
                    'Calculator proficiency'
                ]),
                'duration' => '12 weeks',
                'total_topics' => 15,
                'total_resources' => 45,
                'learning_outcomes' => json_encode([
                    'Ability to solve complex mathematical problems',
                    'Understanding of mathematical concepts and their applications',
                    'Skills in mathematical modeling and analysis'
                ]),
                'assessment_methods' => json_encode([
                    'Regular quizzes and assignments',
                    'Mid-term examination',
                    'Final project',
                    'End-of-term examination'
                ]),
                'passing_score' => 60.00
            ],
            'physics' => [
                'content' => 'Our physics course explores the fundamental principles of mechanics, thermodynamics, and modern physics. Students will engage in both theoretical study and laboratory experiments.',
                'objectives' => json_encode([
                    'Understand fundamental physics principles',
                    'Develop experimental skills',
                    'Learn to apply physics concepts to real-world problems',
                    'Master mathematical modeling in physics'
                ]),
                'prerequisites' => json_encode([
                    'Basic mathematics knowledge',
                    'Understanding of basic physics concepts',
                    'Laboratory safety awareness'
                ]),
                'duration' => '14 weeks',
                'total_topics' => 18,
                'total_resources' => 54,
                'learning_outcomes' => json_encode([
                    'Ability to solve physics problems',
                    'Understanding of physical laws and principles',
                    'Skills in conducting physics experiments'
                ]),
                'assessment_methods' => json_encode([
                    'Laboratory reports',
                    'Problem-solving assignments',
                    'Mid-term examination',
                    'Final project and presentation'
                ]),
                'passing_score' => 65.00
            ],
            'chemistry' => [
                'content' => 'Our chemistry course covers organic, inorganic, and physical chemistry. Students will learn through lectures, laboratory work, and practical demonstrations.',
                'objectives' => json_encode([
                    'Master chemical principles and reactions',
                    'Develop laboratory skills',
                    'Understand chemical analysis methods',
                    'Learn safety protocols in chemistry'
                ]),
                'prerequisites' => json_encode([
                    'Basic chemistry knowledge',
                    'Understanding of laboratory safety',
                    'Basic mathematics skills'
                ]),
                'duration' => '13 weeks',
                'total_topics' => 16,
                'total_resources' => 48,
                'learning_outcomes' => json_encode([
                    'Ability to perform chemical experiments',
                    'Understanding of chemical reactions',
                    'Skills in chemical analysis'
                ]),
                'assessment_methods' => json_encode([
                    'Laboratory practicals',
                    'Written assignments',
                    'Mid-term examination',
                    'Final laboratory project'
                ]),
                'passing_score' => 60.00
            ],
            'biology' => [
                'content' => 'Our biology course covers cell biology, genetics, ecology, and human physiology. Students will learn through lectures, laboratory work, and field studies.',
                'objectives' => json_encode([
                    'Understand biological systems',
                    'Master laboratory techniques',
                    'Learn about genetic principles',
                    'Study ecological relationships'
                ]),
                'prerequisites' => json_encode([
                    'Basic biology knowledge',
                    'Understanding of scientific method',
                    'Laboratory safety awareness'
                ]),
                'duration' => '15 weeks',
                'total_topics' => 20,
                'total_resources' => 60,
                'learning_outcomes' => json_encode([
                    'Ability to conduct biological experiments',
                    'Understanding of biological systems',
                    'Skills in microscopic analysis'
                ]),
                'assessment_methods' => json_encode([
                    'Laboratory reports',
                    'Field study projects',
                    'Mid-term examination',
                    'Final research project'
                ]),
                'passing_score' => 60.00
            ],
            'english' => [
                'content' => 'Our English course covers grammar, literature, composition, and communication skills. Students will develop their language proficiency through various activities and assignments.',
                'objectives' => json_encode([
                    'Improve language proficiency',
                    'Develop writing skills',
                    'Master grammar and composition',
                    'Enhance communication abilities'
                ]),
                'prerequisites' => json_encode([
                    'Basic English proficiency',
                    'Reading comprehension skills',
                    'Writing ability'
                ]),
                'duration' => '12 weeks',
                'total_topics' => 14,
                'total_resources' => 42,
                'learning_outcomes' => json_encode([
                    'Enhanced writing skills',
                    'Improved communication abilities',
                    'Better grammar and vocabulary'
                ]),
                'assessment_methods' => json_encode([
                    'Written assignments',
                    'Oral presentations',
                    'Mid-term examination',
                    'Final essay project'
                ]),
                'passing_score' => 60.00
            ]
        ];

        foreach ($subjects as $slug => $details) {
            $subject = DB::table('subjects')->where('slug', $slug)->first();
            
            if ($subject && !$subject->content) {
                DB::table('subjects')
                    ->where('slug', $slug)
                    ->update($details);
            }
        }
    }
}
