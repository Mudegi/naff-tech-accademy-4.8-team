@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="dashboard-title">Manage {{ $class->name }} Subjects</h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span>
                <span>School</span> <span class="breadcrumb-sep">/</span>
                <span><a href="{{ route('admin.school.classes.index') }}">Classes</a></span> <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-active">Manage Subjects</span>
            </div>
        </div>
        <a href="{{ route('admin.school.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Classes
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Class Info Banner -->
    <div class="dashboard-card" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border-left: 4px solid #0ea5e9; margin-bottom: 20px;">
        <div style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div>
                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #0ea5e9;"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #0c4a6e; font-size: 1.25rem;">{{ $class->name }}</h3>
                    <p style="margin: 0; color: #374151;">
                        <strong>{{ $class->level }}</strong> • Grade {{ $class->grade_level }} • {{ $class->term }}
                        <br>
                        <small>{{ $class->description }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-card" style="max-width:1000px;margin:0 auto;">
        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: 20px;">
            <h4 style="margin: 0; color: #1f2937;">
                <i class="fas fa-book me-2"></i>Assign Subjects to {{ $class->name }}
            </h4>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.9rem;">
                Select the subjects that students in this class will study. You can modify this assignment at any time.
            </p>
        </div>

        <form action="{{ route('admin.school.classes.update', $class->id) }}" method="POST" class="dashboard-form">
            @csrf
            @method('PUT')

            <div class="profile-form-group">
                <label for="subjects" style="font-weight: 600; color: #1f2937; margin-bottom: 15px; display: block;">
                    Available Subjects <span class="text-red-500">*</span>
                </label>

                @if($subjects->count() > 0)
                    <div class="subjects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        @foreach($subjects as $subject)
                            <label class="subject-card {{ $class->subjects->contains($subject->id) ? 'selected' : '' }}" style="display: block; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s; background: white;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                           {{ $class->subjects->contains($subject->id) ? 'checked' : '' }}
                                           style="width: 18px; height: 18px; accent-color: #0ea5e9;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">{{ $subject->name }}</div>
                                        @if($subject->description)
                                            <div style="font-size: 0.85rem; color: #6b7280;">{{ Str::limit($subject->description, 60) }}</div>
                                        @endif
                                        <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">
                                            <i class="fas fa-layer-group me-1"></i>{{ $subject->level ?? 'General' }}
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5" style="background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px;">
                        <i class="fas fa-book-open" style="font-size: 3rem; color: #d1d5db; margin-bottom: 15px; display: block;"></i>
                        <h4 style="color: #6b7280; margin-bottom: 10px;">No Subjects Available</h4>
                        <p style="color: #9ca3af; margin-bottom: 20px;">You need to create subjects for your school before you can assign them to classes.</p>
                        <a href="{{ route('admin.subjects.create') }}" class="dashboard-btn dashboard-btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Subject
                        </a>
                    </div>
                @endif

                @error('subjects')
                    <div class="dashboard-alert dashboard-alert-error" style="margin-top: 15px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-actions" style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                <button type="submit" class="dashboard-btn dashboard-btn-primary" {{ $subjects->count() == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-save me-2"></i>
                    {{ $class->subjects->count() > 0 ? 'Update Subject Assignments' : 'Assign Subjects to Class' }}
                </button>
                <a href="{{ route('admin.school.classes.index') }}" class="dashboard-btn dashboard-btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.subject-card {
    transition: all 0.2s ease;
}

.subject-card:hover {
    border-color: #0ea5e9;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
}

.subject-card.selected {
    border-color: #0ea5e9;
    background: #f0f9ff;
}

.subject-card input[type="checkbox"]:checked {
    accent-color: #0ea5e9;
}

@media (max-width: 768px) {
    .subjects-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handler to subject cards
    const subjectCards = document.querySelectorAll('.subject-card');
    subjectCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking on the checkbox itself
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                this.classList.toggle('selected', checkbox.checked);
            }
        });
    });

    // Update card styling when checkbox changes
    const checkboxes = document.querySelectorAll('.subject-card input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.subject-card').classList.toggle('selected', this.checked);
        });
    });
});
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handler to subject cards
    const subjectCards = document.querySelectorAll('.subject-card');
    subjectCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking on the checkbox itself
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                this.classList.toggle('selected', checkbox.checked);
            }
        });
    });

    // Update card styling when checkbox changes
    const checkboxes = document.querySelectorAll('.subject-card input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.subject-card').classList.toggle('selected', this.checked);
        });
    });
});
</script>
@endpush

