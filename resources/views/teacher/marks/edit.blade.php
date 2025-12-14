@extends('layouts.dashboard')

@section('content')
<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 1.5rem;">
        <a href="{{ route('teacher.marks.index') }}" class="dashboard-btn dashboard-btn-secondary" style="margin-bottom: 1rem; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Marks
        </a>
        <h1>Edit Student Mark</h1>
        <p>Update examination result for {{ $mark->user->name ?? 'student' }}</p>
    </div>

    @if($errors->any())
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="dashboard-card">
        <form action="{{ route('teacher.marks.update', $mark->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f0f9ff; border-radius: 0.5rem; border: 1px solid #bae6fd;">
                <h3 style="margin: 0 0 0.5rem 0; color: #1e40af;">Student Information</h3>
                <p style="margin: 0; color: #1e3a8a;">
                    <strong>Name:</strong> {{ $mark->user->name ?? 'N/A' }}<br>
                    @if($mark->user->student && $mark->user->student->registration_number)
                        <strong>Registration Number:</strong> {{ $mark->user->student->registration_number }}<br>
                    @endif
                    <strong>Class:</strong> {{ $mark->class->name ?? 'N/A' }}
                </p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="academic_level" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Level *</label>
                <select name="academic_level" id="academic_level" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="UACE" {{ old('academic_level', $mark->academic_level) == 'UACE' ? 'selected' : '' }}>UACE (A-Level)</option>
                    <option value="UCE" {{ old('academic_level', $mark->academic_level) == 'UCE' ? 'selected' : '' }}>UCE (O-Level)</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="subject_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Subject Name *</label>
                <input type="text" name="subject_name" id="subject_name" value="{{ old('subject_name', $mark->subject_name) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Mathematics, Physics">
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">Note: You can only edit marks for subjects you teach</small>
                @error('subject_name')
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="paper_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Paper Name (Optional)</label>
                <input type="text" name="paper_name" id="paper_name" value="{{ old('paper_name', $mark->paper_name) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Paper 1, Paper 2">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="academic_year" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Year</label>
                <input type="number" name="academic_year" id="academic_year" value="{{ old('academic_year', $mark->academic_year ?? date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="exam_type" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Exam Type *</label>
                <select name="exam_type" id="exam_type" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="toggleOtherExamType(this)">
                    <option value="">-- Select Exam Type --</option>
                    <option value="Beginning of Term" {{ old('exam_type', $mark->exam_type) == 'Beginning of Term' ? 'selected' : '' }}>Beginning of Term Exams</option>
                    <option value="Mid Term" {{ old('exam_type', $mark->exam_type) == 'Mid Term' ? 'selected' : '' }}>Mid Term Exams</option>
                    <option value="End of Term" {{ old('exam_type', $mark->exam_type) == 'End of Term' ? 'selected' : '' }}>End of Term Exams</option>
                    <option value="Mock" {{ old('exam_type', $mark->exam_type) == 'Mock' ? 'selected' : '' }}>Mock Exams</option>
                    <option value="Other" {{ old('exam_type', $mark->exam_type) == 'Other' ? 'selected' : '' }}>Other (Specify)</option>
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">This helps track student progress across different exam periods</small>
                @error('exam_type')
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div id="exam_type_other_field" style="margin-bottom: 1.5rem; display: {{ old('exam_type', $mark->exam_type) == 'Other' ? 'block' : 'none' }};">
                <label for="exam_type_other" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Specify Exam Type</label>
                <input type="text" name="exam_type_other" id="exam_type_other" value="{{ old('exam_type_other', $mark->exam_type_other) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Pre-Mock, Weekly Test">
                @error('exam_type_other')
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="grade" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Grade *</label>
                    <input type="text" name="grade" id="grade" value="{{ old('grade', $mark->grade) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="A, B, C, D, E">
                </div>

                <div>
                    <label for="numeric_mark" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Numeric Mark</label>
                    <input type="number" name="numeric_mark" id="numeric_mark" value="{{ old('numeric_mark', $mark->numeric_mark) }}" min="0" max="100" step="0.1" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="0-100">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_principal_pass" value="1" {{ old('is_principal_pass', $mark->is_principal_pass) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #374151;">Principal Pass</span>
                </label>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="remarks" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="Enter any remarks about this result">{{ old('remarks', $mark->remarks) }}</textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-save"></i> Update Mark
                </button>
                <a href="{{ route('teacher.marks.index') }}" class="dashboard-btn dashboard-btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleOtherExamType(selectElement) {
    const otherField = document.getElementById('exam_type_other_field');
    const otherInput = document.getElementById('exam_type_other');
    
    if (selectElement.value === 'Other') {
        otherField.style.display = 'block';
        otherInput.required = true;
    } else {
        otherField.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}
</script>
@endsection
