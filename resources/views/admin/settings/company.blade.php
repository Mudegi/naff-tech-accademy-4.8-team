@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="header">
        <div class="header-content">
            <div>
                <h1>Company Settings</h1>
                <p>Manage your company information and preferences</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" 
                        value="{{ old('company_name', $settings->company_name ?? '') }}" 
                        class="form-input @error('company_name') is-invalid @enderror" required>
                    @error('company_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_email">Company Email</label>
                    <input type="email" id="company_email" name="company_email" 
                        value="{{ old('company_email', $settings->company_email ?? '') }}" 
                        class="form-input @error('company_email') is-invalid @enderror" required>
                    @error('company_email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_phone">Company Phone</label>
                    <input type="text" id="company_phone" name="company_phone" 
                        value="{{ old('company_phone', $settings->company_phone ?? '') }}" 
                        class="form-input @error('company_phone') is-invalid @enderror">
                    @error('company_phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_address">Company Address</label>
                    <textarea id="company_address" name="company_address" 
                        class="form-input @error('company_address') is-invalid @enderror">{{ old('company_address', $settings->company_address ?? '') }}</textarea>
                    @error('company_address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_logo">Company Logo</label>
                    <input type="file" id="company_logo" name="company_logo" 
                        class="form-input @error('company_logo') is-invalid @enderror">
                    @error('company_logo')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    @if(isset($settings->company_logo))
                        <div class="current-logo">
                            <img src="{{ asset('storage/' . $settings->company_logo) }}" alt="Current Logo" style="max-width: 200px; margin-top: 10px;">
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="company_website">Company Website</label>
                    <input type="url" id="company_website" name="company_website" 
                        value="{{ old('company_website', $settings->company_website ?? '') }}" 
                        class="form-input @error('company_website') is-invalid @enderror">
                    @error('company_website')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_description">Company Description</label>
                    <textarea id="company_description" name="company_description" 
                        class="form-input @error('company_description') is-invalid @enderror">{{ old('company_description', $settings->company_description ?? '') }}</textarea>
                    @error('company_description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tax_number">Tax Number</label>
                    <input type="text" id="tax_number" name="tax_number" 
                        value="{{ old('tax_number', $settings->tax_number ?? '') }}" 
                        class="form-input @error('tax_number') is-invalid @enderror">
                    @error('tax_number')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency" class="form-input @error('currency') is-invalid @enderror">
                        <option value="UGX" {{ (old('currency', $settings->currency ?? '') == 'UGX') ? 'selected' : '' }}>UGX</option>
                        <option value="USD" {{ (old('currency', $settings->currency ?? '') == 'USD') ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ (old('currency', $settings->currency ?? '') == 'EUR') ? 'selected' : '' }}>EUR</option>
                    </select>
                    @error('currency')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" name="timezone" class="form-input @error('timezone') is-invalid @enderror">
                        <option value="Africa/Kampala" {{ (old('timezone', $settings->timezone ?? '') == 'Africa/Kampala') ? 'selected' : '' }}>Africa/Kampala</option>
                        <option value="UTC" {{ (old('timezone', $settings->timezone ?? '') == 'UTC') ? 'selected' : '' }}>UTC</option>
                    </select>
                    @error('timezone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 20px; color: #2563eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                    <i class="fas fa-credit-card"></i> Payment Details
                </h3>

                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name"
                        value="{{ old('bank_name', $settings->bank_name ?? '') }}"
                        class="form-input @error('bank_name') is-invalid @enderror"
                        placeholder="e.g., Centenary Bank">
                    @error('bank_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" id="account_name" name="account_name"
                        value="{{ old('account_name', $settings->account_name ?? '') }}"
                        class="form-input @error('account_name') is-invalid @enderror"
                        placeholder="e.g., Naf Academy Ltd">
                    @error('account_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" id="account_number" name="account_number"
                        value="{{ old('account_number', $settings->account_number ?? '') }}"
                        class="form-input @error('account_number') is-invalid @enderror"
                        placeholder="e.g., 1234567890">
                    @error('account_number')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mtn_registered_name">MTN Registered Name</label>
                    <input type="text" id="mtn_registered_name" name="mtn_registered_name"
                        value="{{ old('mtn_registered_name', $settings->mtn_registered_name ?? '') }}"
                        class="form-input @error('mtn_registered_name') is-invalid @enderror"
                        placeholder="e.g., Naf Academy MTN">
                    @error('mtn_registered_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mtn_mobile_number">MTN Mobile Money Number</label>
                    <input type="text" id="mtn_mobile_number" name="mtn_mobile_number"
                        value="{{ old('mtn_mobile_number', $settings->mtn_mobile_number ?? '') }}"
                        class="form-input @error('mtn_mobile_number') is-invalid @enderror"
                        placeholder="e.g., +256 700 000 000">
                    @error('mtn_mobile_number')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="airtel_registered_name">Airtel Registered Name</label>
                    <input type="text" id="airtel_registered_name" name="airtel_registered_name"
                        value="{{ old('airtel_registered_name', $settings->airtel_registered_name ?? '') }}"
                        class="form-input @error('airtel_registered_name') is-invalid @enderror"
                        placeholder="e.g., Naf Academy Airtel">
                    @error('airtel_registered_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="airtel_mobile_number">Airtel Mobile Money Number</label>
                    <input type="text" id="airtel_mobile_number" name="airtel_mobile_number"
                        value="{{ old('airtel_mobile_number', $settings->airtel_mobile_number ?? '') }}"
                        class="form-input @error('airtel_mobile_number') is-invalid @enderror"
                        placeholder="e.g., +256 750 000 000">
                    @error('airtel_mobile_number')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">
                        <i class="fas fa-save"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    {{ session('error') }}
</div>
@endif

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    color: #333;
}

.header p {
    margin: 0;
    color: #666;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-input:focus {
    outline: none;
    border-color: #007bff;
}

.form-input.is-invalid {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.submit-button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
}

.submit-button:hover {
    background: #0056b3;
}

.alert {
    padding: 16px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

textarea.form-input {
    min-height: 100px;
    resize: vertical;
}

.current-logo {
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}
</style>
@endsection 