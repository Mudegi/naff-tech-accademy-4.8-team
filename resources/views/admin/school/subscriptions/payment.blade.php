@extends('layouts.dashboard')

@section('content')
<style>
.payment-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.payment-layout {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}
.summary-card {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
    padding: 35px;
    color: white;
    position: sticky;
    top: 20px;
    height: fit-content;
}
.summary-header {
    text-align: center;
    margin-bottom: 30px;
}
.summary-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}
.summary-icon i {
    font-size: 32px;
}
.summary-header h3 {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 8px 0;
}
.summary-header p {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}
.summary-details {
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}
.summary-item {
    margin-bottom: 20px;
}
.summary-label {
    font-size: 13px;
    opacity: 0.8;
    margin-bottom: 5px;
}
.summary-value {
    font-size: 18px;
    font-weight: 600;
}
.summary-amount {
    font-size: 32px;
    font-weight: bold;
}
.summary-meta {
    margin-top: 20px;
}
.summary-meta-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 14px;
}
.summary-meta-label {
    opacity: 0.8;
}
.summary-meta-value {
    font-weight: 500;
}
.summary-footer {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    opacity: 0.9;
}

/* Responsive Design */
@media (max-width: 768px) {
    .payment-page {
        padding: 15px;
    }

    .payment-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .summary-card {
        position: static;
        order: 2;
    }

    .mobile-money-options {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .payment-info-box {
        padding: 15px;
    }

    .mobile-money-option {
        padding: 12px;
    }

    .network-badge {
        font-size: 12px;
        padding: 4px 8px;
    }
}
.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}
.form-header {
    background: linear-gradient(135deg, #f9fafb 0%, #eff6ff 100%);
    padding: 30px 40px;
    border-bottom: 2px solid #e5e7eb;
}
.form-header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}
.form-header-icon {
    width: 50px;
    height: 50px;
    background: #2563eb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}
.form-header-text h3 {
    font-size: 26px;
    font-weight: bold;
    color: #1f2937;
    margin: 0 0 5px 0;
}
.form-header-text p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
}
.form-body {
    padding: 40px;
}
.payment-info-box {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}
.payment-info-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}
.payment-info-header i {
    font-size: 20px;
    color: #2563eb;
}
.payment-info-header h4 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}
.payment-info-content {
    color: #374151;
}
.payment-detail {
    margin-bottom: 8px;
    font-size: 14px;
}
.payment-note {
    background: #dbeafe;
    border: 1px solid #93c5fd;
    border-radius: 6px;
    padding: 12px;
    margin-top: 15px;
    font-size: 13px;
    color: #1e40af;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.payment-note i {
    margin-top: 1px;
    flex-shrink: 0;
}
.mobile-money-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}
.mobile-money-option {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}
.network-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #2563eb;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
}
.network-badge.mtn {
    background: #ffc700;
    color: #000;
}
.network-badge.airtel {
    background: #e40000;
    color: white;
}
.network-details {
    font-size: 14px;
    color: #6b7280;
}
.form-group {
    margin-bottom: 25px;
}
.form-label {
    display: block;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 10px;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-label i {
    color: #2563eb;
}
.form-label .required {
    color: #ef4444;
}
.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.2s;
    font-family: inherit;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.form-textarea {
    resize: vertical;
    min-height: 100px;
}
.error-message {
    margin-top: 8px;
    color: #ef4444;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.transaction-fields {
    display: none;
    padding: 25px;
    background: #f9fafb;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    margin-top: 20px;
}
.transaction-fields.show {
    display: block;
    animation: fadeIn 0.3s ease-in;
}
.info-box {
    background: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 12px;
    padding: 20px;
    margin-top: 25px;
}
.info-box-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}
.info-box-icon {
    color: #2563eb;
    font-size: 22px;
    margin-top: 2px;
}
.info-box-text h4 {
    font-weight: 600;
    color: #1e40af;
    margin: 0 0 8px 0;
    font-size: 15px;
}
.info-box-text p {
    color: #1e3a8a;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}
.form-actions {
    margin-top: 35px;
    padding-top: 25px;
    border-top: 2px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}
.btn-cancel {
    padding: 14px 28px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    color: #374151;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-cancel:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}
.btn-submit {
    padding: 14px 32px;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}
.btn-submit:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@media (max-width: 1024px) {
    .payment-layout {
        grid-template-columns: 1fr;
    }
    .summary-card {
        position: relative;
        top: 0;
    }
}
</style>

<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="margin-bottom: 30px;">
        <h1 class="dashboard-title" style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-credit-card" style="color: #2563eb;"></i>
            <span>Complete Payment</span>
        </h1>
        <div class="breadcrumbs" style="margin-top: 10px;">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="{{ route('admin.school.subscriptions.index') }}" style="color: #2563eb; text-decoration: none;">Subscriptions</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Payment</span>
        </div>
    </div>

    <div class="payment-page">
        <div class="payment-layout">
            <!-- Summary Card -->
            <div class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3>Order Summary</h3>
                    <p>Review your subscription details</p>
                </div>

                <div class="summary-details">
                    <div class="summary-item">
                        <div class="summary-label">Package</div>
                        <div class="summary-value">{{ $subscription->subscriptionPackage->name }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Amount</div>
                        <div class="summary-amount">{{ number_format($subscription->amount_paid, 0) }} UGX</div>
                    </div>
                </div>

                <div class="summary-meta">
                    <div class="summary-meta-item">
                        <span class="summary-meta-label">Start Date</span>
                        <span class="summary-meta-value">{{ $subscription->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="summary-meta-item">
                        <span class="summary-meta-label">End Date</span>
                        <span class="summary-meta-value">{{ $subscription->end_date->format('M d, Y') }}</span>
                    </div>
                    <div class="summary-meta-item">
                        <span class="summary-meta-label">Duration</span>
                        <span class="summary-meta-value">{{ round($subscription->subscriptionPackage->duration_days / 30, 1) }} months</span>
                    </div>
                    <div class="summary-meta-item">
                        <span class="summary-meta-label">Annual Renewal</span>
                        <span class="summary-meta-value">1,000,000 UGX</span>
                    </div>
                </div>

                <div class="summary-footer">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Payment</span>
                </div>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-header">
                    <div class="form-header-content">
                        <div class="form-header-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="form-header-text">
                            <h3>Payment Information</h3>
                            <p>Choose your preferred payment method</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.school.subscriptions.process-payment', $subscription->id) }}" method="POST" class="form-body">
                    @csrf

                    <!-- Payment Method -->
                    <div class="form-group">
                        <label for="payment_method" class="form-label">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment Method <span class="required">*</span></span>
                        </label>
                        <select id="payment_method" name="payment_method" required class="form-select">
                            <option value="">Select Payment Method</option>
                            <option value="manual" {{ old('payment_method') == 'manual' ? 'selected' : '' }}>Manual Payment (Bank Transfer/Cash)</option>
                            <option value="flutterwave" {{ old('payment_method') == 'flutterwave' ? 'selected' : '' }}>Flutterwave (Card/Mobile Money)</option>
                            <option value="easypay" {{ old('payment_method') == 'easypay' ? 'selected' : '' }}>EasyPay (Mobile Money)</option>
                        </select>
                        @error('payment_method')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Transaction Fields (for manual payment) -->
                    <div id="transaction-fields" class="transaction-fields">
                        <!-- Bank Transfer/Cash Payment Details -->
                        <div class="payment-info-box">
                            <div class="payment-info-header">
                                <i class="fas fa-university"></i>
                                <h4>Bank Transfer / Cash Payment Details</h4>
                            </div>
                            <div class="payment-info-content">
                                <div class="payment-detail">
                                    <strong>Bank Name:</strong> {{ $companySettings->bank_name ?? 'Bank Name Not Set' }}
                                </div>
                                <div class="payment-detail">
                                    <strong>Account Name:</strong> {{ $companySettings->account_name ?? 'Account Name Not Set' }}
                                </div>
                                <div class="payment-detail">
                                    <strong>Account Number:</strong> {{ $companySettings->account_number ?? 'Account Number Not Set' }}
                                </div>
                                <div class="payment-note">
                                    <i class="fas fa-info-circle"></i>
                                    Please make payment to the above account details and provide transaction details below.
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="transaction_id" class="form-label">
                                <i class="fas fa-hashtag"></i>
                                <span>Transaction ID <span class="required">*</span></span>
                            </label>
                            <input type="text" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" class="form-input" placeholder="Enter transaction ID or receipt number" required>
                            @error('transaction_id')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_reference" class="form-label">
                                <i class="fas fa-file-invoice"></i>
                                <span>Payment Reference</span>
                            </label>
                            <input type="text" id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" class="form-input" placeholder="Enter payment reference (optional)">
                            @error('payment_reference')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone Field (for Easypay) -->
                    <div id="phone-fields" class="transaction-fields" style="display: none;">
                        <!-- Mobile Money Payment Details -->
                        <div class="payment-info-box">
                            <div class="payment-info-header">
                                <i class="fas fa-mobile-alt"></i>
                                <h4>Mobile Money Payment Details</h4>
                            </div>
                            <div class="payment-info-content">
                                <div class="mobile-money-options">
                                    <div class="mobile-money-option">
                                        <div class="network-badge mtn">
                                            <i class="fas fa-mobile"></i>
                                            MTN
                                        </div>
                                        <div class="network-details">
                                            <div><strong>Registered Name:</strong> {{ $companySettings->mtn_registered_name ?? 'Name Not Set' }}</div>
                                            <div><strong>Phone:</strong> {{ $companySettings->mtn_mobile_number ?? 'MTN Number Not Set' }}</div>
                                        </div>
                                    </div>
                                    <div class="mobile-money-option">
                                        <div class="network-badge airtel">
                                            <i class="fas fa-mobile"></i>
                                            Airtel
                                        </div>
                                        <div class="network-details">
                                            <div><strong>Registered Name:</strong> {{ $companySettings->airtel_registered_name ?? 'Name Not Set' }}</div>
                                            <div><strong>Phone:</strong> {{ $companySettings->airtel_mobile_number ?? 'Airtel Number Not Set' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-note">
                                    <i class="fas fa-info-circle"></i>
                                    Select your mobile money network and use the corresponding phone number for payment.
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i>
                                <span>Mobile Money Phone Number <span class="required">*</span></span>
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone_number ?? '') }}" class="form-input" placeholder="Enter your mobile money phone number">
                            <small style="color: #6b7280; font-size: 13px; margin-top: 5px; display: block;">Enter the phone number registered with your mobile money account</small>
                            @error('phone')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes" class="form-label">
                            <i class="fas fa-sticky-note"></i>
                            <span>Additional Notes <span style="color: #6b7280; font-weight: normal;">(Optional)</span></span>
                        </label>
                        <textarea id="notes" name="notes" rows="4" class="form-textarea" placeholder="Any additional notes about this payment...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div id="payment-info-box" class="info-box">
                        <div class="info-box-content">
                            <i class="fas fa-info-circle info-box-icon"></i>
                            <div class="info-box-text">
                                <h4 id="info-title">Payment Instructions</h4>
                                <p id="info-message">
                                    Please select a payment method to see specific instructions.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('admin.school.subscriptions.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i>
                            <span>Complete Payment</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    const transactionFields = document.getElementById('transaction-fields');
    const phoneFields = document.getElementById('phone-fields');
    const paymentMethod = this.value;
    const infoTitle = document.getElementById('info-title');
    const infoMessage = document.getElementById('info-message');

    // Hide all fields first
    transactionFields.style.display = 'none';
    transactionFields.classList.remove('show');
    phoneFields.style.display = 'none';
    phoneFields.classList.remove('show');

    if (paymentMethod === 'manual') {
        transactionFields.style.display = 'block';
        transactionFields.classList.add('show');
        infoTitle.textContent = 'Manual Payment Instructions';
        infoMessage.innerHTML = 'For <strong>manual payments</strong> (bank transfer or cash), please use the account details provided above and enter your transaction details below. Your payment will be reviewed by an administrator and you will be notified once it is approved. Access will be granted only after approval.';
    } else if (paymentMethod === 'easypay') {
        phoneFields.style.display = 'block';
        phoneFields.classList.add('show');
        infoTitle.textContent = 'EasyPay Payment Instructions';
        infoMessage.innerHTML = 'For <strong>EasyPay payments</strong>, select your mobile money network from the options above and enter your phone number below. You will receive a prompt on your phone to complete the payment. Your subscription will be activated automatically once payment is confirmed.';
    } else if (paymentMethod === 'flutterwave') {
        infoTitle.textContent = 'Flutterwave Payment Instructions';
        infoMessage.innerHTML = 'For <strong>Flutterwave payments</strong>, you will be redirected to a secure payment page where you can pay using your card or mobile money. Your subscription will be activated automatically once payment is confirmed.';
    } else {
        infoTitle.textContent = 'Payment Instructions';
        infoMessage.textContent = 'Please select a payment method to see specific instructions.';
    }
});

window.addEventListener('load', function() {
    const paymentMethod = document.getElementById('payment_method').value;
    if (paymentMethod) {
        document.getElementById('payment_method').dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
