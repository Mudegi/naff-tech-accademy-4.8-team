@extends('frontend.layouts.app')

@section('title', 'Profile & Settings')

@section('styles')
<style>
    .profile-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .profile-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .profile-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }
    
    .profile-header p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
    }
    
    .profile-body {
        padding: 2rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #555;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #667eea;
    }
    
    .form-input:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .error-text {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .password-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f0f0f0;
    }
    
    .info-text {
        color: #666;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h1>Profile & Settings</h1>
            <p>Manage your account information and security</p>
        </div>
        
        <div class="profile-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Please correct the following errors:</strong>
                    <ul style="margin: 0.5rem 0 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('parent.profile.update') }}">
                @csrf
                
                <div class="section-title">Account Information</div>
                
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input" 
                           value="{{ old('name', $parent->name) }}" 
                           required>
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-input" 
                           value="{{ old('email', $parent->email) }}" 
                           required>
                    <p class="info-text">Used for notifications and account recovery</p>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="phone_number">Phone Number</label>
                    <input type="text" 
                           id="phone_number" 
                           name="phone_number" 
                           class="form-input" 
                           value="{{ old('phone_number', $parent->phone_number) }}" 
                           required>
                    <p class="info-text">Used for SMS notifications and teacher communication</p>
                    @error('phone_number')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="school_name">School</label>
                    <input type="text" 
                           id="school_name" 
                           class="form-input" 
                           value="{{ $parent->school->name ?? 'N/A' }}" 
                           disabled>
                </div>
                
                <div class="password-section">
                    <div class="section-title">Change Password</div>
                    <p class="info-text" style="margin-bottom: 1.5rem;">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-input">
                        <p class="info-text">Required to change your password</p>
                        @error('current_password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-input">
                        <p class="info-text">At least 8 characters</p>
                        @error('new_password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               class="form-input">
                        @error('new_password_confirmation')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-top: 2rem; text-align: center;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
