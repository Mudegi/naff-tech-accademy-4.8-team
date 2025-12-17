@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Subscription Package Details</h6>
                        <div>
                            <a href="{{ route('admin.subscription-packages.edit', $subscriptionPackage) }}" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-4">{{ $subscriptionPackage->name }}</h4>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Type:</strong> {{ ucfirst($subscriptionPackage->subscription_type) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Price:</strong> ${{ number_format($subscriptionPackage->price, 2) }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Duration:</strong> {{ $subscriptionPackage->duration_days }} days
                                </div>
                                <div class="col-md-6">
                                    <strong>Max Sessions:</strong> {{ $subscriptionPackage->maximum_active_sessions }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    @if($subscriptionPackage->is_active)
                                        <span class="badge badge-sm bg-gradient-success">Active</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong> {{ $subscriptionPackage->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $subscriptionPackage->description }}</p>
                            </div>

                            @if($subscriptionPackage->features)
                            <div class="mb-4">
                                <strong>Features:</strong>
                                <ul class="list-unstyled mt-2">
                                    @foreach(json_decode($subscriptionPackage->features, true) as $feature)
                                        <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Access Permissions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->access_to_notices ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Access to Notices
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->access_to_videos ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Access to Videos
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->downloadable_content ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Downloadable Content
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->practice_questions_bank ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Practice Questions Bank
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->performance_analytics ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Performance Analytics
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->parent_progress_reports ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Parent Progress Reports
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->one_on_one_tutoring_sessions ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            1-on-1 Tutoring Sessions
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->shared_resources ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Shared Resources
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->priority_support ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Priority Support
                                        </div>
                                        <div class="col-12 mb-2">
                                            <i class="fas fa-{{ $subscriptionPackage->email_support ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                            Email Support
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
