@extends('layouts.teacher-dashboard')

@section('content')
<div class="group-submissions-page">
    <div class="page-header">
        <h1>Group Submissions</h1>
        <p>View and grade group work from students in your classes</p>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('teacher.projects.group-submissions') }}" class="filters-form">
            <div class="filter-group">
                <label for="subject_id">Subject</label>
                <select name="subject_id" id="subject_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="class_id">Class</label>
                <select name="class_id" id="class_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Submissions List -->
    @if($submissions->count() > 0)
    <div class="submissions-grid">
        @foreach($submissions as $submission)
        <div class="submission-card">
            <div class="submission-header">
                <div class="submission-icon">
                    @if($submission->file_type === 'pdf')
                        <i class="fas fa-file-pdf"></i>
                    @else
                        <i class="fas fa-file-image"></i>
                    @endif
                </div>
                <div class="submission-title-area">
                    <h3>{{ $submission->title }}</h3>
                    <span class="submission-status status-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
                </div>
            </div>

            @if($submission->description)
            <p class="submission-description">{{ $submission->description }}</p>
            @endif

            <div class="submission-meta">
                <div class="meta-item">
                    <strong>Group:</strong> {{ $submission->group->name }}
                </div>
                <div class="meta-item">
                    <strong>Subject:</strong> {{ $submission->subject->name ?? '—' }}
                </div>
                <div class="meta-item">
                    <strong>Class:</strong> {{ $submission->schoolClass->name ?? '—' }}
                </div>
                <div class="meta-item">
                    <strong>Uploaded by:</strong> {{ $submission->uploader->name }}
                </div>
                <div class="meta-item">
                    <strong>Submitted:</strong> {{ $submission->submitted_at->format('M d, Y H:i') }}
                </div>
            </div>

            <div class="submission-actions">
                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> View File
                </a>
                <a href="{{ asset('storage/' . $submission->file_path) }}" download class="btn btn-secondary btn-sm">
                    <i class="fas fa-download"></i> Download
                </a>
                <a href="{{ route('teacher.groups.show', $submission->group_id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-users"></i> View Group
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-container">
        {{ $submissions->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>No group submissions found for your classes</p>
    </div>
    @endif
</div>

<style>
.group-submissions-page {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.filter-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.filter-select {
    width: 100%;
    padding: 0.65rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.95rem;
}

.filter-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.submissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.submission-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.submission-header {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.submission-icon {
    font-size: 2.5rem;
    flex-shrink: 0;
}

.submission-icon .fa-file-pdf {
    color: #dc2626;
}

.submission-icon .fa-file-image {
    color: #059669;
}

.submission-title-area {
    flex: 1;
}

.submission-title-area h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.submission-status {
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
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

.submission-description {
    color: #6b7280;
    font-size: 0.95rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.submission-meta {
    display: grid;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.meta-item {
    color: #4b5563;
}

.meta-item strong {
    color: #111827;
}

.submission-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-sm {
    padding: 0.4rem 0.7rem;
    font-size: 0.8rem;
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

.btn-info {
    background: #0891b2;
    color: white;
}

.btn-info:hover {
    background: #0e7490;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6b7280;
    font-size: 1.125rem;
}

.pagination-container {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .group-submissions-page {
        padding: 1rem;
    }

    .submissions-grid {
        grid-template-columns: 1fr;
    }

    .filters-form {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
