@extends('layouts.student-dashboard')

@section('title', 'Assignment Details')

@section('content')
<div class="assignment-detail-page">
    <div class="page-header">
        <a href="{{ route('teacher.assignments.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Assignments</span>
        </a>
        <h1><i class="fas fa-clipboard-check"></i> Assignment Details</h1>
    </div>

    <div class="assignment-detail">
        <div class="assignment-info">
            <div class="info-section">
                <h3>Student Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Student Name:</label>
                        <span>{{ $assignment->student->name }}</span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span>{{ $assignment->student->email ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span>{{ $assignment->student->phone_number ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3>Assignment Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Video/Resource:</label>
                        <span>{{ $assignment->resource->title }}</span>
                    </div>
                    <div class="info-item">
                        <label>Submitted:</label>
                        <span>{{ $assignment->submitted_at->format('M d, Y H:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-{{ $assignment->status }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    @if($assignment->reviewed_at)
                    <div class="info-item">
                        <label>Reviewed:</label>
                        <span>{{ $assignment->reviewed_at->format('M d, Y H:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="info-section">
                <h3>Assignment File</h3>
                <div class="file-info">
                    <div class="file-icon">
                        <i class="fas fa-file-pdf" style="color: #ef4444;"></i>
                    </div>
                    <div class="file-details">
                        <p><strong>File Type:</strong> {{ strtoupper($assignment->assignment_file_type) }}</p>
                        <p><strong>Submitted:</strong> {{ $assignment->submitted_at->format('M d, Y H:i A') }}</p>
                    </div>
                    <div class="file-actions">
                        <a href="{{ route('teacher.assignments.download', $assignment->id) }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Assignment
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="assignment-preview">
            <h3>Assignment Preview</h3>
            <div class="pdf-container">
                @php
                    $fileExtension = strtolower($assignment->assignment_file_type);
                @endphp
                
                @if($fileExtension === 'pdf')
                    <iframe 
                        src="{{ route('teacher.assignments.view', $assignment->id) }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                        width="100%" 
                        height="600px" 
                        frameborder="0"
                        class="preview-frame"
                        title="Assignment PDF Preview"
                    ></iframe>
                @elseif(in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                    <div class="office-preview">
                        <iframe 
                            src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(route('teacher.assignments.view', $assignment->id)) }}" 
                            width="100%" 
                            height="600px" 
                            frameborder="0"
                            class="preview-frame"
                            title="Assignment Document Preview"
                        ></iframe>
                    </div>
                @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                    <div class="image-preview">
                        <img src="{{ route('teacher.assignments.view', $assignment->id) }}" alt="Assignment Image" style="max-width: 100%; height: auto; border-radius: 0.5rem;">
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        <i class="fas fa-file" style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                        <p>Preview not available for this file type.</p>
                        <a href="{{ route('teacher.assignments.download', $assignment->id) }}" class="dashboard-btn dashboard-btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-download"></i> Download to View
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="assignment-feedback">
            <h3>Review & Feedback</h3>
            <form id="feedbackForm">
                @csrf
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="submitted" {{ $assignment->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="reviewed" {{ $assignment->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="graded" {{ $assignment->status === 'graded' ? 'selected' : '' }}>Graded</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="grade">Grade (%):</label>
                    <input type="number" name="grade" id="grade" class="form-control" 
                           value="{{ $assignment->grade }}" min="0" max="100" 
                           placeholder="Enter grade (0-100)">
                </div>

                <div class="form-group">
                    <label for="teacher_feedback">Teacher Feedback:</label>
                    <textarea name="teacher_feedback" id="teacher_feedback" class="form-control" 
                              rows="4" placeholder="Provide feedback to the student...">{{ $assignment->teacher_feedback }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Feedback
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.assignment-detail-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
}

.back-button:hover {
    background-color: #f3f4f6;
    color: #4b5563;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.assignment-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.assignment-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.info-section h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #3b82f6;
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 500;
    color: #374151;
    margin: 0;
}

.info-item span {
    color: #6b7280;
    text-align: right;
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

.file-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
}

.file-icon {
    font-size: 2rem;
    color: #ef4444;
}

.file-details {
    flex: 1;
}

.file-details p {
    margin: 0.25rem 0;
    color: #374151;
    font-size: 0.875rem;
}

.file-actions {
    flex-shrink: 0;
}

.assignment-preview {
    grid-column: 1 / -1;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.assignment-preview h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #3b82f6;
}

.pdf-container {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #f8fafc;
}

.preview-frame {
    display: block;
    border: none;
}

.assignment-feedback {
    grid-column: 1 / -1;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.assignment-feedback h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #3b82f6;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 1rem;
    color: #374151;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

@media (max-width: 768px) {
    .assignment-detail {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .assignment-preview,
    .assignment-feedback {
        grid-column: 1;
    }
    
    .file-info {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedbackForm');
    const originalData = {
        status: document.getElementById('status').value,
        grade: document.getElementById('grade').value,
        teacher_feedback: document.getElementById('teacher_feedback').value
    };

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = {
            status: formData.get('status'),
            grade: formData.get('grade') ? parseInt(formData.get('grade')) : null,
            teacher_feedback: formData.get('teacher_feedback')
        };

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(`/student/assignments/{{ $assignment->id }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback saved successfully!');
                location.reload();
            } else {
                alert('Error saving feedback: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving feedback. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    window.resetForm = function() {
        document.getElementById('status').value = originalData.status;
        document.getElementById('grade').value = originalData.grade;
        document.getElementById('teacher_feedback').value = originalData.teacher_feedback;
    };
});
</script>
@endsection
