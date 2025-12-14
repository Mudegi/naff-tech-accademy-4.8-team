<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $query = Department::where('school_id', $school->id);

        // Search by name or code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $departments = $query->with(['headOfDepartment', 'teachers'])->latest()->paginate(15);

        return view('admin.school.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Get available Head of Departments (users who are HODs and don't have a department assigned yet, or current HODs)
        $availableHODs = User::where('school_id', $school->id)
            ->where('account_type', 'head_of_department')
            ->where(function($query) {
                $query->whereNull('department_id')
                      ->orWhereHas('managedDepartments');
            })
            ->get();

        return view('admin.school.departments.create', compact('availableHODs'));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'head_of_department_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify the HOD belongs to the same school
        if ($request->head_of_department_id) {
            $hod = User::where('id', $request->head_of_department_id)
                ->where('school_id', $school->id)
                ->where('account_type', 'head_of_department')
                ->first();

            if (!$hod) {
                return redirect()->back()
                    ->with('error', 'Invalid Head of Department selected.')
                    ->withInput();
            }
        }

        try {
            $department = Department::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'head_of_department_id' => $request->head_of_department_id,
                'is_active' => $request->has('is_active'),
            ]);

            // Update the HOD's department_id if assigned
            if ($request->head_of_department_id) {
                User::where('id', $request->head_of_department_id)
                    ->update(['department_id' => $department->id]);
            }

            return redirect()->route('admin.school.departments.index')
                ->with('success', 'Department created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a department
     */
    public function edit($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)->findOrFail($id);

        // Get available Head of Departments
        $availableHODs = User::where('school_id', $school->id)
            ->where('account_type', 'head_of_department')
            ->where(function($query) use ($department) {
                $query->whereNull('department_id')
                      ->orWhere('department_id', $department->id)
                      ->orWhereHas('managedDepartments', function($q) use ($department) {
                          $q->where('id', $department->id);
                      });
            })
            ->get();

        return view('admin.school.departments.edit', compact('department', 'availableHODs'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'head_of_department_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify the HOD belongs to the same school
        if ($request->head_of_department_id) {
            $hod = User::where('id', $request->head_of_department_id)
                ->where('school_id', $school->id)
                ->where('account_type', 'head_of_department')
                ->first();

            if (!$hod) {
                return redirect()->back()
                    ->with('error', 'Invalid Head of Department selected.')
                    ->withInput();
            }
        }

        try {
            $oldHodId = $department->head_of_department_id;

            $department->name = $request->name;
            $department->code = $request->code;
            $department->description = $request->description;
            $department->head_of_department_id = $request->head_of_department_id;
            $department->is_active = $request->has('is_active');
            $department->save();

            // Update old HOD's department_id if changed
            if ($oldHodId && $oldHodId != $request->head_of_department_id) {
                User::where('id', $oldHodId)
                    ->update(['department_id' => null]);
            }

            // Update new HOD's department_id
            if ($request->head_of_department_id) {
                User::where('id', $request->head_of_department_id)
                    ->update(['department_id' => $department->id]);
            }

            return redirect()->route('admin.school.departments.index')
                ->with('success', 'Department updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show department details with all teachers
     */
    public function show($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)
            ->with(['headOfDepartment', 'teachers', 'school'])
            ->findOrFail($id);

        // Get all teachers in this department
        $teachers = User::where('school_id', $school->id)
            ->where('department_id', $department->id)
            ->where('account_type', 'subject_teacher')
            ->latest()
            ->get();

        // Get available teachers (subject teachers not assigned to any department or assigned to this department)
        $availableTeachers = User::where('school_id', $school->id)
            ->where('account_type', 'subject_teacher')
            ->where(function($query) use ($department) {
                $query->whereNull('department_id')
                      ->orWhere('department_id', $department->id);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.school.departments.show', compact('department', 'teachers', 'availableTeachers'));
    }

    /**
     * Assign teachers to department
     */
    public function assignTeachers(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Verify all teachers belong to the same school and are subject teachers
            $teachers = User::where('school_id', $school->id)
                ->where('account_type', 'subject_teacher')
                ->whereIn('id', $request->teacher_ids)
                ->get();

            if ($teachers->count() !== count($request->teacher_ids)) {
                return redirect()->back()
                    ->with('error', 'Some selected teachers are invalid.');
            }

            // Assign teachers to department
            User::whereIn('id', $request->teacher_ids)
                ->update(['department_id' => $department->id]);

            return redirect()->route('admin.school.departments.show', $department->id)
                ->with('success', 'Teachers assigned to department successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign teachers: ' . $e->getMessage());
        }
    }

    /**
     * Remove teacher from department
     */
    public function removeTeacher($id, $teacherId)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)->findOrFail($id);

        $teacher = User::where('school_id', $school->id)
            ->where('department_id', $department->id)
            ->where('id', $teacherId)
            ->firstOrFail();

        try {
            $teacher->department_id = null;
            $teacher->save();

            return redirect()->route('admin.school.departments.show', $department->id)
                ->with('success', 'Teacher removed from department successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove teacher: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified department
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $department = Department::where('school_id', $school->id)->findOrFail($id);

        try {
            // Remove department_id from all users in this department
            User::where('department_id', $department->id)
                ->update(['department_id' => null]);

            $department->delete();

            return redirect()->route('admin.school.departments.index')
                ->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}
