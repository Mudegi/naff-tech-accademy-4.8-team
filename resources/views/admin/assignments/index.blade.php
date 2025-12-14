@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Student Assignment Submissions</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span>Assignment Management</span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Student Submissions</span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="dashboard-table-container mb-4">
        <div class="dashboard-table-header">
            <h3>Filter Assignments</h3>
        </div>
        <form method="GET" action="{{ route('admin.assignments.index') }}" class="filters-form">
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

                <!-- Teacher Filter -->
                <div class="filter-group">
                    <label for="teacher_id">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="filter-select">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
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
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="{{ route('admin.assignments.index') }}" class="dashboard-btn dashboard-btn-secondary">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    @if($assignments->count() > 0)
        <!-- Assignments Table -->
        <div class="dashboard-table-container">
            <div class="dashboard-table-header">
                <h3>Student Assignment Submissions ({{ $assignments->total() }} total)</h3>
            </div>
            <div class="dashboard-table-scroll">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Assignment</th>
                            <th>Teacher</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Grade/Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td class="font-bold">{{ $assignment->student->name }}</td>
                            <td>
                                <div class="assignment-title">
                                    {{ $assignment->resource->title }}
                                </div>
                                <small class="text-muted">{{ $assignment->resource->subject->name ?? 'N/A' }} - Grade {{ $assignment->resource->grade_level ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $assignment->resource->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->resource->subject->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->resource->grade_level ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $assignment->status }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </td>
                            <td>{{ $assignment->submitted_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($assignment->grade)
                                    <span class="grade-value">{{ $assignment->grade }}%</span>
                                @else
                                    <span class="text-muted">Not graded</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.assignments.show', $assignment->id) }}" class="dashboard-btn dashboard-btn-primary dashboard-btn-xs">
                                    <i class="fas fa-eye"></i> View Online
                                </a>
                                <a href="{{ route('admin.assignments.download', $assignment->id) }}" class="dashboard-btn dashboard-btn-secondary dashboard-btn-xs">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $assignments->appends(request()->query())->links() }}
        </div>
    @else
        <div class="dashboard-table-container">
            <div class="no-assignments">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Assignment Submissions Found</h3>
                <p>No student assignments match your current filters.</p>
            </div>
        </div>
    @endif
</div>

<style>
.filters-form {
    padding: 1.5rem;
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

.assignment-title {
    font-weight: 500;
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
    
    .filter-actions .dashboard-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
