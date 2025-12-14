@extends('layouts.student-dashboard')

@section('content')
<div class="create-group-page">
    <!-- Header -->
    <div class="page-header">
        <h1>Create New Group</h1>
        <p>Form a group with your classmates to work on projects together</p>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('student.projects.groups.store') }}" method="POST" class="group-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Group Name *</label>
                <input type="text" name="name" id="name"
                       class="form-input" value="{{ old('name') }}"
                       placeholder="Enter group name" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Group Description</label>
                <textarea name="description" id="description" class="form-textarea"
                          placeholder="Describe your group's purpose..." rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="subject_id" class="form-label">Subject *</label>
                <select name="subject_id" id="subject_id" class="form-select" required>
                    <option value="">Select a subject...</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <small class="form-help">Choose the subject this group will work on</small>
            </div>

            <div class="form-group">
                <label for="max_members" class="form-label">Maximum Members *</label>
                <select name="max_members" id="max_members" class="form-select" required>
                    <option value="">Select maximum members...</option>
                    @for($i = 2; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ old('max_members') == $i ? 'selected' : '' }}>
                        {{ $i }} members
                    </option>
                    @endfor
                </select>
                @error('max_members')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <small class="form-help">Including yourself as the group leader</small>
            </div>

            <div class="form-actions">
                <a href="{{ route('student.projects.groups.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Groups
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Group
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.create-group-page {
    padding: 1rem;
    max-width: 600px;
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

.group-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
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

.form-help {
    color: #6b7280;
    font-size: 0.75rem;
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

@media (max-width: 768px) {
    .create-group-page {
        padding: 0.75rem;
    }

    .form-container {
        padding: 1.5rem;
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
@endsection
