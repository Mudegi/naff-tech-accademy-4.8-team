@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Edit User Subscription</h6>
                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="form-control-label">User *</label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->account_type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subscription_package_id" class="form-control-label">Subscription Package *</label>
                                    <select name="subscription_package_id" id="subscription_package_id" class="form-control @error('subscription_package_id') is-invalid @enderror" required>
                                        <option value="">Select Package</option>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->id }}" {{ old('subscription_package_id', $subscription->subscription_package_id) == $package->id ? 'selected' : '' }}>
                                                {{ $package->name }} - ${{ number_format($package->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subscription_package_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method" class="form-control-label">Payment Method *</label>
                                    <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="flutterwave" {{ old('payment_method', $subscription->payment_method) == 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                                        <option value="easypay" {{ old('payment_method', $subscription->payment_method) == 'easypay' ? 'selected' : '' }}>EasyPay</option>
                                        <option value="mobile_money" {{ old('payment_method', $subscription->payment_method) == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                        <option value="bank_transfer" {{ old('payment_method', $subscription->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cash" {{ old('payment_method', $subscription->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="trial" {{ old('payment_method', $subscription->payment_method) == 'trial' ? 'selected' : '' }}>Trial</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status" class="form-control-label">Payment Status *</label>
                                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ old('payment_status', $subscription->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="success" {{ old('payment_status', $subscription->payment_status) == 'success' ? 'selected' : '' }}>Success</option>
                                        <option value="failed" {{ old('payment_status', $subscription->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount_paid" class="form-control-label">Amount Paid ($) *</label>
                                    <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid', $subscription->amount_paid) }}" required>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date" class="form-control-label">Start Date *</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $subscription->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date" class="form-control-label">End Date *</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $subscription->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Subscription Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
