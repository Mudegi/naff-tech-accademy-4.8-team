<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\SubscriptionPackage;
use App\Models\SchoolSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SchoolController extends Controller
{
    /**
     * Show school registration form
     */
    public function showRegistrationForm()
    {
        // Get active subscription packages for schools
        $packages = SubscriptionPackage::where('is_active', true)
            ->where('subscription_type', '!=', 'topic') // Schools typically don't use topic-based subscriptions
            ->orderBy('price')
            ->get();
        
        return view('school.register', compact('packages'));
    }

    /**
     * Register a new school
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'school_email' => 'required|email|unique:schools,email',
            'school_phone' => 'nullable|string|max:20',
            'school_address' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_phone' => 'nullable|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get subscription package
            $package = SubscriptionPackage::findOrFail($request->subscription_package_id);
            
            // Create school (initially inactive until payment)
            $school = School::create([
                'name' => $request->school_name,
                'slug' => Str::slug($request->school_name),
                'email' => $request->school_email,
                'phone_number' => $request->school_phone,
                'address' => $request->school_address,
                'status' => 'inactive', // Will be activated after payment
                'subscription_package_id' => $package->id,
            ]);

            // Create school admin user
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'phone_number' => $request->admin_phone,
                'password' => Hash::make($request->password),
                'account_type' => 'school_admin',
                'school_id' => $school->id,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Create pending subscription
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($package->duration_days);

            $subscription = SchoolSubscription::create([
                'school_id' => $school->id,
                'subscription_package_id' => $package->id,
                'amount_paid' => $package->price,
                'payment_status' => 'pending',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false,
            ]);

            // Redirect to payment page
            return redirect()->route('school.subscription.payment', $subscription->id)
                ->with('success', 'School account created! Please complete payment to activate your subscription.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
