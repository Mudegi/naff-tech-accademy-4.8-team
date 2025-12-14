@extends('layouts.student-dashboard')

@section('title', 'Assignments for ' . $resource->title)

@section('content')
<div class="assignments-by-resource-page">
    <div class="page-header">
        <a href="{{ route('teacher.assignments.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back to All Assignments</span>
        </a>
        <h1><i class="fas fa-clipboard-check"></i> Assignments for "{{ $resource->title }}"</h1>
    </div>

    @if($assignments->count() > 0)
        <div class="assignments-list">
            @foreach($assignments as $assignment)
                <div class="assignment-card">
                    <div class="assignment-header">
                        <div class="student-info">
                            <h3>{{ $assignment->student->name }}</h3>
                            <p class="submission-date">
                                <i class="fas fa-calendar"></i>
                                Submitted: {{ $assignment->submitted_at->format('M d, Y H:i A') }}
                            </p>
                        </div>
                        <div class="assignment-meta">
                            <span class="status-badge status-{{ $assignment->status }}">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="assignment-actions">
                        <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Assignment
                        </a>
                        <a href="{{ route('teacher.assignments.download', $assignment->id) }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Download
                        </a>
                        @if($assignment->status === 'submitted')
                            <button class="btn btn-success" onclick="markAsReviewed({{ $assignment->id }})">
                                <i class="fas fa-check"></i> Mark as Reviewed
                            </button>
                        @endif
                    </div>
                    
                    @if($assignment->teacher_feedback)
                        <div class="feedback-section">
                            <h4>Your Feedback:</h4>
                            <p>{{ $assignment->teacher_feedback }}</p>
                        </div>
                    @endif
                    
                    @if($assignment->grade)
                        <div class="grade-section">
                            <h4>Grade: <span class="grade-value">{{ $assignment->grade }}%</span></h4>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="no-assignments">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Assignments Submitted Yet</h3>
            <p>Students haven't submitted any assignments for this video yet.</p>
        </div>
    @endif
</div>

<style>
.assignments-by-resource-page {
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

.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.assignment-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.assignment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.student-info h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.25rem 0;
}

.submission-date {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.assignment-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
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

.assignment-actions {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    color: white;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    color: white;
}

.feedback-section, .grade-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.feedback-section h4, .grade-section h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.feedback-section p {
    color: #6b7280;
    margin: 0;
    font-size: 0.875rem;
}

.grade-value {
    color: #059669;
    font-weight: 700;
}

.no-assignments {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.no-assignments i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #9ca3af;
}

.no-assignments h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.no-assignments p {
    margin: 0;
}

@media (max-width: 640px) {
    .assignment-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .assignment-meta {
        align-items: flex-start;
    }
    
    .assignment-actions {
        flex-direction: column;
    }
}
</style>

<script>
function markAsReviewed(assignmentId) {
    if (confirm('Are you sure you want to mark this assignment as reviewed?')) {
        fetch(`/student/assignments/${assignmentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: 'reviewed'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating assignment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating assignment. Please try again.');
        });
    }
}
</script>
@endsection
