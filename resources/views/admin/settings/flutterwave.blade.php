@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="header">
        <div class="header-content">
            <div>
                <h1>Flutterwave Settings</h1>
                <p>Configure your payment gateway settings</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.flutterwave.update') }}">
                @csrf
                
                <div class="form-group">
                    <label for="public_key">Public Key</label>
                    <input type="text" id="public_key" name="public_key" 
                        value="{{ old('public_key', $settings->public_key ?? '') }}" 
                        class="form-input @error('public_key') is-invalid @enderror" required>
                    @error('public_key')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="secret_key">Secret Key</label>
                    <input type="password" id="secret_key" name="secret_key" 
                        value="{{ old('secret_key', $settings->secret_key ?? '') }}" 
                        class="form-input @error('secret_key') is-invalid @enderror" required>
                    @error('secret_key')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="encryption_key">Encryption Key</label>
                    <input type="password" id="encryption_key" name="encryption_key" 
                        value="{{ old('encryption_key', $settings->encryption_key ?? '') }}" 
                        class="form-input @error('encryption_key') is-invalid @enderror" required>
                    @error('encryption_key')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="webhook_secret">Webhook Secret</label>
                    <input type="password" id="webhook_secret" name="webhook_secret" 
                        value="{{ old('webhook_secret', $settings->webhook_secret ?? '') }}" 
                        class="form-input @error('webhook_secret') is-invalid @enderror">
                    @error('webhook_secret')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="currency_code">Currency Code</label>
                    <input type="text" id="currency_code" name="currency_code" 
                        value="{{ old('currency_code', $settings->currency_code ?? 'UGX') }}" 
                        class="form-input @error('currency_code') is-invalid @enderror" 
                        maxlength="3" required>
                    @error('currency_code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="test_mode" value="1" 
                            {{ old('test_mode', $settings->test_mode ?? true) ? 'checked' : '' }}>
                        Test Mode
                    </label>
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

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
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
</style>
@endsection 