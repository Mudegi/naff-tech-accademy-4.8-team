@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Subscription Packages</h4>
                    <p class="text-sm text-muted mb-0">Manage your subscription packages and pricing</p>
                </div>
                <a href="{{ route('admin.subscription-packages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Package
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Packages</p>
                                <h5 class="font-weight-bolder">{{ $packages->total() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-box text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Active Packages</p>
                                <h5 class="font-weight-bolder">{{ $packages->where('is_active', true)->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fas fa-check text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Avg Price</p>
                                <h5 class="font-weight-bolder">UGX {{ $packages->avg('price') ? number_format($packages->avg('price'), 0) : '0' }}</h5>
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
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">This Month</p>
                                <h5 class="font-weight-bolder">{{ $packages->where('created_at', '>=', now()->startOfMonth())->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-calendar text-lg opacity-10" aria-hidden="true"></i>
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
                <div class="card-header">
                    <h6 class="mb-0">All Subscription Packages</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Package Details</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pricing</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Duration</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm font-weight-bold">{{ $package->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($package->description, 60) }}</p>
                                                <small class="text-muted">{{ $package->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm font-weight-bold">UGX {{ number_format($package->price, 0) }}</span>
                                            <small class="text-muted">{{ ucfirst($package->subscription_type) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold">{{ $package->duration_days }} days</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm
                                            @if($package->subscription_type == 'term') bg-gradient-primary
                                            @elseif($package->subscription_type == 'subject') bg-gradient-info
                                            @else bg-gradient-warning @endif">
                                            {{ ucfirst($package->subscription_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($package->is_active)
                                            <span class="badge badge-sm bg-gradient-success">
                                                <i class="fas fa-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">
                                                <i class="fas fa-pause me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary mb-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-lg"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.subscription-packages.show', $package) }}">
                                                    <i class="fas fa-eye me-2"></i>View Details
                                                </a></li>
                                                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.subscription-packages.edit', $package) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit Package
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.subscription-packages.destroy', $package) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Are you sure you want to delete this package?')">
                                                            <i class="fas fa-trash me-2"></i>Delete Package
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open text-muted" style="font-size: 3rem;"></i>
                                            <h6 class="mt-3 text-muted">No subscription packages found</h6>
                                            <p class="text-sm text-muted mb-3">Get started by creating your first subscription package</p>
                                            <a href="{{ route('admin.subscription-packages.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Create First Package
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($packages->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $packages->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
