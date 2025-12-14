@extends('layouts.student-dashboard')

@section('content')
<div class="create-project-page">
    <!-- Header -->
    <div class="page-header">
        <h1>Create New Project</h1>
        <p>Start a new project for your group</p>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('student.projects.store') }}" method="POST" class="project-form">
            @csrf

            <div class="form-group">
                <label for="group_id" class="form-label">Select Group *</label>
                <select name="group_id" id="group_id" class="form-select" required>
                    <option value="">Choose a group...</option>
                    @foreach($availableGroups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }} ({{ $group->members->count() }}/{{ $group->max_members }} members)
                    </option>
                    @endforeach
                </select>
                @error('group_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Project Title *</label>
                <input type="text" name="title" id="title" class="form-input"
                       value="{{ old('title') }}" placeholder="Enter project title" required>
                @error('title')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Project Description</label>
                <textarea name="description" id="description" class="form-textarea"
                          placeholder="Describe your project..." rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                           class="form-input" value="{{ old('start_date') }}">
                    @error('start_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date"
                           class="form-input" value="{{ old('end_date') }}">
                    @error('end_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('student.projects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Projects
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Project
                </button>
            </div>
        </form>
    </div>

    @if($availableGroups->isEmpty())
    <div class="info-box">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <h3>No Available Groups</h3>
            <p>You need to be a member of a group that doesn't already have a project to create a new project.</p>
            <a href="{{ route('student.projects.groups.index') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> Manage Groups
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.create-project-page {
    padding: 1rem;
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 1rem;
}

.form-container {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.project-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-label {
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.error-message {
    color: #dc2626;
    font-size: 0.75rem;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.info-box {
    background: #dbeafe;
    border: 1px solid #93c5fd;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 2rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.info-icon {
    color: #2563eb;
    font-size: 1.5rem;
    margin-top: 0.125rem;
}

.info-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 0.5rem;
}

.info-content p {
    color: #1e40af;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .create-project-page {
        padding: 0.75rem;
    }

    .form-container {
        padding: 1.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        justify-content: center;
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDateInput.value && endDateInput.value && startDate >= endDate) {
            endDateInput.setCustomValidity('End date must be after start date');
        } else {
            endDateInput.setCustomValidity('');
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
});
</script>
@endsection
