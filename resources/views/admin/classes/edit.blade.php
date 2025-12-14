@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Edit Class</h1>
        <a href="{{ route('admin.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Classes</a>
    </div>

    <div class="profile-card" style="max-width:800px;margin:0 auto;">
        <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="dashboard-form">
            @csrf
            @method('PUT')
            
            <div class="profile-form-group">
                <label for="name">Class Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $class->name) }}" class="profile-input" required>
                @error('name')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="profile-input" rows="3">{{ old('description', $class->description) }}</textarea>
                @error('description')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-row">
                <div class="profile-form-group">
                    <label for="grade_level">Grade Level</label>
                    <input type="number" id="grade_level" name="grade_level" value="{{ old('grade_level', $class->grade_level) }}" class="profile-input" min="1" required>
                    @error('grade_level')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="level">Academic Level</label>
                    <select id="level" name="level" class="profile-input" required>
                        <option value="">Select Level</option>
                        @foreach($levels as $value => $label)
                            <option value="{{ $value }}" {{ old('level', $class->level) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">S.1 – S.4 = O Level, S.5 – S.6 = A Level.</small>
                    @error('level')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="term">Term</label>
                    <select id="term" name="term" class="profile-input" required>
                        <option value="">Select Term</option>
                        <option value="First Term" {{ old('term', $class->term) == 'First Term' ? 'selected' : '' }}>First Term</option>
                        <option value="Second Term" {{ old('term', $class->term) == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                        <option value="Third Term" {{ old('term', $class->term) == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                    </select>
                    @error('term')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="profile-form-row">
                <div class="profile-form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $class->start_date->format('Y-m-d')) }}" class="profile-input" required>
                    @error('start_date')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $class->end_date->format('Y-m-d')) }}" class="profile-input" required>
                    @error('end_date')
                        <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="profile-form-group">
                <label for="subjects">Subjects</label>
                <select id="subjects" name="subjects[]" class="profile-input" multiple required>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ in_array($subject->id, old('subjects', $class->subjects->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subjects')
                    <div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-group">
                <label class="profile-checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>

            <div class="profile-form-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">Update Class</button>
                <a href="{{ route('admin.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Initialize select2 for subjects
    $(document).ready(function() {
        $('#subjects').select2({
            placeholder: 'Select subjects',
            allowClear: true
        });
    });

    const gradeInput = document.getElementById('grade_level');
    const levelSelect = document.getElementById('level');
    let levelManuallyChanged = false;

    if (levelSelect) {
        levelSelect.addEventListener('change', () => {
            levelManuallyChanged = true;
        });
    }

    function autoAssignLevel() {
        if (!gradeInput || !levelSelect || levelManuallyChanged) {
            return;
        }
        const gradeValue = parseInt(gradeInput.value, 10);
        if (!isNaN(gradeValue)) {
            if (gradeValue >= 1 && gradeValue <= 4) {
                levelSelect.value = 'O Level';
            } else if (gradeValue >= 5) {
                levelSelect.value = 'A Level';
            }
        }
    }

    if (gradeInput) {
        gradeInput.addEventListener('input', () => {
            levelManuallyChanged = false;
            autoAssignLevel();
        });
        autoAssignLevel();
    }
</script>
@endpush
@endsection 