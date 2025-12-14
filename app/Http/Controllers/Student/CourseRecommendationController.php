<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentMark;
use App\Models\UniversityCutOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRecommendationController extends Controller
{
    /**
     * Display course recommendations based on student marks.
     */
    public function index(Request $request)
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }
        
        $user = Auth::user();
        
        // Check if student is O-Level - redirect to Career Guidance (A-Level combination recommendations)
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        
        // Check student's level field first
        if ($student && $student->level === 'O Level') {
            return redirect()->route('student.career-guidance.index')
                ->with('info', 'As an O-Level student, you can view A-Level combination recommendations based on your academic performance.');
        }
        
        // Also check class name for O-Level (Form 1-4, S1-S4)
        $user->load('classes');
        $studentClass = $user->classes->first();
        
        if ($studentClass) {
            $className = strtolower($studentClass->name);
            if (preg_match('/(form\s*[1-4]|s[1-4])/i', $className)) {
                return redirect()->route('student.career-guidance.index')
                    ->with('info', 'As an O-Level student, you can view A-Level combination recommendations based on your academic performance.');
            }
        }
        
        // Get available exam types for this student
        $availableExamTypes = StudentMark::where('user_id', $user->id)
            ->whereNotNull('exam_type')
            ->distinct()
            ->pluck('exam_type')
            ->sort()
            ->values();
        
        // Get selected exam type (student can choose which exam's marks to use)
        $selectedExamType = $request->input('exam_type');
        
        // Calculate student's aggregate points (filtered by exam type if selected)
        $aggregatePoints = $this->calculateAggregatePoints($user->id, $selectedExamType);
        $principalPassesQuery = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true)
            ->where('points', '>=', 2); // E or better
        
        if ($selectedExamType) {
            $principalPassesQuery->where('exam_type', $selectedExamType);
        }
        
        $principalPasses = $principalPassesQuery->count();

        // Get student's subjects (filtered by exam type if selected)
        $studentSubjectsQuery = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true);
        
        if ($selectedExamType) {
            $studentSubjectsQuery->where('exam_type', $selectedExamType);
        }
        
        $studentSubjects = $studentSubjectsQuery->pluck('subject_name')->toArray();

        // Get student's gender for gender-specific cut-offs
        $studentGender = $user->gender; // 'male', 'female', or null

        // Get current academic year (most recent)
        $currentYear = UniversityCutOff::max('academic_year') ?? date('Y');
        
        // Get performance comparison data
        $performanceComparison = $this->getPerformanceComparison($user->id, $selectedExamType);

        // Get all qualifying courses (pass gender to scope)
        $qualifyingCourses = UniversityCutOff::active()
            ->forYear($currentYear)
            ->qualifying($aggregatePoints, $studentGender)
            ->orderBy('cut_off_points', 'asc')
            ->get()
            ->filter(function ($course) use ($aggregatePoints, $principalPasses, $studentGender, $studentSubjects) {
                // First check if student meets cut-off and principal pass requirements
                if (!$course->studentQualifies($aggregatePoints, $principalPasses, $studentGender)) {
                    return false;
                }
                
                // Then check if student has all essential subjects (if course has essential subjects defined)
                if (!$this->meetsEssentialSubjects($course, $studentSubjects)) {
                    return false;
                }
                
                return true;
            })
            ->map(function ($course) use ($aggregatePoints, $studentSubjects, $studentGender) {
                // Calculate match score based on subject requirements
                $matchScore = $this->calculateMatchScore($course, $studentSubjects);
                $effectiveCutOff = $course->getEffectiveCutOff($studentGender);
                $course->match_score = $matchScore;
                $course->effective_cut_off = $effectiveCutOff;
                return $course;
            })
            ->sortByDesc('match_score')
            ->values();

        // Group by university
        $groupedByUniversity = $qualifyingCourses->groupBy('university_name');

        return view('student.course-recommendations.index', compact(
            'qualifyingCourses',
            'groupedByUniversity',
            'aggregatePoints',
            'principalPasses',
            'currentYear',
            'availableExamTypes',
            'selectedExamType',
            'performanceComparison'
        ));
    }

    /**
     * Download PDF report of course recommendations
     */
    public function downloadPdf()
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }
        
        $user = Auth::user();
        
        // Calculate student's aggregate points (from any exam marks)
        $aggregatePoints = $this->calculateAggregatePoints($user->id);
        $principalPasses = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true)
            ->where('points', '>=', 2)
            ->count();

        // Get student's subjects
        $studentSubjects = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true)
            ->pluck('subject_name')
            ->toArray();

        // Get student's gender for gender-specific cut-offs
        $studentGender = $user->gender; // 'male', 'female', or null

        // Get current academic year
        $currentYear = UniversityCutOff::max('academic_year') ?? date('Y');

        // Get all qualifying courses (pass gender to scope)
        $qualifyingCourses = UniversityCutOff::active()
            ->forYear($currentYear)
            ->qualifying($aggregatePoints, $studentGender)
            ->orderBy('cut_off_points', 'asc')
            ->get()
            ->filter(function ($course) use ($aggregatePoints, $principalPasses, $studentGender, $studentSubjects) {
                // First check if student meets cut-off and principal pass requirements
                if (!$course->studentQualifies($aggregatePoints, $principalPasses, $studentGender)) {
                    return false;
                }
                
                // Then check if student has all essential subjects (if course has essential subjects defined)
                if (!$this->meetsEssentialSubjects($course, $studentSubjects)) {
                    return false;
                }
                
                return true;
            })
            ->map(function ($course) use ($aggregatePoints, $studentSubjects, $studentGender) {
                $matchScore = $this->calculateMatchScore($course, $studentSubjects);
                $effectiveCutOff = $course->getEffectiveCutOff($studentGender);
                $course->match_score = $matchScore;
                $course->effective_cut_off = $effectiveCutOff;
                $course->points_difference = $aggregatePoints - $effectiveCutOff;
                return $course;
            })
            ->sortByDesc('match_score')
            ->values();

        // Group by university
        $groupedByUniversity = $qualifyingCourses->groupBy('university_name');

        // Get student marks for display
        $studentMarks = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true)
            ->orderByDesc('points')
            ->get();

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.course-recommendations-pdf', [
                'user' => $user,
                'qualifyingCourses' => $qualifyingCourses,
                'groupedByUniversity' => $groupedByUniversity,
                'aggregatePoints' => $aggregatePoints,
                'principalPasses' => $principalPasses,
                'currentYear' => $currentYear,
                'studentMarks' => $studentMarks,
                'studentSubjects' => $studentSubjects,
            ]);

            $fileName = 'Course_Recommendations_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->route('student.course-recommendations.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Calculate aggregate points for a student.
     */
    private function calculateAggregatePoints($userId, $examType = null)
    {
        // Calculate from specific exam type or any exam marks
        $query = StudentMark::where('user_id', $userId)
            ->where('is_principal_pass', true);
        
        if ($examType) {
            $query->where('exam_type', $examType);
        }
        
        $principalPasses = $query->orderByDesc('points')
            ->take(3)
            ->get();

        if ($principalPasses->count() < 2) {
            return 0; // Need at least 2 principal passes
        }

        return $principalPasses->sum('points');
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
            'current_aggregate' => $this->calculateAggregatePoints($userId, $currentExamType),
            'previous_aggregate' => $this->calculateAggregatePoints($userId, $previousExamType),
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
        
        $comparison['aggregate_diff'] = $comparison['current_aggregate'] - $comparison['previous_aggregate'];
        $comparison['overall_status'] = $comparison['aggregate_diff'] > 0 ? 'improved' : 
                                       ($comparison['aggregate_diff'] < 0 ? 'declined' : 'stable');
        
        return $comparison;
    }

    /**
     * Check if student meets essential subject requirements.
     * If a course has essential subjects defined, the student must have ALL of them.
     * If no essential subjects are defined, return true (course doesn't require specific subjects).
     */
    private function meetsEssentialSubjects($course, $studentSubjects)
    {
        // If course has no essential subjects defined, allow it (for backward compatibility)
        if (!$course->essential_subjects || empty($course->essential_subjects)) {
            return true;
        }

        $studentSubjectsLower = array_map('strtolower', $studentSubjects);
        $essentialSubjectsLower = array_map('strtolower', $course->essential_subjects);

        // Student must have ALL essential subjects
        foreach ($essentialSubjectsLower as $essentialSubject) {
            if (!in_array(strtolower($essentialSubject), $studentSubjectsLower)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate match score based on subject requirements.
     */
    private function calculateMatchScore($course, $studentSubjects)
    {
        $score = 0;
        $studentSubjectsLower = array_map('strtolower', $studentSubjects);

        // Check essential subjects (highest weight)
        if ($course->essential_subjects) {
            foreach ($course->essential_subjects as $subject) {
                if (in_array(strtolower($subject), $studentSubjectsLower)) {
                    $score += 10;
                }
            }
        }

        // Check relevant subjects (medium weight)
        if ($course->relevant_subjects) {
            foreach ($course->relevant_subjects as $subject) {
                if (in_array(strtolower($subject), $studentSubjectsLower)) {
                    $score += 5;
                }
            }
        }

        // Check desirable subjects (lower weight)
        if ($course->desirable_subjects) {
            foreach ($course->desirable_subjects as $subject) {
                if (in_array(strtolower($subject), $studentSubjectsLower)) {
                    $score += 2;
                }
            }
        }

        return $score;
    }
}
