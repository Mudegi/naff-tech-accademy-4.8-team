@extends('layouts.student-dashboard')

@section('content')
<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 1.5rem;">
        <a href="{{ route('student.marks.index') }}" class="dashboard-btn dashboard-btn-secondary" style="margin-bottom: 1rem; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Marks
        </a>
        <h1>Add Academic Mark</h1>
        <p>Enter your A Level examination result details</p>
        @if(isset($combination))
            <div class="dashboard-alert dashboard-alert-info" style="margin-top: 0.5rem;">
                <i class="fas fa-info-circle"></i>
                <strong>Your Combination:</strong> {{ $combination }}
            </div>
        @endif
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

    <form action="{{ route('student.marks.store') }}" method="POST" class="form-card" style="background: white; border-radius: 0.5rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
        @csrf

        <div class="form-grid" style="display: grid; gap: 1.5rem;">
            <!-- Subject Name - Auto-populated from combination -->
            <div class="form-group">
                <label for="subject_name" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-book" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Subject <span style="color: #ef4444;">*</span>
                </label>
                <select name="subject_name" id="subject_name" required class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">Select Subject from Your Combination</option>
                    @if(isset($subjects) && count($subjects) > 0)
                        @foreach($subjects as $subject)
                            <option value="{{ $subject }}" {{ old('subject_name') == $subject ? 'selected' : '' }}>
                                {{ $subject }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No subjects found in your combination</option>
                    @endif
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">
                    <i class="fas fa-info-circle"></i>
                    Select a subject from your registered combination
                </small>
            </div>

            <!-- Paper Name -->
            <div class="form-group">
                <label for="paper_name" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-file-alt" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Paper Name (Optional)
                </label>
                <input type="text" name="paper_name" id="paper_name" value="{{ old('paper_name') }}" 
                    placeholder="e.g., Mathematics Paper 1, Physics Paper 2" 
                    class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
            </div>

            <!-- Grade Type -->
            <div class="form-group">
                <label for="grade_type" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-tag" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Grade Type <span style="color: #ef4444;">*</span>
                </label>
                <select name="grade_type" id="grade_type" required class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="toggleGradeInputs()">
                    <option value="">Select Type</option>
                    <option value="letter" {{ old('grade_type') == 'letter' ? 'selected' : '' }}>Letter Grade (A, B, C, D, E, O, F)</option>
                    <option value="distinction_credit_pass" {{ old('grade_type') == 'distinction_credit_pass' ? 'selected' : '' }}>Distinction/Credit/Pass</option>
                    <option value="numeric" {{ old('grade_type') == 'numeric' ? 'selected' : '' }}>Numeric Mark (0-100)</option>
                </select>
            </div>

            <!-- Grade Input (Letter or Distinction/Credit/Pass) -->
            <div class="form-group" id="grade-input-group">
                <label for="grade" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-star" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Grade <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="grade" id="grade" value="{{ old('grade') }}" required 
                    placeholder="Enter grade (e.g., A, B, Distinction 1, Credit 3, Pass 7)" 
                    class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;" id="grade-help">
                    Enter letter grade (A-F) or Distinction/Credit/Pass format
                </small>
            </div>

            <!-- Numeric Mark Input -->
            <div class="form-group" id="numeric-mark-group" style="display: none;">
                <label for="numeric_mark" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-percentage" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Numeric Mark (0-100)
                </label>
                <input type="number" name="numeric_mark" id="numeric_mark" value="{{ old('numeric_mark') }}" 
                    min="0" max="100" step="0.01"
                    placeholder="e.g., 85.5" 
                    class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
            </div>

            <!-- Subject Classification -->
            <div class="form-group" style="padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-tags" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Subject Classification
                </label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_principal_pass" value="1" {{ old('is_principal_pass') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: #374151;">Principal Pass</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_essential" value="1" {{ old('is_essential') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: #374151;">Essential Subject</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_relevant" value="1" {{ old('is_relevant') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: #374151;">Relevant Subject</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_desirable" value="1" {{ old('is_desirable') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: #374151;">Desirable Subject</span>
                    </label>
                </div>
            </div>

            <!-- Academic Year -->
            <div class="form-group">
                <label for="academic_year" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-calendar" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Academic Year (Optional)
                </label>
                <input type="number" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y')) }}" 
                    min="2000" max="{{ date('Y') + 1 }}"
                    class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
            </div>

            <!-- Remarks -->
            <div class="form-group">
                <label for="remarks" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                    <i class="fas fa-comment" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Remarks (Optional)
                </label>
                <textarea name="remarks" id="remarks" rows="3" 
                    placeholder="Any additional notes about this result..." 
                    class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; resize: vertical;">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('student.marks.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-save"></i> Save Mark
            </button>
        </div>
    </form>
</div>

<script>
function toggleGradeInputs() {
    var gradeType = document.getElementById('grade_type').value;
    var gradeGroup = document.getElementById('grade-input-group');
    var numericGroup = document.getElementById('numeric-mark-group');
    var gradeHelp = document.getElementById('grade-help');
    var gradeInput = document.getElementById('grade');
    var numericInput = document.getElementById('numeric_mark');

    if (gradeType === 'numeric') {
        gradeGroup.style.display = 'none';
        numericGroup.style.display = 'block';
        gradeInput.required = false;
        numericInput.required = true;
    } else {
        gradeGroup.style.display = 'block';
        numericGroup.style.display = 'none';
        gradeInput.required = true;
        numericInput.required = false;
        
        if (gradeType === 'letter') {
            gradeHelp.textContent = 'Enter letter grade: A, B, C, D, E, O, or F';
            gradeInput.placeholder = 'e.g., A, B, C';
        } else if (gradeType === 'distinction_credit_pass') {
            gradeHelp.textContent = 'Enter grade: Distinction 1-2, Credit 3-6, or Pass 7-9';
            gradeInput.placeholder = 'e.g., Distinction 1, Credit 3, Pass 7';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleGradeInputs();
});
</script>
@endsection
