@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="javascript:;">Admin</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Subscriptions</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Subscription Management</h6>
    </nav>

    <!-- Page Header with Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">User Subscriptions Overview</h6>
                            <p class="text-sm text-muted mb-0">Monitor and manage all user subscription records</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="text-center me-3">
                                    <i class="fas fa-users text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $subscriptions->total() }}</h4>
                                    <p class="text-xs text-muted mb-0">Total Subscriptions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="text-center me-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $subscriptions->where('is_active', true)->count() }}</h4>
                                    <p class="text-xs text-muted mb-0">Active Subscriptions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="text-center me-3">
                                    <i class="fas fa-dollar-sign text-info" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">${{ number_format($subscriptions->sum('amount_paid'), 0) }}</h4>
                                    <p class="text-xs text-muted mb-0">Total Revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="text-center me-3">
                                    <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $subscriptions->where('payment_status', 'pending')->count() }}</h4>
                                    <p class="text-xs text-muted mb-0">Pending Payments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body d-grid gap-3">
                    <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i>
                        <span>Add New Subscription</span>
                    </a>
                    <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                        <i class="fas fa-box me-2"></i>
                        <span>Manage Packages</span>
                    </a>
                    <button class="btn btn-outline-info d-flex align-items-center" onclick="exportSubscriptions()">
                        <i class="fas fa-download me-2"></i>
                        <span>Export Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Advanced Filters
                    </h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFilters()">
                            <i class="fas fa-chevron-up me-1"></i>Collapse
                        </button>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear All
                        </a>
                    </div>
                </div>
                <div class="card-body" id="filters-section">
                    <form method="GET" class="row g-3">
                        <!-- Search and Basic Filters -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>Search Users
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Name or email" value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">
                                <i class="fas fa-credit-card me-1"></i>Payment Status
                            </label>
                            <select name="payment_status" class="form-select">
                                <option value="">All Status</option>
                                @foreach($paymentStatuses as $status)
                                    <option value="{{ $status }}" {{ request('payment_status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill me-1"></i>Payment Method
                            </label>
                            <select name="payment_method" class="form-select">
                                <option value="">All Methods</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $method)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">
                                <i class="fas fa-box me-1"></i>Package
                            </label>
                            <select name="package_id" class="form-select">
                                <option value="">All Packages</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-1"></i>Subscription Status
                            </label>
                            <select name="is_active" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                                <div class="vr"></div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>More
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="exportSubscriptions()">
                                            <i class="fas fa-file-export me-2"></i>Export to CSV
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bulkActions()">
                                            <i class="fas fa-tasks me-2"></i>Bulk Actions
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="showAnalytics()">
                                            <i class="fas fa-chart-bar me-2"></i>View Analytics
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Subscriptions
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search Users</label>
                            <input type="text" name="search" class="form-control" placeholder="Name or email" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="">All Status</option>
                                @foreach($paymentStatuses as $status)
                                    <option value="{{ $status }}" {{ request('payment_status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">All Methods</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Package</label>
                            <select name="package_id" class="form-select">
                                <option value="">All Packages</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>All User Subscriptions
                            <span class="badge bg-primary ms-2">{{ $subscriptions->total() }}</span>
                        </h6>
                        <p class="text-sm text-muted mb-0 mt-1">Showing {{ $subscriptions->count() }} of {{ $subscriptions->total() }} subscriptions</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" class="form-control" placeholder="Quick search..." id="tableSearch">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <!-- Table Controls -->
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" style="width: auto;" id="entriesPerPage">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 entries</option>
                                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 entries</option>
                                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 entries</option>
                                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100 entries</option>
                                </select>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Click on any row to view details
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0 table-hover" id="subscriptionsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
                                    </th>
                                    <th class="fw-bold">
                                        <i class="fas fa-user me-2"></i>Subscriber
                                    </th>
                                    <th class="fw-bold">
                                        <i class="fas fa-box me-2"></i>Package Details
                                    </th>
                                    <th class="fw-bold">
                                        <i class="fas fa-dollar-sign me-2"></i>Payment Info
                                    </th>
                                    <th class="fw-bold">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="fw-bold">
                                        <i class="fas fa-calendar me-2"></i>Validity Period
                                    </th>
                                    <th class="text-center fw-bold" style="width: 140px;">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                <tr class="subscription-row" data-subscription-id="{{ $subscription->id }}">
                                    <!-- Selection Checkbox -->
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $subscription->id }}">
                                    </td>

                                    <!-- User Information -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-wrapper me-3">
                                                @if($subscription->user && $subscription->user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $subscription->user->profile_photo_path) }}" class="avatar-img rounded-circle" alt="User">
                                                @else
                                                    <div class="avatar-fallback rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center fw-bold">
                                                        {{ $subscription->user ? strtoupper(substr($subscription->user->name, 0, 1)) : '?' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="user-info">
                                                <div class="fw-bold text-dark mb-0">{{ $subscription->user ? $subscription->user->name : 'Unknown User' }}</div>
                                                <small class="text-muted">{{ $subscription->user ? $subscription->user->email : 'No email' }}</small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>ID: {{ $subscription->id }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Package Information -->
                                    <td>
                                        <div class="package-info">
                                            <div class="fw-bold text-primary mb-1">{{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->name : 'Unknown Package' }}</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge
                                                    @if($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'term') bg-primary
                                                    @elseif($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'subject') bg-info
                                                    @else bg-warning @endif badge-sm">
                                                    <i class="fas fa-tag me-1"></i>{{ $subscription->subscriptionPackage ? ucfirst($subscription->subscriptionPackage->subscription_type) : 'N/A' }}
                                                </span>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->duration_days : 0 }} days
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Payment Information -->
                                    <td>
                                        <div class="payment-info">
                                            <div class="fw-bold text-success mb-1">${{ number_format($subscription->amount_paid, 2) }}</div>
                                            <div class="d-flex align-items-center">
                                                <span class="badge
                                                    @if($subscription->payment_method == 'flutterwave') bg-success
                                                    @elseif($subscription->payment_method == 'easypay') bg-info
                                                    @elseif($subscription->payment_method == 'cash') bg-warning
                                                    @else bg-secondary @endif badge-sm">
                                                    <i class="fas fa-credit-card me-1"></i>{{ ucfirst(str_replace('_', ' ', $subscription->payment_method)) }}
                                                </span>
                                            </div>
                                            @if($subscription->transaction_id)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-hashtag me-1"></i>{{ substr($subscription->transaction_id, 0, 10) }}...
                                            </small>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status Information -->
                                    <td>
                                        <div class="status-info">
                                            <!-- Subscription Status -->
                                            <div class="mb-2">
                                                <span class="badge
                                                    @if($subscription->is_active) bg-success
                                                    @else bg-secondary @endif badge-sm w-100 justify-content-center py-2">
                                                    @if($subscription->is_active)
                                                        <i class="fas fa-play-circle me-1"></i>Active Subscription
                                                    @else
                                                        <i class="fas fa-pause-circle me-1"></i>Inactive Subscription
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Payment Status -->
                                            <div>
                                                <span class="badge
                                                    @if($subscription->payment_status == 'success') bg-success
                                                    @elseif($subscription->payment_status == 'pending') bg-warning text-dark
                                                    @elseif($subscription->payment_status == 'failed') bg-danger
                                                    @else bg-info @endif badge-sm w-100 justify-content-center">
                                                    @if($subscription->payment_status == 'success')
                                                        <i class="fas fa-check-circle me-1"></i>Payment Completed
                                                    @elseif($subscription->payment_status == 'pending')
                                                        <i class="fas fa-clock me-1"></i>Payment Pending
                                                    @elseif($subscription->payment_status == 'failed')
                                                        <i class="fas fa-times-circle me-1"></i>Payment Failed
                                                    @else
                                                        <i class="fas fa-info-circle me-1"></i>{{ ucfirst($subscription->payment_status) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Validity Period -->
                                    <td>
                                        <div class="validity-info">
                                            <div class="fw-bold mb-1">{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</div>
                                            <div class="d-flex align-items-center">
                                                @if($subscription->end_date)
                                                    @if($subscription->end_date->isPast())
                                                        <span class="text-danger small">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Expired {{ $subscription->end_date->diffForHumans() }}
                                                        </span>
                                                    @elseif($subscription->end_date->diffInDays() <= 7)
                                                        <span class="text-warning small">
                                                            <i class="fas fa-clock me-1"></i>Expires in {{ $subscription->end_date->diffInDays() }} days
                                                        </span>
                                                    @else
                                                        <span class="text-success small">
                                                            <i class="fas fa-check-circle me-1"></i>{{ $subscription->end_date->diffInDays() }} days remaining
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                            <small class="text-muted d-block">
                                                Started: {{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}
                                            </small>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <div class="action-buttons d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="View Details"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.subscriptions.edit', $subscription) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Edit Subscription"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button"
                                                        data-bs-toggle="dropdown"
                                                        title="More Actions">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <form action="{{ route('admin.subscriptions.update-status', $subscription) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="is_active" value="{{ $subscription->is_active ? '0' : '1' }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-{{ $subscription->is_active ? 'pause' : 'play' }} me-2"></i>
                                                                {{ $subscription->is_active ? 'Deactivate' : 'Activate' }} Subscription
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-primary" onclick="sendNotification({{ $subscription->id }})">
                                                            <i class="fas fa-envelope me-2"></i>Send Notification
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this subscription? This action cannot be undone.')">
                                                                <i class="fas fa-trash me-2"></i>Delete Subscription
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="fas fa-users text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No Subscriptions Found</h5>
                                            <p class="text-muted text-center mb-4" style="max-width: 400px;">
                                                There are no user subscriptions in the system yet. Users will appear here once they subscribe to packages.
                                            </p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Create First Subscription
                                                </a>
                                                <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-box me-2"></i>Manage Packages
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer with Pagination -->
                    @if($subscriptions->hasPages())
                    <div class="px-3 py-3 border-top">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <p class="text-sm text-muted mb-0">
                                    Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} entries
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex justify-content-end">
                                    {{ $subscriptions->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick search functionality
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#subscriptionsTable tbody tr');

            rows.forEach(row => {
                if (row.classList.contains('subscription-row')) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                }
            });
        });
    }

    // Select all checkbox functionality
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // Row click to view details
    const rows = document.querySelectorAll('.subscription-row');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on checkbox, button, or dropdown
            if (e.target.type === 'checkbox' ||
                e.target.tagName === 'BUTTON' ||
                e.target.closest('.dropdown') ||
                e.target.closest('.btn')) {
                return;
            }

            const subscriptionId = this.dataset.subscriptionId;
            window.location.href = `/admin/subscriptions/${subscriptionId}`;
        });
    });

    // Filter toggle functionality
    window.toggleFilters = function() {
        const filtersSection = document.getElementById('filters-section');
        const toggleBtn = document.querySelector('[onclick="toggleFilters()"]');

        if (filtersSection.style.display === 'none') {
            filtersSection.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-up me-1"></i>Collapse';
        } else {
            filtersSection.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-down me-1"></i>Expand';
        }
    };

// Placeholder functions for future implementation
window.exportSubscriptions = function() {
    const selectedRows = document.querySelectorAll('.row-checkbox:checked');
    if (selectedRows.length === 0) {
        alert('Please select at least one subscription to export.');
        return;
    }

    // Create form to submit selected IDs
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/subscriptions/export';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }

    // Add selected IDs
    selectedRows.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'subscription_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
};

window.bulkActions = function() {
    const selectedRows = document.querySelectorAll('.row-checkbox:checked');
    if (selectedRows.length === 0) {
        alert('Please select at least one subscription for bulk actions.');
        return;
    }

    // Show bulk actions modal or dropdown
    const action = prompt(`Selected ${selectedRows.length} subscriptions. Choose action:\n1. Activate All\n2. Deactivate All\n3. Delete All\n\nEnter 1, 2, or 3:`);

    if (action && ['1', '2', '3'].includes(action)) {
        const form = document.createElement('form');
        form.method = 'POST';

        if (action === '1') form.action = '/admin/subscriptions/bulk-activate';
        else if (action === '2') form.action = '/admin/subscriptions/bulk-deactivate';
        else if (action === '3') form.action = '/admin/subscriptions/bulk-delete';

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Add selected IDs
        selectedRows.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'subscription_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
};

window.showAnalytics = function() {
    // Redirect to analytics page or show modal
    window.location.href = '/admin/subscriptions/analytics';
};

window.sendNotification = function(subscriptionId) {
    if (confirm('Send a notification to this subscriber?')) {
        // Create form for notification
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/subscriptions/${subscriptionId}/notify`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
};

    // Entries per page functionality
    const entriesSelect = document.getElementById('entriesPerPage');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    }
});
</script>

<style>
/* Enhanced Table Styling */
.subscription-row {
    transition: all 0.2s ease;
}

.subscription-row:hover {
    background-color: rgba(0,0,0,0.03);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table-dark th {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
    border: none !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

/* Avatar Styling */
.avatar-wrapper {
    position: relative;
}

.avatar-img, .avatar-fallback {
    width: 40px;
    height: 40px;
    border: 2px solid #e5e7eb;
}

.avatar-fallback {
    font-size: 0.875rem;
    font-weight: 700;
}

/* User Info Styling */
.user-info .fw-bold {
    font-size: 0.875rem;
    line-height: 1.2;
}

/* Package Info Styling */
.package-info .fw-bold {
    color: #2563eb;
    font-size: 0.875rem;
}

/* Payment Info Styling */
.payment-info .fw-bold {
    color: #059669;
    font-size: 0.875rem;
}

/* Status Info Styling */
.status-info .badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 100px;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.375rem 0.5rem;
}

/* Validity Info Styling */
.validity-info .fw-bold {
    color: #1f2937;
    font-size: 0.875rem;
}

/* Action Buttons Styling */
.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.375rem;
    margin: 0 1px;
}

.action-buttons .dropdown-toggle {
    padding: 0.25rem 0.375rem;
}

/* Enhanced Card Styling */
.card {
    border: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    border-radius: 0.75rem;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
}

.card-header h6 {
    color: white;
    margin: 0;
    font-weight: 600;
}

/* Filter Section Styling */
#filters-section {
    max-height: 500px;
    overflow-y: auto;
}

/* Table Controls Styling */
.table-controls {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
}

/* Badge Enhancements */
.badge {
    font-weight: 600;
    letter-spacing: 0.025em;
}

/* Responsive Table */
@media (max-width: 1200px) {
    .table-responsive {
        font-size: 0.8rem;
    }

    .avatar-img, .avatar-fallback {
        width: 35px;
        height: 35px;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }

    .action-buttons .btn {
        width: 100%;
        margin: 0;
    }
}

@media (max-width: 768px) {
    .card-header {
        padding: 1rem;
    }

    .table-controls {
        padding: 0.5rem;
    }

    .filters-row .col-md-2 {
        margin-bottom: 1rem;
    }

    .status-info .badge {
        font-size: 0.65rem;
        min-width: 80px;
    }
}

/* Custom Scrollbar */
#filters-section::-webkit-scrollbar {
    width: 6px;
}

#filters-section::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#filters-section::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#filters-section::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Loading Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.loading-row {
    animation: pulse 1.5s ease-in-out infinite;
}

/* Tooltip Enhancements */
.tooltip-inner {
    background-color: #1f2937;
    font-size: 0.75rem;
}

/* Focus States */
.form-check-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Enhanced Dropdown */
.dropdown-menu {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f3f4f6;
}

/* Status Color Coding */
.text-expired { color: #dc2626 !important; }
.text-warning { color: #d97706 !important; }
.text-success { color: #059669 !important; }
.text-muted { color: #6b7280 !important; }
</style>
@endsection
