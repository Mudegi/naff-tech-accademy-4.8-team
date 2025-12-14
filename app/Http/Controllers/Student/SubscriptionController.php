<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\SubscriptionPackage;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = UserSubscription::where('user_id', $user->id)->orderByDesc('created_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }
        if ($request->filled('package_id')) {
            $query->where('subscription_package_id', $request->input('package_id'));
        }

        $subscriptions = $query->paginate(10);
        // Only show packages the user has at least one subscription for
        $packageIds = UserSubscription::where('user_id', $user->id)
            ->pluck('subscription_package_id')
            ->unique()
            ->toArray();
        $packages = SubscriptionPackage::whereIn('id', $packageIds)->get();

        $statuses = UserSubscription::where('user_id', $user->id)
            ->pluck('payment_status')
            ->unique()
            ->toArray();

        return view('student.subscription', compact('subscriptions', 'packages', 'statuses'));
    }

    public function receipt(Request $request, $subscription)
    {
        $user = $request->user();
        $sub = UserSubscription::where('id', $subscription)
            ->where('user_id', $user->id)
            ->where('payment_status', 'success')
            ->firstOrFail();
        $package = SubscriptionPackage::find($sub->subscription_package_id);
        return view('student.subscription-receipt', compact('sub', 'package', 'user'));
    }
} 