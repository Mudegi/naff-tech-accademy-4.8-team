@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Edit University Cut-Off</h1>
        <div class="breadcrumbs">
            <a href="{{ route('admin.university-cut-offs.index') }}">University Cut-Offs</a> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Edit</span>
        </div>
    </div>

    @if($errors->any())
        <div class="dashboard-alert dashboard-alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.university-cut-offs.update', $universityCutOff->id) }}" method="POST" class="form-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 1200px;">
        @csrf
        @method('PUT')
        
        <!-- Hidden fields required by validation -->
        <input type="hidden" name="degree_type" value="{{ old('degree_type', $universityCutOff->degree_type ?? 'bachelor') }}">
        <input type="hidden" name="program_category" value="{{ old('program_category', $universityCutOff->program_category ?? ($universityCutOff->cut_off_points_male || $universityCutOff->cut_off_points_female ? 'stem' : 'other')) }}">
        <input type="hidden" name="cut_off_format" value="{{ old('cut_off_format', $universityCutOff->cut_off_format ?? 'standard') }}">

        <!-- Course Information -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-graduation-cap" style="color: #667eea;"></i> Course Information
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="university_name" class="form-label">
                        <i class="fas fa-university" style="margin-right: 8px; color: #667eea;"></i> University <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="university_name" id="university_name" value="{{ old('university_name', $universityCutOff->university_name) }}" required class="form-input" readonly style="background: #f3f4f6;">
                </div>

                <div class="form-group">
                    <label for="course_name" class="form-label">
                        <i class="fas fa-book" style="margin-right: 8px; color: #667eea;"></i> Program Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="course_name" id="course_name" value="{{ old('course_name', $universityCutOff->course_name) }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="course_code" class="form-label">
                        <i class="fas fa-tag" style="margin-right: 8px; color: #667eea;"></i> Program Code
                    </label>
                    <input type="text" name="course_code" id="course_code" value="{{ old('course_code', $universityCutOff->course_code) }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="academic_year" class="form-label">
                        <i class="fas fa-calendar" style="margin-right: 8px; color: #667eea;"></i> Academic Year <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="academic_year" id="academic_year" value="{{ old('academic_year', $universityCutOff->academic_year) }}" required min="2000" max="{{ date('Y') + 1 }}" class="form-input">
                </div>
            </div>
        </div>

        <!-- Cut-Off Points -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-chart-line" style="color: #667eea;"></i> Cut-Off Points & Requirements
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="minimum_principal_passes" class="form-label">
                        <i class="fas fa-check-circle" style="margin-right: 8px; color: #667eea;"></i> Min Principal Passes <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="minimum_principal_passes" id="minimum_principal_passes" value="{{ old('minimum_principal_passes', $universityCutOff->minimum_principal_passes) }}" required min="1" max="5" class="form-input">
                </div>

                <div class="form-group">
                    <label for="cut_off_points" class="form-label">
                        <i class="fas fa-chart-line" style="margin-right: 8px; color: #667eea;"></i> General Cut-Off Points
                    </label>
                    <input type="number" name="cut_off_points" id="cut_off_points" value="{{ old('cut_off_points', $universityCutOff->cut_off_points) }}" step="0.1" min="0" max="100" class="form-input" placeholder="20.5">
                </div>

                <div class="form-group">
                    <label for="cut_off_points_male" class="form-label">
                        <i class="fas fa-mars" style="margin-right: 8px; color: #3b82f6;"></i> Male Cut-Off
                    </label>
                    <input type="number" name="cut_off_points_male" id="cut_off_points_male" value="{{ old('cut_off_points_male', $universityCutOff->cut_off_points_male) }}" step="0.1" min="0" max="100" class="form-input" placeholder="43.3">
                </div>

                <div class="form-group">
                    <label for="cut_off_points_female" class="form-label">
                        <i class="fas fa-venus" style="margin-right: 8px; color: #ec4899;"></i> Female Cut-Off
                    </label>
                    <input type="number" name="cut_off_points_female" id="cut_off_points_female" value="{{ old('cut_off_points_female', $universityCutOff->cut_off_points_female) }}" step="0.1" min="0" max="100" class="form-input" placeholder="36.4">
                </div>
            </div>
        </div>

        <!-- Subject Requirements -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-book-open" style="color: #667eea;"></i> Subject Requirements (A-Level Combination Matching)
            </h2>

            <div style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                    <strong><i class="fas fa-info-circle"></i> Important:</strong> System will match students' A-level subjects with course requirements. 
                    Students must have <strong>at least 2 matching subjects</strong> from the essential subjects list before cut-off point calculation.
                    <strong>General Paper (GP)</strong> and <strong>Subsidiary (Sub ICT/Sub Math)</strong> are automatically included for all Ugandan A-level students.
                </p>
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="essential_subjects" class="form-label">
                        <i class="fas fa-star" style="margin-right: 8px; color: #ef4444;"></i> Essential Subjects <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="essential_subjects" id="essential_subjects" rows="6" class="form-input" placeholder="Physics&#10;Chemistry&#10;Mathematics&#10;&#10;Common subjects: Biology, Chemistry, Physics, Mathematics, Economics, Geography, History, Literature, Computer/ICT, Entrepreneurship">{{ old('essential_subjects', is_array(old('essential_subjects')) ? implode("\n", old('essential_subjects')) : ($universityCutOff->essential_subjects ? implode("\n", $universityCutOff->essential_subjects) : '')) }}</textarea>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        <strong>One subject per line.</strong> Students must have at least 2 of these subjects at A-level to qualify for this course.
                    </small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-save"></i> Update Cut-Off
            </button>
        </div>
    </form>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const essentialTextarea = document.getElementById('essential_subjects');
        if (essentialTextarea.value.trim()) {
            const subjects = essentialTextarea.value.split('\n').filter(s => s.trim());
            subjects.forEach((subject) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'essential_subjects[]';
                input.value = subject.trim();
                form.appendChild(input);
            });
            essentialTextarea.remove();
        }

        const relevantTextarea = document.getElementById('relevant_subjects');
        if (relevantTextarea.value.trim()) {
            const subjects = relevantTextarea.value.split('\n').filter(s => s.trim());
            subjects.forEach((subject) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'relevant_subjects[]';
                input.value = subject.trim();
                form.appendChild(input);
            });
            relevantTextarea.remove();
        }

        const desirableTextarea = document.getElementById('desirable_subjects');
        if (desirableTextarea.value.trim()) {
            const subjects = desirableTextarea.value.split('\n').filter(s => s.trim());
            subjects.forEach((subject) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'desirable_subjects[]';
                input.value = subject.trim();
                form.appendChild(input);
            });
            desirableTextarea.remove();
        }
    });
});
</script>

<style>
.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
    font-family: inherit;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

textarea.form-input {
    resize: vertical;
}
</style>
@endsection

