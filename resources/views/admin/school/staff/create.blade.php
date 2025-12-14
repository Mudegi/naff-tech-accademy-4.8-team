@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner staff-create-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                Add Staff Member
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.staff.index') }}" class="breadcrumb-link">Staff</a></span> <span class="breadcrumb-sep">/</span> 
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

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-header">
            <div class="form-header-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <h2 class="form-title">Staff Information</h2>
                <p class="form-subtitle">Fill in the details to create a new staff member account</p>
            </div>
        </div>

        <form action="{{ route('admin.school.staff.store') }}" method="POST" class="staff-form">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user form-section-icon"></i>
                    <h3 class="form-section-title">Personal Information</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-user-circle form-label-icon"></i>
                            Full Name <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="Enter full name"
                                   class="form-input">
                            <i class="fas fa-user form-input-icon"></i>
                        </div>
                        @error('name')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope form-label-icon"></i>
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="Enter email address"
                                   class="form-input">
                            <i class="fas fa-envelope form-input-icon"></i>
                        </div>
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
                            Phone Number
                        </label>
                        <div class="form-input-wrapper">
                            <input type="text" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number') }}"
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

            <!-- Role & Department Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-briefcase form-section-icon"></i>
                    <h3 class="form-section-title">Role & Department</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="account_type" class="form-label">
                            <i class="fas fa-user-tag form-label-icon"></i>
                            Role <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <select id="account_type" 
                                    name="account_type" 
                                    required
                                    class="form-input form-select">
                                <option value="">Select Role</option>
                                @foreach($availableRoles as $key => $label)
                                    <option value="{{ $key }}" {{ old('account_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down form-select-icon"></i>
                        </div>
                        @error('account_type')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group" id="department-field" style="display: none;">
                        <label for="department_id" class="form-label">
                            <i class="fas fa-building form-label-icon"></i>
                            Department
                        </label>
                        <div class="form-input-wrapper">
                            <select id="department_id" 
                                    name="department_id" 
                                    class="form-input form-select">
                                <option value="">Select Department (Optional)</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $selectedDepartmentId ?? null) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}@if($department->code) ({{ $department->code }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down form-select-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Assign to a department (for Head of Department and Subject Teachers)
                        </p>
                        @error('department_id')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Teaching Assignment Section -->
            <div class="form-section" id="teaching-assignment-section" style="display: none;">
                <div class="form-section-header">
                    <i class="fas fa-chalkboard-teacher form-section-icon"></i>
                    <h3 class="form-section-title">Teaching Assignment</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="classes" class="form-label">
                            <i class="fas fa-users-class form-label-icon"></i>
                            Classes
                        </label>
                        <div class="form-input-wrapper">
                            <select id="classes" 
                                    name="classes[]" 
                                    multiple 
                                    class="form-input form-select"
                                    style="height: 120px;">
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-users-class form-input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Hold Ctrl (Cmd on Mac) to select multiple classes
                        </p>
                        @error('classes')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group" id="subjects-field">
                        <label for="subjects" class="form-label">
                            <i class="fas fa-book form-label-icon"></i>
                            Subjects
                        </label>
                        <div class="form-input-wrapper">
                            <select id="subjects" 
                                    name="subjects[]" 
                                    multiple 
                                    class="form-input form-select"
                                    style="height: 120px;">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-book form-input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Hold Ctrl (Cmd on Mac) to select multiple subjects
                        </p>
                        @error('subjects')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-lock form-section-icon"></i>
                    <h3 class="form-section-title">Security</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-key form-label-icon"></i>
                            Password <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   minlength="8"
                                   placeholder="Enter password (min. 8 characters)"
                                   class="form-input">
                            <i class="fas fa-lock form-input-icon"></i>
                        </div>
                        @error('password')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="form-help">
                            <i class="fas fa-shield-alt"></i>
                            Minimum 8 characters required
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-key form-label-icon"></i>
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   minlength="8"
                                   placeholder="Confirm password"
                                   class="form-input">
                            <i class="fas fa-lock form-input-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.school.staff.index') }}" class="btn-cancel">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Staff Member
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Staff Create Page Styles */
.staff-create-page {
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
.staff-form {
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
@media (max-width: 768px) {
    .form-header {
        flex-direction: column;
        text-align: center;
    }
    
    .form-grid {
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
    
    .staff-form {
        padding: 1.5rem;
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
    // Show/hide fields based on role selection
    document.getElementById('account_type').addEventListener('change', function() {
        const departmentField = document.getElementById('department-field');
        const teachingAssignmentSection = document.getElementById('teaching-assignment-section');
        const subjectsField = document.getElementById('subjects-field');
        const role = this.value;
        
        // Show department field for HOD and Subject Teacher roles
        if (role === 'head_of_department' || role === 'subject_teacher') {
            departmentField.style.display = 'block';
            // Add smooth animation
            setTimeout(() => {
                departmentField.style.opacity = '1';
            }, 10);
        } else {
            departmentField.style.opacity = '0';
            setTimeout(() => {
                departmentField.style.display = 'none';
            }, 200);
            document.getElementById('department_id').value = '';
        }

        // Show teaching assignment section for teachers, subject teachers, and HODs
        if (role === 'teacher' || role === 'subject_teacher' || role === 'head_of_department') {
            teachingAssignmentSection.style.display = 'block';
            setTimeout(() => {
                teachingAssignmentSection.style.opacity = '1';
            }, 10);

            // Show subjects field only for subject teachers and HODs
            if (role === 'subject_teacher' || role === 'head_of_department') {
                subjectsField.style.display = 'block';
            } else {
                subjectsField.style.display = 'none';
                document.getElementById('subjects').selectedIndex = -1; // Clear selection
            }
        } else {
            teachingAssignmentSection.style.opacity = '0';
            setTimeout(() => {
                teachingAssignmentSection.style.display = 'none';
            }, 200);
            // Clear selections
            document.getElementById('classes').selectedIndex = -1;
            document.getElementById('subjects').selectedIndex = -1;
        }
    });
    
    // Trigger on page load if old value exists or fields are pre-selected
    window.addEventListener('load', function() {
        const accountType = document.getElementById('account_type').value;
        const departmentId = document.getElementById('department_id').value;
        
        // If department is pre-selected, show the field and auto-select appropriate role
        if (departmentId && (accountType === 'head_of_department' || accountType === 'subject_teacher')) {
            document.getElementById('department-field').style.display = 'block';
        } else if (departmentId) {
            // If department is pre-selected but role isn't HOD or Teacher, auto-select Subject Teacher
            document.getElementById('account_type').value = 'subject_teacher';
            document.getElementById('department-field').style.display = 'block';
        } else if (accountType === 'head_of_department' || accountType === 'subject_teacher') {
            document.getElementById('department-field').style.display = 'block';
        }

        // Handle teaching assignment section visibility on load
        if (accountType === 'teacher' || accountType === 'subject_teacher' || accountType === 'head_of_department') {
            document.getElementById('teaching-assignment-section').style.display = 'block';
            
            // Handle subjects field visibility
            if (accountType === 'subject_teacher' || accountType === 'head_of_department') {
                document.getElementById('subjects-field').style.display = 'block';
            } else {
                document.getElementById('subjects-field').style.display = 'none';
            }
        }
    });
</script>
@endsection
