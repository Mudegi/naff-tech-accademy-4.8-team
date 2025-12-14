@extends('layouts.student-dashboard')

@section('title', 'My Assignments')

@section('content')
<div class="my-assignments-page">
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> My Assignments</h1>
        <p>View all your submitted assignments, grades, and feedback</p>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-header">
            <h3><i class="fas fa-filter"></i> Filters</h3>
            <button id="toggleFilters" class="toggle-filters-btn">
                <i class="fas fa-chevron-down"></i> Show Filters
            </button>
        </div>
        
        <div id="filtersContent" class="filters-content" style="display: none;">
            <form method="GET" action="{{ route('student.my-assignments.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="subject_id">Subject:</label>
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="term_id">Term:</label>
                        <select name="term_id" id="term_id" class="form-control">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="grade_range">Grade Range:</label>
                        <select name="grade_range" id="grade_range" class="form-control">
                            <option value="">All Grades</option>
                            <option value="excellent" {{ request('grade_range') === 'excellent' ? 'selected' : '' }}>Excellent (90-100%)</option>
                            <option value="good" {{ request('grade_range') === 'good' ? 'selected' : '' }}>Good (80-89%)</option>
                            <option value="average" {{ request('grade_range') === 'average' ? 'selected' : '' }}>Average (70-79%)</option>
                            <option value="below_average" {{ request('grade_range') === 'below_average' ? 'selected' : '' }}>Below Average (60-69%)</option>
                            <option value="poor" {{ request('grade_range') === 'poor' ? 'selected' : '' }}>Poor (< 60%)</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="date_from">From Date:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date_to">To Date:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('student.my-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Download Best 3 Button -->
    @php
        $gradedCount = $assignments->whereNotNull('grade')->count();
    @endphp
    @if($gradedCount >= 3)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Download Best 3 Assignments</h3>
                    <p class="text-sm text-blue-700">Download a PDF report of your top 3 highest-scoring assignments.</p>
                </div>
                <a href="{{ route('student.my-assignments.best-three') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i> Download Best 3 PDF
                </a>
            </div>
        </div>
    @endif

    <!-- Assignments Table -->
    <div class="assignments-table-section">
        @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="assignments-table">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Subject</th>
                            <th>Term</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Grade</th>
                            <th>Feedback</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td class="assignment-title">
                                    <div class="title-info">
                                        <h4>{{ $assignment->resource->title }}</h4>
                                        @if($assignment->resource->topic)
                                            <span class="topic">{{ $assignment->resource->topic->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="subject">
                                    <span class="subject-badge">{{ $assignment->resource->subject->name }}</span>
                                </td>
                                <td class="term">
                                    {{ $assignment->resource->term->name }}
                                </td>
                                <td class="submitted-date">
                                    <div class="date-info">
                                        <span class="date">{{ $assignment->submitted_at->format('M d, Y') }}</span>
                                        <span class="time">{{ $assignment->submitted_at->format('H:i A') }}</span>
                                    </div>
                                </td>
                                <td class="status">
                                    <span class="status-badge status-{{ $assignment->status }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </td>
                                <td class="grade">
                                    @if($assignment->grade)
                                        <span class="grade-value grade-{{ $assignment->grade >= 90 ? 'excellent' : ($assignment->grade >= 80 ? 'good' : ($assignment->grade >= 70 ? 'average' : ($assignment->grade >= 60 ? 'below-average' : 'poor'))) }}">
                                            {{ $assignment->grade }}%
                                        </span>
                                    @else
                                        <span class="no-grade">Not graded</span>
                                    @endif
                                </td>
                                <td class="feedback">
                                    @if($assignment->teacher_feedback)
                                        <div class="feedback-preview" title="{{ $assignment->teacher_feedback }}">
                                            {{ Str::limit($assignment->teacher_feedback, 50) }}
                                        </div>
                                    @else
                                        <span class="no-feedback">No feedback</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('student.my-videos.show', $assignment->resource->id) }}" class="btn btn-sm btn-primary" title="View Assignment">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('student.my-assignments.download', $assignment->id) }}" class="btn btn-sm btn-secondary" title="Download Assignment">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('student.my-assignments.report', $assignment->id) }}" class="btn btn-sm btn-success" title="Download Report">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Download Best 3 Action Button -->
            @php
                $gradedAssignments = $assignments->whereNotNull('grade');
                $hasBestThree = $gradedAssignments->count() >= 3;
            @endphp
            @if($hasBestThree)
                <div class="mt-4 text-center">
                    <a href="{{ route('student.my-assignments.best-three') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-trophy"></i>
                        Download Best 3 Assignments PDF
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $assignments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-assignments">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Assignments Found</h3>
                <p>You haven't submitted any assignments yet, or no assignments match your current filters.</p>
                <a href="{{ route('student.my-videos') }}" class="btn btn-primary">
                    <i class="fas fa-video"></i> View My Videos
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.my-assignments-page {
    padding: 1rem;
    max-width: 1400px;
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

.filters-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.filters-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-filters-btn {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-filters-btn:hover {
    background: #2563eb;
}

.filters-content {
    padding: 1.5rem;
}

.filters-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.form-control {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #374151;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
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

.btn-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
}

.assignments-table-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.assignments-table {
    width: 100%;
    border-collapse: collapse;
}

.assignments-table th {
    background: #f8fafc;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.875rem;
}

.assignments-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.assignments-table tr:hover {
    background: #f9fafb;
}

.assignment-title .title-info h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.25rem 0;
}

.assignment-title .topic {
    font-size: 0.75rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
}

.subject-badge {
    background: #dbeafe;
    color: #1e40af;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.date-info .date {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.date-info .time {
    font-size: 0.75rem;
    color: #6b7280;
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
    font-weight: 700;
    font-size: 0.875rem;
}

.grade-excellent {
    color: #059669;
}

.grade-good {
    color: #3b82f6;
}

.grade-average {
    color: #f59e0b;
}

.grade-below-average {
    color: #f97316;
}

.grade-poor {
    color: #ef4444;
}

.no-grade, .no-feedback {
    color: #9ca3af;
    font-size: 0.875rem;
    font-style: italic;
}

.feedback-preview {
    font-size: 0.875rem;
    color: #6b7280;
    cursor: help;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.pagination-wrapper {
    padding: 1rem;
    display: flex;
    justify-content: center;
    border-top: 1px solid #e5e7eb;
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
    margin: 0 0 1.5rem 0;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .assignments-table {
        font-size: 0.75rem;
    }
    
    .assignments-table th,
    .assignments-table td {
        padding: 0.75rem 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFilters');
    const filtersContent = document.getElementById('filtersContent');
    const chevron = toggleBtn.querySelector('i');
    
    toggleBtn.addEventListener('click', function() {
        if (filtersContent.style.display === 'none') {
            filtersContent.style.display = 'block';
            chevron.className = 'fas fa-chevron-up';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Filters';
        } else {
            filtersContent.style.display = 'none';
            chevron.className = 'fas fa-chevron-down';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Show Filters';
        }
    });
    
    // Show filters if any are applied
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = Array.from(urlParams.keys()).some(key => 
        ['status', 'subject_id', 'term_id', 'grade_range', 'date_from', 'date_to'].includes(key) && urlParams.get(key)
    );
    
    if (hasFilters) {
        filtersContent.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Filters';
    }
});
</script>
@endsection
