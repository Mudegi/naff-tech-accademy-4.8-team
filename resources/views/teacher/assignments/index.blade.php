@extends('layouts.student-dashboard')

@section('title', 'Student Assignments')

@section('content')
<div class="assignments-page">
    <div class="page-header">
        <h1><i class="fas fa-clipboard-check"></i> Student Assignments</h1>
        <p>Review and grade student assignment submissions</p>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form method="GET" action="{{ route('teacher.assignments.index') }}" class="filters-form">
            <div class="filters-grid">
                <!-- Status Filter -->
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
                    </select>
                </div>

                <!-- Subject Filter -->
                <div class="filter-group">
                    <label for="subject_id">Subject</label>
                    <select name="subject_id" id="subject_id" class="filter-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Term Filter -->
                <div class="filter-group">
                    <label for="term_id">Term</label>
                    <select name="term_id" id="term_id" class="filter-select">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade Level Filter -->
                <div class="filter-group">
                    <label for="grade_level">Grade Level</label>
                    <select name="grade_level" id="grade_level" class="filter-select">
                        <option value="">All Grades</option>
                        @foreach($gradeLevels as $grade)
                            <option value="{{ $grade }}" {{ request('grade_level') == $grade ? 'selected' : '' }}>
                                Grade {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Topic Filter -->
                <div class="filter-group">
                    <label for="topic_id">Topic</label>
                    <select name="topic_id" id="topic_id" class="filter-select">
                        <option value="">All Topics</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Class Filter -->
                <div class="filter-group">
                    <label for="class_id">Class</label>
                    <select name="class_id" id="class_id" class="filter-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade Range Filter -->
                <div class="filter-group">
                    <label for="grade_range">Grade Range</label>
                    <select name="grade_range" id="grade_range" class="filter-select">
                        <option value="">All Grades</option>
                        <option value="excellent" {{ request('grade_range') === 'excellent' ? 'selected' : '' }}>Excellent (90-100%)</option>
                        <option value="good" {{ request('grade_range') === 'good' ? 'selected' : '' }}>Good (80-89%)</option>
                        <option value="average" {{ request('grade_range') === 'average' ? 'selected' : '' }}>Average (70-79%)</option>
                        <option value="below_average" {{ request('grade_range') === 'below_average' ? 'selected' : '' }}>Below Average (60-69%)</option>
                        <option value="poor" {{ request('grade_range') === 'poor' ? 'selected' : '' }}>Poor (<60%)</option>
                    </select>
                </div>

                <!-- Student Name Filter -->
                <div class="filter-group">
                    <label for="student_name">Student Name</label>
                    <input type="text" name="student_name" id="student_name" class="filter-input" 
                           placeholder="Search by student name..." value="{{ request('student_name') }}">
                </div>

                <!-- Date Range Filters -->
                <div class="filter-group">
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="filter-input" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="filter-group">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="filter-input" 
                           value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="{{ route('teacher.assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
                <a href="{{ route('teacher.assignments.reports.student-scores') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-download"></i> Download Scores Report
                </a>
                <a href="{{ route('teacher.assignments.bulk.form') }}" class="btn btn-info">
                    <i class="fas fa-clipboard-list"></i> Bulk Grade
                </a>
            </div>
        </form>
    </div>

    @if($assignments->count() > 0)
        <div class="assignments-list">
            @foreach($assignments as $assignment)
                <div class="assignment-card">
                    <div class="assignment-header">
                        <div class="student-info">
                            <h3>{{ $assignment->student->name }}</h3>
                            <p class="resource-title">{{ $assignment->resource->title }}</p>
                        </div>
                        <div class="assignment-meta">
                            <span class="submission-date">
                                <i class="fas fa-calendar"></i>
                                {{ $assignment->submitted_at->format('M d, Y H:i A') }}
                            </span>
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
        
        <div class="pagination-wrapper">
            {{ $assignments->links() }}
        </div>
    @else
        <div class="no-assignments">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Assignments Submitted Yet</h3>
            <p>Students haven't submitted any assignments for your videos yet.</p>
        </div>
    @endif
</div>

<style>
.assignments-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #3b82f6;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.page-header p {
    color: #6b7280;
    margin: 0;
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
}

.filters-form {
    width: 100%;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.filter-select, .filter-input {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background: white;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-actions .btn {
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

.filter-actions .btn-primary {
    background: #3b82f6;
    color: white;
}

.filter-actions .btn-primary:hover {
    background: #2563eb;
    color: white;
}

.filter-actions .btn-secondary {
    background: #6b7280;
    color: white;
}

.filter-actions .btn-secondary:hover {
    background: #4b5563;
    color: white;
}

.filter-actions .btn-success {
    background: #10b981;
    color: white;
}

.filter-actions .btn-success:hover {
    background: #059669;
    color: white;
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

.resource-title {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.assignment-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.submission-date {
    color: #6b7280;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
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

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

@media (max-width: 640px) {
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .filter-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
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
