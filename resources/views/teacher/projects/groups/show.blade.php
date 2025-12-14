@extends('layouts.dashboard')

@section('content')
<div class="group-show-page">
    <!-- Header -->
    <div class="page-header">
        <div class="header-nav">
            <a href="{{ route('teacher.groups.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Groups
            </a>
        </div>
        <h1>{{ $group->name }}</h1>
        <p>Group Details and Members</p>
    </div>

    <div class="group-content">
        <!-- Main Content -->
        <div class="group-main">
            <!-- Group Information -->
            <div class="detail-section">
                <h2>Group Information</h2>
                
                @if($group->description)
                <div class="info-block">
                    <h3>Description</h3>
                    <p>{{ $group->description }}</p>
                </div>
                @endif

                <div class="info-block">
                    <h3>Group Details</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @if($group->status === 'open')
                                    <span class="badge badge-open">Open</span>
                                @else
                                    <span class="badge badge-closed">Closed</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Created Date</span>
                            <span class="info-value">{{ $group->created_at->format('F d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Members</span>
                            <span class="info-value">{{ $group->approvedMembers->count() }} / {{ $group->max_members ?? 'Unlimited' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Projects</span>
                            <span class="info-value">{{ $group->projects->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Members -->
            <div class="detail-section">
                <h2>Group Members ({{ $group->approvedMembers->count() }})</h2>
                
                @if($group->approvedMembers->count() > 0)
                <div class="members-list">
                    @foreach($group->approvedMembers as $member)
                    <div class="member-item">
                        <div class="member-avatar">
                            @if($member->profile_photo_path)
                                <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}">
                            @else
                                {{ substr($member->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="member-details">
                            <div class="member-header">
                                <h4 class="member-name">{{ $member->name }}</h4>
                                @if($group->creator->id === $member->id)
                                <span class="member-role-badge">Group Creator</span>
                                @endif
                            </div>
                            <p class="member-email">{{ $member->email }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state-small">
                    <p>No members in this group yet</p>
                </div>
                @endif
            </div>

            <!-- Group Projects -->
            @if($group->projects->count() > 0)
            <div class="detail-section">
                <h2>Projects in This Group ({{ $group->projects->count() }})</h2>
                
                <div class="projects-list">
                    @foreach($group->projects as $project)
                    <div class="project-item">
                        <div class="project-info">
                            <h4 class="project-title">{{ $project->title }}</h4>
                            <p class="project-meta">
                                <span class="status-badge status-{{ $project->getStatusColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                                @if($project->start_date)
                                <span class="date-badge">
                                    {{ $project->start_date->format('M d') }}
                                    @if($project->end_date)
                                    - {{ $project->end_date->format('M d, Y') }}
                                    @endif
                                </span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('teacher.projects.show', $project) }}" class="btn-link">
                            View Project <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="detail-section">
                <div class="empty-state-small">
                    <p><i class="fas fa-project-diagram"></i> No projects yet in this group</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="group-sidebar">
            <!-- Quick Stats -->
            <div class="stat-card-group">
                <div class="stat-card">
                    <div class="stat-number">{{ $group->approvedMembers->count() }}</div>
                    <div class="stat-label">Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $group->projects->count() }}</div>
                    <div class="stat-label">Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $group->max_members ?? 'Unlimited' }}</div>
                    <div class="stat-label">Max Size</div>
                </div>
            </div>

            <!-- Group Creator -->
            <div class="info-card">
                <h3>Group Creator</h3>
                <div class="creator-info">
                    <div class="creator-avatar">
                        @if($group->creator->profile_photo_path)
                            <img src="{{ asset('storage/' . $group->creator->profile_photo_path) }}" alt="{{ $group->creator->name }}">
                        @else
                            {{ substr($group->creator->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p class="creator-name">{{ $group->creator->name }}</p>
                        <p class="creator-email">{{ $group->creator->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="info-card">
                <h3>Group Information</h3>
                <div class="info-list">
                    <div class="info-row">
                        <span class="label">Created</span>
                        <span class="value">{{ $group->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Last Updated</span>
                        <span class="value">{{ $group->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status</span>
                        <span class="value">
                            @if($group->status === 'open')
                                <span class="badge-small badge-open">Open</span>
                            @else
                                <span class="badge-small badge-closed">Closed</span>
                            @endif
                        </span>
                    </div>
                    @if($group->isFull())
                    <div class="info-row">
                        <span class="label">Capacity</span>
                        <span class="value"><span class="badge-small badge-full">Full</span></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.group-show-page {
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
    font-size: 0.875rem;
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

.group-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .group-content {
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

.detail-section h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin: 0 0 1rem;
}

.info-block {
    margin-bottom: 1.5rem;
}

.info-block:last-child {
    margin-bottom: 0;
}

.info-block h3 {
    color: #2d3748;
    font-size: 1rem;
    margin: 0 0 0.75rem;
}

.info-block p {
    color: #4a5568;
    line-height: 1.6;
    margin: 0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (max-width: 640px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}

.info-item {
    padding: 0.75rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.info-label {
    display: block;
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-value {
    display: block;
    color: #2d3748;
    font-weight: 600;
}

.badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.badge-open {
    background-color: #c6f6d5;
    color: #22543d;
}

.badge-closed {
    background-color: #fed7d7;
    color: #742a2a;
}

.badge-full {
    background-color: #fbd38d;
    color: #744210;
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.member-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.member-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #4299e1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
    overflow: hidden;
}

.member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-details {
    flex: 1;
    min-width: 0;
}

.member-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.member-name {
    color: #2d3748;
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.member-role-badge {
    background-color: #faf089;
    color: #5f4e0b;
    padding: 0.2rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.member-email {
    color: #718096;
    font-size: 0.875rem;
    margin: 0;
}

.projects-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.project-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
    border-left: 4px solid #4299e1;
}

.project-info {
    flex: 1;
    min-width: 0;
}

.project-title {
    color: #2d3748;
    font-weight: 600;
    margin: 0 0 0.5rem;
}

.project-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 0;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
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

.date-badge {
    font-size: 0.75rem;
    color: #718096;
}

.btn-link {
    color: #4299e1;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-link:hover {
    text-decoration: underline;
}

.empty-state-small {
    text-align: center;
    padding: 2rem 1rem;
    color: #718096;
}

.empty-state-small p {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* Sidebar */
.stat-card-group {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .stat-card-group {
        grid-template-columns: repeat(3, 1fr);
    }
}

.stat-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #4299e1;
}

.stat-label {
    color: #718096;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.info-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-card h3 {
    color: #2d3748;
    font-size: 1rem;
    margin: 0 0 1rem;
}

.creator-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.creator-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #4299e1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
    overflow: hidden;
}

.creator-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.creator-name {
    color: #2d3748;
    font-weight: 600;
    margin: 0;
}

.creator-email {
    color: #718096;
    font-size: 0.875rem;
    margin: 0.25rem 0 0;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background-color: #f7fafc;
    border-radius: 0.375rem;
}

.info-row .label {
    color: #718096;
    font-size: 0.875rem;
}

.info-row .value {
    color: #2d3748;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge-small {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-small.badge-open {
    background-color: #c6f6d5;
    color: #22543d;
}

.badge-small.badge-closed {
    background-color: #fed7d7;
    color: #742a2a;
}

.badge-small.badge-full {
    background-color: #fbd38d;
    color: #744210;
}
</style>
@endsection
