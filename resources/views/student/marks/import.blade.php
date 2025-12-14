@extends('layouts.student-dashboard')

@section('content')
<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 1.5rem;">
        <a href="{{ route('student.marks.index') }}" class="dashboard-btn dashboard-btn-secondary" style="margin-bottom: 1rem; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Marks
        </a>
        <h1>Import Marks from Excel/CSV</h1>
        <p>Upload your examination results in bulk</p>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 1.5rem;">
            {{ session('success') }}
            @if(session('import_results'))
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.3);">
                    <strong>Import Summary:</strong><br>
                    ✅ Successfully imported: {{ session('import_results')['success'] ?? 0 }} marks<br>
                    @if((session('import_results')['failed'] ?? 0) > 0)
                        ❌ Failed: {{ session('import_results')['failed'] ?? 0 }} marks
                        @if(!empty(session('import_results')['errors']))
                            <details style="margin-top: 0.5rem;">
                                <summary style="cursor: pointer; text-decoration: underline;">View Errors</summary>
                                <ul style="margin-top: 0.5rem; padding-left: 20px;">
                                    @foreach(array_slice(session('import_results')['errors'], 0, 10) as $error)
                                        <li style="font-size: 0.875rem;">{{ $error }}</li>
                                    @endforeach
                                    @if(count(session('import_results')['errors']) > 10)
                                        <li style="font-size: 0.875rem; color: #fbbf24;">... and {{ count(session('import_results')['errors']) - 10 }} more errors</li>
                                    @endif
                                </ul>
                            </details>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Import Banner -->
    <div class="import-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem; padding: 2rem; margin-bottom: 2rem; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-file-upload"></i>
            </div>
            <div style="flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.5rem 0;">Bulk Import Marks</h2>
                <p style="margin: 0; opacity: 0.9;">Upload your examination results from Excel or CSV file</p>
            </div>
        </div>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('student.marks.template.download', ['format' => 'excel']) }}" class="dashboard-btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-download"></i> Download Excel Template
            </a>
            <a href="{{ route('student.marks.template.download', ['format' => 'csv']) }}" class="dashboard-btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-download"></i> Download CSV Template
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <form action="{{ route('student.marks.import.store') }}" method="POST" enctype="multipart/form-data" class="form-card" style="background: white; border-radius: 0.5rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
        @csrf

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="marks_file" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                <i class="fas fa-file-excel" style="margin-right: 0.5rem; color: #667eea;"></i>
                Select File <span style="color: #ef4444;">*</span>
            </label>
            <input type="file" name="marks_file" id="marks_file" accept=".xlsx,.xls,.csv,.txt" required 
                class="form-input" style="width: 100%; padding: 0.75rem; border: 2px dashed #d1d5db; border-radius: 0.375rem; font-size: 1rem; transition: all 0.2s;"
                onchange="updateFileName(this)">
            <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                Accepted formats: .xlsx, .xls, .csv, .txt (Max size: 10MB)
            </small>
            <div id="file-name" style="margin-top: 0.5rem; color: #059669; font-weight: 500; display: none;"></div>
        </div>

        <!-- Instructions -->
        <div class="instructions-card" style="background: #f9fafb; border-left: 4px solid #667eea; padding: 1.5rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a1a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle" style="color: #667eea;"></i> File Format Instructions
            </h3>
            <div style="color: #4b5563; line-height: 1.8;">
                <p style="margin-bottom: 0.75rem;"><strong>Required Columns:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 1rem;">
                    <li><strong>Academic Level:</strong> UACE or UCE</li>
                    <li><strong>Subject Name:</strong> e.g., Mathematics, Physics, Chemistry</li>
                    <li><strong>Paper Name:</strong> (Optional) e.g., Paper 1, Paper 2</li>
                    <li><strong>Grade:</strong> A, B, C, D, E, O, F or Distinction 1, Credit 3, Pass 7</li>
                    <li><strong>Numeric Mark:</strong> (Optional) 0-100</li>
                    <li><strong>Grade Type:</strong> letter, distinction_credit_pass, or numeric</li>
                    <li><strong>Principal Pass:</strong> Yes or No</li>
                    <li><strong>Essential/Relevant/Desirable:</strong> Yes or No (optional)</li>
                    <li><strong>Academic Year:</strong> (Optional) e.g., 2024</li>
                </ul>
                <p style="margin-bottom: 0.5rem;"><strong>Tips:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Download the template first to see the exact format</li>
                    <li>Mark subjects as "Principal Pass: Yes" for UACE aggregate calculation</li>
                    <li>Leave optional fields empty if not applicable</li>
                    <li>Ensure dates are in YYYY-MM-DD format if included</li>
                </ul>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('student.marks.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-upload"></i> Upload and Import
            </button>
        </div>
    </form>
</div>

<script>
function updateFileName(input) {
    const fileNameDiv = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        fileNameDiv.textContent = 'Selected: ' + input.files[0].name;
        fileNameDiv.style.display = 'block';
    } else {
        fileNameDiv.style.display = 'none';
    }
}
</script>
@endsection

