@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner dos-dashboard">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-chalkboard-teacher text-blue-600 mr-3"></i>
                Director of Studies Dashboard
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Educational Activities</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-6 animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-6 animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- School Info Hero Card -->
    <div class="school-hero-card mb-8">
        <div class="school-hero-content">
            <div class="school-hero-left">
                <div class="school-logo-wrapper">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="school-logo">
                    @else
                        <div class="school-logo-placeholder">
                            <i class="fas fa-school"></i>
                        </div>
                    @endif
                </div>
                <div class="school-hero-info">
                    <h2 class="school-hero-title">{{ $school->name }}</h2>
                    <p class="school-hero-subtitle">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        Educational Activities Management
                    </p>
                    <p class="school-hero-role">
                        <i class="fas fa-user-tie mr-2"></i>
                        Director of Studies Dashboard
                    </p>
                </div>
            </div>
            <div class="school-hero-right">
                <div class="school-status-badge {{ $school->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    <i class="fas fa-circle mr-2"></i>
                    {{ ucfirst($school->status) }}
                </div>
                <div class="school-stats-mini">
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $stats['total_students'] }}</div>
                        <div class="mini-stat-label">Students</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $stats['total_staff'] }}</div>
                        <div class="mini-stat-label">Staff</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $stats['total_classes'] }}</div>
                        <div class="mini-stat-label">Classes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Educational Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card-modern stat-card-primary">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-primary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_departments'] }}</div>
                <div class="stat-label-modern">Total Departments</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-active">{{ $stats['active_departments'] }} Active</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-success">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_staff'] }}</div>
                <div class="stat-label-modern">Teaching Staff</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-info">{{ $stats['total_heads_of_department'] }} HODs</span>
                    <span class="stat-badge-secondary">{{ $stats['total_teachers'] }} Teachers</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-warning">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-warning">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_students'] }}</div>
                <div class="stat-label-modern">Total Students</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-success">{{ $stats['active_students'] }} Active</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-info">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-info">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_resources'] }}</div>
                <div class="stat-label-modern">Learning Resources</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-active">{{ $stats['active_resources'] }} Active</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-secondary">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-secondary">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_subjects'] }}</div>
                <div class="stat-label-modern">Subjects</div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-accent">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-accent">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_classes'] }}</div>
                <div class="stat-label-modern">Classes</div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-teal">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-teal">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $stats['total_assignments'] }}</div>
                <div class="stat-label-modern">Total Assignments</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-success">{{ $stats['graded_assignments'] }} Graded</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-excellent">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-excellent">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ number_format($stats['average_grade'] ?? 0, 1) }}%</div>
                <div class="stat-label-modern">Average Grade</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-performance">School Performance</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Status Breakdown -->
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-tasks section-icon"></i>
                <h3 class="section-title">Assignment Status Overview</h3>
            </div>
        </div>
        <div class="assignment-status-grid">
            <div class="assignment-status-card status-submitted">
                <div class="status-icon-wrapper">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['submitted_assignments'] }}</div>
                    <div class="status-label">Submitted</div>
                    <div class="status-progress">
                        <div class="status-progress-bar" style="width: {{ $stats['total_assignments'] > 0 ? ($stats['submitted_assignments'] / $stats['total_assignments'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="assignment-status-card status-reviewing">
                <div class="status-icon-wrapper">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['reviewed_assignments'] }}</div>
                    <div class="status-label">Under Review</div>
                    <div class="status-progress">
                        <div class="status-progress-bar" style="width: {{ $stats['total_assignments'] > 0 ? ($stats['reviewed_assignments'] / $stats['total_assignments'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="assignment-status-card status-graded">
                <div class="status-icon-wrapper">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['graded_assignments'] }}</div>
                    <div class="status-label">Graded</div>
                    <div class="status-progress">
                        <div class="status-progress-bar" style="width: {{ $stats['total_assignments'] > 0 ? ($stats['graded_assignments'] / $stats['total_assignments'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="assignment-status-card status-pending">
                <div class="status-icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['total_assignments'] - $stats['submitted_assignments'] - $stats['reviewed_assignments'] - $stats['graded_assignments'] }}</div>
                    <div class="status-label">Pending</div>
                    <div class="status-progress">
                        <div class="status-progress-bar" style="width: {{ $stats['total_assignments'] > 0 ? (($stats['total_assignments'] - $stats['submitted_assignments'] - $stats['reviewed_assignments'] - $stats['graded_assignments']) / $stats['total_assignments'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-bolt section-icon"></i>
                <h3 class="section-title">Quick Actions</h3>
            </div>
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('admin.school.departments.index') }}" class="quick-action-card action-orange">
                <div class="action-icon-wrapper">
                    <i class="fas fa-building"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Manage Departments</div>
                    <div class="action-description">View and manage departments</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('admin.school.staff.index') }}" class="quick-action-card action-blue">
                <div class="action-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Manage Staff</div>
                    <div class="action-description">HODs and Teachers</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('admin.school.resources.index') }}" class="quick-action-card action-indigo">
                <div class="action-icon-wrapper">
                    <i class="fas fa-video"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Learning Resources</div>
                    <div class="action-description">Manage videos and materials</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="quick-action-card action-purple">
                <div class="action-icon-wrapper">
                    <i class="fas fa-book"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Manage Subjects</div>
                    <div class="action-description">View and manage subjects</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('student.assignments.index') }}" class="quick-action-card action-teal">
                <div class="action-icon-wrapper">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">View Assignments</div>
                    <div class="action-description">Monitor student assignments</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
            <a href="{{ route('admin.school.students.index') }}" class="quick-action-card action-green">
                <div class="action-icon-wrapper">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">View Students</div>
                    <div class="action-description">Student information</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            @endif
            <a href="{{ route('admin.school.classes.index') }}" class="quick-action-card action-pink">
                <div class="action-icon-wrapper">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Manage Classes</div>
                    <div class="action-description">Create and manage classes</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('admin.director-of-studies.class-assignment-report') }}" class="quick-action-card action-red">
                <div class="action-icon-wrapper">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Download Class Reports</div>
                    <div class="action-description">Best assignments per student</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Departments Overview -->
    @if($departments->count() > 0)
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-sitemap section-icon"></i>
                <h3 class="section-title">Departments Overview</h3>
            </div>
            <a href="{{ route('admin.school.departments.index') }}" class="section-link">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Head of Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teachers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departments as $department)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $department->name }}</div>
                            @if($department->code)
                                <div class="text-sm text-gray-500">{{ $department->code }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($department->headOfDepartment)
                                {{ $department->headOfDepartment->name }}
                            @else
                                <span class="text-gray-400">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $department->teachers_count ?? 0 }} teachers
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.school.departments.show', $department->id) }}" class="text-blue-600 hover:text-blue-900">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Department Performance -->
    @if($departmentPerformance->count() > 0)
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-chart-line section-icon"></i>
                <h3 class="section-title">Department Performance</h3>
            </div>
        </div>
        <div class="performance-grid">
            @foreach($departmentPerformance as $performance)
            <div class="performance-card">
                <div class="performance-header">
                    <div class="performance-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="performance-title-group">
                        <h4 class="performance-title">{{ $performance['department']->name }}</h4>
                        <span class="performance-count">{{ $performance['total_assignments'] }} assignments</span>
                    </div>
                </div>
                <div class="performance-body">
                    <div class="performance-grade-wrapper">
                        <span class="performance-grade-label">Average Grade</span>
                        <span class="performance-grade-value {{ $performance['average_grade'] >= 80 ? 'grade-excellent' : ($performance['average_grade'] >= 70 ? 'grade-good' : 'grade-average') }}">
                            {{ number_format($performance['average_grade'], 1) }}%
                        </span>
                    </div>
                    <div class="performance-progress">
                        <div class="performance-progress-bar" style="width: {{ min($performance['average_grade'], 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Resources -->
    @if($recentResources->count() > 0)
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-video section-icon"></i>
                <h3 class="section-title">Recent Learning Resources</h3>
            </div>
            <a href="{{ route('admin.school.resources.index') }}" class="section-link">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="recent-list">
            @foreach($recentResources as $resource)
            <div class="recent-item">
                <div class="recent-item-icon">
                    <i class="fas fa-file-video"></i>
                </div>
                <div class="recent-item-content">
                    <div class="recent-item-title">{{ $resource->title }}</div>
                    <div class="recent-item-meta">
                        <span class="recent-item-badge">
                            <i class="fas fa-book mr-1"></i>
                            {{ $resource->subject->name ?? 'N/A' }}
                        </span>
                        <span class="recent-item-badge">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $resource->term->name ?? 'N/A' }}
                        </span>
                        @if($resource->teacher)
                        <span class="recent-item-badge">
                            <i class="fas fa-user mr-1"></i>
                            {{ $resource->teacher->name }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="recent-item-status">
                    <span class="status-badge-modern {{ $resource->is_active ? 'status-active-modern' : 'status-inactive-modern' }}">
                        <i class="fas fa-circle mr-1"></i>
                        {{ $resource->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Assignments -->
    @if($recentAssignments->count() > 0)
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fas fa-clipboard-list section-icon"></i>
                <h3 class="section-title">Recent Assignments</h3>
            </div>
            <a href="{{ route('student.assignments.index') }}" class="section-link">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="table-container-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Assignment</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAssignments as $assignment)
                    <tr>
                        <td>
                            <div class="table-cell-student">
                                <i class="fas fa-user-graduate table-icon"></i>
                                <span>{{ $assignment->student->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="table-cell-text">{{ \Illuminate\Support\Str::limit($assignment->resource->title, 40) }}</div>
                        </td>
                        <td>
                            <span class="table-badge">{{ $assignment->resource->subject->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="status-badge-modern 
                                {{ $assignment->status === 'graded' ? 'status-graded-modern' : 
                                   ($assignment->status === 'reviewed' ? 'status-reviewing-modern' : 'status-submitted-modern') }}">
                                <i class="fas fa-circle mr-1"></i>
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($assignment->grade)
                                <span class="grade-display {{ $assignment->grade >= 80 ? 'grade-excellent' : ($assignment->grade >= 70 ? 'grade-good' : 'grade-average') }}">
                                    {{ $assignment->grade }}%
                                </span>
                            @else
                                <span class="grade-display grade-pending">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
/* Director of Studies Dashboard Styles */
.dos-dashboard {
    padding: 1.5rem;
}

/* School Hero Card */
.school-hero-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.school-hero-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 20s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

.school-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.school-hero-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.school-logo-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.school-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 1rem;
}

.school-logo-placeholder {
    font-size: 2rem;
    color: white;
}

.school-hero-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.school-hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.school-hero-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
    display: flex;
    align-items: center;
}

.school-hero-role {
    font-size: 0.875rem;
    opacity: 0.8;
    margin: 0;
    display: flex;
    align-items: center;
}

.school-hero-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.school-status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    backdrop-filter: blur(10px);
}

.school-status-badge.status-active {
    background: rgba(16, 185, 129, 0.3);
    border: 1px solid rgba(16, 185, 129, 0.5);
}

.school-status-badge.status-inactive {
    background: rgba(239, 68, 68, 0.3);
    border: 1px solid rgba(239, 68, 68, 0.5);
}

.school-stats-mini {
    display: flex;
    gap: 1rem;
}

.mini-stat {
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.mini-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.mini-stat-label {
    font-size: 0.75rem;
    opacity: 0.9;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-modern {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.stat-card-modern:hover::before {
    width: 100%;
    opacity: 0.1;
}

.stat-card-primary::before { background: #3b82f6; }
.stat-card-success::before { background: #10b981; }
.stat-card-warning::before { background: #f59e0b; }
.stat-card-info::before { background: #06b6d4; }
.stat-card-secondary::before { background: #8b5cf6; }
.stat-card-accent::before { background: #ec4899; }
.stat-card-teal::before { background: #14b8a6; }
.stat-card-excellent::before { background: #fbbf24; }

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-icon-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-icon-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.stat-icon-secondary { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-icon-accent { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
.stat-icon-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
.stat-icon-excellent { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }

.stat-trend {
    color: #10b981;
    font-size: 0.875rem;
}

.stat-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.stat-value-modern {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    line-height: 1;
}

.stat-label-modern {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.stat-sublabel-modern {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.25rem;
}

.stat-badge-active,
.stat-badge-success,
.stat-badge-info,
.stat-badge-secondary,
.stat-badge-performance {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-badge-active { background: #dbeafe; color: #1e40af; }
.stat-badge-success { background: #d1fae5; color: #065f46; }
.stat-badge-info { background: #dbeafe; color: #1e40af; }
.stat-badge-secondary { background: #e9d5ff; color: #6b21a8; }
.stat-badge-performance { background: #fef3c7; color: #92400e; }

/* Dashboard Section Card */
.dashboard-section-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.section-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-icon {
    font-size: 1.25rem;
    color: #3b82f6;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
    border-radius: 0.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.section-link {
    color: #3b82f6;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: color 0.2s;
}

.section-link:hover {
    color: #2563eb;
}

/* Assignment Status Grid */
.assignment-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.assignment-status-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.assignment-status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.status-submitted {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.status-reviewing {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.status-graded {
    border-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
}

.status-pending {
    border-color: #6b7280;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.status-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.status-submitted .status-icon-wrapper { background: #3b82f6; }
.status-reviewing .status-icon-wrapper { background: #f59e0b; }
.status-graded .status-icon-wrapper { background: #10b981; }
.status-pending .status-icon-wrapper { background: #6b7280; }

.status-content {
    flex: 1;
}

.status-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.status-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.status-progress {
    width: 100%;
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    overflow: hidden;
}

.status-progress-bar {
    height: 100%;
    background: currentColor;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.status-submitted .status-progress-bar { background: #3b82f6; }
.status-reviewing .status-progress-bar { background: #f59e0b; }
.status-graded .status-progress-bar { background: #10b981; }
.status-pending .status-progress-bar { background: #6b7280; }

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.quick-action-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    transition: width 0.3s ease;
    z-index: 0;
}

.quick-action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    border-color: transparent;
}

.quick-action-card:hover::before {
    width: 100%;
}

.action-orange::before { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
.action-blue::before { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
.action-indigo::before { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); }
.action-purple::before { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); }
.action-teal::before { background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); }
.action-green::before { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
.action-pink::before { background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); }
.action-red::before { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }

.action-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

.action-orange .action-icon-wrapper { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.action-blue .action-icon-wrapper { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.action-indigo .action-icon-wrapper { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.action-purple .action-icon-wrapper { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.action-teal .action-icon-wrapper { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
.action-green .action-icon-wrapper { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.action-pink .action-icon-wrapper { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
.action-red .action-icon-wrapper { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

.action-content {
    flex: 1;
    position: relative;
    z-index: 1;
}

.action-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.action-description {
    font-size: 0.8125rem;
    color: #6b7280;
}

.action-arrow {
    color: #9ca3af;
    font-size: 0.875rem;
    position: relative;
    z-index: 1;
    transition: transform 0.3s ease, color 0.3s ease;
}

.quick-action-card:hover .action-arrow {
    transform: translateX(4px);
    color: #3b82f6;
}

/* Performance Grid */
.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.performance-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.performance-card:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.performance-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.performance-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.performance-title-group {
    flex: 1;
}

.performance-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.performance-count {
    font-size: 0.75rem;
    color: #6b7280;
}

.performance-body {
    margin-top: 1rem;
}

.performance-grade-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.performance-grade-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.performance-grade-value {
    font-size: 1.25rem;
    font-weight: 700;
}

.grade-excellent { color: #10b981; }
.grade-good { color: #3b82f6; }
.grade-average { color: #f59e0b; }

.performance-progress {
    width: 100%;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.performance-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Recent List */
.recent-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.recent-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    transform: translateX(4px);
}

.recent-item-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.recent-item-content {
    flex: 1;
}

.recent-item-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.recent-item-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.recent-item-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.recent-item-status {
    flex-shrink: 0;
}

/* Table Modern */
.table-container-modern {
    overflow-x: auto;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
}

.table-modern thead {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.table-modern th {
    padding: 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s ease;
}

.table-modern tbody tr:hover {
    background: #f9fafb;
}

.table-modern td {
    padding: 1rem;
    font-size: 0.875rem;
}

.table-cell-student {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #1a1a1a;
}

.table-icon {
    color: #3b82f6;
}

.table-cell-text {
    color: #4b5563;
}

.table-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #eff6ff;
    color: #1e40af;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-active-modern {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive-modern {
    background: #fee2e2;
    color: #991b1b;
}

.status-submitted-modern {
    background: #dbeafe;
    color: #1e40af;
}

.status-reviewing-modern {
    background: #fef3c7;
    color: #92400e;
}

.status-graded-modern {
    background: #d1fae5;
    color: #065f46;
}

.grade-display {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.grade-excellent {
    background: #d1fae5;
    color: #065f46;
}

.grade-good {
    background: #dbeafe;
    color: #1e40af;
}

.grade-average {
    background: #fef3c7;
    color: #92400e;
}

.grade-pending {
    color: #9ca3af;
}

/* Animations */
@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slide-down 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .school-hero-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }
    
    .school-hero-right {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .performance-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
@endsection

