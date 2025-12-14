@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Add University Programs</h1>
        <div class="breadcrumbs">
            <a href="{{ route('admin.university-cut-offs.index') }}">University Cut-Offs</a> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Add</span>
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

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tab Navigation -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 1200px; margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; border-bottom: 2px solid #e5e7eb;">
            <button type="button" class="tab-btn active" onclick="switchTab('manual')" id="manualTab" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid #667eea; color: #667eea; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-keyboard"></i> Manual Entry
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('import')" id="importTab" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: #6b7280; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-file-upload"></i> Import from CSV/Excel
            </button>
        </div>
    </div>

    <!-- Manual Entry Form -->
    <form action="{{ route('admin.university-cut-offs.store') }}" method="POST" class="form-card" id="manualForm" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 1200px;">
        @csrf

        <!-- University Information -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-university" style="color: #667eea;"></i> University Information
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="university_name" class="form-label">
                        <i class="fas fa-university" style="margin-right: 8px; color: #667eea;"></i> Select University <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="university_name" id="university_name" required class="form-input">
                        <option value="">-- Select University --</option>
                        <option value="Makerere University" {{ old('university_name') == 'Makerere University' ? 'selected' : '' }}>Makerere University</option>
                        <option value="Kyambogo University" {{ old('university_name') == 'Kyambogo University' ? 'selected' : '' }}>Kyambogo University</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-graduation-cap" style="color: #667eea;"></i> Course/Program Information
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="course_name" class="form-label">
                        <i class="fas fa-book" style="margin-right: 8px; color: #667eea;"></i> Program Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="course_name" id="course_name" value="{{ old('course_name') }}" required class="form-input" placeholder="e.g., Bachelor of Education">
                </div>

                <div class="form-group">
                    <label for="course_code" class="form-label">
                        <i class="fas fa-code" style="margin-right: 8px; color: #667eea;"></i> Program Code
                    </label>
                    <input type="text" name="course_code" id="course_code" value="{{ old('course_code') }}" class="form-input" placeholder="e.g., BED">
                    <small style="color: #6b7280; font-size: 12px;">Program code/abbreviation if available</small>
                </div>

                <div class="form-group">
                    <label for="degree_type" class="form-label">
                        <i class="fas fa-certificate" style="margin-right: 8px; color: #667eea;"></i> Degree Type <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="degree_type" id="degree_type" required class="form-input">
                        <option value="bachelor" {{ old('degree_type') == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                        <option value="diploma" {{ old('degree_type') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="certificate" {{ old('degree_type') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="masters" {{ old('degree_type') == 'masters' ? 'selected' : '' }}>Masters</option>
                        <option value="phd" {{ old('degree_type') == 'phd' ? 'selected' : '' }}>PhD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="duration_years" class="form-label">
                        <i class="fas fa-clock" style="margin-right: 8px; color: #667eea;"></i> Duration (Years)
                    </label>
                    <input type="number" name="duration_years" id="duration_years" value="{{ old('duration_years') }}" min="1" max="10" step="0.5" class="form-input" placeholder="e.g., 3">
                    <small style="color: #6b7280; font-size: 12px;">Program duration in years</small>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="course_description" class="form-label">
                        <i class="fas fa-align-left" style="margin-right: 8px; color: #667eea;"></i> Program Description
                    </label>
                    <textarea name="course_description" id="course_description" rows="3" class="form-input" placeholder="Brief description of the program (optional)">{{ old('course_description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Optional: Faculty/Department Information -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-building" style="color: #667eea;"></i> Faculty/Department (Optional)
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="faculty" class="form-label">
                        <i class="fas fa-building" style="margin-right: 8px; color: #667eea;"></i> Faculty
                    </label>
                    <input type="text" name="faculty" id="faculty" value="{{ old('faculty') }}" class="form-input" placeholder="e.g., Faculty of Education">
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">
                        <i class="fas fa-sitemap" style="margin-right: 8px; color: #667eea;"></i> Department
                    </label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}" class="form-input" placeholder="e.g., Department of Science Education">
                </div>
            </div>
        </div>

        <!-- Admission Requirements -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-clipboard-check" style="color: #667eea;"></i> Admission Requirements
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="minimum_principal_passes" class="form-label">
                        <i class="fas fa-check-circle" style="margin-right: 8px; color: #667eea;"></i> Minimum Principal Passes <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="minimum_principal_passes" id="minimum_principal_passes" value="{{ old('minimum_principal_passes', 2) }}" required min="1" max="5" class="form-input">
                </div>

                <div class="form-group">
                    <label for="academic_year" class="form-label">
                        <i class="fas fa-calendar" style="margin-right: 8px; color: #667eea;"></i> Academic Year <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y')) }}" required min="2000" max="{{ date('Y') + 1 }}" class="form-input">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" style="margin-bottom: 15px;">
                        <i class="fas fa-chart-line" style="margin-right: 8px; color: #667eea;"></i> Cut-Off Points <span style="color: #ef4444;">*</span>
                    </label>
                    
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                        <div style="margin-bottom: 15px; color: #6b7280; font-size: 14px;">
                            <strong style="color: #374151;">Instructions:</strong>
                            <ul style="margin: 10px 0; padding-left: 20px; line-height: 1.6;">
                                <li><strong>Single cut-off:</strong> Enter value in "General Cut-Off" only (e.g., Kyambogo courses, some Makerere courses)</li>
                                <li><strong>Gender-specific:</strong> Enter values in "Male" and "Female" fields (e.g., some Makerere courses)</li>
                                <li><strong>Leave empty</strong> the fields you don't need</li>
                            </ul>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <label for="cut_off_points" style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151; font-size: 14px;">
                                    General Cut-Off (All Students)
                                </label>
                                <input type="number" name="cut_off_points" id="cut_off_points" value="{{ old('cut_off_points') }}" step="0.1" min="0" max="100" class="form-input" placeholder="e.g., 20.5" style="background: white;">
                                <small style="color: #6b7280; font-size: 12px;">Use this for courses without gender split</small>
                            </div>

                            <div>
                                <label for="cut_off_points_male" style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151; font-size: 14px;">
                                    <i class="fas fa-mars" style="color: #3b82f6;"></i> Male Cut-Off
                                </label>
                                <input type="number" name="cut_off_points_male" id="cut_off_points_male" value="{{ old('cut_off_points_male') }}" step="0.1" min="0" max="100" class="form-input" placeholder="e.g., 43.3" style="background: white;">
                                <small style="color: #6b7280; font-size: 12px;">For gender-specific courses only</small>
                            </div>

                            <div>
                                <label for="cut_off_points_female" style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151; font-size: 14px;">
                                    <i class="fas fa-venus" style="color: #ec4899;"></i> Female Cut-Off
                                </label>
                                <input type="number" name="cut_off_points_female" id="cut_off_points_female" value="{{ old('cut_off_points_female') }}" step="0.1" min="0" max="100" class="form-input" placeholder="e.g., 36.4" style="background: white;">
                                <small style="color: #6b7280; font-size: 12px;">For gender-specific courses only</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Requirements (A-Level Matching) -->
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
                        <i class="fas fa-star" style="margin-right: 8px; color: #ef4444;"></i> Essential Subjects
                    </label>
                    <textarea name="essential_subjects" id="essential_subjects" rows="6" class="form-input" placeholder="Physics&#10;Chemistry&#10;Mathematics&#10;&#10;Common subjects: Biology, Chemistry, Physics, Mathematics, Economics, Geography, History, Literature, Computer/ICT, Entrepreneurship">{{ old('essential_subjects') }}</textarea>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        <strong>One subject per line.</strong> Students must have at least 2 of these subjects at A-level to qualify for this course.
                    </small>
                </div>

                <div class="form-group">
                    <label for="relevant_subjects" class="form-label">
                        <i class="fas fa-check" style="margin-right: 8px; color: #f59e0b;"></i> Relevant Subjects
                    </label>
                    <textarea name="relevant_subjects" id="relevant_subjects" rows="6" class="form-input" placeholder="Enter one subject per line">{{ old('relevant_subjects') }}</textarea>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        <strong>Optional.</strong> These subjects improve the match but are not mandatory.
                    </small>
                </div>

                <div class="form-group">
                    <label for="desirable_subjects" class="form-label">
                        <i class="fas fa-heart" style="margin-right: 8px; color: #10b981;"></i> Desirable Subjects
                    </label>
                    <textarea name="desirable_subjects" id="desirable_subjects" rows="6" class="form-input" placeholder="Enter one subject per line">{{ old('desirable_subjects') }}</textarea>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        <strong>Optional.</strong> These are bonus subjects that enhance eligibility.
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
                <i class="fas fa-save"></i> Create Cut-Off
            </button>
        </div>
    </form>

    <!-- Import Form -->
    <form action="{{ route('admin.university-cut-offs.import') }}" method="POST" enctype="multipart/form-data" class="form-card" id="importForm" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 1200px; display: none;">
        @csrf

        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-university" style="color: #667eea;"></i> Select University
            </h2>

            <div class="form-group">
                <label for="import_university_name" class="form-label">
                    <i class="fas fa-university" style="margin-right: 8px; color: #667eea;"></i> University <span style="color: #ef4444;">*</span>
                </label>
                <select name="university_name" id="import_university_name" required class="form-input" style="max-width: 400px;">
                    <option value="">-- Select University --</option>
                    <option value="Makerere University">Makerere University</option>
                    <option value="Kyambogo University">Kyambogo University</option>
                </select>
            </div>
        </div>

        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-upload" style="color: #667eea;"></i> Upload File
            </h2>

            <!-- Download Template Section -->
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #0c4a6e; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle"></i> Download Template First
                </h3>
                <p style="color: #075985; margin-bottom: 15px; line-height: 1.6;">
                    Before importing, select the university above, then download the appropriate CSV template and fill it with your programs data.
                </p>
                <button type="button" onclick="downloadTemplate()" id="downloadTemplateBtn" class="dashboard-btn" style="background: #0284c7; color: white; display: inline-flex; align-items: center; gap: 8px;" disabled>
                    <i class="fas fa-download"></i> Download CSV Template
                </button>
                <small style="display: block; margin-top: 8px; color: #6b7280; font-size: 13px;">
                    <i class="fas fa-arrow-up"></i> Select a university first to enable download
                </small>
            </div>

            <!-- CSV Format Instructions -->
            <div id="formatInstructions" style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: none;">
                <h3 style="font-size: 16px; font-weight: 600; color: #78350f; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-list"></i> CSV Format Requirements
                </h3>
                
                <!-- Kyambogo Format -->
                <div id="kyambogoFormat" style="color: #92400e; line-height: 1.8; display: none;">
                    <strong style="color: #0284c7; display: block; margin-bottom: 8px;">
                        <i class="fas fa-university"></i> Kyambogo University Format:
                    </strong>
                    <strong>Required Columns (in order):</strong>
                    <ol style="margin: 10px 0; padding-left: 25px;">
                        <li><strong>course_name</strong> - Program name (e.g., Bachelor of Education)</li>
                        <li><strong>course_code</strong> - Program code (e.g., BED) - Optional</li>
                        <li><strong>cut_off_points</strong> - Cut-off points (e.g., 20.5) - Required</li>
                        <li><strong>degree_type</strong> - bachelor, diploma, certificate, masters, or phd</li>
                        <li><strong>minimum_principal_passes</strong> - Number (1-5, usually 2)</li>
                        <li><strong>academic_year</strong> - Year (e.g., 2025)</li>
                        <li><strong>duration_years</strong> - Program duration in years (optional)</li>
                        <li><strong>faculty</strong> - Faculty name (optional)</li>
                        <li><strong>department</strong> - Department name (optional)</li>
                    </ol>
                    <p style="margin: 8px 0; padding: 10px; background: #dbeafe; border-left: 4px solid #0284c7; border-radius: 4px;">
                        <strong>Example:</strong> Bachelor of Education,BED,20.5,bachelor,2,2025,3,Faculty of Education,Department of Educational Foundations
                    </p>
                    <p style="margin: 8px 0; padding: 8px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 13px;">
                        <strong>Display Order:</strong> No | Program Name | Program Code | Cut Off Points | Degree Type | Min Principal Passes | Academic Year
                    </p>
                </div>
                
                <!-- Makerere Format -->
                <div id="makerereFormat" style="color: #92400e; line-height: 1.8; display: none;">
                    <strong style="color: #ec4899; display: block; margin-bottom: 8px;">
                        <i class="fas fa-university"></i> Makerere University Format (Matches Official PDF):
                    </strong>
                    <strong>Required Columns (in order):</strong>
                    <ol style="margin: 10px 0; padding-left: 25px;">
                        <li><strong>course_name</strong> - Programme Name (exactly as in PDF)</li>
                        <li><strong>course_code</strong> - Programme Code (e.g., MED, DEN, CIV)</li>
                        <li><strong>cut_off_points</strong> - General Cut-off Points (non-weighted)</li>
                        <li><strong>cut_off_points_male</strong> - Male Points (weighted)</li>
                        <li><strong>cut_off_points_female</strong> - Female Points (weighted)</li>
                    </ol>
                    <p style="margin: 8px 0; padding: 10px; background: #fce7f3; border-left: 4px solid #ec4899; border-radius: 4px;">
                        <strong>Example:</strong> Bachelor of Medicine and Surgery,MED,39.8,43.3,36.4
                    </p>
                    <p style="margin: 8px 0; padding: 8px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 13px;">
                        <strong>Note:</strong> Copy all 5 columns directly from the PDF table in order
                    </p>
                </div>
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label for="import_file" class="form-label">
                    <i class="fas fa-file-csv" style="margin-right: 8px; color: #667eea;"></i> Select CSV/Excel File <span style="color: #ef4444;">*</span>
                </label>
                <input type="file" name="file" id="import_file" required accept=".csv,.xlsx,.xls" class="form-input" style="padding: 15px;">
                <small style="display: block; margin-top: 8px; color: #6b7280; font-size: 13px;">
                    Accepted formats: CSV (.csv), Excel (.xlsx, .xls)
                </small>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-file-import"></i> Import Programs
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Convert textarea inputs to arrays for form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Handle essential subjects
        const essentialTextarea = document.getElementById('essential_subjects');
        if (essentialTextarea.value.trim()) {
            const subjects = essentialTextarea.value.split('\n').filter(s => s.trim());
            essentialTextarea.name = 'essential_subjects[]';
            // Create hidden inputs for each subject
            subjects.forEach((subject, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'essential_subjects[]';
                input.value = subject.trim();
                form.appendChild(input);
            });
            essentialTextarea.remove();
        }

        // Handle relevant subjects
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

        // Handle desirable subjects
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

function switchTab(tab) {
    const manualForm = document.getElementById('manualForm');
    const importForm = document.getElementById('importForm');
    const manualTab = document.getElementById('manualTab');
    const importTab = document.getElementById('importTab');

    if (tab === 'manual') {
        manualForm.style.display = 'block';
        importForm.style.display = 'none';
        manualTab.classList.add('active');
        importTab.classList.remove('active');
        manualTab.style.borderBottomColor = '#667eea';
        manualTab.style.color = '#667eea';
        importTab.style.borderBottomColor = 'transparent';
        importTab.style.color = '#6b7280';
    } else {
        manualForm.style.display = 'none';
        importForm.style.display = 'block';
        manualTab.classList.remove('active');
        importTab.classList.add('active');
        manualTab.style.borderBottomColor = 'transparent';
        manualTab.style.color = '#6b7280';
        importTab.style.borderBottomColor = '#667eea';
        importTab.style.color = '#667eea';
    }
}

function updateImportFormat() {
    const university = document.getElementById('import_university_name').value;
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    const formatInstructions = document.getElementById('formatInstructions');
    const kyambogoFormat = document.getElementById('kyambogoFormat');
    const makerereFormat = document.getElementById('makerereFormat');
    
    if (university) {
        downloadBtn.disabled = false;
        formatInstructions.style.display = 'block';
        
        if (university === 'Makerere University') {
            kyambogoFormat.style.display = 'none';
            makerereFormat.style.display = 'block';
        } else {
            kyambogoFormat.style.display = 'block';
            makerereFormat.style.display = 'none';
        }
    } else {
        downloadBtn.disabled = true;
        formatInstructions.style.display = 'none';
    }
}

function downloadTemplate() {
    const university = document.getElementById('import_university_name').value;
    if (university) {
        window.location.href = '{{ route('admin.university-cut-offs.download-template') }}?university=' + encodeURIComponent(university);
    }
}

// Listen for university selection changes
document.addEventListener('DOMContentLoaded', function() {
    const universitySelect = document.getElementById('import_university_name');
    if (universitySelect) {
        universitySelect.addEventListener('change', updateImportFormat);
    }
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

