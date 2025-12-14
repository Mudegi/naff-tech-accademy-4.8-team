@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Teacher Assignment Details</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span>Assignment Management</span> <span class="breadcrumb-sep">/</span> 
            <span>Teacher Assignments</span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Assignment Details</span>
        </div>
    </div>

    <div class="teacher-assignment-details-container">
        <!-- Assignment Header -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Assignment Information</h3>
            </div>
            <div class="assignment-info">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Assignment Title:</label>
                        <span class="font-bold">{{ $resource->title }}</span>
                    </div>
                    <div class="info-item">
                        <label>Teacher:</label>
                        <span>{{ $resource->teacher->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Subject:</label>
                        <span>{{ $resource->subject->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Grade Level:</label>
                        <span>Grade {{ $resource->grade_level ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Term:</label>
                        <span>{{ $resource->term->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Created:</label>
                        <span>{{ $resource->created_at->format('M d, Y H:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <label>File Type:</label>
                        <span class="file-type-badge">{{ strtoupper($resource->assessment_tests_type) }}</span>
                    </div>
                    <div class="info-item">
                        <label>Student Submissions:</label>
                        <span class="submission-count">{{ $studentSubmissions->count() }} submissions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Description -->
        @if($resource->description)
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Assignment Description</h3>
            </div>
            <div class="assignment-description">
                <p>{{ $resource->description }}</p>
            </div>
        </div>
        @endif

        <!-- Assignment File -->
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Assignment File</h3>
            </div>
            <div class="assignment-file-info">
                <div class="file-details">
                    <div class="file-icon">
                        @if($resource->assessment_tests_type === 'pdf')
                            <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                        @elseif(in_array($resource->assessment_tests_type, ['doc', 'docx']))
                            <i class="fas fa-file-word" style="color: #2563eb;"></i>
                        @elseif(in_array($resource->assessment_tests_type, ['jpg', 'jpeg', 'png', 'gif']))
                            <i class="fas fa-file-image" style="color: #059669;"></i>
                        @else
                            <i class="fas fa-file" style="color: #6b7280;"></i>
                        @endif
                    </div>
                    <div class="file-info">
                        <div class="file-name">{{ $resource->title }}_assignment.{{ $resource->assessment_tests_type }}</div>
                        <div class="file-type">File Type: {{ strtoupper($resource->assessment_tests_type) }}</div>
                        <div class="file-size">Created: {{ $resource->created_at->format('M d, Y H:i A') }}</div>
                    </div>
                </div>
                <div class="file-actions">
                    <a href="{{ route('admin.teacher-assignments.download', $resource->id) }}" class="dashboard-btn dashboard-btn-primary">
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
                    $fileUrl = asset('storage/' . $resource->assessment_tests_path);
                    $fileExtension = strtolower($resource->assessment_tests_type);
                @endphp
                
                @if($fileExtension === 'pdf')
                    <div class="pdf-container">
                        <iframe 
                            src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                            width="100%" 
                            height="600px" 
                            frameborder="0"
                            class="preview-frame"
                            title="Teacher Assignment PDF Preview"
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
                            title="Teacher Assignment Document Preview"
                        ></iframe>
                    </div>
                @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                    <div class="image-preview">
                        <img src="{{ $fileUrl }}" alt="Teacher Assignment Image" class="preview-image" style="max-width: 100%; height: auto; border-radius: 0.5rem;">
                    </div>
                @else
                    <div class="unsupported-format">
                        <div class="unsupported-content">
                            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i>
                            <h4>Preview Not Available</h4>
                            <p>Online preview is not available for this file type ({{ strtoupper($fileExtension) }}).</p>
                            <p>Please download the file to view it.</p>
                            <a href="{{ route('admin.teacher-assignments.download', $resource->id) }}" class="dashboard-btn dashboard-btn-primary">
                                <i class="fas fa-download"></i> Download to View
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Submissions -->
        @if($studentSubmissions->count() > 0)
        <div class="dashboard-table-container mb-4">
            <div class="dashboard-table-header">
                <h3>Student Submissions ({{ $studentSubmissions->count() }} total)</h3>
            </div>
            <div class="dashboard-table-scroll">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentSubmissions as $submission)
                        <tr>
                            <td>{{ $submission->id }}</td>
                            <td class="font-bold">{{ $submission->student->name }}</td>
                            <td>
                                <span class="status-badge status-{{ $submission->status }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td>{{ $submission->submitted_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($submission->grade)
                                    <span class="grade-value">{{ $submission->grade }}%</span>
                                @else
                                    <span class="text-muted">Not graded</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.assignments.show', $submission->id) }}" class="dashboard-btn dashboard-btn-primary dashboard-btn-xs">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="dashboard-table-container mb-4">
            <div class="no-submissions">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Student Submissions Yet</h3>
                <p>No students have submitted assignments for this teacher assignment yet.</p>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="assignment-actions">
            <a href="{{ route('admin.teacher-assignments.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Teacher Assignments
            </a>
            <a href="{{ route('admin.teacher-assignments.download', $resource->id) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-download"></i> Download Assignment
            </a>
        </div>
    </div>
</div>

<style>
.teacher-assignment-details-container {
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

.file-type-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: #e5e7eb;
    color: #374151;
}

.submission-count {
    color: #3b82f6;
    font-weight: 600;
}

.assignment-description {
    padding: 1.5rem;
}

.assignment-description p {
    color: #6b7280;
    line-height: 1.6;
    margin: 0;
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

.no-submissions {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.no-submissions i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #9ca3af;
}

.no-submissions h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.no-submissions p {
    margin: 0;
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
    
    .assignment-actions {
        flex-direction: column;
    }
    
    .assignment-actions .dashboard-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
