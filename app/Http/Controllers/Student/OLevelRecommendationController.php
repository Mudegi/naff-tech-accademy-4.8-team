<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OLevelRecommendationController extends Controller
{
    /**
     * Display A-Level combination recommendations based on O-Level marks.
     */
    public function index(Request $request)
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }
        
        $user = Auth::user();
        
        // Get available exam types for this student
        $availableExamTypes = StudentMark::where('user_id', $user->id)
            ->whereNotNull('exam_type')
            ->distinct()
            ->pluck('exam_type')
            ->sort()
            ->values();
        
        // Get selected exam type (student can choose which exam's marks to use)
        $selectedExamType = $request->input('exam_type');
        
        // Get student's marks (filtered by exam type if selected)
        $studentMarksQuery = StudentMark::where('user_id', $user->id);
        
        if ($selectedExamType) {
            $studentMarksQuery->where('exam_type', $selectedExamType);
        }
        
        $studentMarks = $studentMarksQuery->get();

        // Analyze student's strong subjects
        $strongSubjects = $this->analyzeStrongSubjects($studentMarks);
        
        // Get recommended combinations
        $recommendations = $this->getRecommendedCombinations($strongSubjects, $studentMarks);
        
        // Get performance comparison data
        $performanceComparison = $this->getPerformanceComparison($user->id, $selectedExamType);

        return view('student.o-level-recommendations.index', compact(
            'studentMarks',
            'strongSubjects',
            'recommendations',
            'availableExamTypes',
            'selectedExamType',
            'performanceComparison'
        ));
    }

    /**
     * Analyze student's strong subjects from O-Level marks.
     * Returns array of subjects where student performed well (Div 1-2 or 70%+)
     */
    private function analyzeStrongSubjects($marks)
    {
        $strongSubjects = [];
        
        foreach ($marks as $mark) {
            $isStrong = false;
            
            // Check based on grade type
            if ($mark->grade_type === 'numeric' && $mark->numeric_mark >= 70) {
                $isStrong = true;
            } elseif ($mark->grade_type === 'distinction_credit_pass') {
                $isStrong = in_array($mark->grade, ['D1', 'D2', 'C3', 'C4', 'C5', 'C6']);
            } elseif ($mark->grade_type === 'letter') {
                $isStrong = in_array($mark->grade, ['A', 'B', 'C']);
            }
            
            if ($isStrong) {
                $strongSubjects[] = strtolower($mark->subject_name);
            }
        }
        
        return $strongSubjects;
    }

    /**
     * Get recommended A-Level combinations based on strong subjects.
     */
    private function getRecommendedCombinations($strongSubjects, $marks)
    {
        // Define all available combinations with requirements and career paths
        $allCombinations = [
            [
                'code' => 'PCM/ICT',
                'name' => 'Physics, Chemistry, Mathematics with ICT',
                'subjects' => ['physics', 'chemistry', 'mathematics'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences',
                'difficulty' => 'High',
                'careers' => [
                    'Engineering (Civil, Mechanical, Electrical, etc.)',
                    'Architecture',
                    'Computer Science & IT',
                    'Medicine (if combined with Biology)',
                    'Pharmacy',
                    'Actuarial Science',
                    'Quantity Surveying',
                    'Aviation',
                    'Physics & Applied Sciences'
                ],
                'universities' => 'Most Science & Engineering programs at Makerere, MUBS, UCU, KIU, etc.'
            ],
            [
                'code' => 'BCM/ICT',
                'name' => 'Biology, Chemistry, Mathematics with ICT',
                'subjects' => ['biology', 'chemistry', 'mathematics'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences',
                'difficulty' => 'High',
                'careers' => [
                    'Medicine & Surgery',
                    'Dentistry',
                    'Nursing',
                    'Pharmacy',
                    'Veterinary Medicine',
                    'Biotechnology',
                    'Medical Laboratory Technology',
                    'Public Health',
                    'Nutrition & Dietetics',
                    'Environmental Science'
                ],
                'universities' => 'Medical schools at Makerere, Mbarara, Gulu, Busitema, KIU, etc.'
            ],
            [
                'code' => 'PCB/ICT',
                'name' => 'Physics, Chemistry, Biology with ICT',
                'subjects' => ['physics', 'chemistry', 'biology'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences',
                'difficulty' => 'Very High',
                'careers' => [
                    'Medicine',
                    'Dentistry',
                    'Biomedical Engineering',
                    'Biotechnology',
                    'Medical Physics',
                    'Pharmaceutical Science',
                    'Veterinary Medicine',
                    'Environmental Engineering',
                    'Industrial Chemistry'
                ],
                'universities' => 'Top medical and engineering programs nationwide'
            ],
            [
                'code' => 'PEM/ICT',
                'name' => 'Physics, Economics, Mathematics with ICT',
                'subjects' => ['physics', 'economics', 'mathematics'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences & Business',
                'difficulty' => 'Medium-High',
                'careers' => [
                    'Engineering',
                    'Business Administration',
                    'Economics',
                    'Finance & Accounting',
                    'Actuarial Science',
                    'Project Management',
                    'Data Science & Analytics',
                    'Quantity Surveying',
                    'Real Estate Management'
                ],
                'universities' => 'Engineering and business programs at all major universities'
            ],
            [
                'code' => 'HEG/ICT',
                'name' => 'History, Economics, Geography with ICT',
                'subjects' => ['history', 'economics', 'geography'],
                'subsidiary' => 'ICT',
                'category' => 'Arts',
                'difficulty' => 'Medium',
                'careers' => [
                    'Law',
                    'Business Administration',
                    'Economics',
                    'Social Work',
                    'Public Administration',
                    'International Relations',
                    'Political Science',
                    'Journalism',
                    'Tourism & Hotel Management',
                    'Urban Planning'
                ],
                'universities' => 'Arts and Social Sciences programs at all universities'
            ],
            [
                'code' => 'HEL/ICT',
                'name' => 'History, Economics, Literature with ICT',
                'subjects' => ['history', 'economics', 'literature'],
                'subsidiary' => 'ICT',
                'category' => 'Arts',
                'difficulty' => 'Medium',
                'careers' => [
                    'Law',
                    'Business Administration',
                    'Education',
                    'Journalism & Mass Communication',
                    'Public Relations',
                    'International Relations',
                    'Social Sciences',
                    'Development Studies',
                    'Human Resource Management'
                ],
                'universities' => 'Arts, Law, and Business programs nationwide'
            ],
            [
                'code' => 'MEG/ICT',
                'name' => 'Mathematics, Economics, Geography with ICT',
                'subjects' => ['mathematics', 'economics', 'geography'],
                'subsidiary' => 'ICT',
                'category' => 'Business & Sciences',
                'difficulty' => 'Medium',
                'careers' => [
                    'Business Administration',
                    'Economics',
                    'Accounting & Finance',
                    'Statistics',
                    'Data Science',
                    'Actuarial Science',
                    'Banking & Finance',
                    'Urban Planning',
                    'Land Surveying',
                    'GIS & Remote Sensing'
                ],
                'universities' => 'Business schools and science programs at major universities'
            ],
            [
                'code' => 'BCG/ICT',
                'name' => 'Biology, Chemistry, Geography with ICT',
                'subjects' => ['biology', 'chemistry', 'geography'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences',
                'difficulty' => 'Medium-High',
                'careers' => [
                    'Environmental Science',
                    'Agriculture',
                    'Forestry',
                    'Food Science & Technology',
                    'Pharmacy',
                    'Nursing',
                    'Public Health',
                    'Veterinary Medicine',
                    'Wildlife Management',
                    'Climate Science'
                ],
                'universities' => 'Agriculture and environmental programs at Makerere, Gulu, Busitema, etc.'
            ],
            [
                'code' => 'BCA/ICT',
                'name' => 'Biology, Chemistry, Agriculture with ICT',
                'subjects' => ['biology', 'chemistry', 'agriculture'],
                'subsidiary' => 'ICT',
                'category' => 'Sciences',
                'difficulty' => 'Medium-High',
                'careers' => [
                    'Agriculture & Agribusiness',
                    'Veterinary Medicine',
                    'Food Science & Technology',
                    'Agricultural Engineering',
                    'Animal Science',
                    'Crop Science',
                    'Agricultural Extension',
                    'Fisheries & Aquaculture',
                    'Environmental Management'
                ],
                'universities' => 'Agriculture programs at Makerere, Gulu, Busitema, Nkumba, etc.'
            ],
            [
                'code' => 'HGL/ICT',
                'name' => 'History, Geography, Literature with ICT',
                'subjects' => ['history', 'geography', 'literature'],
                'subsidiary' => 'ICT',
                'category' => 'Arts',
                'difficulty' => 'Medium',
                'careers' => [
                    'Education (Teaching)',
                    'Social Sciences',
                    'Development Studies',
                    'Tourism Management',
                    'Journalism',
                    'Public Relations',
                    'Urban Planning',
                    'Cultural Studies',
                    'Museum & Heritage Management'
                ],
                'universities' => 'Education and Arts programs at all universities'
            ],
            [
                'code' => 'LEK/ICT',
                'name' => 'Literature, Economics, Kiswahili with ICT',
                'subjects' => ['literature', 'economics', 'kiswahili'],
                'subsidiary' => 'ICT',
                'category' => 'Arts',
                'difficulty' => 'Medium',
                'careers' => [
                    'Education (Language Teaching)',
                    'Journalism & Media',
                    'Translation & Interpretation',
                    'Public Relations',
                    'Business Administration',
                    'Development Studies',
                    'International Relations',
                    'Cultural Studies'
                ],
                'universities' => 'Education and Arts programs nationwide'
            ],
        ];

        // Calculate match scores for each combination
        $scoredCombinations = [];
        
        foreach ($allCombinations as $combination) {
            $matchScore = 0;
            $matchedSubjects = [];
            $missingSubjects = [];
            
            foreach ($combination['subjects'] as $subject) {
                if (in_array($subject, $strongSubjects)) {
                    $matchScore += 10;
                    $matchedSubjects[] = ucfirst($subject);
                } else {
                    // Check if subject exists in marks (even if not strong)
                    $hasSubject = false;
                    foreach ($marks as $mark) {
                        if (strtolower($mark->subject_name) === $subject) {
                            $hasSubject = true;
                            $matchScore += 3; // Small bonus for having the subject
                            $matchedSubjects[] = ucfirst($subject) . ' (needs improvement)';
                            break;
                        }
                    }
                    if (!$hasSubject) {
                        $missingSubjects[] = ucfirst($subject);
                    }
                }
            }
            
            $combination['match_score'] = $matchScore;
            $combination['matched_subjects'] = $matchedSubjects;
            $combination['missing_subjects'] = $missingSubjects;
            $combination['is_excellent_match'] = count($missingSubjects) === 0 && $matchScore >= 30;
            $combination['is_good_match'] = count($missingSubjects) === 0 && $matchScore >= 20;
            $combination['is_possible_match'] = count($missingSubjects) === 0 || count($missingSubjects) === 1;
            
            $scoredCombinations[] = $combination;
        }
        
        // Sort by match score (highest first)
        usort($scoredCombinations, function($a, $b) {
            return $b['match_score'] - $a['match_score'];
        });
        
        return $scoredCombinations;
    }
    
    /**
     * Get performance comparison between current and previous exams.
     */
    private function getPerformanceComparison($userId, $currentExamType)
    {
        if (!$currentExamType) {
            return null;
        }
        
        // Get all exam types for this student ordered by typical sequence
        $examTypeOrder = ['Beginning of Term', 'Mid Term', 'End of Term', 'Mock', 'Other'];
        
        $studentExamTypes = StudentMark::where('user_id', $userId)
            ->whereNotNull('exam_type')
            ->distinct()
            ->pluck('exam_type')
            ->sortBy(function($type) use ($examTypeOrder) {
                $index = array_search($type, $examTypeOrder);
                return $index !== false ? $index : 999;
            })
            ->values();
        
        // Find the previous exam type
        $currentIndex = $studentExamTypes->search($currentExamType);
        if ($currentIndex === false || $currentIndex === 0) {
            return null; // No previous exam to compare with
        }
        
        $previousExamType = $studentExamTypes[$currentIndex - 1];
        
        // Get current exam marks
        $currentMarks = StudentMark::where('user_id', $userId)
            ->where('exam_type', $currentExamType)
            ->get()
            ->keyBy('subject_name');
        
        // Get previous exam marks
        $previousMarks = StudentMark::where('user_id', $userId)
            ->where('exam_type', $previousExamType)
            ->get()
            ->keyBy('subject_name');
        
        // Calculate comparison
        $comparison = [
            'previous_exam_type' => $previousExamType,
            'current_exam_type' => $currentExamType,
            'current_avg_points' => $currentMarks->avg('points') ?? 0,
            'previous_avg_points' => $previousMarks->avg('points') ?? 0,
            'subjects' => [],
            'improved_subjects' => 0,
            'declined_subjects' => 0,
            'stable_subjects' => 0,
        ];
        
        // Compare subjects that appear in both exams
        foreach ($currentMarks as $subject => $currentMark) {
            if (isset($previousMarks[$subject])) {
                $previousMark = $previousMarks[$subject];
                $pointsDiff = $currentMark->points - $previousMark->points;
                
                $comparison['subjects'][$subject] = [
                    'current_grade' => $currentMark->grade,
                    'previous_grade' => $previousMark->grade,
                    'current_points' => $currentMark->points,
                    'previous_points' => $previousMark->points,
                    'points_diff' => $pointsDiff,
                    'status' => $pointsDiff > 0 ? 'improved' : ($pointsDiff < 0 ? 'declined' : 'stable'),
                ];
                
                if ($pointsDiff > 0) {
                    $comparison['improved_subjects']++;
                } elseif ($pointsDiff < 0) {
                    $comparison['declined_subjects']++;
                } else {
                    $comparison['stable_subjects']++;
                }
            }
        }
        
        $comparison['avg_points_diff'] = $comparison['current_avg_points'] - $comparison['previous_avg_points'];
        $comparison['overall_status'] = $comparison['avg_points_diff'] > 0 ? 'improved' : 
                                       ($comparison['avg_points_diff'] < 0 ? 'declined' : 'stable');
        
        return $comparison;
    }
}
