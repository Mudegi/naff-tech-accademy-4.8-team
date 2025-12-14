@extends('layouts.dashboard')

@section('content')
<div class="groups-page">
    <!-- Header -->
    <div class="page-header">
        <div class="header-nav">
            <a href="{{ route('teacher.projects.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
        <h1>Student Groups</h1>
        <p>Monitor and review all student project groups</p>
    </div>

    <!-- Groups List -->
    @if($groups->count() > 0)
    <div class="groups-grid">
        @foreach($groups as $group)
        <div class="group-card">
            <div class="group-header">
                <h3 class="group-name">{{ $group->name }}</h3>
                <div class="group-badges">
                    @if($group->status === 'open')
                        <span class="badge badge-open">Open</span>
                    @else
                        <span class="badge badge-closed">Closed</span>
                    @endif
                </div>
            </div>

            @if($group->description)
            <p class="group-description">{{ Str::limit($group->description, 100) }}</p>
            @endif

            <div class="group-stats">
                <div class="stat">
                    <span class="stat-label">Members</span>
                    <span class="stat-number">{{ $group->approvedMembers->count() }}</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Max Size</span>
                    <span class="stat-number">{{ $group->max_members ?? 'N/A' }}</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Projects</span>
                    <span class="stat-number">{{ $group->projects->count() }}</span>
                </div>
            </div>

            <div class="group-members">
                <div class="members-preview">
                    @foreach($group->approvedMembers->take(3) as $member)
                    <div class="member-avatar" title="{{ $member->name }}">
                        @if($member->profile_photo_path)
                            <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}">
                        @else
                            <span>{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        @endif
                        @if($group->creator->id === $member->id)
                            <div class="leader-indicator" title="Group Creator">
                                <i class="fas fa-crown"></i>
                            </div>
                        @endif
                    </div>
                    @endforeach
                    @if($group->approvedMembers->count() > 3)
                    <div class="member-more">
                        +{{ $group->approvedMembers->count() - 3 }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="group-meta">
                <span class="meta-item">
                    <i class="fas fa-user"></i>
                    Created by {{ $group->creator->name }}
                </span>
                <span class="meta-item">
                    <i class="fas fa-calendar"></i>
                    {{ $group->created_at->format('M d, Y') }}
                </span>
            </div>

            <div class="group-actions">
                <a href="{{ route('teacher.groups.submissions', $group) }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3>No Groups Yet</h3>
        <p>There are currently no student groups in your school.</p>
        <div class="empty-state-actions">
            <a href="{{ route('teacher.projects.index') }}" class="btn btn-primary">
                <i class="fas fa-project-diagram"></i> View Projects
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.groups-page {
    padding: 1rem;
    max-width: 1200px;
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
    font-size: 2rem;
    color: #2d3748;
    margin: 0.5rem 0 0.25rem;
}

.page-header p {
    color: #718096;
    font-size: 1rem;
    margin: 0;
}

.groups-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.group-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.group-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-color: #cbd5e0;
}

.group-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.group-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.group-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-open {
    background-color: #c6f6d5;
    color: #22543d;
}

.badge-closed {
    background-color: #fed7d7;
    color: #742a2a;
}

.badge-leader {
    background-color: #faf089;
    color: #5f4e0b;
}

.group-description {
    color: #4a5568;
    margin: 0 0 1rem;
    line-height: 1.5;
    font-size: 0.875rem;
}

.group-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.stat {
    flex: 1;
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 0.75rem;
    color: #718096;
    margin-bottom: 0.25rem;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
}

.group-members {
    margin-bottom: 1rem;
}

.members-preview {
    display: flex;
    gap: 0.5rem;
    align-items: center;
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
    font-size: 0.875rem;
    position: relative;
    border: 2px solid white;
    overflow: hidden;
    flex-shrink: 0;
}

.member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-avatar span {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.leader-indicator {
    position: absolute;
    bottom: -3px;
    right: -3px;
    width: 18px;
    height: 18px;
    background-color: #ffd700;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: #744210;
}

.member-more {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #e2e8f0;
    color: #4a5568;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.group-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.75rem;
    color: #718096;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.group-actions {
    display: flex;
    gap: 0.5rem;
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
