@extends('layouts.dashboard')

@section('content')
<div class="project-detail-page">
    <!-- Header -->
    <div class="page-header">
        <div class="header-nav">
            <a href="{{ route('teacher.projects.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
        <h1>{{ $project->title }}</h1>
        <p>Group Project Details</p>
    </div>

    <div class="project-detail-container">
        <!-- Main Content -->
        <div class="project-main">
            <!-- Project Status -->
            <div class="detail-section">
                <div class="section-header">
                    <h2>Project Status</h2>
                    <span class="status-badge status-{{ $project->getStatusColor() }}">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
                
                @if($project->description)
                <div class="section-content">
                    <h3>Description</h3>
                    <p>{{ $project->description }}</p>
                </div>
                @endif

                @if($project->start_date || $project->end_date)
                <div class="section-content">
                    <h3>Timeline</h3>
                    <div class="timeline-info">
                        @if($project->start_date)
                        <div class="timeline-item">
                            <span class="timeline-label">Start Date:</span>
                            <span class="timeline-value">{{ $project->start_date->format('F d, Y') }}</span>
                        </div>
                        @endif
                        @if($project->end_date)
                        <div class="timeline-item">
                            <span class="timeline-label">End Date:</span>
                            <span class="timeline-value">{{ $project->end_date->format('F d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Project Progress -->
            <div class="detail-section">
                <h2>Project Progress</h2>
                <div class="progress-flow">
                    @if($project->isInPlanning())
                        <div class="progress-item">
                            <div class="progress-icon completed">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="progress-details">
                                <h3>Planning Phase</h3>
                                @if($project->planning)
                                    <p>Status: <strong>{{ ucfirst(str_replace('_', ' ', $project->planning->status)) }}</strong></p>
                                    @if($project->planning->objectives)
                                    <div class="planning-details">
                                        <h4>Objectives</h4>
                                        <p>{{ $project->planning->objectives }}</p>
                                    </div>
                                    @endif
                                @else
                                    <p>Planning not yet started</p>
                                @endif
                            </div>
                        </div>
                        <div class="progress-divider"></div>
                        <div class="progress-item">
                            <div class="progress-icon pending">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="progress-details">
                                <h3>Implementation Phase</h3>
                                <p>Awaiting planning approval</p>
                            </div>
                        </div>
                    @elseif($project->isInImplementation())
                        <div class="progress-item">
                            <div class="progress-icon completed">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="progress-details">
                                <h3>Planning Phase</h3>
                                <p>Status: <strong>Approved</strong></p>
                            </div>
                        </div>
                        <div class="progress-divider"></div>
                        <div class="progress-item">
                            <div class="progress-icon {{ $project->implementation ? 'completed' : 'pending' }}">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="progress-details">
                                <h3>Implementation Phase</h3>
                                @if($project->implementation)
                                    <p>Status: <strong>{{ ucfirst(str_replace('_', ' ', $project->implementation->status)) }}</strong></p>
                                    @if($project->implementation->approach)
                                    <div class="implementation-details">
                                        <h4>Approach</h4>
                                        <p>{{ $project->implementation->approach }}</p>
                                    </div>
                                    @endif
                                @else
                                    <p>Implementation not yet started</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="progress-item">
                            <div class="progress-icon completed">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="progress-details">
                                <h3>Project Completed</h3>
                                <p>All phases have been completed</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="project-sidebar">
            <!-- Grading Section -->
            <div class="detail-card" style="border: 2px solid #4299e1; background: #ebf8ff;">
                <h3 style="color: #2c5282;">Grading & Marks</h3>
                <div class="card-content">
                    @if($project->implementation && $project->implementation->status === 'graded')
                        <div style="background: #c6f6d5; border: 1px solid #9ae6b4; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1rem;">
                            <p style="color: #22543d; font-weight: 600; margin: 0;">✓ Project Graded</p>
                            @if($project->implementation->graded_at)
                                <p style="color: #276749; margin: 0.25rem 0 0;">{{ $project->implementation->graded_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <a href="{{ route('teacher.projects.feedback', $project) }}" style="display: inline-block; padding: 0.5rem 1rem; background: #4299e1; color: white; border-radius: 0.375rem; text-decoration: none; font-weight: 500; text-align: center; width: 100%; margin-bottom: 0.5rem;">
                            View Feedback & Marks
                        </a>
                        <a href="{{ route('teacher.projects.grade.form', $project) }}" style="display: inline-block; padding: 0.5rem 1rem; background: #ed8936; color: white; border-radius: 0.375rem; text-decoration: none; font-weight: 500; text-align: center; width: 100%;">
                            Edit Grades
                        </a>
                    @else
                        <p style="color: #744210; margin-bottom: 1rem;">Ready to grade this project? Click below to enter marks for all group members.</p>
                        <a href="{{ route('teacher.projects.grade.form', $project) }}" style="display: block; padding: 0.75rem 1rem; background: #48bb78; color: white; border-radius: 0.375rem; text-decoration: none; font-weight: 600; text-align: center;">
                            Grade Project
                        </a>
                    @endif
                </div>
            </div>

            <!-- Group Information -->
            <div class="detail-card">
                <h3>Project Group</h3>
                <div class="card-content">
                    <p class="group-name">{{ $project->group->name }}</p>
                    @if($project->group->description)
                    <p class="group-description">{{ $project->group->description }}</p>
                    @endif
                </div>
                <a href="{{ route('teacher.groups.submissions', $project->group) }}" class="btn-link">
                    View Group Details
                </a>
            </div>

            <!-- Group Members -->
            <div class="detail-card">
                <h3>Group Members</h3>
                <div class="members-list">
                    @forelse($project->group->approvedMembers as $member)
                        <div class="member-item">
                            <div class="member-avatar">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="member-info">
                                <p class="member-name">{{ $member->name }}</p>
                                <p class="member-email">{{ $member->email }}</p>
                            </div>
                            @if($member->pivot->role === 'creator')
                            <span class="member-badge">Creator</span>
                            @endif
                        </div>
                    @empty
                        <p class="no-data">No members yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Group Statistics -->
            <div class="detail-card">
                <h3>Group Statistics</h3>
                <div class="stats-list">
                    <div class="stat-item">
                        <span class="stat-label">Members</span>
                        <span class="stat-value">{{ $project->group->approvedMembers->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Projects</span>
                        <span class="stat-value">{{ $project->group->projects->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Created</span>
                        <span class="stat-value">{{ $project->group->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.project-detail-page {
    padding: 1rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.header-nav {
    margin-bottom: 1rem;
}

.back-link {
    color: #4299e1;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

.page-header h1 {
    font-size: 2.5rem;
    color: #2d3748;
    margin: 0.5rem 0 0.25rem;
}

.page-header p {
    color: #718096;
    font-size: 1rem;
    margin: 0;
}

.project-detail-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .project-detail-container {
        grid-template-columns: 1fr;
    }
}

.detail-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin: 0;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.section-content {
    margin-top: 1rem;
}

.section-content h3 {
    color: #2d3748;
    font-size: 1rem;
    margin: 0 0 0.75rem;
}

.section-content p {
    color: #4a5568;
    line-height: 1.6;
    margin: 0;
}

.timeline-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.timeline-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.timeline-label {
    color: #718096;
    font-weight: 500;
}

.timeline-value {
    color: #2d3748;
    font-weight: 600;
}

.progress-flow {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.progress-item {
    display: flex;
    gap: 1rem;
}

.progress-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.progress-icon.completed {
    background-color: #48bb78;
    color: white;
}

.progress-icon.pending {
    background-color: #e2e8f0;
    color: #718096;
}

.progress-details h3 {
    color: #2d3748;
    margin: 0 0 0.5rem;
}

.progress-details p {
    color: #4a5568;
    margin: 0 0 0.5rem;
}

.planning-details,
.implementation-details {
    background-color: #f7fafc;
    padding: 0.75rem;
    border-radius: 0.375rem;
    margin-top: 0.75rem;
}

.planning-details h4,
.implementation-details h4 {
    color: #2d3748;
    font-size: 0.875rem;
    margin: 0 0 0.5rem;
}

.planning-details p,
.implementation-details p {
    color: #4a5568;
    font-size: 0.875rem;
    margin: 0;
}

.progress-divider {
    margin: 0.5rem 0 0.5rem 24px;
    border-left: 2px solid #cbd5e0;
    height: 1rem;
}

.detail-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.detail-card h3 {
    color: #2d3748;
    font-size: 1rem;
    margin: 0 0 1rem;
}

.card-content {
    margin-bottom: 1rem;
}

.group-name {
    color: #2d3748;
    font-weight: 600;
    margin: 0 0 0.5rem;
}

.group-description {
    color: #4a5568;
    font-size: 0.875rem;
    margin: 0;
    line-height: 1.5;
}

.btn-link {
    color: #4299e1;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
}

.btn-link:hover {
    text-decoration: underline;
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.member-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #4299e1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.member-info {
    flex: 1;
    min-width: 0;
}

.member-name {
    color: #2d3748;
    font-weight: 500;
    margin: 0 0 0.25rem;
    font-size: 0.875rem;
}

.member-email {
    color: #718096;
    margin: 0;
    font-size: 0.75rem;
}

.member-badge {
    background-color: #bee3f8;
    color: #2c5282;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.no-data {
    color: #718096;
    text-align: center;
    font-size: 0.875rem;
    margin: 1rem 0;
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.stat-label {
    color: #718096;
    font-size: 0.875rem;
}

.stat-value {
    color: #2d3748;
    font-weight: 600;
}
</style>
@endsection
