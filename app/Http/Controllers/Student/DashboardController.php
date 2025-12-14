<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\VideoPlayHistory;
use App\Models\StudentMark;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $isTeacher = in_array($user->account_type, ['teacher', 'subject_teacher']);
            $isSchoolStudent = $user->account_type === 'student' && $user->school_id;
            
            \Log::info('User info', [
                'is_school_student' => $isSchoolStudent,
                'school_id' => $user->school_id,
            ]);
            
            // Get sample videos count (restricted to school if school student)
            $sampleVideosQuery = Resource::where('visible_as_sample', true)
                ->whereNotNull('google_drive_link');
            
            if ($isSchoolStudent) {
                $sampleVideosQuery->where(function($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->orWhereHas('schools', function($subQuery) use ($user) {
                          $subQuery->where('schools.id', $user->school_id);
                      });
                });
            }
            
            $sampleVideosCount = $sampleVideosQuery->count();
            // Available videos for this user (matching their subscription and preferences)
            $availableVideosCount = 0;
            $availableSubjectsCount = 0;
            $availableTopicsCount = 0;
            $hasAvailableVideosLink = false;
            $preference = $user->preference;
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('end_date', '>', now())
                ->where('is_active', true)
                ->first();
                
            // No mobile redirect logic needed - users with subscriptions should access their videos regardless of device
            
            // Get user's videos based on subscription and preferences
        $userVideos = collect();
        $sampleVideos = collect();
        
        // Initialize school student specific data
        $schoolStudentData = null;
        
        if ($isSchoolStudent) {
            // Get student's class directly from database - simple and reliable
            $studentClassId = \DB::table('class_user')
                ->where('user_id', $user->id)
                ->value('class_id');
            
            $studentClass = null;
            if ($studentClassId) {
                $studentClass = \App\Models\SchoolClass::find($studentClassId);
            }
            
            // School students get free access to their school's resources
            $videoQuery = Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->where('is_active', true)
                ->whereNotNull('google_drive_link')
                ->where(function($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->orWhereHas('schools', function($subQuery) use ($user) {
                          $subQuery->where('schools.id', $user->school_id);
                      });
                });
            
            // Filter by student's class - show only videos for their class
            if ($studentClassId) {
                $videoQuery->where('class_id', $studentClassId);
            }
            
            // For A Level students (Form 5-6), filter by their subject combination
            if ($studentClass && preg_match('/form\s*[5-6]|s[5-6]/i', strtolower($studentClass->name))) {
                $student = $user->student;
                if ($student && $student->combination) {
                    $combinationSubjects = $this->getStudentCombinationSubjects($user);
                    if ($combinationSubjects && count($combinationSubjects) > 0) {
                        $videoQuery->whereIn('subject_id', $combinationSubjects);
                    }
                }
            }
            
            $userVideos = $videoQuery->latest()->take(6)->get();
            
            // Apply same filtering to counts
            $countQuery = Resource::where('is_active', true)
                ->whereNotNull('google_drive_link')
                ->where(function($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->orWhereHas('schools', function($subQuery) use ($user) {
                          $subQuery->where('schools.id', $user->school_id);
                      });
                });
            
            if ($studentClassId) {
                $countQuery->where('class_id', $studentClassId);
            }
            
            if ($studentClass && preg_match('/form\s*[5-6]|s[5-6]/i', strtolower($studentClass->name))) {
                $student = $user->student;
                if ($student && $student->combination) {
                    $combinationSubjects = $this->getStudentCombinationSubjects($user);
                    if ($combinationSubjects && count($combinationSubjects) > 0) {
                        $countQuery->whereIn('subject_id', $combinationSubjects);
                    }
                }
            }
            
            $availableVideosCount = $countQuery->count();
            $availableSubjectsCount = $countQuery->pluck('subject_id')->unique()->filter()->count();
            $availableTopicsCount = $countQuery->pluck('topic_id')->unique()->filter()->count();
            
            $hasAvailableVideosLink = true;
            
            // Get school student specific data - simplified to avoid errors
            $student = $user->student;
            if ($student) {
                // Fetch groups the user is already a member of
                $myGroups = collect();
                try {
                    $myGroups = $user->approvedGroups()->with(['members', 'projects', 'schoolClass'])->get();
                } catch (\Exception $e) {
                    \Log::error('Error fetching student groups: ' . $e->getMessage());
                }

                // Fetch available groups (open groups in the student's class that they're not in)
                $availableGroups = collect();
                try {
                    if ($student->class_id) {
                        $memberGroupIds = $myGroups->pluck('id');
                        $availableGroups = Group::where('school_id', $user->school_id)
                            ->where('class_id', $student->class_id)
                            ->where('status', 'open')
                            ->whereNotIn('id', $memberGroupIds)
                            ->with(['members', 'creator'])
                            ->get()
                            ->filter(function ($group) {
                                return !$group->isFull();
                            });
                    }
                } catch (\Exception $e) {
                    \Log::error('Error fetching available groups: ' . $e->getMessage());
                }

                // Fetch projects for groups the student belongs to
                $projects = collect();
                try {
                    $projectIds = $myGroups->pluck('projects')->flatten()->pluck('id')->unique();
                    if (!$projectIds->isEmpty()) {
                        $projects = Project::with(['group', 'planning', 'implementation'])
                            ->whereIn('id', $projectIds)
                            ->orderBy('created_at', 'desc')
                            ->get();
                    }
                } catch (\Exception $e) {
                    \Log::error('Error fetching student projects: ' . $e->getMessage());
                }

                // Fetch recent marks
                $marks = collect();
                try {
                    $marks = StudentMark::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();
                } catch (\Exception $e) {
                    \Log::error('Error fetching student marks: ' . $e->getMessage());
                }

                // Build stats
                $stats = [
                    'total_marks' => $marks->count(),
                    'total_groups' => $myGroups->count(),
                    'total_projects' => $projects->count(),
                    'total_assignments' => 0,
                    'pending_assignments' => 0,
                    'average_percentage' => $this->calculateAveragePercentage($marks),
                ];

                $schoolStudentData = [
                    'student' => $student,
                    'school' => $student->school,
                    'classes' => $user->classes()->get(),
                    'marks' => $marks,
                    'myGroups' => $myGroups,
                    'availableGroups' => $availableGroups,
                    'projects' => $projects,
                    'assignments' => collect(),
                    'resources' => collect(),
                    'stats' => $stats,
                ];
            }
        } elseif ($isTeacher) {
            // For teachers, show only videos they created
            $query = Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->where('teacher_id', $user->id)
                ->whereNotNull('google_drive_link');
            
            $userVideos = $query->latest()->take(6)->get();
            $availableVideosCount = $query->count();
            $availableSubjectsCount = $query->pluck('subject_id')->unique()->filter()->count();
            $availableTopicsCount = $query->pluck('topic_id')->unique()->filter()->count();
            $hasAvailableVideosLink = true;
            
            // Add comment counts to each video
            $userVideos->transform(function($video) use ($user) {
                $video->unreplied_comments_count = $video->getUnrepliedStudentCommentsCount($user->id);
                $video->replied_comments_count = $video->getRepliedStudentCommentsCount($user->id);
                return $video;
            });
        } elseif ($subscription) {
            $subscriptionPackage = DB::table('subscription_packages')->where('id', $subscription->subscription_package_id)->first();
            $query = Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->whereNotNull('google_drive_link');
            
            if ($subscriptionPackage->subscription_type === 'term') {
                if ($preference && $preference->class_id && $preference->term_id) {
                    $query->where('class_id', $preference->class_id)
                        ->where('term_id', $preference->term_id);
                    $hasAvailableVideosLink = true;
                }
            } elseif ($subscriptionPackage->subscription_type === 'subject') {
                if ($preference && $preference->class_id && $preference->subject_id) {
                    $query->where('class_id', $preference->class_id)
                        ->where('subject_id', $preference->subject_id);
                    $hasAvailableVideosLink = true;
                }
            } elseif ($subscriptionPackage->subscription_type === 'topic') {
                if ($preference && $preference->class_id && $preference->subject_id && $preference->topic_id) {
                    $query->where('class_id', $preference->class_id)
                        ->where('subject_id', $preference->subject_id)
                        ->where('topic_id', $preference->topic_id);
                    $hasAvailableVideosLink = true;
                }
            }
            
            if ($hasAvailableVideosLink) {
                $userVideos = $query->latest()->take(6)->get();
                $availableVideosCount = $query->count();
                $availableSubjectsCount = $query->pluck('subject_id')->unique()->filter()->count();
                $availableTopicsCount = $query->pluck('topic_id')->unique()->filter()->count();
            }
        } else {
            // Get sample videos if user has no subscription (restricted to school if school student)
            $sampleVideosQuery = Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->where('visible_as_sample', true)
                ->whereNotNull('google_drive_link');
            
            if ($isSchoolStudent) {
                $sampleVideosQuery->where('school_id', $user->school_id);
            }
            
            $sampleVideos = $sampleVideosQuery->latest()->take(6)->get();
        }
        
        // For now, set other stats to 0 since we don't have the models yet
        $totalCourses = 0;
        $certificatesCount = 0;
        // Check if the user has an active subscription
        $hasActiveSubscription = UserSubscription::where('user_id', $user->id)
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->exists();
        // Get current active subscription (license)
        $currentLicense = null;
        $currentLicensePackage = null;
        if (Auth::check()) {
            $active = UserSubscription::where('user_id', $user->id)
                ->where('end_date', '>', now())
                ->where('is_active', true)
                ->first();
            if ($active && $active->subscription_package_id) {
                $currentLicense = $active;
                $currentLicensePackage = DB::table('subscription_packages')->where('id', $active->subscription_package_id)->first();
            }
        }

        // Get recent video play history (restricted to school if school student)
        $recentVideosQuery = VideoPlayHistory::with(['resource'])
            ->where('user_id', $user->id);
        
        if ($isSchoolStudent) {
            $recentVideosQuery->whereHas('resource', function($q) use ($user) {
                $q->where('school_id', $user->school_id);
            });
        }
        
        $recentVideos = $recentVideosQuery->orderBy('played_at', 'desc')->take(5)->get();

        // Get student marks/grades (for school students or if user has taken assessments)
        $studentMarks = StudentMark::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('student.dashboard', compact(
            'totalCourses',
            'certificatesCount',
            'sampleVideosCount',
            'availableVideosCount',
            'availableSubjectsCount',
            'availableTopicsCount',
            'hasAvailableVideosLink',
            'hasActiveSubscription',
            'currentLicense',
            'currentLicensePackage',
            'recentVideos',
            'userVideos',
            'sampleVideos',
            'isTeacher',
            'isSchoolStudent',
            'studentMarks',
            'schoolStudentData'
        ));
        } catch (\Exception $e) {
            \Log::error('Dashboard error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    
    /**
     * Calculate average percentage for student marks
     */
    private function calculateAveragePercentage($marks)
    {
        if ($marks->isEmpty()) {
            return 0;
        }

        $percentageMarks = $marks->filter(fn($m) => $m->marks_percentage !== null);

        if ($percentageMarks->isEmpty()) {
            return 0;
        }

        return round($percentageMarks->avg('marks_percentage'), 2);
    }
    
    /**
     * Get subject IDs for A Level student's combination
     */
    private function getStudentCombinationSubjects($user)
    {
        $student = $user->student;
        if (!$student || !$student->combination) {
            return null;
        }

        $combination = strtoupper(trim($student->combination));
        
        // Map common combination abbreviations to full names
        $combinationMap = [
            'PCM' => ['Physics', 'Chemistry', 'Mathematics'],
            'PCB' => ['Physics', 'Chemistry', 'Biology'],
            'BCM' => ['Biology', 'Chemistry', 'Mathematics'],
            'HEG' => ['History', 'Economics', 'Geography'],
            'HGL' => ['History', 'Geography', 'Luganda'],
            'MEG' => ['Mathematics', 'Economics', 'Geography'],
            'PCM/ICT' => ['Physics', 'Chemistry', 'Mathematics', 'Information and Communication Technology'],
            'PCB/ICT' => ['Physics', 'Chemistry', 'Biology', 'Information and Communication Technology'],
        ];

        $subjects = [];
        
        // Check if it's a known combination
        if (isset($combinationMap[$combination])) {
            $subjects = $combinationMap[$combination];
        } else {
            // Try to parse individual subjects from the combination string
            $parts = preg_split('/[\/,\s]+/', $combination);
            foreach ($parts as $part) {
                $part = trim($part);
                if (isset($combinationMap[$part])) {
                    $subjects = array_merge($subjects, $combinationMap[$part]);
                }
            }
        }

        if (empty($subjects)) {
            return null;
        }

        // Get subject IDs from database
        $subjectIds = \App\Models\Subject::whereIn('name', $subjects)->pluck('id')->toArray();
        
        return $subjectIds;
    }
} 