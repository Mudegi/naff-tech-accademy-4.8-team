@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner student-create-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                Add New Student
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.students.index') }}" class="breadcrumb-link">Students</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Create</span>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-error mb-6 animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Import Option Banner -->
    <div class="import-banner mb-6">
        <div class="import-banner-content">
            <div class="import-banner-icon">
                <i class="fas fa-file-import"></i>
            </div>
            <div class="import-banner-text">
                <h3 class="import-banner-title">Bulk Import Students</h3>
                <p class="import-banner-desc">Need to import many students at once? Use our import feature to add hundreds or thousands of students quickly from CSV or Excel files.</p>
            </div>
            <div class="import-banner-actions">
                <a href="{{ route('admin.school.students.import') }}" class="btn-import btn-import-primary">
                    <i class="fas fa-file-excel mr-2"></i>
                    Import from Excel
                </a>
                <a href="{{ route('admin.school.students.import') }}" class="btn-import btn-import-secondary">
                    <i class="fas fa-file-csv mr-2"></i>
                    Import from CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-header">
            <div class="form-header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <h2 class="form-title">Student Information</h2>
                <p class="form-subtitle">Fill in the details to create a new student account</p>
            </div>
        </div>

        <form action="{{ route('admin.school.students.store') }}" method="POST" class="student-form">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user form-section-icon"></i>
                    <h3 class="form-section-title">Personal Information</h3>
                </div>
                
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="first_name" class="form-label">
                            <i class="fas fa-user-circle form-label-icon"></i>
                            First Name <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}"
                                   required
                                   placeholder="Enter first name"
                                   class="form-input">
                            <i class="fas fa-user form-input-icon"></i>
                        </div>
                        @error('first_name')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="middle_name" class="form-label">
                            <i class="fas fa-user form-label-icon"></i>
                            Middle Name
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="middle_name" 
                                   name="middle_name" 
                                   value="{{ old('middle_name') }}"
                                   placeholder="Enter middle name (optional)"
                                   class="form-input">
                            <i class="fas fa-user form-input-icon"></i>
                        </div>
                        @error('middle_name')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">
                            <i class="fas fa-user-circle form-label-icon"></i>
                            Last Name <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}"
                                   required
                                   placeholder="Enter last name"
                                   class="form-input">
                            <i class="fas fa-user form-input-icon"></i>
                        </div>
                        @error('last_name')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-address-book form-section-icon"></i>
                    <h3 class="form-section-title">Contact Information</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope form-label-icon"></i>
                            Email Address
                        </label>
                        <div class="form-input-wrapper">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="Enter email address (optional)"
                                   class="form-input">
                            <i class="fas fa-envelope form-input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Optional, but recommended for better communication
                        </p>
                        @error('email')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone_number" class="form-label">
                            <i class="fas fa-phone form-label-icon"></i>
                            Phone Number <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number') }}"
                                   required
                                   placeholder="Enter phone number"
                                   class="form-input">
                            <i class="fas fa-phone form-input-icon"></i>
                        </div>
                        @error('phone_number')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Academic Information Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-graduation-cap form-section-icon"></i>
                    <h3 class="form-section-title">Academic Information</h3>
                </div>
                
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="registration_number" class="form-label">
                            <i class="fas fa-id-card form-label-icon"></i>
                            Registration Number <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="registration_number" 
                                   name="registration_number" 
                                   value="{{ old('registration_number') }}"
                                   required
                                   placeholder="Enter registration number"
                                   class="form-input">
                            <i class="fas fa-id-card form-input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Unique student registration number
                        </p>
                        @error('registration_number')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="class" class="form-label">
                            <i class="fas fa-chalkboard form-label-icon"></i>
                            Class <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <select id="class" 
                                    name="class" 
                                    required
                                    class="form-input form-select">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->name }}" {{ old('class') == $class->name ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down form-select-icon"></i>
                        </div>
                        @error('class')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="level" class="form-label">
                            <i class="fas fa-layer-group form-label-icon"></i>
                            Academic Level <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <select id="level"
                                    name="level"
                                    class="form-input form-select"
                                    required
                                    onchange="toggleCombinationField()">
                                <option value="">Select Level</option>
                                @foreach($levels as $value => $label)
                                    <option value="{{ $value }}" {{ old('level') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down form-select-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Determines which subjects and papers apply to this student (O Level = UCE, A Level = UACE).
                        </p>
                        @error('level')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group" id="combination-group" style="display: none;">
                        <label for="combination" class="form-label">
                            <i class="fas fa-book form-label-icon"></i>
                            Subject Combination <span class="required" id="combination-required" style="display: none;">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="combination" 
                                   name="combination" 
                                   value="{{ old('combination') }}"
                                   placeholder="e.g., PCM/ICT, BCM/ICT, PEM/ICT"
                                   class="form-input">
                            <i class="fas fa-book form-input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Enter the subject combination in short form (e.g., PCM/ICT for Physics, Chemistry, Mathematics with ICT as subsidiary).
                        </p>
                        @error('combination')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">
                            <i class="fas fa-calendar form-label-icon"></i>
                            Date of Birth
                        </label>
                        <div class="form-input-wrapper">
                            <input type="date" 
                                   id="date_of_birth" 
                                   name="date_of_birth" 
                                   value="{{ old('date_of_birth') }}"
                                   class="form-input">
                            <i class="fas fa-calendar form-input-icon"></i>
                        </div>
                        @error('date_of_birth')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Login Credentials Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-lock form-section-icon"></i>
                    <h3 class="form-section-title">Login Credentials</h3>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-key form-label-icon"></i>
                        Password <span class="required">*</span>
                    </label>
                    <div class="password-input-wrapper">
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="password" 
                                   name="password" 
                                   value="{{ old('password', \Illuminate\Support\Str::random(8)) }}"
                                   required
                                   placeholder="Enter password"
                                   class="form-input">
                            <i class="fas fa-lock form-input-icon"></i>
                        </div>
                        <button type="button" onclick="generatePassword()" class="btn-generate-password" title="Generate new password">
                            <i class="fas fa-sync-alt"></i>
                            Generate
                        </button>
                    </div>
                    <p class="form-help">
                        <i class="fas fa-shield-alt"></i>
                        Default password is auto-generated. You can change it or generate a new one.
                    </p>
                    @error('password')
                        <p class="form-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Status Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-toggle-on form-section-icon"></i>
                    <h3 class="form-section-title">Account Status</h3>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="checkbox-input">
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">
                            <i class="fas fa-check-circle"></i>
                            Active (Student can log in immediately)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.school.students.index') }}" class="btn-cancel">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Student
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Student Create Page Styles */
.student-create-page {
    padding: 0 0 32px 0;
}

.breadcrumb-link {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-link:hover {
    color: #2563eb;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

/* Import Banner */
.import-banner {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border-radius: 1rem;
    border-left: 4px solid #3b82f6;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

.import-banner-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    flex-wrap: wrap;
}

.import-banner-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.import-banner-icon {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.import-banner-text {
    flex: 1;
    min-width: 250px;
}

.import-banner-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.5rem 0;
}

.import-banner-desc {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.btn-import {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s;
    white-space: nowrap;
    border: none;
    cursor: pointer;
}

.btn-import-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.btn-import-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(16, 185, 129, 0.4);
}

.btn-import-secondary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-import-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    margin-top: 1.5rem;
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.form-header-icon {
    width: 64px;
    height: 64px;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
}

.form-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.form-subtitle {
    font-size: 0.875rem;
    opacity: 0.9;
    margin: 0;
}

/* Form Sections */
.student-form {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f3f4f6;
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.form-section-icon {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

/* Form Groups */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-label-icon {
    color: #3b82f6;
    font-size: 1rem;
}

.required {
    color: #ef4444;
}

.form-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #1a1a1a;
    background: white;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

.form-input-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    pointer-events: none;
    transition: color 0.2s;
}

.form-input:focus + .form-input-icon,
.form-input-wrapper:focus-within .form-input-icon {
    color: #3b82f6;
}

/* Select Input */
.form-select {
    appearance: none;
    padding-right: 2.75rem;
    cursor: pointer;
}

.form-select-icon {
    position: absolute;
    right: 1rem;
    color: #9ca3af;
    pointer-events: none;
}

/* Password Input Wrapper */
.password-input-wrapper {
    display: flex;
    gap: 0.75rem;
}

.password-input-wrapper .form-input-wrapper {
    flex: 1;
}

.btn-generate-password {
    padding: 0.75rem 1rem;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-generate-password:hover {
    background: #e5e7eb;
    border-color: #3b82f6;
    color: #3b82f6;
}

.btn-generate-password i {
    transition: transform 0.3s;
}

.btn-generate-password:hover i {
    transform: rotate(180deg);
}

/* Checkbox */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 2px solid #e5e7eb;
    transition: all 0.2s;
}

.checkbox-label:hover {
    background: #f3f4f6;
    border-color: #3b82f6;
}

.checkbox-input {
    display: none;
}

.checkbox-input:checked + .checkbox-custom {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-color: #3b82f6;
}

.checkbox-input:checked + .checkbox-custom::after {
    opacity: 1;
    transform: scale(1);
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 0.25rem;
    background: white;
    position: relative;
    transition: all 0.2s;
    flex-shrink: 0;
}

.checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    color: white;
    font-size: 0.875rem;
    font-weight: bold;
    opacity: 0;
    transition: all 0.2s;
}

.checkbox-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.checkbox-text i {
    color: #3b82f6;
}

/* Form Help Text */
.form-help {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.form-help i {
    color: #3b82f6;
}

/* Form Error */
.form-error {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.5rem;
}

.form-error i {
    font-size: 0.875rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #f3f4f6;
}

.btn-cancel,
.btn-submit {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-cancel:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.btn-submit {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

.btn-submit:active {
    transform: translateY(0);
}

/* Alert Styles */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .form-grid-3 {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .form-header {
        flex-direction: column;
        text-align: center;
    }
    
    .form-grid,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .btn-cancel,
    .btn-submit {
        width: 100%;
        justify-content: center;
    }
    
    .student-form {
        padding: 1.5rem;
    }
    
    .import-banner-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .import-banner-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-import {
        width: 100%;
        justify-content: center;
    }
    
    .password-input-wrapper {
        flex-direction: column;
    }
    
    .btn-generate-password {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .form-title {
        font-size: 1.5rem;
    }
    
    .form-header {
        padding: 1.5rem;
    }
    
    .form-header-icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
    }
}
</style>

<script>
function toggleCombinationField() {
    const levelSelect = document.getElementById('level');
    const classSelect = document.getElementById('class');
    const combinationGroup = document.getElementById('combination-group');
    const combinationInput = document.getElementById('combination');
    const combinationRequired = document.getElementById('combination-required');
    
    const level = levelSelect.value;
    const className = classSelect.value || '';
    const isS5OrS6 = /S\.?[56]/i.test(className);
    const isALevel = level === 'A Level';
    
    // Show combination field if A Level or class is S.5/S.6
    if (isALevel || isS5OrS6) {
        combinationGroup.style.display = 'block';
        combinationInput.required = true;
        combinationRequired.style.display = 'inline';
    } else {
        combinationGroup.style.display = 'none';
        combinationInput.required = false;
        combinationRequired.style.display = 'none';
        combinationInput.value = ''; // Clear value when hidden
    }
}

// Also check when class changes
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class');
    if (classSelect) {
        classSelect.addEventListener('change', toggleCombinationField);
    }
    // Initial check on page load
    toggleCombinationField();
});

function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    const passwordInput = document.getElementById('password');
    passwordInput.value = password;
    
    // Add visual feedback
    passwordInput.style.borderColor = '#10b981';
    setTimeout(() => {
        passwordInput.style.borderColor = '';
    }, 1000);
}
</script>
@endsection
