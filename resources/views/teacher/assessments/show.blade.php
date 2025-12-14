@extends('layouts.student-dashboard')

@section('title', 'Assessment Details - ' . $resource->title)

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <a href="{{ route('teacher.assessments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assessments
            </a>
        </div>
        <div class="header-right">
            <h1 class="content-title">{{ $resource->title }}</h1>
            <p class="content-subtitle">Assessment and study material management</p>
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

        <div class="resource-details">
            <div class="resource-info">
                <div class="info-card">
                    <h3>Resource Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Subject:</label>
                            <span>{{ $resource->subject->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Class:</label>
                            <span>{{ $resource->classRoom->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Topic:</label>
                            <span>{{ $resource->topic->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Created:</label>
                            <span>{{ $resource->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="description">
                        <label>Description:</label>
                        <p>{{ $resource->description }}</p>
                    </div>
                </div>
            </div>

            <div class="assessment-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>Assessment Test</h3>
                        @if($resource->assessment_tests_path)
                            <div class="file-status uploaded">
                                <i class="fas fa-check-circle"></i>
                                Uploaded
                            </div>
                        @else
                            <div class="file-status not-uploaded">
                                <i class="fas fa-exclamation-circle"></i>
                                Not Uploaded
                            </div>
                        @endif
                    </div>
                    
                    <div class="section-content">
                        @if($resource->assessment_tests_path)
                            <div class="file-details">
                                <div class="file-info">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    <div class="file-meta">
                                        <span class="file-name">Assessment Test</span>
                                        <span class="file-type">PDF Document</span>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a href="{{ route('teacher.assessments.download.assessment', $resource->id) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button class="btn btn-danger" onclick="deleteAssessment({{ $resource->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                            
                            <!-- PDF Preview Section -->
                            <div class="file-preview mt-4">
                                <div class="pdf-container">
                                    <iframe 
                                        src="{{ asset('storage/' . $resource->assessment_tests_path) }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                                        width="100%" 
                                        height="600px" 
                                        frameborder="0"
                                        class="preview-frame"
                                        title="Assessment Test PDF Preview"
                                    ></iframe>
                                </div>
                            </div>
                        @else
                            <div class="upload-section">
                                <form id="assessmentForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="upload-area">
                                        <input type="file" 
                                               id="assessmentFile" 
                                               name="assessment_tests" 
                                               accept=".pdf" 
                                               class="file-input"
                                               onchange="uploadAssessment({{ $resource->id }})">
                                        <label for="assessmentFile" class="upload-label">
                                            <i class="fas fa-upload"></i>
                                            <span>Upload Assessment Test</span>
                                            <small>PDF files only, max 10MB</small>
                                        </label>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-header">
                        <h3>Study Materials</h3>
                        @if($resource->notes_file_path)
                            <div class="file-status uploaded">
                                <i class="fas fa-check-circle"></i>
                                Uploaded
                            </div>
                        @else
                            <div class="file-status not-uploaded">
                                <i class="fas fa-exclamation-circle"></i>
                                Not Uploaded
                            </div>
                        @endif
                    </div>
                    
                    <div class="section-content">
                        @if($resource->notes_file_path)
                            <div class="file-details">
                                <div class="file-info">
                                    <i class="fas fa-file-alt text-primary"></i>
                                    <div class="file-meta">
                                        <span class="file-name">Study Material</span>
                                        <span class="file-type">{{ strtoupper($resource->notes_file_type) }} Document</span>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a href="{{ route('teacher.assessments.download.notes', $resource->id) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button class="btn btn-danger" onclick="deleteNotes({{ $resource->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                            
                            <!-- File Preview Section -->
                            <div class="file-preview mt-4">
                                @php
                                    $fileUrl = asset('storage/' . $resource->notes_file_path);
                                    $fileExtension = strtolower(pathinfo($resource->notes_file_path, PATHINFO_EXTENSION));
                                @endphp
                                
                                @if($fileExtension == 'pdf')
                                    <div class="pdf-container">
                                        <iframe 
                                            src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                                            width="100%" 
                                            height="600px" 
                                            frameborder="0"
                                            class="preview-frame"
                                            title="Study Material PDF Preview"
                                        ></iframe>
                                    </div>
                                @elseif(in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                                    <div class="office-preview">
                                        <iframe 
                                            src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" 
                                            width="100%" 
                                            height="600px" 
                                            frameborder="0"
                                            class="preview-frame"
                                            title="Study Material Preview"
                                        ></iframe>
                                    </div>
                                @else
                                    <div class="unsupported-format">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <p>Preview not available for this file type. Please download to view the file.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="upload-section">
                                <form id="notesForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="upload-area">
                                        <input type="file" 
                                               id="notesFile" 
                                               name="notes_file" 
                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" 
                                               class="file-input"
                                               onchange="uploadNotes({{ $resource->id }})">
                                        <label for="notesFile" class="upload-label">
                                            <i class="fas fa-upload"></i>
                                            <span>Upload Study Material</span>
                                            <small>PDF, DOC, PPT, XLS files, max 10MB</small>
                                        </label>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($assignments->count() > 0)
                <div class="assignments-section">
                    <div class="section-card">
                        <div class="section-header">
                            <h3>Student Submissions</h3>
                            <span class="badge">{{ $assignments->count() }} submissions</span>
                        </div>
                        
                        <div class="section-content">
                            <div class="assignments-list">
                                @foreach($assignments as $assignment)
                                    <div class="assignment-item">
                                        <div class="assignment-info">
                                            <div class="student-info">
                                                <i class="fas fa-user"></i>
                                                <span class="student-name">{{ $assignment->student->name }}</span>
                                            </div>
                                            <div class="assignment-meta">
                                                <span class="submission-date">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $assignment->submitted_at->format('M d, Y H:i') }}
                                                </span>
                                                <span class="assignment-status status-{{ $assignment->status }}">
                                                    {{ ucfirst($assignment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="assignment-actions">
                                            <a href="{{ route('student.my-assignments.download', $assignment->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @if($assignment->status === 'submitted')
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="reviewAssignment({{ $assignment->id }})">
                                                    <i class="fas fa-check"></i> Review
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left {
    flex: 0 0 auto;
}

.header-right {
    flex: 1;
    text-align: right;
}

.resource-details {
    display: grid;
    gap: 2rem;
}

.resource-info {
    grid-column: 1 / -1;
}

.info-card, .section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.info-card {
    padding: 2rem;
}

.section-card {
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.section-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.file-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.file-status.uploaded {
    background: #d1fae5;
    color: #065f46;
}

.file-status.not-uploaded {
    background: #fef3c7;
    color: #92400e;
}

.section-content {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
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

.description {
    margin-top: 1rem;
}

.description label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    display: block;
    margin-bottom: 0.5rem;
}

.description p {
    color: #6b7280;
    line-height: 1.6;
    margin: 0;
}

.file-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.file-info i {
    font-size: 2rem;
}

.file-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.file-name {
    font-weight: 600;
    color: #1f2937;
}

.file-type {
    font-size: 0.875rem;
    color: #6b7280;
}

.file-actions {
    display: flex;
    gap: 0.75rem;
}

.upload-section {
    padding: 1rem;
}

.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: border-color 0.2s ease, background-color 0.2s ease;
}

.upload-area:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
}

.file-input {
    display: none;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    color: #6b7280;
}

.upload-label:hover {
    color: #3b82f6;
}

.upload-label i {
    font-size: 2rem;
}

.upload-label span {
    font-weight: 600;
    font-size: 1.125rem;
}

.upload-label small {
    font-size: 0.875rem;
    opacity: 0.8;
}

.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.assignment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.assignment-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.student-name {
    font-weight: 600;
    color: #1f2937;
}

.assignment-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.assignment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-submitted {
    background: #dbeafe;
    color: #1e40af;
}

.status-reviewed {
    background: #d1fae5;
    color: #065f46;
}

.assignment-actions {
    display: flex;
    gap: 0.5rem;
}

.badge {
    background: #3b82f6;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
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

/* PDF and File Preview Styles */
.file-preview {
    margin-top: 1rem;
}

.pdf-container, .office-preview {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    background: #f9fafb;
}

.preview-frame {
    display: block;
    border: none;
    background: white;
}

.unsupported-format {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
}

.unsupported-format i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #f59e0b;
}

.unsupported-format p {
    margin: 0;
    text-align: center;
    font-size: 0.875rem;
}
</style>

<script>
function uploadAssessment(resourceId) {
    const form = document.getElementById('assessmentForm');
    const fileInput = document.getElementById('assessmentFile');
    const formData = new FormData(form);
    
    if (!fileInput.files[0]) {
        alert('Please select a file to upload.');
        return;
    }
    
    // Show loading state
    const uploadLabel = form.querySelector('.upload-label');
    const originalContent = uploadLabel.innerHTML;
    uploadLabel.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Uploading...</span><small>Please wait</small>';
    
    fetch(`/student/assessments/${resourceId}/upload-assessment`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Upload failed: ' + data.message);
            uploadLabel.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload failed. Please try again.');
        uploadLabel.innerHTML = originalContent;
    });
}

function uploadNotes(resourceId) {
    const form = document.getElementById('notesForm');
    const fileInput = document.getElementById('notesFile');
    const formData = new FormData(form);
    
    if (!fileInput.files[0]) {
        alert('Please select a file to upload.');
        return;
    }
    
    // Show loading state
    const uploadLabel = form.querySelector('.upload-label');
    const originalContent = uploadLabel.innerHTML;
    uploadLabel.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Uploading...</span><small>Please wait</small>';
    
    fetch(`/student/assessments/${resourceId}/upload-notes`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Upload failed: ' + data.message);
            uploadLabel.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload failed. Please try again.');
        uploadLabel.innerHTML = originalContent;
    });
}

function deleteAssessment(resourceId) {
    if (!confirm('Are you sure you want to delete this assessment test?')) {
        return;
    }
    
    fetch(`/student/assessments/${resourceId}/delete-assessment`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Delete failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Delete failed. Please try again.');
    });
}

function deleteNotes(resourceId) {
    if (!confirm('Are you sure you want to delete this study material?')) {
        return;
    }
    
    fetch(`/student/assessments/${resourceId}/delete-notes`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Delete failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Delete failed. Please try again.');
    });
}

function reviewAssignment(assignmentId) {
    // This would open a modal or redirect to a review page
    alert('Assignment review functionality will be implemented here.');
}
</script>
@endsection
