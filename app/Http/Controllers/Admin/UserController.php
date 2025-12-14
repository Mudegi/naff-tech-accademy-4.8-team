<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Regular admins can view users, but only super admin can create/edit/delete
        $query = User::query();

        // Search by name, email, or phone number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        // Filter by account type
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 10);
        $users = $query->orderByDesc('id')->paginate($perPage);
        $users->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can create users.');
        }
        $schools = \App\Models\School::where('status', 'active')->orderBy('name')->get();
        $selectedSchoolId = $request->get('school_id');
        return view('admin.users.create', compact('schools', 'selectedSchoolId'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can create users.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|string',
            'phone_number' => 'nullable|string|max:20|unique:users',
            'school_id' => 'nullable|exists:schools,id',
            'is_active' => 'boolean',
        ]);
        if (empty($request->email) && empty($request->phone_number)) {
            return back()->withErrors(['email' => 'Either email or phone number is required.', 'phone_number' => 'Either email or phone number is required.'])->withInput();
        }
        $validated['password'] = bcrypt($validated['password']);
        
        // Set school_id for school staff roles
        $schoolStaffRoles = ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'student'];
        if (in_array($validated['account_type'], $schoolStaffRoles) && $request->filled('school_id')) {
            $validated['school_id'] = $request->school_id;
        } elseif (!in_array($validated['account_type'], $schoolStaffRoles)) {
            // Remove school_id for non-school roles
            unset($validated['school_id']);
        }
        
        $user = User::create($validated);
        
        // Auto-create parent account for students
        if ($request->account_type === 'student') {
            $this->autoCreateParentAccount($user);
        }
        
        // If teacher, sync subjects and classes
        if ($request->account_type === 'teacher') {
            if ($request->has('subjects')) {
                $user->subjects()->sync($request->input('subjects', []));
            }
            if ($request->has('classes')) {
                $user->classes()->sync($request->input('classes', []));
            }
        }
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can edit users.');
        }
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can update users.');
        }
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'account_type' => 'required|string',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . $id,
            'is_active' => 'boolean',
        ]);
        if (empty($request->email) && empty($request->phone_number)) {
            return back()->withErrors(['email' => 'Either email or phone number is required.', 'phone_number' => 'Either email or phone number is required.'])->withInput();
        }
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        $user->update($validated);
        // If teacher, sync subjects and classes
        if ($request->account_type === 'teacher') {
            $user->subjects()->sync($request->input('subjects', []));
            $user->classes()->sync($request->input('classes', []));
        } else {
            // If not teacher, remove all subject and class assignments
            $user->subjects()->detach();
            $user->classes()->detach();
        }
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can delete users.');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function impersonate(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can impersonate users.');
        }
        // Store the admin's ID in the session
        session()->put('impersonator_id', Auth::id());
        
        // Regenerate session to prevent session fixation
        session()->regenerate();
        
        // Log in as the impersonated user
        Auth::login($user);
        
        // Store user type in session (same as AuthController)
        session()->put('user_type', $user->account_type);
        
        // Handle redirect based on account type, matching AuthController behavior
        if ($user->account_type === 'student') {
            return redirect()->route('student.dashboard')
                ->with('success', 'You are now impersonating ' . $user->name);
        } elseif ($user->account_type === 'parent') {
            return redirect()->route('student.dashboard')
                ->with('success', 'You are now impersonating ' . $user->name);
        } elseif ($user->account_type === 'teacher') {
            return redirect()->route('student.dashboard')
                ->with('success', 'You are now impersonating ' . $user->name);
        }
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'You are now impersonating ' . $user->name);
    }

    public function stopImpersonating()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'No impersonation session found.');
        }

        // Get the admin user
        $admin = User::find(session()->get('impersonator_id'));
        
        if (!$admin) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin account not found.');
        }

        // Regenerate session to prevent session fixation
        session()->regenerate();
        
        // Log back in as admin
        Auth::login($admin);
        
        // Remove impersonator ID and user_type from session
        session()->forget('impersonator_id');
        session()->forget('user_type');
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Stopped impersonating user.');
    }

    public function updateStatus(Request $request, User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can change user status.');
        }
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.show', $user)
            ->with('success', "User has been {$status} successfully.");
    }

    /**
     * Auto-create parent account for a student.
     */
    private function autoCreateParentAccount($student)
    {
        // Generate parent email based on student info
        $parentEmail = $this->generateParentEmail($student);
        
        // Default password: student's phone number or "parent123"
        $defaultPassword = $student->phone_number ? $student->phone_number : 'parent123';
        
        // Create parent account
        $parent = User::create([
            'name' => $student->name . ' (Parent)',
            'email' => $parentEmail,
            'phone_number' => $student->phone_number ? '+parent_' . $student->phone_number : null,
            'password' => bcrypt($defaultPassword),
            'account_type' => 'parent',
            'school_id' => $student->school_id,
            'is_active' => true,
        ]);

        // Link parent to student
        DB::table('parent_student')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'parent',
            'is_primary' => true,
            'receive_notifications' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $parent;
    }

    /**
     * Generate unique parent email.
     */
    private function generateParentEmail($student)
    {
        // Try to use student email pattern
        if ($student->email) {
            $emailParts = explode('@', $student->email);
            $baseEmail = $emailParts[0] . '_parent@' . $emailParts[1];
            
            // Check if email exists
            $counter = 1;
            $finalEmail = $baseEmail;
            while (User::where('email', $finalEmail)->exists()) {
                $finalEmail = $emailParts[0] . '_parent' . $counter . '@' . $emailParts[1];
                $counter++;
            }
            
            return $finalEmail;
        }
        
        // Fallback: use student ID
        $baseEmail = 'parent_student' . $student->id . '@school.local';
        $counter = 1;
        $finalEmail = $baseEmail;
        while (User::where('email', $finalEmail)->exists()) {
            $finalEmail = 'parent_student' . $student->id . '_' . $counter . '@school.local';
            $counter++;
        }
        
        return $finalEmail;
    }

    /**
     * Show student-parent accounts list.
     */
    public function studentParentList(Request $request)
    {
        $query = User::where('account_type', 'student');

        $user = Auth::user();
        
        // Filter by school for school admins
        if ($user->school_id) {
            // School admin - only show students from their school
            $query->where('school_id', $user->school_id);
        } elseif ($request->has('school_id') && $request->school_id) {
            // System admin with school filter selected
            $query->where('school_id', $request->school_id);
        }
        
        // Get all schools for system admin dropdown
        $schools = $user->school_id ? [] : School::orderBy('name')->get();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 50);
        $students = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Load parent information for each student
        foreach ($students as $student) {
            $student->parentLinks = DB::table('parent_student')
                ->join('users', 'parent_student.parent_id', '=', 'users.id')
                ->where('parent_student.student_id', $student->id)
                ->select(
                    'parent_student.*',
                    'users.name as parent_name',
                    'users.email as parent_email',
                    'users.phone_number as parent_phone'
                )
                ->get();
        }

        return view('admin.student-parent-list', compact('students', 'schools'));
    }

    /**
     * Generate parent accounts for students who don't have them.
     */
    public function generateMissingParentAccounts(Request $request)
    {
        // Find all students without parent accounts
        $query = User::where('account_type', 'student')
            ->whereDoesntHave('parents');

        $user = Auth::user();
        
        // Filter by school for school admins
        if ($user->school_id) {
            // School admin - only generate for students from their school
            $query->where('school_id', $user->school_id);
        } elseif ($request->has('school_id') && $request->school_id) {
            // System admin with school filter
            $query->where('school_id', $request->school_id);
        }

        $studentsWithoutParents = $query->get();

        $created = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($studentsWithoutParents as $student) {
                try {
                    $this->autoCreateParentAccount($student);
                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to create parent for {$student->name} (ID: {$student->id}): " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully created {$created} parent account(s) for existing students.";
            
            if (!empty($errors)) {
                session()->flash('generation_errors', $errors);
            }

            return redirect()->route('admin.users.student-parent-list')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.student-parent-list')
                ->with('error', 'Failed to generate parent accounts: ' . $e->getMessage());
        }
    }

    /**
     * Export student-parent list to CSV.
     */
    public function exportStudentParentList(Request $request)
    {
        $user = Auth::user();
        
        $query = User::where('account_type', 'student');
        
        // Filter by school for school admins
        if ($user->school_id) {
            $query->where('school_id', $user->school_id);
        } elseif ($request->has('school_id') && $request->school_id) {
            // System admin with school filter
            $query->where('school_id', $request->school_id);
        } elseif ($request->has('school_id') && $request->school_id) {
            // System admin with school filter
            $query->where('school_id', $request->school_id);
        }
        
        $students = $query->get();

        // Load parent information for each student
        foreach ($students as $student) {
            $student->parentLinks = DB::table('parent_student')
                ->join('users', 'parent_student.parent_id', '=', 'users.id')
                ->where('parent_student.student_id', $student->id)
                ->select(
                    'parent_student.*',
                    'users.name as parent_name',
                    'users.email as parent_email',
                    'users.phone_number as parent_phone'
                )
                ->get();
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student-parent-accounts-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Student Email',
                'Student Phone',
                'Parent ID',
                'Parent Name',
                'Parent Email',
                'Parent Phone',
                'Default Password',
                'Relationship',
                'Created Date'
            ]);
            
            foreach ($students as $student) {
                foreach ($student->parentLinks as $link) {
                    fputcsv($file, [
                        $student->id,
                        $student->name,
                        $student->email,
                        $student->phone_number,
                        $link->parent_id,
                        $link->parent_name,
                        $link->parent_email,
                        $link->parent_phone,
                        $student->phone_number ?: 'parent123',
                        $link->relationship,
                        $link->created_at
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 