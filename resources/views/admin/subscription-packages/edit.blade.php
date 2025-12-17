@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Edit Subscription Package</h6>
                        <a href="{{ route('admin.subscription-packages.show', $subscriptionPackage) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subscription-packages.update', $subscriptionPackage) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Package Name *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subscriptionPackage->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subscription_type" class="form-control-label">Subscription Type *</label>
                                    <select name="subscription_type" id="subscription_type" class="form-control @error('subscription_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="term" {{ old('subscription_type', $subscriptionPackage->subscription_type) == 'term' ? 'selected' : '' }}>Term</option>
                                        <option value="subject" {{ old('subscription_type', $subscriptionPackage->subscription_type) == 'subject' ? 'selected' : '' }}>Subject</option>
                                        <option value="topic" {{ old('subscription_type', $subscriptionPackage->subscription_type) == 'topic' ? 'selected' : '' }}>Topic</option>
                                    </select>
                                    @error('subscription_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-control-label">Price ($) *</label>
                                    <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $subscriptionPackage->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration_days" class="form-control-label">Duration (Days) *</label>
                                    <input type="number" name="duration_days" id="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $subscriptionPackage->duration_days) }}" required>
                                    @error('duration_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="maximum_active_sessions" class="form-control-label">Max Active Sessions *</label>
                                    <input type="number" name="maximum_active_sessions" id="maximum_active_sessions" class="form-control @error('maximum_active_sessions') is-invalid @enderror" value="{{ old('maximum_active_sessions', $subscriptionPackage->maximum_active_sessions) }}" required>
                                    @error('maximum_active_sessions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $subscriptionPackage->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-control-label">Description *</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $subscriptionPackage->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="features" class="form-control-label">Features (one per line) *</label>
                            <textarea name="features" id="features" rows="5" class="form-control @error('features') is-invalid @enderror" placeholder="Enter each feature on a new line" required>{{ old('features', implode("\n", json_decode($subscriptionPackage->features, true) ?? [])) }}</textarea>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Access Permissions</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="access_to_notices" id="access_to_notices" value="1" {{ old('access_to_notices', $subscriptionPackage->access_to_notices) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="access_to_notices">Access to Notices</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="access_to_videos" id="access_to_videos" value="1" {{ old('access_to_videos', $subscriptionPackage->access_to_videos) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="access_to_videos">Access to Videos</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="downloadable_content" id="downloadable_content" value="1" {{ old('downloadable_content', $subscriptionPackage->downloadable_content) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="downloadable_content">Downloadable Content</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="practice_questions_bank" id="practice_questions_bank" value="1" {{ old('practice_questions_bank', $subscriptionPackage->practice_questions_bank) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="practice_questions_bank">Practice Questions Bank</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="performance_analytics" id="performance_analytics" value="1" {{ old('performance_analytics', $subscriptionPackage->performance_analytics) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="performance_analytics">Performance Analytics</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="parent_progress_reports" id="parent_progress_reports" value="1" {{ old('parent_progress_reports', $subscriptionPackage->parent_progress_reports) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="parent_progress_reports">Parent Progress Reports</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="one_on_one_tutoring_sessions" id="one_on_one_tutoring_sessions" value="1" {{ old('one_on_one_tutoring_sessions', $subscriptionPackage->one_on_one_tutoring_sessions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="one_on_one_tutoring_sessions">1-on-1 Tutoring Sessions</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="shared_resources" id="shared_resources" value="1" {{ old('shared_resources', $subscriptionPackage->shared_resources) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shared_resources">Shared Resources</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="priority_support" id="priority_support" value="1" {{ old('priority_support', $subscriptionPackage->priority_support) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="priority_support">Priority Support</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="email_support" id="email_support" value="1" {{ old('email_support', $subscriptionPackage->email_support) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_support">Email Support</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subscription-packages.show', $subscriptionPackage) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
