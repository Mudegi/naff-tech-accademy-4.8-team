@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="dashboard-title">Create New Class</h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.classes.index') }}">Classes</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Create</span>
            </div>
        </div>
        <a href="{{ route('admin.school.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Classes</a>
    </div>

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-card" style="max-width:800px;margin:0 auto;">
        <form action="{{ route('admin.school.classes.store') }}" method="POST" class="dashboard-form">
            @csrf
            
            <div class="profile-form-group">
                <label for="name">Class Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="profile-input" required>
                @error('name')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="profile-input" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-row">
                <div class="profile-form-group">
                    <label for="grade_level">Grade Level <span class="text-red-500">*</span></label>
                    <input type="number" id="grade_level" name="grade_level" value="{{ old('grade_level') }}" class="profile-input" min="1" required>
                    @error('grade_level')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="term_id">Term <span class="text-red-500">*</span></label>
                    <select id="term_id" name="term_id" class="profile-input" required>
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('term_id')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="profile-form-row">
                <div class="profile-form-group">
                    <label for="start_date">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="profile-input" required>
                    @error('start_date')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="end_date">End Date <span class="text-red-500">*</span></label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="profile-input" required>
                    @error('end_date')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="profile-form-group">
                <label for="subjects">Subjects <span class="text-red-500">*</span></label>
                <select id="subjects" name="subjects[]" class="profile-input" multiple required>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ in_array($subject->id, old('subjects', [])) ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-gray-500">Hold Ctrl (or Cmd on Mac) to select multiple subjects</small>
                @error('subjects')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-group">
                <label class="profile-checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    Active
                </label>
            </div>

            <div class="profile-form-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">Create Class</button>
                <a href="{{ route('admin.school.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Initialize select2 for subjects if available
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#subjects').select2({
                placeholder: 'Select subjects',
                allowClear: true
            });
        }
    });
</script>
@endpush
@endsection

