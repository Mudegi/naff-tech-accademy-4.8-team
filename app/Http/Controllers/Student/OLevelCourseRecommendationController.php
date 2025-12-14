<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentMark;
use App\Models\UniversityCutOff;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OLevelCourseRecommendationController extends Controller
{
    /**
     * Show subject selection form for O-Level students.
     */
    public function selectSubjects()
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }
        
        $user = Auth::user();
        
        // Get student's O-Level marks (UCE or Form 1-4)
        $studentMarks = StudentMark::where('user_id', $user->id)
            ->whereIn('academic_level', ['UCE', 'O-Level', 'Form 1', 'Form 2', 'Form 3', 'Form 4'])
            ->orderBy('subject_name')
            ->get();

        if ($studentMarks->isEmpty()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No O-Level marks found. Please contact your teacher to upload your marks.');
        }

        // Get grading scale for display
        $gradeScale = GradeScale::getScalesForSchool('O-Level', $user->school_id);

        return view('student.o-level-recommendations.select-subjects', compact('studentMarks', 'gradeScale'));
    }

    /**
     * Calculate and display course recommendations based on selected subjects.
     */
    public function showRecommendations(Request $request)
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }

        $validated = $request->validate([
            'selected_subjects' => 'required|array|min:3',
            'selected_subjects.*' => 'required|exists:student_marks,id',
        ]);

        $user = Auth::user();

        // Get the selected marks
        $selectedMarks = StudentMark::whereIn('id', $validated['selected_subjects'])
            ->where('user_id', $user->id)
            ->get();

        if ($selectedMarks->count() < 3) {
            return back()->with('error', 'Please select at least 3 subjects.');
        }

        // Calculate aggregate points using the grading scale
        $aggregatePoints = 0;
        $marksWithPoints = [];

        foreach ($selectedMarks as $mark) {
            // Get points based on percentage/numeric mark using school's grading scale
            if ($mark->numeric_mark !== null) {
                $gradeAndPoints = GradeScale::getGradeAndPoints(
                    $mark->numeric_mark, 
                    'O-Level', 
                    $user->school_id
                );
                $points = $gradeAndPoints['points'];
                $calculatedGrade = $gradeAndPoints['grade'];
            } else {
                // If only letter grade is available, try to match it
                $gradeScale = GradeScale::active()
                    ->forLevel('O-Level')
                    ->forSchool($user->school_id)
                    ->where('grade', strtoupper($mark->grade))
                    ->first();

                if (!$gradeScale) {
                    // Fallback to default scale
                    $gradeScale = GradeScale::active()
                        ->forLevel('O-Level')
                        ->forSchool(null)
                        ->where('grade', strtoupper($mark->grade))
                        ->first();
                }

                $points = $gradeScale ? $gradeScale->points : 0;
                $calculatedGrade = $mark->grade;
            }

            $aggregatePoints += $points;

            $marksWithPoints[] = [
                'subject_name' => $mark->subject_name,
                'grade' => $calculatedGrade,
                'numeric_mark' => $mark->numeric_mark,
                'points' => $points,
            ];
        }

        // Get student's subjects for matching
        $studentSubjects = $selectedMarks->pluck('subject_name')->toArray();
        $studentGender = $user->gender;

        // Get current academic year
        $currentYear = UniversityCutOff::max('academic_year') ?? date('Y');

        // Get qualifying courses for O-Level
        $qualifyingCourses = UniversityCutOff::active()
            ->forYear($currentYear)
            ->where('academic_level', 'O-Level') // Filter for O-Level courses
            ->where(function($query) use ($aggregatePoints, $studentGender) {
                $query->where('cut_off_points', '<=', $aggregatePoints);
                
                // Handle gender-specific cut-offs
                if ($studentGender === 'female') {
                    $query->orWhere(function($q) use ($aggregatePoints) {
                        $q->whereNotNull('female_cut_off')
                          ->where('female_cut_off', '<=', $aggregatePoints);
                    });
                } elseif ($studentGender === 'male') {
                    $query->orWhere(function($q) use ($aggregatePoints) {
                        $q->whereNotNull('male_cut_off')
                          ->where('male_cut_off', '<=', $aggregatePoints);
                    });
                }
            })
            ->orderBy('cut_off_points', 'asc')
            ->get()
            ->map(function ($course) use ($aggregatePoints, $studentSubjects, $studentGender) {
                // Calculate match score based on subject requirements (for ranking/badges only)
                $matchScore = $this->calculateMatchScore($course, $studentSubjects);
                $meetsEssentialSubjects = $this->meetsEssentialSubjects($course, $studentSubjects);
                $effectiveCutOff = $course->getEffectiveCutOff($studentGender);
                
                $course->match_score = $matchScore;
                $course->meets_essential_subjects = $meetsEssentialSubjects;
                $course->effective_cut_off = $effectiveCutOff;
                $course->points_difference = $aggregatePoints - $effectiveCutOff;
                return $course;
            })
            ->sortByDesc('match_score')
            ->values();

        // Group by university
        $groupedByUniversity = $qualifyingCourses->groupBy('university_name');

        return view('student.o-level-recommendations.results', compact(
            'qualifyingCourses',
            'groupedByUniversity',
            'aggregatePoints',
            'marksWithPoints',
            'currentYear'
        ));
    }

    /**
     * Download PDF report of O-Level course recommendations.
     */
    public function downloadPdf(Request $request)
    {
        // Only allow students, not teachers
        if (session('user_type') === 'teacher') {
            abort(403, 'This feature is only available for students.');
        }

        $validated = $request->validate([
            'selected_subjects' => 'required|array|min:3',
            'selected_subjects.*' => 'required|exists:student_marks,id',
        ]);

        $user = Auth::user();

        // Get the selected marks
        $selectedMarks = StudentMark::whereIn('id', $validated['selected_subjects'])
            ->where('user_id', $user->id)
            ->get();

        // Calculate aggregate points
        $aggregatePoints = 0;
        $marksWithPoints = [];

        foreach ($selectedMarks as $mark) {
            if ($mark->numeric_mark !== null) {
                $gradeAndPoints = GradeScale::getGradeAndPoints(
                    $mark->numeric_mark, 
                    'O-Level', 
                    $user->school_id
                );
                $points = $gradeAndPoints['points'];
                $calculatedGrade = $gradeAndPoints['grade'];
            } else {
                $gradeScale = GradeScale::active()
                    ->forLevel('O-Level')
                    ->where('grade', strtoupper($mark->grade))
                    ->first();
                $points = $gradeScale ? $gradeScale->points : 0;
                $calculatedGrade = $mark->grade;
            }

            $aggregatePoints += $points;

            $marksWithPoints[] = [
                'subject_name' => $mark->subject_name,
                'grade' => $calculatedGrade,
                'numeric_mark' => $mark->numeric_mark,
                'points' => $points,
            ];
        }

        $studentSubjects = $selectedMarks->pluck('subject_name')->toArray();
        $studentGender = $user->gender;
        $currentYear = UniversityCutOff::max('academic_year') ?? date('Y');

        // Get qualifying courses (same logic as showRecommendations)
        $qualifyingCourses = UniversityCutOff::active()
            ->forYear($currentYear)
            ->where('academic_level', 'O-Level')
            ->where(function($query) use ($aggregatePoints, $studentGender) {
                $query->where('cut_off_points', '<=', $aggregatePoints);
                
                if ($studentGender === 'female') {
                    $query->orWhere(function($q) use ($aggregatePoints) {
                        $q->whereNotNull('female_cut_off')
                          ->where('female_cut_off', '<=', $aggregatePoints);
                    });
                } elseif ($studentGender === 'male') {
                    $query->orWhere(function($q) use ($aggregatePoints) {
                        $q->whereNotNull('male_cut_off')
                          ->where('male_cut_off', '<=', $aggregatePoints);
                    });
                }
            })
            ->orderBy('cut_off_points', 'asc')
            ->get()
            ->map(function ($course) use ($aggregatePoints, $studentSubjects, $studentGender) {
                $matchScore = $this->calculateMatchScore($course, $studentSubjects);
                $meetsEssentialSubjects = $this->meetsEssentialSubjects($course, $studentSubjects);
                $effectiveCutOff = $course->getEffectiveCutOff($studentGender);
                
                $course->match_score = $matchScore;
                $course->meets_essential_subjects = $meetsEssentialSubjects;
                $course->effective_cut_off = $effectiveCutOff;
                $course->points_difference = $aggregatePoints - $effectiveCutOff;
                return $course;
            })
            ->sortByDesc('match_score')
            ->values();

        $groupedByUniversity = $qualifyingCourses->groupBy('university_name');

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.o-level-recommendations.pdf', [
                'user' => $user,
                'qualifyingCourses' => $qualifyingCourses,
                'groupedByUniversity' => $groupedByUniversity,
                'aggregatePoints' => $aggregatePoints,
                'marksWithPoints' => $marksWithPoints,
                'currentYear' => $currentYear,
            ]);

            $fileName = 'OLevel_Course_Recommendations_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Check if student meets essential subject requirements.
     */
    private function meetsEssentialSubjects($course, $studentSubjects)
    {
        if (!$course->essential_subjects || empty($course->essential_subjects)) {
            return true;
        }

        $studentSubjectsLower = array_map('strtolower', $studentSubjects);
        $essentialSubjectsLower = array_map('strtolower', $course->essential_subjects);

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

        if ($course->essential_subjects) {
            foreach ($course->essential_subjects as $subject) {
                if (in_array(strtolower($subject), $studentSubjectsLower)) {
                    $score += 10;
                }
            }
        }

        if ($course->relevant_subjects) {
            foreach ($course->relevant_subjects as $subject) {
                if (in_array(strtolower($subject), $studentSubjectsLower)) {
                    $score += 5;
                }
            }
        }

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
