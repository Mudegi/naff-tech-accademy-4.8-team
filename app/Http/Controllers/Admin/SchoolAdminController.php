<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolAdminController extends Controller
{
    /**
     * Display the school admin dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Get statistics
        $stats = [
            'total_staff' => User::where('school_id', $school->id)
                ->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher'])
                ->count(),
            'total_students' => Student::where('school_id', $school->id)->count(),
            'total_subjects' => Subject::where('school_id', $school->id)->count(),
            'total_classes' => SchoolClass::where('school_id', $school->id)->count(),
            'directors' => User::where('school_id', $school->id)
                ->where('account_type', 'director_of_studies')
                ->count(),
            'heads_of_department' => User::where('school_id', $school->id)
                ->where('account_type', 'head_of_department')
                ->count(),
            'teachers' => User::where('school_id', $school->id)
                ->where('account_type', 'subject_teacher')
                ->count(),
        ];

        // Recent staff members
        $recentStaff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['director_of_studies', 'head_of_department', 'subject_teacher'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.school.dashboard', compact('school', 'stats', 'recentStaff'));
    }
}
