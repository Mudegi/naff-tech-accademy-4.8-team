@extends('layouts.dashboard')

@section('content')
@php
    $accountTypes = [
        'admin' => 'Admin',
        'staff' => 'Staff',
        'student' => 'Student',
        'parent' => 'Parent',
        'teacher' => 'Teacher',
        'school_admin' => 'School Admin',
        'director_of_studies' => 'Director of Studies',
        'head_of_department' => 'Head of Department',
        'subject_teacher' => 'Subject Teacher'
    ];
    $allSubjects = \App\Models\Subject::where('is_active', true)->orderBy('name')->get();
    $allClasses = \App\Models\SchoolClass::where('is_active', true)->orderBy('name')->get();
    $schoolStaffRoles = ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'student'];
@endphp
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Add New User</h1>
        <a href="{{ route('admin.users.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Users</a>
    </div>
    <div class="profile-card" style="max-width:600px;margin:0 auto;">
        <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">
            @csrf
            <div class="profile-form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="profile-input" required autocomplete="off">
                @error('name')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="profile-input" autocomplete="off">
                @error('email')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" class="profile-input" autocomplete="off">
                @error('phone_number')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="account_type">Account Type</label>
                <select id="account_type" name="account_type" class="profile-input" required onchange="toggleSchoolFields(); toggleTeacherFields();" autocomplete="off">
                    <option value="">Select Type</option>
                    @foreach($accountTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('account_type', request('account_type')) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('account_type')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group" id="school-field" style="display: none;">
                <label for="school_id">School <span style="color: #ef4444;">*</span></label>
                <select id="school_id" name="school_id" class="profile-input" autocomplete="off">
                    <option value="">Select School</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id', $selectedSchoolId ?? '') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
                @error('school_id')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group" id="teacher-fields" style="display: none;">
                <label>Subjects Taught</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                    @foreach($allSubjects as $subject)
                        <label style="min-width: 150px;"><input type="checkbox" name="subjects[]" value="{{ $subject->id }}" {{ (is_array(old('subjects')) && in_array($subject->id, old('subjects', []))) ? 'checked' : '' }} autocomplete="off"> {{ $subject->name }}</label>
                    @endforeach
                </div>
                
                <label>Classes Taught</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($allClasses as $class)
                        <label style="min-width: 150px;"><input type="checkbox" name="classes[]" value="{{ $class->id }}" {{ (is_array(old('classes')) && in_array($class->id, old('classes', []))) ? 'checked' : '' }} autocomplete="off"> {{ $class->name }}</label>
                    @endforeach
                </div>
            </div>
            <div class="profile-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="profile-input" required autocomplete="new-password">
                @error('password')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="profile-input" required autocomplete="new-password">
            </div>
            <div class="profile-form-group">
                <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} autocomplete="off"> Active</label>
            </div>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">Create User</button>
        </form>
    </div>
</div>

<script>
function toggleSchoolFields() {
    var type = document.getElementById('account_type').value;
    var schoolField = document.getElementById('school-field');
    var schoolSelect = document.getElementById('school_id');
    var schoolStaffRoles = ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher', 'student'];
    
    if(schoolStaffRoles.includes(type)) {
        schoolField.style.display = 'block';
        schoolSelect.required = true;
    } else {
        schoolField.style.display = 'none';
        schoolSelect.required = false;
        schoolSelect.value = '';
    }
}

function toggleTeacherFields() {
    var type = document.getElementById('account_type').value;
    var teacherFields = document.getElementById('teacher-fields');
    if(type === 'teacher' || type === 'subject_teacher') {
        teacherFields.style.display = 'block';
    } else {
        teacherFields.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleSchoolFields();
    toggleTeacherFields();
});
</script>
@endsection 