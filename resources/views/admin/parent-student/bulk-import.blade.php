@extends('layouts.dashboard')

@section('title', 'Bulk Import Parent-Student Links')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-file-upload me-2"></i>Bulk Import Parent-Student Links
            </h1>
            <p class="text-muted mb-0">Import multiple parent-student relationships from CSV file</p>
        </div>
        <a href="{{ route('admin.parent-student.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Links
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Import Warnings</h5>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Instructions Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Instructions</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">CSV File Format:</h6>
                    <ol class="mb-4">
                        <li class="mb-2">Download the template file below</li>
                        <li class="mb-2">Fill in the required columns:
                            <ul class="mt-2">
                                <li><strong>parent_email</strong>: Email of the parent account</li>
                                <li><strong>student_email</strong>: Email of the student account</li>
                                <li><strong>relationship</strong>: parent, guardian, or sponsor</li>
                                <li><strong>is_primary</strong>: yes or no (primary contact)</li>
                                <li><strong>receive_notifications</strong>: yes or no</li>
                            </ul>
                        </li>
                        <li class="mb-2">Save as CSV file</li>
                        <li class="mb-2">Upload the file using the form</li>
                    </ol>

                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Both parent and student accounts must already exist in the system before importing.
                    </div>

                    <a href="{{ route('admin.parent-student.download-template') }}" class="btn btn-success w-100">
                        <i class="fas fa-download me-2"></i>Download CSV Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload Form Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload CSV File</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.parent-student.process-bulk-import') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="csv_file" class="form-label fw-bold">
                                Select CSV File <span class="text-danger">*</span>
                            </label>
                            <input type="file" 
                                   class="form-control @error('csv_file') is-invalid @enderror" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv,.txt"
                                   required>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Accepted formats: .csv, .txt (Max size: 10MB)
                            </small>
                        </div>

                        <div id="filePreview" class="mb-4" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-csv text-success fs-3 me-3"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold" id="fileName"></div>
                                            <small class="text-muted" id="fileSize"></small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-upload me-2"></i>Upload and Import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Example Data Card -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Example CSV Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>parent_email</th>
                                    <th>student_email</th>
                                    <th>relationship</th>
                                    <th>is_primary</th>
                                    <th>receive_notifications</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>john.doe@example.com</td>
                                    <td>mary.doe@school.com</td>
                                    <td>parent</td>
                                    <td>yes</td>
                                    <td>yes</td>
                                </tr>
                                <tr>
                                    <td>john.doe@example.com</td>
                                    <td>james.doe@school.com</td>
                                    <td>parent</td>
                                    <td>no</td>
                                    <td>yes</td>
                                </tr>
                                <tr>
                                    <td>jane.smith@example.com</td>
                                    <td>tom.smith@school.com</td>
                                    <td>guardian</td>
                                    <td>yes</td>
                                    <td>no</td>
                                </tr>
                                <tr>
                                    <td>sponsor@company.com</td>
                                    <td>sarah.jones@school.com</td>
                                    <td>sponsor</td>
                                    <td>yes</td>
                                    <td>yes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-lightbulb me-1"></i>
                        <strong>Tip:</strong> You can link one parent to multiple students (for siblings) by using the same parent_email in multiple rows with different student_email values.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size) + ' - ' + file.type;
        document.getElementById('filePreview').style.display = 'block';
    }
});

function clearFile() {
    document.getElementById('csv_file').value = '';
    document.getElementById('filePreview').style.display = 'none';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

document.getElementById('uploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';
});
</script>
@endsection
