@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner teacher-dashboard">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-chalkboard-teacher text-blue-600 mr-3"></i>
                Teacher Dashboard
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Teacher Dashboard</span>
            </div>
        </div>
        <div class="dashboard-actions">
            <span class="welcome-text">Welcome back, {{ Auth::user()->name }}!</span>
            <a href="{{ route('teacher.resources.upload.form') }}" class="btn btn-success ml-4">
                <i class="fas fa-upload"></i> Upload Resource
            </a>
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

    <!-- Stats Overview -->
    <div class="stats-grid mb-8">
        <div class="stat-card-modern stat-card-primary">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $studentsCount }}</div>
                <div class="stat-label-modern">My Students</div>
                <div class="stat-sublabel-modern">Students in your classes</div>
            </div>
            <a href="{{ route('teacher.marks.index') }}" class="stat-card-link">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card-modern stat-card-success">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-success">
                    <i class="fas fa-video"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $totalResources }}</div>
                <div class="stat-label-modern">My Resources</div>
                <div class="stat-sublabel-modern">Videos & materials created</div>
            </div>
            <a href="{{ route('teacher.assessments.index') }}" class="stat-card-link">
                Manage Resources <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card-modern stat-card-warning">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-warning">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-trend">
                    @if($pendingAssignments > 0)
                        <span class="badge badge-warning-bright">{{ $pendingAssignments }}</span>
                    @endif
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $totalAssignments }}</div>
                <div class="stat-label-modern">Total Assignments</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-warning">{{ $pendingAssignments }} pending review</span>
                </div>
            </div>
            <a href="{{ route('teacher.assignments.index') }}" class="stat-card-link">
                Review Assignments <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card-modern stat-card-info">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern">{{ $totalMarksUploaded }}</div>
                <div class="stat-label-modern">Marks Uploaded</div>
                <div class="stat-sublabel-modern">Student marks entered</div>
            </div>
            <a href="{{ route('teacher.marks.index') }}" class="stat-card-link">
                Manage Marks <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card-modern stat-card-purple">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-purple">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-trend">
                    <span class="badge badge-purple-bright" id="unread-messages-badge" style="display: none;">0</span>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern" id="total-messages-count">0</div>
                <div class="stat-label-modern">Parent Messages</div>
                <div class="stat-sublabel-modern">Messages from parents</div>
            </div>
            <a href="{{ route('teacher.messages.index') }}" class="stat-card-link">
                View Messages <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section-card mb-8">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="section-title">Quick Actions</h3>
            </div>
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('teacher.assessments.create') }}" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-blue">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Create Resource</div>
                    <div class="quick-action-desc">Add new video or material</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="{{ route('teacher.marks.create') }}" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-green">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Upload Marks</div>
                    <div class="quick-action-desc">Enter student marks</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="{{ route('teacher.assignments.index') }}" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-orange">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Review Assignments</div>
                    <div class="quick-action-desc">
                        @if($pendingAssignments > 0)
                            <span class="badge-count">{{ $pendingAssignments }} pending</span>
                        @else
                            No pending assignments
                        @endif
                    </div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="{{ route('teacher.messages.index') }}" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-purple">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Parent Messages</div>
                    <div class="quick-action-desc" id="quick-action-messages-desc">View messages from parents</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="{{ route('teacher.assessments.index') }}" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-teal">
                    <i class="fas fa-book"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">View Comments</div>
                    <div class="quick-action-desc">
                        @if($unrepliedCommentsCount > 0)
                            <span class="badge-count">{{ $unrepliedCommentsCount }} unreplied</span>
                        @else
                            All comments replied
                        @endif
                    </div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Assignments -->
        <div class="dashboard-card-modern">
            <div class="card-header-modern">
                <div class="card-header-left">
                    <div class="card-icon-wrapper card-icon-blue">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="card-title-modern">Recent Assignments</h2>
                        <p class="card-subtitle-modern">Latest student submissions</p>
                    </div>
                </div>
                <a href="{{ route('teacher.assignments.index') }}" class="card-header-link">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body-modern">
                @if($recentAssignments->count() > 0)
                    <div class="activity-list">
                        @foreach($recentAssignments as $assignment)
                            <div class="activity-item-modern">
                                <div class="activity-avatar activity-avatar-{{ $assignment->status === 'graded' ? 'success' : ($assignment->status === 'submitted' ? 'warning' : 'info') }}">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="activity-content-modern">
                                    <div class="activity-header">
                                        <h4 class="activity-title-modern">{{ $assignment->user->name ?? 'Unknown Student' }}</h4>
                                        <span class="activity-time-modern">{{ $assignment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="activity-desc-modern">{{ $assignment->resource->title ?? 'N/A' }}</p>
                                    <div class="activity-footer">
                                        <span class="status-badge status-badge-{{ $assignment->status === 'graded' ? 'success' : ($assignment->status === 'submitted' ? 'warning' : 'info') }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                        @if($assignment->grade)
                                            <span class="grade-badge">Grade: {{ $assignment->grade }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-state-title">No assignments yet</h3>
                        <p class="empty-state-desc">Student assignments will appear here once submitted.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Marks -->
        <div class="dashboard-card-modern">
            <div class="card-header-modern">
                <div class="card-header-left">
                    <div class="card-icon-wrapper card-icon-green">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h2 class="card-title-modern">Recent Marks Uploaded</h2>
                        <p class="card-subtitle-modern">Latest marks entries</p>
                    </div>
                </div>
                <a href="{{ route('teacher.marks.index') }}" class="card-header-link">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body-modern">
                @if($recentMarks->count() > 0)
                    <div class="activity-list">
                        @foreach($recentMarks as $mark)
                            <div class="activity-item-modern">
                                <div class="activity-avatar activity-avatar-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="activity-content-modern">
                                    <div class="activity-header">
                                        <h4 class="activity-title-modern">{{ $mark->user->name ?? 'Unknown Student' }}</h4>
                                        <span class="activity-time-modern">{{ $mark->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="activity-desc-modern">{{ $mark->subject_name }} - Grade: <strong>{{ $mark->grade }}</strong></p>
                                    <div class="activity-footer">
                                        <span class="status-badge status-badge-info">{{ $mark->academic_level }}</span>
                                        @if($mark->points)
                                            <span class="grade-badge">Points: {{ $mark->points }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-state-title">No marks uploaded yet</h3>
                        <p class="empty-state-desc">Upload student marks to see them here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Classes -->
    @if($classes->count() > 0)
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="card-header-left">
                <div class="card-icon-wrapper card-icon-purple">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div>
                    <h2 class="card-title-modern">My Classes</h2>
                    <p class="card-subtitle-modern">{{ $classes->count() }} class{{ $classes->count() !== 1 ? 'es' : '' }} assigned</p>
                </div>
            </div>
        </div>
        <div class="card-body-modern">
            <div class="classes-grid">
                @foreach($classes as $class)
                    <div class="class-card-modern">
                        <div class="class-card-header">
                            <div class="class-icon-modern">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="class-info-modern">
                                <h3 class="class-name-modern">{{ $class->name }}</h3>
                                <p class="class-level-modern">{{ $class->grade_level ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="class-card-footer">
                            <a href="{{ route('teacher.marks.create', ['class_id' => $class->id]) }}" class="class-action-btn-modern">
                                <i class="fas fa-upload mr-2"></i> Upload Marks
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="dashboard-card-modern">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
            <h3 class="empty-state-title">No classes assigned</h3>
            <p class="empty-state-desc">Contact your administrator to be assigned to classes.</p>
        </div>
    </div>
    @endif
</div>

<style>
.teacher-dashboard {
    padding: 2rem;
    background: #f9fafb;
    min-height: 100vh;
}

.dashboard-breadcrumbs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.welcome-text {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
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
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
}

.stat-card-primary { --card-color: #3b82f6; --card-color-light: #60a5fa; }
.stat-card-success { --card-color: #10b981; --card-color-light: #34d399; }
.stat-card-warning { --card-color: #f59e0b; --card-color-light: #fbbf24; }
.stat-card-info { --card-color: #06b6d4; --card-color-light: #22d3ee; }

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon-wrapper {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon-primary { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
.stat-icon-success { background: linear-gradient(135deg, #10b981, #34d399); }
.stat-icon-warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.stat-icon-info { background: linear-gradient(135deg, #06b6d4, #22d3ee); }

.stat-trend {
    color: #10b981;
    font-size: 0.875rem;
}

.stat-value-modern {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label-modern {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.stat-sublabel-modern {
    font-size: 0.75rem;
    color: #6b7280;
}

.stat-badge-warning {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-warning-bright {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.stat-card-link {
    display: inline-flex;
    align-items: center;
    margin-top: 1rem;
    color: #3b82f6;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
}

.stat-card-link:hover {
    color: #2563eb;
}

/* Quick Actions */
.dashboard-section-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.section-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #fbbf24, #fcd34d);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    position: relative;
}

.quick-action-card:hover {
    background: white;
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.quick-action-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.quick-action-icon-blue { background: #dbeafe; color: #1e40af; }
.quick-action-icon-green { background: #d1fae5; color: #065f46; }
.quick-action-icon-orange { background: #fed7aa; color: #9a3412; }
.quick-action-icon-purple { background: #e9d5ff; color: #6b21a8; }

.quick-action-content {
    flex: 1;
}

.quick-action-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.quick-action-desc {
    font-size: 0.8125rem;
    color: #6b7280;
}

.badge-count {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.quick-action-arrow {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-left: 0.5rem;
    transition: transform 0.2s;
}

.quick-action-card:hover .quick-action-arrow {
    transform: translateX(4px);
    color: #3b82f6;
}

/* Dashboard Cards */
.dashboard-card-modern {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.card-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.card-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-icon-wrapper {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.card-icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
.card-icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
.card-icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

.card-title-modern {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
}

.card-subtitle-modern {
    font-size: 0.8125rem;
    color: #6b7280;
    margin: 0;
}

.card-header-link {
    color: #3b82f6;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
}

.card-header-link:hover {
    color: #2563eb;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item-modern {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.activity-item-modern:hover {
    background: white;
    border-color: #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.activity-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.activity-avatar-success { background: linear-gradient(135deg, #10b981, #34d399); }
.activity-avatar-warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.activity-avatar-info { background: linear-gradient(135deg, #3b82f6, #60a5fa); }

.activity-content-modern {
    flex: 1;
    min-width: 0;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.activity-title-modern {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.activity-time-modern {
    font-size: 0.75rem;
    color: #9ca3af;
    white-space: nowrap;
    margin-left: 0.5rem;
}

.activity-desc-modern {
    font-size: 0.8125rem;
    color: #6b7280;
    margin: 0 0 0.5rem 0;
    line-height: 1.5;
}

.activity-footer {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge-success { background: #d1fae5; color: #065f46; }
.status-badge-warning { background: #fef3c7; color: #92400e; }
.status-badge-info { background: #dbeafe; color: #1e40af; }

.grade-badge {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    background: #f3f4f6;
    color: #374151;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state-icon {
    width: 4rem;
    height: 4rem;
    margin: 0 auto 1rem;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.5rem;
}

.empty-state-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.empty-state-desc {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* Classes Grid */
.classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.class-card-modern {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.class-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-color: #8b5cf6;
}

.class-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.25rem;
}

.class-icon-modern {
    width: 3.5rem;
    height: 3.5rem;
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
    box-shadow: 0 4px 6px rgba(139, 92, 246, 0.3);
}

.class-info-modern {
    flex: 1;
}

.class-name-modern {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
}

.class-level-modern {
    font-size: 0.8125rem;
    color: #6b7280;
    margin: 0;
}

.class-card-footer {
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.class-action-btn-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    color: white;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
}

.class-action-btn-modern:hover {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .teacher-dashboard {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .classes-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-breadcrumbs {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}

.stat-icon-purple {
    background: linear-gradient(135deg, #a855f7, #c084fc);
}

.stat-card-purple {
    border-left: 4px solid #a855f7 !important;
}

.badge-purple-bright {
    background: #a855f7;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.quick-action-icon-teal {
    background: linear-gradient(135deg, #14b8a6, #5eead4);
}
</style>

<script>
// Load unread message count
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("teacher.messages.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const unreadCount = data.unread_count || 0;
            const badge = document.getElementById('unread-messages-badge');
            const totalCount = document.getElementById('total-messages-count');
            const quickActionDesc = document.getElementById('quick-action-messages-desc');
            
            if (totalCount) {
                totalCount.textContent = unreadCount;
            }
            
            if (unreadCount > 0) {
                if (badge) {
                    badge.textContent = unreadCount;
                    badge.style.display = 'inline-block';
                }
                if (quickActionDesc) {
                    quickActionDesc.innerHTML = `<span class="badge-count">${unreadCount} unread</span>`;
                }
            }
        })
        .catch(error => console.error('Error loading message count:', error));
});
</script>
@endsection
