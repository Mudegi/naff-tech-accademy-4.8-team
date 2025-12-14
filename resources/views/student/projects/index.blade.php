@extends('layouts.student-dashboard')

@section('content')
<div class="projects-page">
    <!-- Header -->
    <div class="page-header">
        <h1>My Projects</h1>
        <p>Manage your group projects and track their progress</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('student.projects.groups.index') }}" class="action-btn action-btn-primary">
            <i class="fas fa-users"></i>
            Manage Groups
        </a>
        @if($groups->count() > 0)
        <a href="{{ route('student.projects.create') }}" class="action-btn action-btn-success">
            <i class="fas fa-plus"></i>
            Create New Project
        </a>
        @endif
    </div>

    <!-- Projects List -->
    @if($projects->count() > 0)
    <div class="projects-grid">
        @foreach($projects as $project)
        <div class="project-card">
            <div class="project-header">
                <h3 class="project-title">{{ $project->title }}</h3>
                <span class="project-status status-{{ $project->getStatusColor() }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            <div class="project-info">
                <div class="project-meta">
                    <span class="meta-item">
                        <i class="fas fa-users"></i>
                        {{ $project->group->name }}
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
                <p class="project-description">{{ Str::limit($project->description, 100) }}</p>
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
                        <span class="step-status">{{ ucfirst($project->planning->status) }}</span>
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
                        <span class="step-status">{{ ucfirst($project->implementation->status) }}</span>
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
                <a href="{{ route('student.projects.show', $project) }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Details
                </a>

                @if($project->isInPlanning() && (!$project->planning || $project->planning->status === 'draft' || $project->planning->status === 'rejected'))
                <a href="{{ route('student.projects.edit-planning', $project) }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit Planning
                </a>
                @elseif($project->isInImplementation() && (!$project->implementation || $project->implementation->status === 'in_progress'))
                <a href="{{ route('student.projects.edit-implementation', $project) }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit Implementation
                </a>
                @endif
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
        <p>You haven't created or joined any projects yet. Start by creating or joining a group!</p>
        <div class="empty-state-actions">
            <a href="{{ route('student.projects.groups.index') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> Manage Groups
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
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 1rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.action-btn-primary {
    background: #2563eb;
    color: white;
}

.action-btn-primary:hover {
    background: #1d4ed8;
}

.action-btn-success {
    background: #059669;
    color: white;
}

.action-btn-success:hover {
    background: #047857;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
}

.project-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s, box-shadow 0.2s;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.project-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    flex: 1;
}

.project-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-blue { background: #dbeafe; color: #1e40af; }
.status-yellow { background: #fef3c7; color: #92400e; }
.status-green { background: #d1fae5; color: #065f46; }
.status-red { background: #fee2e2; color: #dc2626; }
.status-gray { background: #f3f4f6; color: #374151; }

.project-info {
    margin-bottom: 1.5rem;
}

.project-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.project-description {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.5;
}

.project-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    position: relative;
}

.step-indicator {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: all 0.2s;
}

.step-indicator.completed {
    background: #059669;
    color: white;
}

.step-indicator.pending {
    background: #e5e7eb;
    color: #9ca3af;
}

.step-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    text-align: center;
}

.step-status {
    font-size: 0.625rem;
    color: #059669;
    font-weight: 600;
    text-transform: uppercase;
}

.progress-connector {
    width: 3rem;
    height: 2px;
    background: #e5e7eb;
    margin: 0 1rem;
}

.project-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.empty-state p {
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.empty-state-actions {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .projects-page {
        padding: 0.75rem;
    }

    .quick-actions {
        flex-direction: column;
        align-items: center;
    }

    .projects-grid {
        grid-template-columns: 1fr;
    }

    .project-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .project-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .project-actions {
        flex-direction: column;
    }

    .btn {
        justify-content: center;
    }
}
</style>
@endsection
