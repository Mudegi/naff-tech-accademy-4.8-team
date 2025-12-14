@extends('layouts.dashboard')

@section('content')
<div class="projects-page">
    <!-- Header -->
    <div class="page-header">
        <h1>Student Projects</h1>
        <p>Monitor and review all student group projects</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('teacher.groups.index') }}" class="action-btn action-btn-primary">
            <i class="fas fa-users"></i>
            View All Groups
        </a>
    </div>

    <!-- Projects List -->
    @if($projects->count() > 0)
    <div class="projects-grid">
        @foreach($projects as $project)
        <div class="project-card">
            <div class="project-header">
                <h3 class="project-title">{{ $project->title }}</h3>
                <span class="project-status status-{{ $project->getStatusColor() }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>

            <div class="project-info">
                <div class="project-meta">
                    <span class="meta-item">
                        <i class="fas fa-users"></i>
                        {{ $project->group->name }}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-user"></i>
                        {{ $project->group->approvedMembers->count() }} members
                    </span>
                    @if($project->start_date)
                    <span class="meta-item">
                        <i class="fas fa-calendar"></i>
                        {{ $project->start_date->format('M d, Y') }}
                        @if($project->end_date)
                        - {{ $project->end_date->format('M d, Y') }}
                        @endif
                    </span>
                    @endif
                </div>

                @if($project->description)
                <p class="project-description">{{ Str::limit($project->description, 150) }}</p>
                @endif
            </div>

            <div class="project-progress">
                @if($project->isInPlanning())
                    <div class="progress-step">
                        <div class="step-indicator {{ $project->planning ? 'completed' : 'pending' }}">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="step-label">Planning</span>
                        @if($project->planning && $project->planning->status !== 'draft')
                        <span class="step-status">{{ ucfirst(str_replace('_', ' ', $project->planning->status)) }}</span>
                        @endif
                    </div>
                    <div class="progress-connector"></div>
                    <div class="progress-step">
                        <div class="step-indicator pending">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <span class="step-label">Implementation</span>
                    </div>
                @elseif($project->isInImplementation())
                    <div class="progress-step">
                        <div class="step-indicator completed">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="step-label">Planning</span>
                        <span class="step-status">Approved</span>
                    </div>
                    <div class="progress-connector"></div>
                    <div class="progress-step">
                        <div class="step-indicator {{ $project->implementation ? 'completed' : 'pending' }}">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <span class="step-label">Implementation</span>
                        @if($project->implementation && $project->implementation->status !== 'in_progress')
                        <span class="step-status">{{ ucfirst(str_replace('_', ' ', $project->implementation->status)) }}</span>
                        @endif
                    </div>
                @else
                    <div class="progress-step">
                        <div class="step-indicator completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="step-label">Completed</span>
                    </div>
                @endif
            </div>

            <div class="project-actions">
                <a href="{{ route('teacher.projects.show', $project) }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-project-diagram"></i>
        </div>
        <h3>No Projects Yet</h3>
        <p>There are currently no student projects in your school.</p>
        <div class="empty-state-actions">
            <a href="{{ route('teacher.groups.index') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> View Groups
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.projects-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    font-size: 2rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #718096;
    font-size: 1rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

.action-btn-primary {
    background-color: #4299e1;
    color: white;
}

.action-btn-primary:hover {
    background-color: #3182ce;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.project-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.project-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-color: #cbd5e0;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.project-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.project-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-planning {
    background-color: #bee3f8;
    color: #2c5282;
}

.status-in_progress,
.status-implementation {
    background-color: #fbd38d;
    color: #744210;
}

.status-completed {
    background-color: #9ae6b4;
    color: #22543d;
}

.project-info {
    margin-bottom: 1rem;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.meta-item {
    font-size: 0.875rem;
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.project-description {
    color: #4a5568;
    margin: 0.75rem 0 0 0;
    line-height: 1.5;
}

.project-progress {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
    gap: 0.5rem;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 70px;
}

.step-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
}

.step-indicator.pending {
    background-color: #e2e8f0;
    color: #718096;
}

.step-indicator.completed {
    background-color: #48bb78;
    color: white;
}

.step-label {
    font-size: 0.75rem;
    color: #4a5568;
    text-align: center;
    line-height: 1.2;
}

.step-status {
    font-size: 0.7rem;
    color: #2d3748;
    font-weight: 600;
    margin-top: 0.25rem;
}

.progress-connector {
    flex-grow: 1;
    height: 2px;
    background-color: #cbd5e0;
    margin: 0 0.5rem;
}

.project-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    flex: 1;
    justify-content: center;
}

.btn-primary {
    background-color: #4299e1;
    color: white;
}

.btn-primary:hover {
    background-color: #3182ce;
}

.empty-state {
    text-align: center;
    padding: 4rem 1rem;
}

.empty-state-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #718096;
    margin-bottom: 1.5rem;
}

.empty-state-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}
</style>
@endsection
