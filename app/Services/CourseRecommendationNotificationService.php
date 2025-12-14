<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\StudentMark;
use App\Models\UniversityCutOff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseRecommendationNotificationService
{
    /**
     * Check and notify students when new courses match their profile
     * This should be called when:
     * 1. A new university cut-off is added/updated
     * 2. A student updates their marks
     */
    public function checkAndNotifyForNewCourses(UniversityCutOff $cutOff = null)
    {
        $currentYear = UniversityCutOff::max('academic_year') ?? date('Y');
        
        // Get all students with UACE marks
        // Use a subquery to find users who have UACE principal passes
        $studentIds = StudentMark::where('academic_level', 'UACE')
            ->where('is_principal_pass', true)
            ->distinct()
            ->pluck('user_id');
        
        $students = User::where('account_type', 'student')
            ->whereIn('id', $studentIds)
            ->get();

        foreach ($students as $student) {
            $this->checkStudentForNewCourses($student, $cutOff, $currentYear);
        }
    }

    /**
     * Check a specific student for new matching courses
     */
    public function checkStudentForNewCourses(User $student, UniversityCutOff $cutOff = null, $academicYear = null)
    {
        if (!$academicYear) {
            $academicYear = UniversityCutOff::max('academic_year') ?? date('Y');
        }

        // Calculate student's aggregate points
        $aggregatePoints = $this->calculateAggregatePoints($student->id);
        
        if ($aggregatePoints == 0) {
            return; // Student doesn't have enough principal passes
        }

        $principalPasses = StudentMark::where('user_id', $student->id)
            ->where('academic_level', 'UACE')
            ->where('is_principal_pass', true)
            ->where('points', '>=', 2)
            ->count();

        // Get student's gender for gender-specific cut-offs
        $studentGender = $student->gender; // 'male', 'female', or null

        // If a specific cut-off was provided, check only that one
        if ($cutOff) {
            if ($cutOff->studentQualifies($aggregatePoints, $principalPasses, $studentGender)) {
                $this->createNotificationForCourse($student, $cutOff, $aggregatePoints, $studentGender);
            }
            return;
        }

        // Otherwise, check all active courses for the current year
        $qualifyingCourses = UniversityCutOff::active()
            ->forYear($academicYear)
            ->qualifying($aggregatePoints, $studentGender)
            ->get()
            ->filter(function ($course) use ($aggregatePoints, $principalPasses, $studentGender) {
                return $course->studentQualifies($aggregatePoints, $principalPasses, $studentGender);
            });

        // Get courses the student was already notified about (in the last 30 days)
        $recentNotifications = Notification::where('user_id', $student->id)
            ->where('type', 'course_recommendation')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('university_cut_off_id')
            ->toArray();

        // Notify about new courses that student qualifies for
        foreach ($qualifyingCourses as $course) {
            // Skip if already notified recently
            if (in_array($course->id, $recentNotifications)) {
                continue;
            }

            $this->createNotificationForCourse($student, $course, $aggregatePoints, $studentGender);
        }
    }

    /**
     * Create a notification for a matching course
     */
    private function createNotificationForCourse(User $student, UniversityCutOff $course, $aggregatePoints, $gender = null)
    {
        // Check if notification already exists for this course (within last 7 days)
        $existingNotification = Notification::where('user_id', $student->id)
            ->where('type', 'course_recommendation')
            ->where('university_cut_off_id', $course->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();

        if ($existingNotification) {
            return; // Already notified recently
        }

        // Get effective cut-off based on format and gender
        $effectiveCutOff = $course->getEffectiveCutOff($gender);
        $pointsDifference = $aggregatePoints - $effectiveCutOff;
        $status = $pointsDifference >= 0 ? 'qualify' : 'close';

        // Build cut-off display text
        $cutOffDisplay = $effectiveCutOff;
        if ($course->cut_off_format === 'makerere' && $course->program_category === 'stem') {
            $cutOffDisplay = "M: {$course->cut_off_points_male}, F: {$course->cut_off_points_female}";
        }

        Notification::create([
            'user_id' => $student->id,
            'resource_id' => null,
            'comment_id' => null,
            'university_cut_off_id' => $course->id,
            'type' => 'course_recommendation',
            'title' => $status === 'qualify' 
                ? 'New Course Recommendation Available!' 
                : 'Course Recommendation Update',
            'message' => $status === 'qualify'
                ? "You qualify for {$course->course_name} at {$course->university_name}! Your points ({$aggregatePoints}) exceed the cut-off ({$effectiveCutOff})."
                : "You're close to qualifying for {$course->course_name} at {$course->university_name}. You need " . abs($pointsDifference) . " more points.",
            'link' => route('student.course-recommendations.index'),
            'is_read' => false,
        ]);
    }

    /**
     * Calculate aggregate points for a student
     */
    private function calculateAggregatePoints($userId)
    {
        $principalPasses = StudentMark::where('user_id', $userId)
            ->where('academic_level', 'UACE')
            ->where('is_principal_pass', true)
            ->orderByDesc('points')
            ->take(3)
            ->get();

        if ($principalPasses->count() < 2) {
            return 0;
        }

        return $principalPasses->sum('points');
    }

    /**
     * Notify student when they update their marks and new courses become available
     */
    public function notifyOnMarksUpdate(User $student)
    {
        $this->checkStudentForNewCourses($student);
    }
}

