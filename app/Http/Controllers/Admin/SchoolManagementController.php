<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SchoolManagementController extends Controller
{
    /**
     * Check if user is super admin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can manage schools.');
        }
    }

    /**
     * Display a listing of schools
     */
    public function index(Request $request)
    {
        $this->checkSuperAdmin();
        $query = School::with(['subscriptionPackage', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by subscription status
        if ($request->filled('subscription_status')) {
            if ($request->subscription_status === 'active') {
                $query->where(function($q) {
                    $q->whereNotNull('subscription_package_id')
                      ->whereNotNull('subscription_start_date')
                      ->whereNotNull('subscription_end_date')
                      ->where('subscription_end_date', '>=', now())
                      ->where('status', 'active');
                });
            } elseif ($request->subscription_status === 'inactive') {
                $query->where(function($q) {
                    $q->whereNull('subscription_package_id')
                      ->orWhereNull('subscription_start_date')
                      ->orWhereNull('subscription_end_date')
                      ->orWhere('subscription_end_date', '<', now())
                      ->orWhere('status', '!=', 'active');
                });
            }
        }

        $perPage = $request->get('per_page', 15);
        $schools = $query->latest()->paginate($perPage);
        $schools->appends($request->query());

        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school
     */
    public function create()
    {
        $this->checkSuperAdmin();
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('name')->get();
        return view('admin.schools.create', compact('packages'));
    }

    /**
     * Store a newly created school
     */
    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:schools,name',
            'email' => 'required|email|unique:schools,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_package_id' => 'nullable|exists:subscription_packages,id',
            // Optional school admin creation
            'create_admin' => 'boolean',
            'admin_name' => 'required_if:create_admin,1|string|max:255',
            'admin_email' => 'required_if:create_admin,1|email|unique:users,email',
            'admin_phone' => 'nullable|string|max:20|unique:users,phone_number',
            'admin_password' => 'required_if:create_admin,1|string|min:8|confirmed',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        // Set school status based on subscription
        if (!isset($validated['subscription_package_id']) || !$validated['subscription_package_id']) {
            $validated['status'] = 'inactive'; // Schools without subscription are inactive
        } elseif ($validated['status'] !== 'suspended') {
            $validated['status'] = 'active'; // Schools with subscription are active (unless suspended)
        }

        $school = School::create($validated);

        // Create school admin if requested
        if ($request->has('create_admin') && $request->create_admin) {
            \App\Models\User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'phone_number' => $validated['admin_phone'] ?? null,
                'password' => bcrypt($validated['admin_password']),
                'account_type' => 'school_admin',
                'school_id' => $school->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('admin.schools.show', $school->id)
            ->with('success', 'School created successfully.' . ($request->has('create_admin') && $request->create_admin ? ' School admin user has also been created.' : ''));
    }

    /**
     * Display the specified school
     */
    public function show(School $school)
    {
        $this->checkSuperAdmin();
        $school->load(['users', 'students', 'subjects', 'classes', 'subscriptionPackage', 'subscriptions']);
        
        $stats = [
            'total_staff' => $school->users()->whereIn('account_type', ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher'])->count(),
            'total_students' => $school->students()->count(),
            'total_subjects' => $school->subjects()->count(),
            'total_classes' => $school->classes()->count(),
        ];

        // Get pending approval subscriptions
        $pendingApprovals = SchoolSubscription::where('school_id', $school->id)
            ->where('payment_status', 'pending_approval')
            ->with('subscriptionPackage')
            ->latest()
            ->get();

        return view('admin.schools.show', compact('school', 'stats', 'pendingApprovals'));
    }

    /**
     * Show the form for editing the specified school
     */
    public function edit(School $school)
    {
        $this->checkSuperAdmin();
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('name')->get();
        return view('admin.schools.edit', compact('school', 'packages'));
    }

    /**
     * Update the specified school
     */
    public function update(Request $request, School $school)
    {
        $this->checkSuperAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:schools,name,' . $school->id,
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_package_id' => 'nullable|exists:subscription_packages,id',
        ]);

        // Update slug if name changed
        if ($school->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo) {
                Storage::delete('public/' . $school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school->update($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'School updated successfully.');
    }

    /**
     * Remove the specified school
     */
    public function destroy(School $school)
    {
        $this->checkSuperAdmin();
        
        // Deactivate all users associated with this school
        $school->users()->update(['is_active' => false]);
        
        // Delete logo if exists
        if ($school->logo) {
            Storage::delete('public/' . $school->logo);
        }

        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'School deleted successfully. All associated accounts have been deactivated.');
    }
}

