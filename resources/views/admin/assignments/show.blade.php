@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Assignment Details</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span>Assignment Management</span> <span class="breadcrumb-sep">/</span> 
            <span>Student Submissions</span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Assignment Details</span>
        </div>
    </div>

    <div class="assignment-details-container">
        <!-- Assignment Header -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Assignment Information</h3>
            </div>
            <div class="assignment-info">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Student Name:</label>
                        <span class="font-bold">{{ $assignment->student->name }}</span>
                    </div>
                    <div class="info-item">
                        <label>Assignment Title:</label>
                        <span>{{ $assignment->resource->title }}</span>
                    </div>
                    <div class="info-item">
                        <label>Teacher:</label>
                        <span>{{ $assignment->resource->teacher->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Subject:</label>
                        <span>{{ $assignment->resource->subject->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Grade Level:</label>
                        <span>Grade {{ $assignment->resource->grade_level ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Term:</label>
                        <span>{{ $assignment->resource->term->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-{{ $assignment->status }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Submitted:</label>
                        <span>{{ $assignment->submitted_at->format('M d, Y H:i A') }}</span>
                    </div>
                    @if($assignment->reviewed_at)
                    <div class="info-item">
                        <label>Reviewed:</label>
                        <span>{{ $assignment->reviewed_at->format('M d, Y H:i A') }}</span>
                    </div>
                    @endif
                    @if($assignment->grade)
                    <div class="info-item">
                        <label>Grade:</label>
                        <span class="grade-value">{{ $assignment->grade }}%</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assignment File -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Submitted Assignment</h3>
            </div>
            <div class="assignment-file-info">
                <div class="file-details">
                    <div class="file-icon">
                        @if($assignment->assignment_file_type === 'pdf')
                            <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                        @elseif(in_array($assignment->assignment_file_type, ['doc', 'docx']))
                            <i class="fas fa-file-word" style="color: #2563eb;"></i>
                        @elseif(in_array($assignment->assignment_file_type, ['jpg', 'jpeg', 'png', 'gif']))
                            <i class="fas fa-file-image" style="color: #059669;"></i>
                        @else
                            <i class="fas fa-file" style="color: #6b7280;"></i>
                        @endif
                    </div>
                    <div class="file-info">
                        <div class="file-name">{{ $assignment->student->name }}_assignment.{{ $assignment->assignment_file_type }}</div>
                        <div class="file-type">File Type: {{ strtoupper($assignment->assignment_file_type) }}</div>
                        <div class="file-size">Submitted: {{ $assignment->submitted_at->format('M d, Y H:i A') }}</div>
                    </div>
                </div>
                <div class="file-actions">
                    <a href="{{ route('admin.assignments.download', $assignment->id) }}" class="dashboard-btn dashboard-btn-primary">
                        <i class="fas fa-download"></i> Download Assignment
                    </a>
                </div>
            </div>
        </div>

        <!-- Assignment Preview -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Assignment Preview</h3>
            </div>
            <div class="assignment-preview">
                @php
                    $fileUrl = asset('storage/' . $assignment->assignment_file_path);
                    $fileExtension = strtolower($assignment->assignment_file_type);
                @endphp
                
                @if($fileExtension === 'pdf')
                    <div class="pdf-container">
                        <iframe 
                            src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                            width="100%" 
                            height="600px" 
                            frameborder="0"
                            class="preview-frame"
                            title="Assignment PDF Preview"
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
                            title="Assignment Document Preview"
                        ></iframe>
                    </div>
                @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                    <div class="image-preview">
                        <img src="{{ $fileUrl }}" alt="Assignment Image" class="preview-image" style="max-width: 100%; height: auto; border-radius: 0.5rem;">
                    </div>
                @else
                    <div class="unsupported-format">
                        <div class="unsupported-content">
                            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i>
                            <h4>Preview Not Available</h4>
                            <p>Online preview is not available for this file type ({{ strtoupper($fileExtension) }}).</p>
                            <p>Please download the file to view it.</p>
                            <a href="{{ route('admin.assignments.download', $assignment->id) }}" class="dashboard-btn dashboard-btn-primary">
                                <i class="fas fa-download"></i> Download to View
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Teacher Feedback -->
        @if($assignment->teacher_feedback)
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Teacher Feedback</h3>
            </div>
            <div class="feedback-content">
                <div class="feedback-text">
                    {{ $assignment->teacher_feedback }}
                </div>
                @if($assignment->reviewed_at)
                <div class="feedback-meta">
                    <small class="text-muted">Feedback provided on {{ $assignment->reviewed_at->format('M d, Y H:i A') }}</small>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Resource Information -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Resource Information</h3>
            </div>
            <div class="resource-info">
                <div class="resource-details">
                    <h4>{{ $assignment->resource->title }}</h4>
                    @if($assignment->resource->description)
                        <p class="resource-description">{{ $assignment->resource->description }}</p>
                    @endif
                    <div class="resource-meta">
                        <span class="meta-item">
                            <i class="fas fa-user"></i> Teacher: {{ $assignment->resource->teacher->name ?? 'N/A' }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-book"></i> Subject: {{ $assignment->resource->subject->name ?? 'N/A' }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-graduation-cap"></i> Grade: {{ $assignment->resource->grade_level ?? 'N/A' }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i> Term: {{ $assignment->resource->term->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="assignment-actions">
            <a href="{{ route('admin.assignments.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
            <a href="{{ route('admin.assignments.download', $assignment->id) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-download"></i> Download Assignment
            </a>
        </div>
    </div>
</div>

<style>
.assignment-details-container {
    max-width: 1000px;
}

.assignment-info {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.info-item span {
    font-size: 0.875rem;
    color: #1a1a1a;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-submitted {
    background-color: #dbeafe;
    color: #1e40af;
}

.status-reviewed {
    background-color: #fef3c7;
    color: #92400e;
}

.status-graded {
    background-color: #d1fae5;
    color: #065f46;
}

.grade-value {
    color: #059669;
    font-weight: 700;
}

.assignment-file-info {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.file-details {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.file-icon {
    font-size: 2rem;
}

.file-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.file-name {
    font-weight: 500;
    color: #1a1a1a;
}

.file-type, .file-size {
    font-size: 0.875rem;
    color: #6b7280;
}

.feedback-content {
    padding: 1.5rem;
}

.feedback-text {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #3b82f6;
    margin-bottom: 1rem;
    line-height: 1.6;
}

.feedback-meta {
    text-align: right;
}

.resource-info {
    padding: 1.5rem;
}

.resource-details h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.5rem 0;
}

.resource-description {
    color: #6b7280;
    margin: 0 0 1rem 0;
    line-height: 1.6;
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

.meta-item i {
    color: #9ca3af;
}

.assignment-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 2rem;
}

@media (max-width: 640px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .assignment-file-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .resource-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .assignment-actions {
        flex-direction: column;
    }
    
.assignment-actions .dashboard-btn {
    width: 100%;
    justify-content: center;
}

/* Assignment Preview Styles */
.assignment-preview {
    padding: 1.5rem;
}

.pdf-container, .office-preview, .image-preview {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #f8fafc;
}

.preview-frame {
    display: block;
    border: none;
    background: white;
}

.preview-image {
    display: block;
    margin: 0 auto;
    max-height: 600px;
    object-fit: contain;
}

.unsupported-format {
    padding: 3rem;
    text-align: center;
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 0.5rem;
}

.unsupported-content h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #92400e;
    margin: 0 0 0.5rem 0;
}

.unsupported-content p {
    color: #92400e;
    margin: 0.5rem 0;
}

.unsupported-content .dashboard-btn {
    margin-top: 1rem;
}
}
</style>
@endsection
