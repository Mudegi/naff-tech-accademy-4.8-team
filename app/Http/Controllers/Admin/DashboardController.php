<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;
use App\Models\StudentAssignment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect school staff to appropriate dashboard
        // But exclude teachers/subject_teachers - they should use student dashboard
        if ($user->isSchoolStaff() && $user->school_id && !in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            if ($user->isDirectorOfStudies()) {
                return redirect()->route('admin.director-of-studies.dashboard');
            }
            return redirect()->route('admin.school.dashboard');
        }
        
        // If teacher or subject_teacher tries to access admin dashboard, redirect to teacher dashboard
        if (in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            return redirect()->route('teacher.dashboard');
        }

        // For admins (both super admin and regular admin), show dashboard
        // If admin has school_id, filter data by school, otherwise show all
        $schoolId = null;
        if ($user->account_type === 'admin' && $user->school_id) {
            $schoolId = $user->school_id;
        } elseif ($user->account_type === 'admin' && !$user->school_id) {
            // Super admin - check if they have school context set
            $schoolId = \Illuminate\Support\Facades\Session::get('admin_school_context');
        }

        // Build queries based on school context
        $baseUserQuery = function() use ($schoolId) {
            $q = User::query();
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
            return $q;
        };

        $baseSubjectQuery = function() use ($schoolId) {
            $q = Subject::query();
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
            return $q;
        };

        $baseTopicQuery = function() use ($schoolId) {
            $q = Topic::query();
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
            return $q;
        };

        $baseResourceQuery = function() use ($schoolId) {
            $q = Resource::query();
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
            return $q;
        };

        $baseAssignmentQuery = function() use ($schoolId) {
            $q = StudentAssignment::query();
            if ($schoolId) {
                $q->whereHas('student', function($subQ) use ($schoolId) {
                    $subQ->where('school_id', $schoolId);
                });
            }
            return $q;
        };

        $data = [
            'totalUsers' => $baseUserQuery()->count(),
            'totalStudents' => $baseUserQuery()->where('account_type', 'student')->count(),
            'totalInstructors' => $baseUserQuery()->where('account_type', 'instructor')->count(),
            'totalAdmins' => User::where('account_type', 'admin')
                ->when($schoolId, function($q) use ($schoolId) {
                    return $q->where('school_id', $schoolId);
                }, function($q) {
                    return $q->whereNull('school_id');
                })
                ->count(),
            'totalSubjects' => $baseSubjectQuery()->count(),
            'totalTopics' => $baseTopicQuery()->count(),
            'totalResources' => $baseResourceQuery()->count(),
            'totalStudentAssignments' => $baseAssignmentQuery()->count(),
            'totalTeacherAssignments' => $baseResourceQuery()->whereNotNull('assessment_tests_path')->count(),
            'submittedAssignments' => $baseAssignmentQuery()->where('status', 'submitted')->count(),
            'reviewedAssignments' => $baseAssignmentQuery()->where('status', 'reviewed')->count(),
            'gradedAssignments' => $baseAssignmentQuery()->where('status', 'graded')->count(),
            'contactMessages' => ContactMessage::latest()->take(10)->get(),
        ];

        return view('admin.dashboard', $data);
    }
} 