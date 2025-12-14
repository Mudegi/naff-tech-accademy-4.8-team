@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ route('admin.subscriptions.index') }}">Subscriptions</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Subscription #{{ $subscription->id }}</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Subscription Details</h6>
    </nav>

    <!-- Action Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        @if($subscription->user && $subscription->user->profile_photo_path)
                            <img src="{{ asset('storage/' . $subscription->user->profile_photo_path) }}" class="rounded-circle" alt="User">
                        @else
                            <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 56px; height: 56px; font-size: 1.25rem;">
                                {{ $subscription->user ? strtoupper(substr($subscription->user->name, 0, 1)) : '?' }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $subscription->user ? $subscription->user->name : 'Unknown User' }}</h5>
                        <p class="text-sm text-muted mb-0">{{ $subscription->user ? $subscription->user->email : 'No email' }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Subscription
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="text-center me-3">
                            <i class="fas fa-dollar-sign text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="w-100">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">Amount Paid</p>
                            <h4 class="mb-0 font-weight-bolder text-success">${{ number_format($subscription->amount_paid, 2) }}</h4>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $subscription->payment_method)) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="text-center me-3">
                            <i class="fas fa-info-circle text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="w-100">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">Payment Status</p>
                            <span class="badge badge-lg
                                @if($subscription->payment_status == 'success') bg-gradient-success
                                @elseif($subscription->payment_status == 'pending') bg-gradient-warning text-dark
                                @elseif($subscription->payment_status == 'failed') bg-gradient-danger
                                @else bg-gradient-secondary @endif">
                                <i class="fas fa-{{ $subscription->payment_status == 'success' ? 'check-circle' : ($subscription->payment_status == 'pending' ? 'clock' : ($subscription->payment_status == 'failed' ? 'times-circle' : 'info-circle')) }} me-1"></i>
                                {{ ucfirst($subscription->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="text-center me-3">
                            <i class="fas fa-calendar text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="w-100">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">Valid Until</p>
                            <h6 class="mb-0 font-weight-bolder">{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</h6>
                            @if($subscription->end_date)
                                <small class="text-{{ $subscription->end_date->isPast() ? 'danger' : ($subscription->end_date->diffInDays() <= 7 ? 'warning' : 'success') }}">
                                    <i class="fas fa-{{ $subscription->end_date->isPast() ? 'exclamation-triangle' : 'check' }} me-1"></i>
                                    {{ $subscription->end_date->isPast() ? 'Expired' : ($subscription->end_date->diffInDays() . ' days left') }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="text-center me-3">
                            <i class="fas fa-{{ $subscription->is_active ? 'play-circle text-success' : 'pause-circle text-secondary' }}" style="font-size: 2rem;"></i>
                        </div>
                        <div class="w-100">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">Subscription Status</p>
                            <span class="badge badge-lg
                                @if($subscription->is_active) bg-gradient-success
                                @else bg-gradient-secondary @endif">
                                <i class="fas fa-{{ $subscription->is_active ? 'play' : 'pause' }} me-1"></i>
                                {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Subscription Details -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Subscription Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <h6 class="mb-0">Subscriber Details</h6>
                                </div>
                                <div class="ps-3">
                                    <p class="mb-1"><strong>Name:</strong> {{ $subscription->user ? $subscription->user->name : 'Unknown User' }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $subscription->user ? $subscription->user->email : 'No email' }}</p>
                                    <p class="mb-0"><strong>User ID:</strong> {{ $subscription->user ? $subscription->user->id : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-box text-info me-2"></i>
                                    <h6 class="mb-0">Package Details</h6>
                                </div>
                                <div class="ps-3">
                                    <p class="mb-1"><strong>Package:</strong> {{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->name : 'Unknown Package' }}</p>
                                    <p class="mb-1"><strong>Type:</strong>
                                        <span class="badge ms-2
                                            @if($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'term') bg-primary
                                            @elseif($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'subject') bg-info
                                            @else bg-warning @endif">
                                            {{ $subscription->subscriptionPackage ? ucfirst($subscription->subscriptionPackage->subscription_type) : 'N/A' }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Duration:</strong> {{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->duration_days : 0 }} days</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-success me-2"></i>
                                    <h6 class="mb-0">Subscription Period</h6>
                                </div>
                                <div class="ps-3">
                                    <p class="mb-1"><strong>Start Date:</strong> {{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}</p>
                                    <p class="mb-1"><strong>End Date:</strong> {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</p>
                                    <p class="mb-0"><strong>Duration:</strong>
                                        @if($subscription->start_date && $subscription->end_date)
                                            {{ $subscription->start_date->diffInDays($subscription->end_date) }} days
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <h6 class="mb-0">Timestamps</h6>
                                </div>
                                <div class="ps-3">
                                    <p class="mb-1"><strong>Created:</strong> {{ $subscription->created_at->format('M d, Y H:i') }}</p>
                                    <p class="mb-0"><strong>Updated:</strong> {{ $subscription->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($subscription->transaction_id)
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-hashtag text-secondary me-2"></i>
                                    <h6 class="mb-0">Transaction Information</h6>
                                </div>
                                <div class="ps-3">
                                    <p class="mb-0"><strong>Transaction ID:</strong> <code>{{ $subscription->transaction_id }}</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Package Features -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2"></i>Package Features
                    </h6>
                </div>
                <div class="card-body">
                    @if($subscription->subscriptionPackage)
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->access_to_notices ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Access to Notices</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->access_to_videos ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Access to Videos</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->downloadable_content ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Downloadable Content</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->practice_questions_bank ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Practice Questions Bank</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->performance_analytics ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Performance Analytics</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $subscription->subscriptionPackage->parent_progress_reports ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                    <span class="small">Parent Progress Reports</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No package information available</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <form action="{{ route('admin.subscriptions.update-status', $subscription) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $subscription->is_active ? '0' : '1' }}">
                        <button type="submit" class="btn {{ $subscription->is_active ? 'btn-warning' : 'btn-success' }} w-100">
                            <i class="fas fa-{{ $subscription->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $subscription->is_active ? 'Deactivate' : 'Activate' }} Subscription
                        </button>
                    </form>

                    <button class="btn btn-outline-primary w-100" onclick="sendNotification({{ $subscription->id }})">
                        <i class="fas fa-envelope me-2"></i>Send Notification
                    </button>

                    <button class="btn btn-outline-info w-100" onclick="downloadReceipt({{ $subscription->id }})">
                        <i class="fas fa-download me-2"></i>Download Receipt
                    </button>

                    <button class="btn btn-outline-secondary w-100" onclick="printDetails({{ $subscription->id }})">
                        <i class="fas fa-print me-2"></i>Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendNotification(subscriptionId) {
    if (confirm('Send a notification to this subscriber?')) {
        // You can implement AJAX call here
        alert('Notification sent successfully!');
    }
}

function downloadReceipt(subscriptionId) {
    // Implement receipt download
    alert('Receipt download feature coming soon!');
}

function printDetails(subscriptionId) {
    window.print();
}
</script>

<style>
.avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card {
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.75rem;
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

.border {
    border-color: #e5e7eb !important;
}

.badge {
    font-weight: 600;
    letter-spacing: 0.025em;
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
}

.text-expired { color: #dc2626 !important; }
.text-warning { color: #d97706 !important; }
.text-success { color: #059669 !important; }

@media (max-width: 768px) {
    .avatar {
        width: 48px;
        height: 48px;
    }

    .card-body {
        padding: 1rem;
    }

    .btn-group {
        flex-direction: column;
    }

    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin: 0.125rem 0;
    }
}
</style>
@endsection
