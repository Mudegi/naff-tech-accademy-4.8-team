@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Subscription Management</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span>School</span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Subscriptions</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Active Subscription Card -->
    @if($activeSubscription)
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-green-900 mb-2">Active Subscription</h3>
                <p class="text-green-700">
                    <strong>{{ $activeSubscription->subscriptionPackage->name }}</strong> - 
                    Valid until {{ $activeSubscription->end_date->format('F d, Y') }}
                </p>
                <p class="text-sm text-green-600 mt-1">
                    {{ $activeSubscription->end_date->diffForHumans() }} remaining
                </p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm font-medium">
                    Active
                </span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-yellow-900 mb-2">No Active Subscription</h3>
                <p class="text-yellow-700">Your school account is inactive. Please purchase a subscription to continue.</p>
            </div>
            <a href="{{ route('admin.school.subscriptions.create') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                Purchase Subscription
            </a>
        </div>
    </div>
    @endif

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Subscription History</h2>
        <a href="{{ route('admin.school.subscriptions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Purchase New Subscription
        </a>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $subscription->subscriptionPackage->name }}</div>
                        <div class="text-sm text-gray-500">{{ $subscription->subscriptionPackage->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($subscription->amount_paid, 0) }} UGX
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->start_date->format('M d, Y') }} - 
                        {{ $subscription->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($subscription->payment_status === 'completed')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                        @elseif($subscription->payment_status === 'pending_approval')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                Pending Approval
                            </span>
                        @elseif($subscription->payment_status === 'pending')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                {{ ucfirst(str_replace('_', ' ', $subscription->payment_status)) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($subscription->is_active)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if($subscription->payment_status === 'pending')
                            <a href="{{ route('admin.school.subscriptions.payment', $subscription->id) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-credit-card mr-1"></i> Pay Now
                            </a>
                        @elseif($subscription->payment_status === 'pending_approval')
                            <span class="text-orange-600">
                                <i class="fas fa-clock mr-1"></i> Awaiting Approval
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection

