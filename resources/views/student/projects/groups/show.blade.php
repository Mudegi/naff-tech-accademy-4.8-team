@extends('layouts.student-dashboard')

@section('content')
<div class="group-show-page">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1>{{ $group->name }}</h1>
                <div class="group-badges">
                    @if($group->isLeader(Auth::user()))
                        <span class="badge badge-leader">You are the leader</span>
                    @endif
                    @if($group->isFull())
                        <span class="badge badge-full">Full</span>
                    @else
                        <span class="badge badge-open">Open</span>
                    @endif
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('student.projects.groups.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Groups
                </a>
                @if($group->isLeader(Auth::user()))
                    <a href="{{ route('student.projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Project
                    </a>
                @endif
            </div>
        </div>

        @if($group->description)
        <p class="group-description">{{ $group->description }}</p>
        @endif
    </div>

    <!-- Group Stats -->
    <div class="group-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $group->approvedMembers->count() }}</div>
            <div class="stat-label">Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $group->max_members }}</div>
            <div class="stat-label">Max Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $group->projects->count() }}</div>
            <div class="stat-label">Projects</div>
        </div>
    </div>

    <!-- Group Members -->
    <div class="section">
        <div class="section-header">
            <h2>Group Members</h2>
        </div>

        <div class="members-grid">
            @foreach($group->approvedMembers as $member)
            <div class="member-card">
                <div class="member-avatar">
                    @if($member->profile_photo_path)
                        <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}">
                    @else
                        <span>{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                    @endif
                    @if($group->isLeader($member))
                        <div class="leader-indicator">
                            <i class="fas fa-crown"></i>
                        </div>
                    @endif
                </div>
                <div class="member-info">
                    <h3 class="member-name">{{ $member->name }}</h3>
                    <p class="member-role">{{ $group->isLeader($member) ? 'Group Leader' : 'Member' }}</p>
                    @if($member->id === Auth::id())
                        <span class="current-user">(You)</span>
                    @endif
                </div>
                @if($group->isLeader(Auth::user()) && $member->id !== Auth::id())
                <div class="member-actions">
                    <form action="{{ route('student.projects.groups.remove-member', [$group, $member->id]) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to remove {{ $member->name }} from the group?')">
                            <i class="fas fa-user-minus"></i> Remove
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if(!$group->isFull() && !$group->isMember(Auth::user()) && $group->status === 'open')
        <div class="join-section">
            <form action="{{ route('student.projects.groups.join', $group) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Join This Group
                </button>
            </form>
        </div>
        @endif

        @if($group->isMember(Auth::user()) && !$group->isLeader(Auth::user()))
        <div class="leave-section">
            <form action="{{ route('student.projects.groups.leave', $group) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to leave this group?')">
                    <i class="fas fa-sign-out-alt"></i> Leave Group
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Group Projects -->
    @if($group->projects->count() > 0)
    <div class="section">
        <div class="section-header">
            <h2>Group Projects</h2>
        </div>

        <div class="projects-list">
            @foreach($group->projects as $project)
            <div class="project-item">
                <div class="project-info">
                    <h3 class="project-title">{{ $project->title }}</h3>
                    <p class="project-status status-{{ $project->getStatusColor() }}">
                        {{ ucfirst($project->status) }}
                    </p>
                </div>
                <div class="project-actions">
                    <a href="{{ route('student.projects.show', $project) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> View Project
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($groupMarks))
    <div class="section">
        <div class="section-header">
            <h2>Your Group Marks</h2>
        </div>

        @if($groupMarks->count() > 0)
        <div class="marks-list">
            @foreach($groupMarks as $mark)
            <div class="mark-card">
                <div class="mark-top">
                    <div class="mark-score">{{ rtrim(rtrim(number_format($mark->numeric_mark, 2), '0'), '.') }}%</div>
                    <div class="mark-grade">{{ $mark->grade ?? '—' }}</div>
                </div>
                <div class="mark-meta">
                    <div><span class="meta-label">Subject:</span> {{ $mark->subject_name ?? 'Group Assignment' }}</div>
                    @if($mark->paper_name)
                    <div><span class="meta-label">Paper:</span> {{ $mark->paper_name }}</div>
                    @endif
                    @if($mark->uploadedBy)
                    <div><span class="meta-label">Marked by:</span> {{ $mark->uploadedBy->name }}</div>
                    @endif
                    <div><span class="meta-label">Date:</span> {{ optional($mark->created_at)->format('M d, Y') }}</div>
                    @if($mark->remarks)
                    <div class="mark-remarks">{{ $mark->remarks }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted">No marks awarded yet for this group.</p>
        @endif
    </div>
    @endif

    <!-- Group Submissions Section -->
    <div class="section">
        <div class="section-header">
            <h2>Group Submissions</h2>
        </div>

        @if($group->isMember(Auth::user()))
        <!-- Upload Form -->
        <div class="upload-form-container">
            <h3 class="upload-heading">Upload Group Work</h3>
            <form action="{{ route('student.projects.groups.upload-submission', $group) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" placeholder="e.g., Assignment 1" required>
                        @error('title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-textarea" placeholder="Brief description of the work..." rows="2">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="file" class="form-label">File (PDF or Image) *</label>
                    <input type="file" name="file" id="file" class="form-file" accept=".pdf,.png,.jpg,.jpeg" required>
                    @error('file')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <small class="form-help">Accepted formats: PDF, PNG, JPG (Max 10MB)</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Submission
                </button>
            </form>
        </div>
        @endif

        <!-- Submissions List -->
        @if($group->submissions->count() > 0)
        <div class="submissions-list">
            <h3 class="submissions-heading">Uploaded Work</h3>
            @foreach($group->submissions->sortByDesc('submitted_at') as $submission)
            <div class="submission-card">
                <div class="submission-icon">
                    @if($submission->file_type === 'pdf')
                        <i class="fas fa-file-pdf"></i>
                    @else
                        <i class="fas fa-file-image"></i>
                    @endif
                </div>
                <div class="submission-details">
                    <h4 class="submission-title">{{ $submission->title }}</h4>
                    @if($submission->description)
                    <p class="submission-description">{{ $submission->description }}</p>
                    @endif
                    <div class="submission-meta">
                        <span><i class="fas fa-user"></i> {{ $submission->uploader->name }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $submission->submitted_at->format('M d, Y H:i') }}</span>
                        <span class="submission-status status-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
                    </div>
                </div>
                <div class="submission-actions">
                    <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ asset('storage/' . $submission->file_path) }}" download class="btn btn-sm btn-secondary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted">No submissions yet. Upload your group's work above.</p>
        @endif
    </div>
</div>

<style>
.group-show-page {
    padding: 1rem;
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-left h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.group-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-leader {
    background: #fef3c7;
    color: #92400e;
}

.badge-full {
    background: #fee2e2;
    color: #dc2626;
}

.badge-open {
    background: #d1fae5;
    color: #065f46;
}

.header-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.group-description {
    color: #6b7280;
    font-size: 1rem;
    line-height: 1.6;
}

.group-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.stat-number {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    text-transform: uppercase;
    font-weight: 500;
}

.section {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-header h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
}

.members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.member-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.member-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: #2563eb;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.leader-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #f59e0b;
    color: white;
    border-radius: 50%;
    width: 1.25rem;
    height: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.625rem;
}

.member-info {
    flex: 1;
}

.member-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.member-role {
    color: #6b7280;
    font-size: 0.875rem;
}

.current-user {
    color: #2563eb;
    font-weight: 500;
}

.member-actions {
    flex-shrink: 0;
}

.join-section,
.leave-section {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
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
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.project-info {
    flex: 1;
}

.project-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.project-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    width: fit-content;
}

.status-blue { background: #dbeafe; color: #1e40af; }
.status-yellow { background: #fef3c7; color: #92400e; }
.status-green { background: #d1fae5; color: #065f46; }
.status-red { background: #fee2e2; color: #dc2626; }
.status-gray { background: #f3f4f6; color: #374151; }

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

.btn-success {
    background: #059669;
    color: white;
}

.btn-success:hover {
    background: #047857;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.inline-form {
    margin: 0;
}

.marks-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1rem;
}

.mark-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    background: #f9fafb;
}

.mark-top {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.75rem;
}

.mark-score {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
}

.mark-grade {
    padding: 0.35rem 0.65rem;
    border-radius: 9999px;
    background: #eef2ff;
    color: #4338ca;
    font-weight: 600;
    font-size: 0.95rem;
}

.mark-meta {
    display: grid;
    gap: 0.35rem;
    color: #4b5563;
    font-size: 0.95rem;
}

.meta-label {
    color: #111827;
    font-weight: 600;
    margin-right: 0.35rem;
}

.mark-remarks {
    margin-top: 0.5rem;
    padding: 0.75rem;
    border-radius: 0.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.text-muted {
    color: #6b7280;
}

.upload-form-container {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
}

.upload-heading, .submissions-heading {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
}

.upload-form {
    display: grid;
    gap: 1rem;
}

.form-row {
    display: grid;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-input, .form-textarea, .form-file {
    padding: 0.65rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.95rem;
}

.form-input:focus, .form-textarea:focus, .form-file:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-help {
    color: #6b7280;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.submissions-list {
    margin-top: 1.5rem;
}

.submission-card {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    align-items: flex-start;
}

.submission-icon {
    font-size: 2rem;
    color: #2563eb;
    flex-shrink: 0;
}

.submission-icon .fa-file-pdf {
    color: #dc2626;
}

.submission-icon .fa-file-image {
    color: #059669;
}

.submission-details {
    flex: 1;
}

.submission-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.submission-description {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.submission-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    color: #6b7280;
    font-size: 0.85rem;
}

.submission-meta i {
    margin-right: 0.25rem;
}

.submission-status {
    padding: 0.2rem 0.5rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.75rem;
}

.submission-status.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.submission-status.status-reviewed {
    background: #dbeafe;
    color: #1e40af;
}

.submission-status.status-graded {
    background: #d1fae5;
    color: #065f46;
}

.submission-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .group-show-page {
        padding: 0.75rem;
    }

    .page-header {
        padding: 1.5rem;
    }

    .header-content {
        flex-direction: column;
        align-items: stretch;
    }

    .header-actions {
        justify-content: center;
    }

    .members-grid {
        grid-template-columns: 1fr;
    }

    .member-card {
        flex-direction: column;
        text-align: center;
    }

    .project-item {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .btn {
        justify-content: center;
    }
}
</style>
@endsection
