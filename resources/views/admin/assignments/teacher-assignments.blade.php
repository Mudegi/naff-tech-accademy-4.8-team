@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Teacher Assignments</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span>Assignment Management</span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Teacher Assignments</span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="dashboard-table-container mb-4">
        <div class="dashboard-table-header">
            <h3>Filter Teacher Assignments</h3>
        </div>
        <form method="GET" action="{{ route('admin.teacher-assignments.index') }}" class="filters-form">
            <div class="filters-grid">
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

                <!-- Title Filter -->
                <div class="filter-group">
                    <label for="title">Assignment Title</label>
                    <input type="text" name="title" id="title" class="filter-input" 
                           placeholder="Search by assignment title..." value="{{ request('title') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="{{ route('admin.teacher-assignments.index') }}" class="dashboard-btn dashboard-btn-secondary">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    @if($resources->count() > 0)
        <!-- Teacher Assignments Table -->
        <div class="dashboard-table-container">
            <div class="dashboard-table-header">
                <h3>Teacher Assignment Files ({{ $resources->total() }} total)</h3>
            </div>
            <div class="dashboard-table-scroll">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Assignment Title</th>
                            <th>Teacher</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Term</th>
                            <th>File Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resources as $resource)
                        <tr>
                            <td>{{ $resource->id }}</td>
                            <td>
                                <div class="assignment-title">
                                    {{ $resource->title }}
                                </div>
                                @if($resource->description)
                                    <small class="text-muted">{{ Str::limit($resource->description, 60) }}</small>
                                @endif
                            </td>
                            <td class="font-bold">{{ $resource->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $resource->subject->name ?? 'N/A' }}</td>
                            <td>{{ $resource->grade_level ?? 'N/A' }}</td>
                            <td>{{ $resource->term->name ?? 'N/A' }}</td>
                            <td>
                                <span class="file-type-badge">
                                    {{ strtoupper($resource->assessment_tests_type) }}
                                </span>
                            </td>
                            <td>{{ $resource->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.teacher-assignments.show', $resource->id) }}" class="dashboard-btn dashboard-btn-primary dashboard-btn-xs">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.teacher-assignments.download', $resource->id) }}" class="dashboard-btn dashboard-btn-secondary dashboard-btn-xs">
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
            {{ $resources->appends(request()->query())->links() }}
        </div>
    @else
        <div class="dashboard-table-container">
            <div class="no-assignments">
                <i class="fas fa-tasks"></i>
                <h3>No Teacher Assignments Found</h3>
                <p>No teacher assignment files match your current filters.</p>
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

.file-type-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: #e5e7eb;
    color: #374151;
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
