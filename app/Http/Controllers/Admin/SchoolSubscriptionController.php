<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPackage;
use App\Models\FlutterwaveSetting;
use App\Models\EasypayConfiguration;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SchoolSubscriptionController extends Controller
{
    /**
     * Show all pending subscription approvals (Super Admin Only)
     */
    public function pendingApprovals()
    {
        $user = Auth::user();

        // Only super admin can access
        if ($user->account_type !== 'admin' || $user->school_id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $pendingApprovals = SchoolSubscription::with(['school', 'subscriptionPackage'])
            ->where('payment_status', 'pending_approval')
            ->latest()
            ->paginate(20);

        return view('admin.school-subscriptions.pending-approvals', compact('pendingApprovals'));
    }

    /**
     * Reject a pending payment
     */
    public function rejectPayment($id)
    {
        try {
            $user = Auth::user();

            // Only super admin can reject payments
            if ($user->account_type !== 'admin' || $user->school_id) {
                \Log::warning('Unauthorized payment rejection attempt', ['user_id' => $user->id, 'user_type' => $user->account_type, 'school_id' => $user->school_id]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to perform this action.');
            }

            $subscription = SchoolSubscription::findOrFail($id);
            \Log::info('Processing payment rejection', ['subscription_id' => $id, 'status' => $subscription->payment_status]);

            if ($subscription->payment_status !== 'pending_approval') {
                \Log::warning('Attempted to reject non-pending subscription', ['subscription_id' => $id, 'status' => $subscription->payment_status]);
                return redirect()->back()
                    ->with('error', 'This subscription is not pending approval.');
            }

            $subscription->update([
                'payment_status' => 'rejected',
                'is_active' => false,
            ]);

            \Log::info('Payment rejected successfully', ['subscription_id' => $id]);

            return redirect()->back()
                ->with('success', 'Payment rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Payment rejection failed', ['subscription_id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }
    /**
     * Display subscription management page
     */
    public function index()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only school admin can access
        if (!$user->isSchoolAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only school administrators can manage subscriptions.');
        }

        $subscriptions = SchoolSubscription::where('school_id', $school->id)
            ->with('subscriptionPackage')
            ->latest()
            ->paginate(15);

        $activeSubscription = $school->activeSubscription;

        return view('admin.school.subscriptions.index', compact('subscriptions', 'activeSubscription', 'school'));
    }

    /**
     * Show form to purchase new subscription
     */
    public function create()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only school admin can access
        if (!$user->isSchoolAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only school administrators can purchase subscriptions.');
        }

        // Get available subscription packages - only 3m UGX package for schools
        $packages = SubscriptionPackage::where('is_active', true)
            ->where('price', 3000000)
            ->orderBy('price')
            ->get();

        return view('admin.school.subscriptions.create', compact('packages', 'school'));
    }

    /**
     * Store new subscription (create pending subscription)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only school admin can access
        if (!$user->isSchoolAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only school administrators can purchase subscriptions.');
        }

        $validator = Validator::make($request->all(), [
            'subscription_package_id' => 'required|exists:subscription_packages,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $package = SubscriptionPackage::findOrFail($request->subscription_package_id);
            
            // Ensure only 3m UGX package can be selected for schools
            if ($package->price != 3000000) {
                return redirect()->back()
                    ->with('error', 'Only the 3m UGX package is available for school subscriptions.')
                    ->withInput();
            }
            
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

            return redirect()->route('admin.school.subscriptions.payment', $subscription->id)
                ->with('success', 'Subscription created! Please complete payment.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show payment page for subscription
     */
    public function showPayment($id)
    {
        $subscription = SchoolSubscription::with('subscriptionPackage', 'school')
            ->findOrFail($id);

        // Check if user is authenticated and belongs to the school
        $user = Auth::user();
        if ($user && $user->school_id !== $subscription->school_id) {
            return redirect()->route('login')
                ->with('error', 'You do not have access to this subscription.');
        }

        if ($subscription->payment_status === 'completed') {
            if ($user) {
                return redirect()->route('admin.school.subscriptions.index')
                    ->with('info', 'This subscription has already been paid.');
            } else {
                return redirect()->route('login')
                    ->with('info', 'This subscription has already been paid. Please login to access your account.');
            }
        }

        if ($subscription->payment_status === 'pending_approval') {
            return redirect()->route('admin.school.subscriptions.index')
                ->with('info', 'Your payment is pending admin approval. You will be notified once it is approved.');
        }

        $companySettings = CompanySetting::first();

        return view('admin.school.subscriptions.payment', compact('subscription', 'companySettings'));
    }

    /**
     * Process payment callback (manual or payment gateway)
     */
    public function processPayment(Request $request, $id)
    {
        $subscription = SchoolSubscription::with('school')
            ->findOrFail($id);

        // Check if user is authenticated and belongs to the school
        $user = Auth::user();
        if ($user && $user->school_id !== $subscription->school_id) {
            return redirect()->route('login')
                ->with('error', 'You do not have access to this subscription.');
        }

        $school = $subscription->school;

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:manual,flutterwave,easypay',
            'phone' => 'required_if:payment_method,easypay|string|max:20',
            'transaction_id' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $paymentMethod = $request->payment_method;

            // Handle manual payments - require admin approval for security
            if ($paymentMethod === 'manual') {
                $subscription->update([
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $request->transaction_id,
                    'payment_reference' => $request->payment_reference,
                    'payment_status' => 'pending_approval',
                    'notes' => $request->notes,
                ]);

                return redirect()->route('admin.school.subscriptions.index')
                    ->with('info', 'Payment details submitted successfully. Your payment is pending admin approval. You will be notified once it is approved.');
            }

            // Handle online payments - redirect to payment gateway
            if ($paymentMethod === 'flutterwave') {
                return $this->initiateFlutterwavePayment($subscription, $user);
            }

            if ($paymentMethod === 'easypay') {
                // Validate phone number for Easypay
                if (!$request->phone) {
                    return redirect()->back()
                        ->with('error', 'Phone number is required for Easypay payments.')
                        ->withInput();
                }

                return $this->initiateEasypayPayment($subscription, $user, $request);
            }

            return redirect()->back()
                ->with('error', 'Invalid payment method selected.')
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Payment processing error', ['error' => $e->getMessage(), 'subscription_id' => $id]);
            return redirect()->back()
                ->with('error', 'Failed to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Initiate Flutterwave payment
     */
    private function initiateFlutterwavePayment(SchoolSubscription $subscription, $user)
    {
        $flutterwave = FlutterwaveSetting::orderByDesc('id')->first();
        if (!$flutterwave) {
            return redirect()->back()
                ->with('error', 'Flutterwave configuration not found. Please contact support.');
        }

        $appName = config('app.name');
        $reference = 'school_sub_' . $subscription->id . '_' . uniqid();
        $amount = $subscription->amount_paid;
        $currency = 'UGX';
        $phone = $user ? $user->phone_number : $subscription->school->phone_number;
        $email = $user ? $user->email : $subscription->school->email;
        $name = $user ? $user->name : $subscription->school->name;

        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => route('admin.school.subscriptions.flutterwave-callback', $subscription->id),
            'payment_options' => 'mobilemoneyuganda,card',
            'customer' => [
                'email' => $email,
                'phonenumber' => $phone,
                'name' => $name,
            ],
            'customizations' => [
                'title' => 'School Subscription Payment',
                'description' => 'Payment for ' . $subscription->subscriptionPackage->name . ' subscription',
            ],
        ];

        Log::info('Flutterwave School Payment Request', ['payload' => $payload]);

        $response = Http::withToken($flutterwave->secret_key)
            ->post('https://api.flutterwave.com/v3/payments', $payload);

        $result = $response->json();
        Log::info('Flutterwave School Payment Response', ['response' => $result]);

        if (isset($result['status']) && $result['status'] === 'success' && isset($result['data']['link'])) {
            // Update subscription with transaction reference
            $subscription->update([
                'payment_method' => 'flutterwave',
                'transaction_id' => $reference,
                'payment_status' => 'pending',
            ]);

            return redirect()->away($result['data']['link']);
        } else {
            $error = $result['message'] ?? 'Flutterwave payment initiation failed. Please try again.';
            Log::error('Flutterwave payment failed', ['error' => $error, 'result' => $result]);
            return redirect()->back()
                ->with('error', $error)
                ->withInput();
        }
    }

    /**
     * Initiate Easypay payment
     */
    private function initiateEasypayPayment(SchoolSubscription $subscription, $user, Request $request)
    {
        $easypay = EasypayConfiguration::where('is_active', true)->first();
        if (!$easypay) {
            return redirect()->back()
                ->with('error', 'Easypay configuration not found. Please contact support.');
        }

        $phone = $request->input('phone') ?? ($user ? $user->phone_number : $subscription->school->phone_number);

        // Format phone number for Easypay (should start with +256)
        if (!str_starts_with($phone, '+256')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+256' . substr($phone, 1);
            } elseif (str_starts_with($phone, '256')) {
                $phone = '+' . $phone;
            } else {
                $phone = '+256' . $phone;
            }
        }

        $appName = config('app.name');
        $reference = 'school_sub_' . $subscription->id . '_' . uniqid();
        $amount = $subscription->amount_paid;
        $currency = 'UGX';
        $reason = 'Payment for ' . $subscription->subscriptionPackage->name . ' subscription';

        $payload = [
            'username' => $easypay->client_id,
            'password' => $easypay->secret,
            'action' => 'mmdeposit',
            'amount' => $amount,
            'currency' => $currency,
            'phone' => $phone,
            'reference' => $reference,
            'reason' => $reason,
        ];

        Log::info('Easypay School Payment Request', ['payload' => $payload]);

        try {
            $response = Http::timeout(30)->post('https://www.easypay.co.ug/api/', $payload);

            if ($response->failed()) {
                Log::error('Easypay API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect()->back()
                    ->with('error', 'Unable to connect to Easypay. Please try again later.')
                    ->withInput();
            }

            $result = $response->json();
            Log::info('Easypay School Payment Response', ['response' => $result]);

            if (isset($result['success']) && $result['success'] == 1) {
                // Update subscription with transaction reference
                $subscription->update([
                    'payment_method' => 'easypay',
                    'transaction_id' => $result['data']['transactionId'] ?? $reference,
                    'payment_status' => 'pending',
                ]);

                return redirect()->route('admin.school.subscriptions.index')
                    ->with('success', 'Payment request sent to your phone. Please check your phone and complete the payment. Your subscription will be activated automatically once payment is confirmed.');
            } else {
                $error = $result['errormsg'] ?? 'Easypay payment initiation failed. Please check your phone number and try again.';
                Log::error('Easypay payment failed', ['error' => $error, 'result' => $result]);
                return redirect()->back()
                    ->with('error', $error)
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Easypay payment exception', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Payment service temporarily unavailable. Please try again later.')
                ->withInput();
        }
    }

    /**
     * Handle Flutterwave callback
     */
    public function flutterwaveCallback(Request $request, $id)
    {
        $subscription = SchoolSubscription::with('school')->findOrFail($id);
        $txRef = $request->input('tx_ref');
        $status = $request->input('status');

        Log::info('Flutterwave School Callback', ['request' => $request->all(), 'subscription_id' => $id]);

        if ($status === 'successful' && $txRef === $subscription->transaction_id) {
            // Verify payment with Flutterwave
            $flutterwave = FlutterwaveSetting::orderByDesc('id')->first();
            if ($flutterwave) {
                $response = Http::withToken($flutterwave->secret_key)
                    ->get("https://api.flutterwave.com/v3/transactions/{$request->input('transaction_id')}/verify");

                $verification = $response->json();
                Log::info('Flutterwave Verification', ['verification' => $verification]);

                if (isset($verification['status']) && $verification['status'] === 'success' 
                    && isset($verification['data']['status']) && $verification['data']['status'] === 'successful') {
                    
                    // Payment verified - activate subscription
                    $subscription->update([
                        'payment_status' => 'completed',
                    ]);

                    $subscription->school->activateSubscription($subscription);

                    return redirect()->route('admin.school.subscriptions.index')
                        ->with('success', 'Payment completed successfully! Your subscription is now active.');
                }
            }
        }

        return redirect()->route('admin.school.subscriptions.index')
            ->with('error', 'Payment verification failed. Please contact support if payment was deducted.');
    }

    /**
     * Handle Easypay callback
     */
    public function easypayCallback(Request $request)
    {
        Log::info('Easypay School IPN Callback:', $request->all());
        
        $transactionId = $request->input('transactionId') ?? $request->input('transaction_id');
        $status = $request->input('status') ?? $request->input('success');

        if ($transactionId) {
            $subscription = SchoolSubscription::where('transaction_id', $transactionId)->first();
            
            if ($subscription) {
                if ($status == 1 || $status === 'success') {
                    // Payment successful - activate subscription
                    $subscription->update([
                        'payment_status' => 'completed',
                    ]);

                    $subscription->school->activateSubscription($subscription);
                    
                    Log::info('Easypay School Payment Completed', ['subscription_id' => $subscription->id]);
                } else {
                    $subscription->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Approve pending manual payment (Admin only)
     */
    public function approvePayment($id)
    {
        try {
            $user = Auth::user();

            // Only super admin can approve payments
            if ($user->account_type !== 'admin' || $user->school_id) {
                \Log::warning('Unauthorized payment approval attempt', ['user_id' => $user->id, 'user_type' => $user->account_type, 'school_id' => $user->school_id]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to perform this action.');
            }

            $subscription = SchoolSubscription::with('school')->findOrFail($id);
            \Log::info('Processing payment approval', ['subscription_id' => $id, 'status' => $subscription->payment_status]);

            if ($subscription->payment_status !== 'pending_approval') {
                \Log::warning('Attempted to approve non-pending subscription', ['subscription_id' => $id, 'status' => $subscription->payment_status]);
                return redirect()->back()
                    ->with('error', 'This subscription is not pending approval.');
            }

            $subscription->update([
                'payment_status' => 'completed',
            ]);

            $subscription->school->activateSubscription($subscription);

            \Log::info('Payment approved successfully', ['subscription_id' => $id, 'school_id' => $subscription->school_id]);

            return redirect()->back()
                ->with('success', 'Payment approved and subscription activated successfully.');
        } catch (\Exception $e) {
            \Log::error('Payment approval failed', ['subscription_id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Failed to approve payment: ' . $e->getMessage());
        }
    }

    /**
     * Mark subscription as manually paid (for admin use)
     */
    public function markAsPaid($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only super admin can mark as paid manually
        if ($user->account_type !== 'admin' || $user->school_id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to perform this action.');
        }

        $subscription = SchoolSubscription::findOrFail($id);

        try {
            $subscription->update([
                'payment_method' => 'manual',
                'payment_status' => 'completed',
            ]);

            $school = $subscription->school;
            $school->activateSubscription($subscription);

            return redirect()->back()
                ->with('success', 'Subscription marked as paid and activated.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }
    }
}
