<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = UserSubscription::with(['user', 'subscriptionPackage']);

            // Filter by payment status
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by payment method
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Filter by subscription status
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Search by user name or email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $subscriptions = $query->latest()->paginate(10);

            // Get filter options
            $paymentMethods = UserSubscription::distinct()->pluck('payment_method')->filter();
            $paymentStatuses = UserSubscription::distinct()->pluck('payment_status')->filter();
            $packages = SubscriptionPackage::where('is_active', true)->get();

            return view('admin.subscriptions.index', compact(
                'subscriptions',
                'paymentMethods',
                'paymentStatuses',
                'packages'
            ));
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function show(UserSubscription $subscription)
    {
        try {
            $subscription->load(['user', 'subscriptionPackage']);
            \Log::info('Subscription show method called for ID: ' . $subscription->id);
            return view('admin.subscriptions.show', compact('subscription'));
        } catch (\Exception $e) {
            \Log::error('Error in subscription show: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function updateStatus(Request $request, UserSubscription $subscription)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $subscription->update([
            'is_active' => $validated['is_active']
        ]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Subscription has been successfully {$status}.");
    }

    public function create()
    {
        // Get only students and parents
        $users = User::whereIn('account_type', ['student', 'parent'])
            ->orderBy('name')
            ->get();

        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.subscriptions.create', compact('users', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
            'payment_method' => 'required|in:flutterwave,easypay,mobile_money,bank_transfer,cash,trial',
            'payment_status' => 'required|in:pending,success,failed',
            'amount_paid' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Verify user is student or parent
        $user = User::findOrFail($validated['user_id']);
        if (!in_array($user->account_type, ['student', 'parent'])) {
            return back()->with('error', 'Only students and parents can have subscriptions.');
        }

        // Create the subscription
        $subscription = UserSubscription::create([
            'user_id' => $validated['user_id'],
            'subscription_package_id' => $validated['subscription_package_id'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'amount_paid' => $validated['amount_paid'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => true, // Set as active by default
        ]);

        return redirect()
            ->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription created successfully.');
    }

    public function edit(UserSubscription $subscription)
    {
        $users = User::whereIn('account_type', ['student', 'parent'])->get();
        $packages = SubscriptionPackage::where('is_active', true)->get();

        return view('admin.subscriptions.edit', compact('subscription', 'users', 'packages'));
    }

    public function update(Request $request, UserSubscription $subscription)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
            'payment_method' => 'required|in:flutterwave,easypay,mobile_money,bank_transfer,cash,trial',
            'payment_status' => 'required|in:pending,success,failed',
            'amount_paid' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean'
        ]);

        // Verify user type
        $user = User::findOrFail($request->user_id);
        if (!in_array($user->account_type, ['student', 'parent'])) {
            return back()->with('error', 'Invalid user type. Only students and parents can have subscriptions.');
        }

        $updateData = [
            'user_id' => $request->user_id,
            'subscription_package_id' => $request->subscription_package_id,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'amount_paid' => $request->amount_paid,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active
        ];

        // If created_by is empty, set it to the current admin's ID
        if (empty($subscription->created_by)) {
            $updateData['created_by'] = Auth::id();
        }

        $subscription->update($updateData);

        return redirect()
            ->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(UserSubscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }
}