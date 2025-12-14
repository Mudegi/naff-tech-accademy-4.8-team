@extends('layouts.student-dashboard')

@section('title', 'Assessment Management')

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <h1 class="content-title">Assessment Management</h1>
            <p class="content-subtitle">Manage assessment tests and study materials for your videos</p>
        </div>
        <div class="header-right">
            <a href="{{ route('teacher.assessments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Upload Assignment
            </a>
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

        @if($resources->count() > 0)
            <div class="resources-grid">
                @foreach($resources as $resource)
                    <div class="resource-card">
                        <div class="resource-header">
                            <h3 class="resource-title">{{ $resource->title }}</h3>
                            <div class="resource-meta">
                                <span class="meta-item">
                                    <i class="fas fa-book"></i>
                                    {{ $resource->subject->name ?? 'N/A' }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-users"></i>
                                    {{ $resource->classRoom->name ?? 'N/A' }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    {{ $resource->topic->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="resource-content">
                            <p class="resource-description">{{ Str::limit($resource->description, 150) }}</p>
                            
                            <div class="resource-files">
                                <div class="file-section">
                                    <h4>Assessment Test</h4>
                                    @if($resource->assessment_tests_path)
                                        <div class="file-info">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            <span>Assessment uploaded</span>
                                            <div class="file-actions">
                                                <a href="{{ route('teacher.assessments.download.assessment', $resource->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteAssessment({{ $resource->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="file-upload">
                                            <form id="assessmentForm{{ $resource->id }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="upload-area">
                                                    <input type="file" 
                                                           id="assessmentFile{{ $resource->id }}" 
                                                           name="assessment_tests" 
                                                           accept=".pdf" 
                                                           class="file-input"
                                                           onchange="uploadAssessment({{ $resource->id }})">
                                                    <label for="assessmentFile{{ $resource->id }}" class="upload-label">
                                                        <i class="fas fa-upload"></i>
                                                        <span>Upload Assessment Test (PDF)</span>
                                                    </label>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-section">
                                    <h4>Study Materials</h4>
                                    @if($resource->notes_file_path)
                                        <div class="file-info">
                                            <i class="fas fa-file-alt text-primary"></i>
                                            <span>Study material uploaded</span>
                                            <div class="file-actions">
                                                <a href="{{ route('teacher.assessments.download.notes', $resource->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteNotes({{ $resource->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="file-upload">
                                            <form id="notesForm{{ $resource->id }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="upload-area">
                                                    <input type="file" 
                                                           id="notesFile{{ $resource->id }}" 
                                                           name="notes_file" 
                                                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" 
                                                           class="file-input"
                                                           onchange="uploadNotes({{ $resource->id }})">
                                                    <label for="notesFile{{ $resource->id }}" class="upload-label">
                                                        <i class="fas fa-upload"></i>
                                                        <span>Upload Study Material</span>
                                                    </label>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="resource-footer">
                            <a href="{{ route('teacher.assessments.show', $resource->id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <span class="resource-date">
                                Created: {{ $resource->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $resources->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>No Resources Found</h3>
                <p>You haven't created any video resources yet. Create your first resource to start managing assessments.</p>
                <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Resource
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.header-left {
    flex: 1;
}

.header-right {
    flex: 0 0 auto;
    margin-left: 2rem;
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

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.resource-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.resource-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.resource-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

.resource-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.resource-content {
    padding: 1.5rem;
}

.resource-description {
    color: #6b7280;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.resource-files {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.file-section h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.75rem 0;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.file-actions {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 1.5rem;
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
    gap: 0.5rem;
    cursor: pointer;
    color: #6b7280;
    font-size: 0.875rem;
}

.upload-label:hover {
    color: #3b82f6;
}

.resource-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.resource-date {
    font-size: 0.875rem;
    color: #6b7280;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
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

.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
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
</style>

<script>
function uploadAssessment(resourceId) {
    const form = document.getElementById(`assessmentForm${resourceId}`);
    const fileInput = document.getElementById(`assessmentFile${resourceId}`);
    const formData = new FormData(form);
    
    if (!fileInput.files[0]) {
        alert('Please select a file to upload.');
        return;
    }
    
    // Show loading state
    const uploadLabel = form.querySelector('.upload-label');
    const originalContent = uploadLabel.innerHTML;
    uploadLabel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
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
    const form = document.getElementById(`notesForm${resourceId}`);
    const fileInput = document.getElementById(`notesFile${resourceId}`);
    const formData = new FormData(form);
    
    if (!fileInput.files[0]) {
        alert('Please select a file to upload.');
        return;
    }
    
    // Show loading state
    const uploadLabel = form.querySelector('.upload-label');
    const originalContent = uploadLabel.innerHTML;
    uploadLabel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
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
</script>
@endsection
