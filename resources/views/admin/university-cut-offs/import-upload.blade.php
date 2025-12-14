@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
        <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
        <a href="{{ route('admin.university-cut-offs.index') }}" style="color: #667eea; text-decoration: none;">University Cut-Offs</a>
        <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
        <span style="color: #6b7280;">Import from Excel/CSV</span>
    </div>

    <div class="dashboard-header" style="margin-bottom: 2rem;">
        <h1 class="dashboard-title">Import University Cut-Offs</h1>
        <p class="dashboard-subtitle">Upload an Excel or CSV file to bulk import or update university cut-offs</p>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 2rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Info Banner -->
    <div class="dashboard-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 2rem; padding: 2rem; border-radius: 0.5rem;">
        <div style="display: flex; align-items-center; gap: 1.5rem;">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="fas fa-file-excel"></i>
            </div>
            <div style="flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.5rem 0;">Bulk Import/Update</h2>
                <p style="margin: 0; opacity: 0.95; line-height: 1.6;">
                    Upload your edited Excel file to update existing cut-offs or add new courses. The system will match by university name, course name, and academic year.
                </p>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="dashboard-card" style="background: #f0f9ff; border-left: 4px solid #3b82f6; margin-bottom: 2rem; padding: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e40af; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-info-circle"></i> How It Works
        </h3>
        <ol style="color: #1e40af; line-height: 1.8; margin: 0; padding-left: 1.5rem;">
            <li><strong>Export current data:</strong> Click "Export to Excel" on the main page to download all current cut-offs</li>
            <li><strong>Edit offline:</strong> Open the Excel file and make your changes:
                <ul style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                    <li>Update cut-off points</li>
                    <li>Modify Essential Subjects (enter as comma-separated values, e.g., "Physics, Mathematics")</li>
                    <li>Add new courses (include all required fields)</li>
                    <li>Change degree types, academic years, etc.</li>
                </ul>
            </li>
            <li><strong>Upload the file:</strong> Select your edited file below and click "Import File"</li>
            <li><strong>Review results:</strong> The system will show you how many records were imported, updated, or skipped</li>
        </ol>
    </div>

    <!-- File Upload Form -->
    <div class="dashboard-card" style="margin-bottom: 2rem;">
        <form action="{{ route('admin.university-cut-offs.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="university_name" class="form-label" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-university" style="color: #667eea; margin-right: 0.5rem;"></i>
                    University Name <span style="color: #ef4444;">*</span>
                </label>
                <select name="university_name" id="university_name" class="form-input" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">-- Select University --</option>
                    <option value="Makerere University">Makerere University</option>
                    <option value="Kyambogo University">Kyambogo University</option>
                    <option value="Mbarara University">Mbarara University</option>
                    <option value="Gulu University">Gulu University</option>
                    <option value="Busitema University">Busitema University</option>
                    <option value="Other">Other University</option>
                </select>
                <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                    Select the university for which you're importing cut-offs
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="file" class="form-label" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-file-upload" style="color: #667eea; margin-right: 0.5rem;"></i>
                    Excel/CSV File <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required 
                           style="width: 100%; padding: 0.75rem; border: 2px dashed #d1d5db; border-radius: 0.375rem; font-size: 1rem; cursor: pointer; background: #f9fafb;"
                           onchange="showFileName(this)">
                    <div id="file-name" style="margin-top: 0.5rem; color: #059669; font-weight: 500; display: none;">
                        <i class="fas fa-check-circle"></i> <span></span>
                    </div>
                </div>
                <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                    Supported formats: Excel (.xlsx, .xls) or CSV (.csv). Maximum file size: 10MB
                </small>
            </div>

            <!-- Required Columns Info -->
            <div class="dashboard-card" style="background: #fef3c7; border-left: 4px solid #f59e0b; margin-bottom: 1.5rem; padding: 1rem;">
                <h4 style="font-weight: 600; color: #78350f; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i> Required Columns in Your File
                </h4>
                <div style="color: #78350f; font-size: 0.875rem; line-height: 1.6;">
                    <p style="margin: 0 0 0.5rem 0;"><strong>Required fields:</strong></p>
                    <ul style="margin: 0 0 0.75rem 1.5rem; padding: 0;">
                        <li><code>course_name</code> - Full name of the program</li>
                        <li><code>degree_type</code> - bachelor, diploma, certificate, masters, or phd</li>
                        <li><code>minimum_principal_passes</code> - Number (1-5)</li>
                        <li><code>academic_year</code> - Year (e.g., 2025)</li>
                    </ul>
                    <p style="margin: 0 0 0.5rem 0;"><strong>Optional but recommended:</strong></p>
                    <ul style="margin: 0 0 0 1.5rem; padding: 0;">
                        <li><code>Essential Subjects</code> - Comma-separated A-Level subjects (e.g., "Physics, Mathematics")</li>
                        <li><code>course_code</code>, <code>faculty</code>, <code>department</code>, <code>duration_years</code></li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button type="submit" class="dashboard-btn dashboard-btn-primary" id="submitBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-upload"></i> Import File
                </button>
            </div>
        </form>
    </div>

    <!-- Tips -->
    <div class="dashboard-card" style="background: #dcfce7; border-left: 4px solid #16a34a; padding: 1rem;">
        <h4 style="font-weight: 600; color: #166534; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-lightbulb"></i> Pro Tips
        </h4>
        <ul style="color: #166534; font-size: 0.875rem; line-height: 1.6; margin: 0; padding-left: 1.5rem;">
            <li>The system will automatically match and update existing records based on university name, course name, and academic year</li>
            <li>For Essential Subjects, use exact A-Level subject names separated by commas (e.g., "Physics, Mathematics, Chemistry")</li>
            <li>Leave Essential Subjects blank for courses that don't have specific requirements</li>
            <li>Any errors during import will be reported - you can fix them and re-import</li>
            <li>Download a fresh export before making edits to ensure you have the latest data</li>
        </ul>
    </div>
</div>

<script>
function showFileName(input) {
    const fileNameDiv = document.getElementById('file-name');
    const fileNameSpan = fileNameDiv.querySelector('span');
    
    if (input.files && input.files[0]) {
        fileNameSpan.textContent = input.files[0].name;
        fileNameDiv.style.display = 'block';
    } else {
        fileNameDiv.style.display = 'none';
    }
}

document.getElementById('importForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing... Please wait';
    }
});
</script>

<style>
.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

code {
    background: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
    color: #1f2937;
    font-family: monospace;
}
</style>
@endsection
