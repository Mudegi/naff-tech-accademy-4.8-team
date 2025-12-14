@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner department-form-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-plus-circle text-orange-600 mr-3"></i>
                Create New Department
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.departments.index') }}">Departments</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Create</span>
            </div>
        </div>
        <a href="{{ route('admin.school.departments.index') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Departments
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card-modern">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h2 class="form-card-title">Department Information</h2>
                <p class="form-card-subtitle">Fill in the details to create a new department</p>
            </div>
        </div>

        <form action="{{ route('admin.school.departments.store') }}" method="POST" class="form-modern">
            @csrf

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-info-circle section-icon"></i>
                    <h3 class="form-section-title">Basic Information</h3>
                </div>
                <div class="form-fields">
                    <div class="form-group-modern">
                        <label for="name" class="form-label-modern">
                            <i class="fas fa-building mr-2"></i>
                            Department Name <span class="required-star">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               class="form-input-modern"
                               placeholder="e.g., Mathematics, Science, Languages">
                        @error('name')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="code" class="form-label-modern">
                            <i class="fas fa-code mr-2"></i>
                            Department Code
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}"
                               class="form-input-modern form-input-code"
                               placeholder="e.g., MATH, SCI, LANG">
                        <div class="form-help-text">
                            <i class="fas fa-info-circle mr-1"></i>
                            Optional abbreviation for the department
                        </div>
                        @error('code')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="description" class="form-label-modern">
                            <i class="fas fa-align-left mr-2"></i>
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="form-textarea-modern"
                                  placeholder="Brief description of the department and its purpose...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user-tie section-icon"></i>
                    <h3 class="form-section-title">Head of Department</h3>
                </div>
                <div class="form-fields">
                    <div class="form-group-modern">
                        <label for="head_of_department_id" class="form-label-modern">
                            <i class="fas fa-user-tie mr-2"></i>
                            Select Head of Department
                        </label>
                        <div class="select-wrapper">
                            <select id="head_of_department_id" 
                                    name="head_of_department_id" 
                                    class="form-select-modern">
                                <option value="">Select Head of Department (Optional)</option>
                                @foreach($availableHODs as $hod)
                                    <option value="{{ $hod->id }}" {{ old('head_of_department_id') == $hod->id ? 'selected' : '' }}>
                                        {{ $hod->name }} ({{ $hod->email }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                        <div class="form-help-text">
                            <i class="fas fa-info-circle mr-1"></i>
                            Select a Head of Department to lead this department. You can assign this later.
                        </div>
                        @error('head_of_department_id')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-toggle-on section-icon"></i>
                    <h3 class="form-section-title">Status</h3>
                </div>
                <div class="form-fields">
                    <div class="form-group-modern">
                        <label class="checkbox-modern">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="checkbox-input">
                            <span class="checkbox-label">
                                <i class="fas fa-check checkbox-icon"></i>
                                <span class="checkbox-text">
                                    <span class="checkbox-title">Active Department</span>
                                    <span class="checkbox-description">Enable this department to be active and functional</span>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.school.departments.index') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-check mr-2"></i> Create Department
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Department Form Page Styles */
.department-form-page {
    padding: 1.5rem;
    max-width: 900px;
    margin: 0 auto;
}

/* Form Card Modern */
.form-card-modern {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    margin-top: 1.5rem;
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f3f4f6;
}

.form-card-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.form-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.25rem 0;
}

.form-card-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* Form Modern */
.form-modern {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-section {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.form-section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.form-section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.form-fields {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group-modern {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label-modern {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
}

.required-star {
    color: #ef4444;
    margin-left: 0.25rem;
}

.form-input-modern,
.form-textarea-modern,
.form-select-modern {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background: white;
    color: #1a1a1a;
}

.form-input-modern:focus,
.form-textarea-modern:focus,
.form-select-modern:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input-code {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-textarea-modern {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.select-wrapper {
    position: relative;
}

.select-wrapper .form-select-modern {
    appearance: none;
    padding-right: 2.5rem;
}

.select-arrow {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
    z-index: 1;
}

.form-help-text {
    font-size: 0.8125rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    margin-top: 0.25rem;
}

.form-error {
    font-size: 0.8125rem;
    color: #ef4444;
    display: flex;
    align-items: center;
    margin-top: 0.25rem;
    padding: 0.5rem;
    background: #fef2f2;
    border-radius: 0.375rem;
    border-left: 3px solid #ef4444;
}

/* Checkbox Modern */
.checkbox-modern {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    padding: 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.checkbox-modern:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.checkbox-input {
    display: none;
}

.checkbox-input:checked + .checkbox-label {
    color: #1a1a1a;
}

.checkbox-input:checked + .checkbox-label .checkbox-icon {
    opacity: 1;
    transform: scale(1);
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    width: 100%;
}

.checkbox-icon {
    width: 20px;
    height: 20px;
    border: 2px solid #3b82f6;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #3b82f6;
    color: white;
    font-size: 0.75rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease;
}

.checkbox-input:checked + .checkbox-label .checkbox-icon {
    opacity: 1;
    transform: scale(1);
}

.checkbox-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.checkbox-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
}

.checkbox-description {
    font-size: 0.8125rem;
    color: #6b7280;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f3f4f6;
    margin-top: 1rem;
}

/* Reuse button styles from index */
.btn-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .department-form-page {
        padding: 1rem;
    }
    
    .form-card-modern {
        padding: 1.5rem;
    }
    
    .form-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .form-actions .btn-modern {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush
@endsection

