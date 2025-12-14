@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Admin</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">School Subscription Approvals</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Pending School Subscription Approvals</h6>
    </nav>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Pending Approvals</p>
                                <h5 class="font-weight-bolder">{{ $pendingApprovals->total() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Amount</p>
                                <h5 class="font-weight-bolder">${{ number_format($pendingApprovals->sum('amount_paid'), 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fas fa-dollar-sign text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Schools Waiting</p>
                                <h5 class="font-weight-bolder">{{ $pendingApprovals->unique('school_id')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-school text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
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
                            <i class="fas fa-list-check me-2"></i>Pending School Subscription Approvals
                        </h6>
                        <p class="text-sm text-muted mb-0 mt-1">Review and approve school subscription payments</p>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Requires Action
                        </span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                        School Details
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        <i class="fas fa-box me-1"></i>Package
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        <i class="fas fa-dollar-sign me-1"></i>Amount
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        <i class="fas fa-calendar me-1"></i>Requested
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        <i class="fas fa-file-invoice me-1"></i>Payment Details
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals as $subscription)
                                <tr class="pending-approval-row" data-subscription-id="{{ $subscription->id }}">
                                    <td>
                                        <div class="d-flex align-items-center px-2 py-1">
                                            <input type="checkbox" class="form-check-input me-3 row-checkbox" value="{{ $subscription->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    @if($subscription->school && $subscription->school->logo)
                                                        <img src="{{ asset('storage/' . $subscription->school->logo) }}" class="rounded-circle" alt="School">
                                                    @else
                                                        <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                                            {{ $subscription->school ? strtoupper(substr($subscription->school->name, 0, 1)) : '?' }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $subscription->school ? $subscription->school->name : 'Unknown School' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $subscription->school ? $subscription->school->email : 'No email' }}</p>
                                                    <small class="text-muted">ID: {{ $subscription->school ? $subscription->school->id : 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm font-weight-bold">{{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->name : 'Unknown Package' }}</span>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge
                                                    @if($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'term') bg-primary
                                                    @elseif($subscription->subscriptionPackage && $subscription->subscriptionPackage->subscription_type == 'subject') bg-info
                                                    @else bg-warning @endif badge-sm">
                                                    <i class="fas fa-tag me-1"></i>{{ $subscription->subscriptionPackage ? ucfirst($subscription->subscriptionPackage->subscription_type) : 'N/A' }}
                                                </span>
                                                <small class="text-muted ms-2">
                                                    {{ $subscription->subscriptionPackage ? $subscription->subscriptionPackage->duration_days : 0 }} days
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm font-weight-bold text-success">${{ number_format($subscription->amount_paid, 2) }}</span>
                                            <small class="text-muted">
                                                <i class="fas fa-credit-card me-1"></i>{{ ucfirst(str_replace('_', ' ', $subscription->payment_method)) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm font-weight-bold">{{ $subscription->created_at->format('M d, Y') }}</span>
                                            <small class="text-muted">{{ $subscription->created_at->format('H:i') }}</small>
                                            <small class="text-muted d-block">
                                                {{ $subscription->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($subscription->transaction_id)
                                                <span class="text-sm font-weight-bold text-primary">{{ substr($subscription->transaction_id, 0, 15) }}...</span>
                                                <small class="text-muted">Transaction ID</small>
                                            @else
                                                <span class="text-sm text-muted">No transaction ID</span>
                                            @endif

                                            @if($subscription->notes)
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                        {{ Str::limit($subscription->notes, 30) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-vertical" role="group">
                                            <form action="{{ route('admin.school-subscriptions.approve', $subscription->id) }}" method="POST" class="d-inline mb-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success w-100"
                                                        onclick="return confirm('Are you sure you want to approve this payment? The school will be activated immediately.')"
                                                        title="Approve Payment">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.school-subscriptions.reject', $subscription->id) }}" method="POST" class="d-inline mb-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                                        onclick="return confirm('Are you sure you want to reject this payment? The subscription will be cancelled.')"
                                                        title="Reject Payment">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.schools.show', $subscription->school_id) }}"
                                               class="btn btn-sm btn-outline-primary w-100"
                                               title="View School Details">
                                                <i class="fas fa-eye me-1"></i>View School
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-success mb-2">All Caught Up!</h5>
                                            <p class="text-muted text-center mb-3" style="max-width: 400px;">
                                                There are no pending school subscription approvals at this time. All payments have been processed.
                                            </p>
                                            <a href="{{ route('admin.schools.index') }}" class="btn btn-primary">
                                                <i class="fas fa-arrow-left me-2"></i>Back to Schools
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pendingApprovals->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $pendingApprovals->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // Row hover effects
    const rows = document.querySelectorAll('.pending-approval-row');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0,0,0,0.03)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

<style>
.pending-approval-row:hover {
    transition: background-color 0.2s ease;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.btn-group-vertical .btn {
    margin-bottom: 0.25rem;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
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

.table thead th {
    border-bottom: 2px solid #e5e7eb;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .btn-group-vertical {
        flex-direction: row !important;
        justify-content: center;
    }

    .btn-group-vertical .btn {
        flex: 1;
        margin: 0 0.125rem;
    }

    .table-responsive {
        font-size: 0.8rem;
    }

    .avatar {
        width: 35px;
        height: 35px;
    }
}
</style>
@endsection
