@extends('layouts.student-dashboard')

@section('title', 'Upload Assignment')

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <a href="{{ route('teacher.assessments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assessments
            </a>
        </div>
        <div class="header-right">
            <h1 class="content-title">Upload Assignment</h1>
            <p class="content-subtitle">Select a video and upload its assignment or study material</p>
        </div>
    </div>

    <div class="content-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="upload-form-container">
            <div class="form-card">
                <div class="form-header">
                    <h3>Assignment Upload Form</h3>
                    <p>Choose a video resource and upload its assignment or study material</p>
                </div>

                <form id="assignmentUploadForm" enctype="multipart/form-data" class="upload-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="resource_id" class="form-label">
                            <i class="fas fa-video"></i>
                            Select Video Resource
                        </label>
                        <select name="resource_id" id="resource_id" class="form-select" required>
                            <option value="">Choose a video resource...</option>
                            @foreach($resources as $resource)
                                <option value="{{ $resource->id }}" 
                                        data-subject="{{ $resource->subject->name ?? 'N/A' }}"
                                        data-class="{{ $resource->classRoom->name ?? 'N/A' }}"
                                        data-topic="{{ $resource->topic->name ?? 'N/A' }}">
                                    {{ $resource->title }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-help">Select the video resource for which you want to upload an assignment</div>
                    </div>

                    <div id="resource-info" class="resource-info-card" style="display: none;">
                        <h4>Resource Information</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Subject:</label>
                                <span id="info-subject">-</span>
                            </div>
                            <div class="info-item">
                                <label>Class:</label>
                                <span id="info-class">-</span>
                            </div>
                            <div class="info-item">
                                <label>Topic:</label>
                                <span id="info-topic">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assignment_type" class="form-label">
                            <i class="fas fa-file-alt"></i>
                            Assignment Type
                        </label>
                        <select name="assignment_type" id="assignment_type" class="form-select" required>
                            <option value="">Select assignment type...</option>
                            <option value="assessment">Assessment Test (PDF only)</option>
                            <option value="notes">Study Material (PDF, DOC, PPT, XLS)</option>
                        </select>
                        <div class="form-help">Choose whether you're uploading an assessment test or study material</div>
                    </div>

                    <div class="form-group">
                        <label for="assignment_file" class="form-label">
                            <i class="fas fa-upload"></i>
                            Upload File
                        </label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <input type="file" 
                                   name="assignment_file" 
                                   id="assignment_file" 
                                   class="file-input" 
                                   required>
                            <label for="assignment_file" class="upload-label">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <span class="upload-title">Click to upload or drag and drop</span>
                                    <span class="upload-subtitle" id="fileTypeInfo">Select file type first</span>
                                </div>
                            </label>
                        </div>
                        <div class="form-help" id="fileHelp">Please select assignment type first</div>
                    </div>

                    <div id="upload-progress" class="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <div class="progress-text" id="progressText">Uploading...</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-upload" id="uploadBtn" disabled>
                            <i class="fas fa-upload"></i>
                            Upload Assignment
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>

            @if($resources->count() === 0)
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>No Video Resources Found</h3>
                    <p>You haven't created any video resources yet. Create your first resource to start uploading assignments.</p>
                    <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Resource
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.upload-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-header {
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
}

.form-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
}

.upload-form {
    padding: 2rem;
}

.form-group {
    margin-bottom: 2rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    background: white;
}

.form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-help {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.resource-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.resource-info-card h4 {
    margin: 0 0 1rem 0;
    color: #1f2937;
    font-size: 1.125rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.info-item span {
    color: #6b7280;
}

.file-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.2s ease;
    background: #fafafa;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.file-upload-area.dragover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: scale(1.02);
}

.file-input {
    display: none;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    color: #6b7280;
}

.upload-icon {
    font-size: 3rem;
    color: #9ca3af;
    transition: color 0.2s ease;
}

.upload-label:hover .upload-icon {
    color: #3b82f6;
}

.upload-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.upload-title {
    font-weight: 600;
    font-size: 1.125rem;
    color: #374151;
}

.upload-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
}

.upload-progress {
    margin-top: 1rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    width: 0%;
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-upload {
    min-width: 180px;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.alert-danger {
    background-color: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resourceSelect = document.getElementById('resource_id');
    const assignmentTypeSelect = document.getElementById('assignment_type');
    const fileInput = document.getElementById('assignment_file');
    const uploadBtn = document.getElementById('uploadBtn');
    const form = document.getElementById('assignmentUploadForm');
    const uploadArea = document.getElementById('fileUploadArea');
    const progressDiv = document.getElementById('upload-progress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const fileTypeInfo = document.getElementById('fileTypeInfo');
    const fileHelp = document.getElementById('fileHelp');
    const resourceInfo = document.getElementById('resource-info');

    // Handle resource selection
    resourceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('info-subject').textContent = selectedOption.dataset.subject;
            document.getElementById('info-class').textContent = selectedOption.dataset.class;
            document.getElementById('info-topic').textContent = selectedOption.dataset.topic;
            resourceInfo.style.display = 'block';
        } else {
            resourceInfo.style.display = 'none';
        }
        updateUploadButton();
    });

    // Handle assignment type selection
    assignmentTypeSelect.addEventListener('change', function() {
        const type = this.value;
        if (type === 'assessment') {
            fileTypeInfo.textContent = 'PDF files only, max 10MB';
            fileHelp.textContent = 'Assessment tests must be in PDF format';
            fileInput.accept = '.pdf';
        } else if (type === 'notes') {
            fileTypeInfo.textContent = 'PDF, DOC, PPT, XLS files, max 10MB';
            fileHelp.textContent = 'Study materials can be PDF, DOC, DOCX, PPT, PPTX, XLS, or XLSX files';
            fileInput.accept = '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx';
        } else {
            fileTypeInfo.textContent = 'Select file type first';
            fileHelp.textContent = 'Please select assignment type first';
            fileInput.accept = '';
        }
        updateUploadButton();
    });

    // Handle file selection
    fileInput.addEventListener('change', function() {
        updateUploadButton();
    });

    // Update upload button state
    function updateUploadButton() {
        const hasResource = resourceSelect.value !== '';
        const hasType = assignmentTypeSelect.value !== '';
        const hasFile = fileInput.files.length > 0;
        
        uploadBtn.disabled = !(hasResource && hasType && hasFile);
    }

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateUploadButton();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Show progress
        progressDiv.style.display = 'block';
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressFill.style.width = progress + '%';
        }, 200);
        
        fetch('/student/assessments/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressFill.style.width = '100%';
            progressText.textContent = 'Upload complete!';
            
            setTimeout(() => {
                if (data.success) {
                    alert('Success: ' + data.message);
                    resetForm();
                } else {
                    alert('Error: ' + data.message);
                    resetUploadButton();
                }
            }, 500);
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Error:', error);
            alert('Upload failed. Please try again.');
            resetUploadButton();
        });
    });

    function resetForm() {
        form.reset();
        resourceInfo.style.display = 'none';
        progressDiv.style.display = 'none';
        progressFill.style.width = '0%';
        fileTypeInfo.textContent = 'Select file type first';
        fileHelp.textContent = 'Please select assignment type first';
        fileInput.accept = '';
        updateUploadButton();
    }

    function resetUploadButton() {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Assignment';
        progressDiv.style.display = 'none';
        progressFill.style.width = '0%';
    }
});
</script>
@endsection
