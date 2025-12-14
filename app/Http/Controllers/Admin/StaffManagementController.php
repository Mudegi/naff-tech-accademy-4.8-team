<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffManagementController extends Controller
{
    /**
     * Display a listing of staff members
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $query = User::where('school_id', $school->id)
            ->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'teacher']);

        // Filter by account type if provided
        if ($request->has('account_type') && $request->account_type) {
            $query->where('account_type', $request->account_type);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $staff = $query->latest()->paginate(15);

        return view('admin.school.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new staff member
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Determine which roles the current user can create
        $availableRoles = $this->getAvailableRoles($user);

        // Get departments for department assignment (school-specific + system-wide)
        $departments = Department::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->where('school_id', $school->id)
                      ->orWhereNull('school_id'); // Include system-wide departments
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get classes for class assignment (for teachers)
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->where('school_id', $school->id)
                      ->orWhere('is_system_class', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get subjects for subject teachers (school-specific + system-wide)
        $subjects = \App\Models\Subject::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id')
                      ->orWhere('school_id', $school->id);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get pre-selected department from query parameter
        $selectedDepartmentId = $request->get('department_id');

        return view('admin.school.staff.create', compact('availableRoles', 'departments', 'classes', 'subjects', 'selectedDepartmentId'));
    }

    /**
     * Store a newly created staff member
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $availableRoles = $this->getAvailableRoles($user);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'account_type' => 'required|in:' . implode(',', array_keys($availableRoles)),
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate department belongs to school or is system-wide and is appropriate for the role
        $departmentId = null;
        if ($request->department_id) {
            $department = Department::withoutGlobalScope('school')
                ->where('id', $request->department_id)
                ->where(function($query) use ($school) {
                    $query->where('school_id', $school->id)
                          ->orWhereNull('school_id'); // Include system-wide departments
                })
                ->first();
            
            if (!$department) {
                return redirect()->back()
                    ->with('error', 'Invalid department selected.')
                    ->withInput();
            }

            // Only HODs and Subject Teachers can be assigned to departments
            if (in_array($request->account_type, ['head_of_department', 'subject_teacher'])) {
                $departmentId = $request->department_id;
            }
        }

        try {
            $staff = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'account_type' => $request->account_type,
                'school_id' => $school->id,
                'department_id' => $departmentId,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Assign classes if provided (for teachers, subject teachers, HODs)
            if ($request->has('classes') && in_array($request->account_type, ['teacher', 'subject_teacher', 'head_of_department'])) {
                $staff->classes()->sync($request->classes);
            }

            // Assign subjects if provided (for subject teachers and HODs)
            if ($request->has('subjects') && in_array($request->account_type, ['subject_teacher', 'head_of_department'])) {
                $staff->subjects()->sync($request->subjects);
            }

            // Store credentials in session to display after redirect
            $request->session()->flash('new_staff_credentials', [
                'name' => $staff->name,
                'email' => $staff->email,
                'phone_number' => $staff->phone_number,
                'password' => $request->password, // Store plain password temporarily
                'account_type' => $staff->account_type,
            ]);

            return redirect()->route('admin.school.staff.index')
                ->with('success', 'Staff member created successfully! Please note the login credentials below.')
                ->with('show_credentials', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create staff member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a staff member
     */
    public function edit($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $staff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'teacher'])
            ->findOrFail($id);

        // Check if user can manage this staff member
        if (!$user->canManageUser($staff)) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You do not have permission to edit this staff member.');
        }

        $availableRoles = $this->getAvailableRoles($user);

        // Get departments for department assignment (school-specific + system-wide)
        $departments = Department::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->where('school_id', $school->id)
                      ->orWhereNull('school_id'); // Include system-wide departments
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get classes for class assignment (for teachers)
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->where('school_id', $school->id)
                      ->orWhere('is_system_class', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get subjects for subject teachers (school-specific + system-wide)
        $subjects = \App\Models\Subject::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id')
                      ->orWhere('school_id', $school->id);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get currently assigned classes and subjects
        $assignedClasses = \DB::table('class_user')
            ->where('user_id', $staff->id)
            ->pluck('class_id')
            ->toArray();

        $assignedSubjects = \DB::table('subject_user')
            ->where('user_id', $staff->id)
            ->pluck('subject_id')
            ->toArray();

        return view('admin.school.staff.edit', compact('staff', 'availableRoles', 'departments', 'classes', 'subjects', 'assignedClasses', 'assignedSubjects'));
    }

    /**
     * Update the specified staff member
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $staff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'teacher'])
            ->findOrFail($id);

        // Check if user can manage this staff member
        if (!$user->canManageUser($staff)) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You do not have permission to update this staff member.');
        }

        $availableRoles = $this->getAvailableRoles($user);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . $id,
            'account_type' => 'required|in:' . implode(',', array_keys($availableRoles)),
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'department_id' => 'nullable|exists:departments,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate department belongs to school (or is system-wide) and is appropriate for the role
        $departmentId = null;
        if ($request->department_id) {
            $department = Department::withoutGlobalScope('school')
                ->where('id', $request->department_id)
                ->where(function($query) use ($school) {
                    $query->where('school_id', $school->id)
                          ->orWhereNull('school_id');
                })
                ->first();
            
            if (!$department) {
                return redirect()->back()
                    ->with('error', 'Invalid department selected.')
                    ->withInput();
            }

            // Only HODs and Subject Teachers can be assigned to departments
            if (in_array($request->account_type, ['head_of_department', 'subject_teacher'])) {
                $departmentId = $request->department_id;
            }
        }

        try {
            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->phone_number = $request->phone_number;
            $staff->account_type = $request->account_type;
            $staff->department_id = $departmentId;
            $staff->is_active = $request->has('is_active');

            if ($request->filled('password')) {
                $staff->password = Hash::make($request->password);
                
                // Store new password in session to display if requested
                if ($request->has('show_password')) {
                    $request->session()->flash('updated_staff_credentials', [
                        'name' => $staff->name,
                        'email' => $staff->email,
                        'password' => $request->password,
                    ]);
                }
            }

            $staff->save();

            // Sync classes for teachers/subject_teachers/HODs
            if (in_array($request->account_type, ['teacher', 'subject_teacher', 'head_of_department'])) {
                $classes = $request->input('classes', []);
                $staff->classes()->sync($classes);
            } else {
                // Remove class assignments if changing to non-teaching role
                $staff->classes()->sync([]);
            }

            // Sync subjects for subject_teachers/HODs
            if (in_array($request->account_type, ['subject_teacher', 'head_of_department'])) {
                $subjects = $request->input('subjects', []);
                $staff->subjects()->sync($subjects);
            } else {
                // Remove subject assignments if changing to non-subject-teaching role
                $staff->subjects()->sync([]);
            }

            $message = 'Staff member updated successfully!';
            if ($request->filled('password') && $request->has('show_password')) {
                $message .= ' New password displayed below.';
            }

            return redirect()->route('admin.school.staff.index')
                ->with('success', $message)
                ->with('show_updated_credentials', $request->filled('password') && $request->has('show_password'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update staff member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified staff member
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $staff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'teacher'])
            ->findOrFail($id);

        // Check if user can manage this staff member
        if (!$user->canManageUser($staff)) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You do not have permission to delete this staff member.');
        }

        // Prevent deleting yourself
        if ($staff->id === $user->id) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            $staff->delete();

            return redirect()->route('admin.school.staff.index')
                ->with('success', 'Staff member deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete staff member: ' . $e->getMessage());
        }
    }

    /**
     * Get available roles based on current user's role
     */
    private function getAvailableRoles(User $user)
    {
        $roles = [
            'director_of_studies' => 'Director of Studies',
            'head_of_department' => 'Head of Department',
            'subject_teacher' => 'Subject Teacher',
            'teacher' => 'Teacher',
        ];

        // School Admin can create all roles
        if ($user->isSchoolAdmin()) {
            return $roles;
        }

        // Director of Studies can create HODs and Teachers
        if ($user->isDirectorOfStudies()) {
            return [
                'head_of_department' => 'Head of Department',
                'subject_teacher' => 'Subject Teacher',
                'teacher' => 'Teacher',
            ];
        }

        // Head of Department can only create Teachers
        if ($user->isHeadOfDepartment()) {
            return [
                'subject_teacher' => 'Subject Teacher',
                'teacher' => 'Teacher',
            ];
        }

        return [];
    }

    /**
     * Show the form for assigning classes to a teacher
     */
    public function assignClasses($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is School Admin or Director of Studies
        if (!$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can assign classes.');
        }

        $staff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['teacher', 'subject_teacher'])
            ->findOrFail($id);

        // Check if user can manage this staff member
        if (!$user->canManageUser($staff)) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You do not have permission to assign classes to this staff member.');
        }

        // Get all active classes for this school
        $classes = SchoolClass::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get currently assigned classes
        $assignedClassIds = $staff->classes()->pluck('classes.id')->toArray();

        return view('admin.school.staff.assign-classes', compact('staff', 'classes', 'assignedClassIds'));
    }

    /**
     * Update class assignments for a teacher
     */
    public function updateClasses(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is School Admin or Director of Studies
        if (!$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can assign classes.');
        }

        $staff = User::where('school_id', $school->id)
            ->whereIn('account_type', ['teacher', 'subject_teacher'])
            ->findOrFail($id);

        // Check if user can manage this staff member
        if (!$user->canManageUser($staff)) {
            return redirect()->route('admin.school.staff.index')
                ->with('error', 'You do not have permission to assign classes to this staff member.');
        }

        $validator = Validator::make($request->all(), [
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate all classes belong to the school
        if ($request->has('classes') && !empty($request->classes)) {
            $classIds = $request->classes;
            $validClasses = SchoolClass::where('school_id', $school->id)
                ->whereIn('id', $classIds)
                ->pluck('id')
                ->toArray();

            if (count($validClasses) !== count($classIds)) {
                return redirect()->back()
                    ->with('error', 'Some selected classes are invalid or do not belong to your school.')
                    ->withInput();
            }

            // Sync classes
            $staff->classes()->sync($validClasses);
        } else {
            // Remove all class assignments
            $staff->classes()->detach();
        }

        return redirect()->route('admin.school.staff.index')
            ->with('success', 'Class assignments updated successfully!');
    }
}
