@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner resources-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-book text-purple-600 mr-3"></i>
                Resources & Videos
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Resources</span>
            </div>
        </div>
        <a href="{{ route('admin.school.resources.create') }}" class="btn-modern btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Resource
        </a>
    </div>

    @if (session('success'))
        <div class="alert-modern alert-success animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-modern alert-error animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('admin.school.resources.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search resources by title, description..." 
                           class="search-input">
                </div>
                <select name="subject" class="filter-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                <select name="grade_level" class="filter-select">
                    <option value="">All Levels</option>
                    @foreach($gradeLevels as $level)
                        <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
                <select name="teacher" class="filter-select">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                @if(request()->hasAny(['search', 'subject', 'grade_level', 'teacher']))
                    <a href="{{ route('admin.school.resources.index') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Resources Table -->
    <div class="table-card-modern">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-book table-header-icon"></i>
                <h3 class="table-header-title">All Resources</h3>
                <span class="table-count-badge">{{ $resources->total() }} {{ Str::plural('Resource', $resources->total()) }}</span>
            </div>
        </div>
        <div class="table-container-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-file-alt mr-2"></i>
                                Resource
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-book-open mr-2"></i>
                                Subject
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-tag mr-2"></i>
                                Topic
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>
                                Teacher
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Level
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-toggle-on mr-2"></i>
                                Status
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-cog mr-2"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resources as $resource)
                    <tr class="table-row-modern">
                        <td>
                            <div class="resource-cell">
                                <div class="resource-icon-wrapper">
                                    @if($resource->video_url)
                                        <i class="fas fa-video"></i>
                                    @elseif($resource->google_drive_link)
                                        <i class="fas fa-file-pdf"></i>
                                    @else
                                        <i class="fas fa-file-alt"></i>
                                    @endif
                                </div>
                                <div class="resource-info">
                                    <div class="resource-title">{{ $resource->title }}</div>
                                    @if($resource->description)
                                        <div class="resource-description">{{ Str::limit($resource->description, 60) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($resource->subject)
                                <span class="subject-badge">
                                    <i class="fas fa-book-open mr-1"></i>
                                    {{ $resource->subject->name }}
                                </span>
                            @else
                                <span class="not-assigned">
                                    <i class="fas fa-minus-circle mr-1"></i>
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($resource->topic)
                                <span class="topic-badge">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $resource->topic->name }}
                                </span>
                            @else
                                <span class="not-assigned">
                                    <i class="fas fa-minus-circle mr-1"></i>
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($resource->teacher)
                                <div class="teacher-cell">
                                    <div class="teacher-avatar-small">
                                        <span>{{ strtoupper(substr($resource->teacher->name, 0, 2)) }}</span>
                                    </div>
                                    <span class="teacher-name">{{ $resource->teacher->name }}</span>
                                </div>
                            @else
                                <span class="not-assigned">
                                    <i class="fas fa-user-slash mr-1"></i>
                                    Not Assigned
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="level-badge">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                {{ $resource->grade_level }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge-modern {{ $resource->is_active ? 'status-active-modern' : 'status-inactive-modern' }}">
                                <i class="fas fa-circle mr-1"></i>
                                {{ $resource->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.school.resources.show', $resource->id) }}" class="action-btn action-view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.school.resources.edit', $resource->id) }}" class="action-btn action-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.school.resources.destroy', $resource->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-content">
                                <div class="empty-state-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h3 class="empty-state-title">No Resources Found</h3>
                                <p class="empty-state-text">
                                    @if(request()->hasAny(['search', 'subject', 'grade_level', 'teacher']))
                                        No resources match your search criteria. Try adjusting your filters.
                                    @else
                                        Get started by creating your first learning resource for the school.
                                    @endif
                                </p>
                                <a href="{{ route('admin.school.resources.create') }}" class="btn-modern btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Add Resource
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($resources->hasPages())
    <div class="pagination-wrapper">
        {{ $resources->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
/* Resources Page Styles */
.resources-page {
    padding: 1.5rem;
}

/* Resource Cell */
.resource-cell {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.resource-icon-wrapper {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.resource-info {
    flex: 1;
    min-width: 0;
}

.resource-title {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
    font-size: 0.9375rem;
}

.resource-description {
    font-size: 0.8125rem;
    color: #6b7280;
    line-height: 1.4;
}

/* Subject Badge */
.subject-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Topic Badge */
.topic-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #6b21a8;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Teacher Cell */
.teacher-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.teacher-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.teacher-name {
    font-size: 0.875rem;
    color: #4b5563;
    font-weight: 500;
}

/* Level Badge */
.level-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Not Assigned */
.not-assigned {
    display: inline-flex;
    align-items: center;
    color: #9ca3af;
    font-size: 0.8125rem;
    font-style: italic;
}

/* Action View Button */
.action-view {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.action-view:hover {
    background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

/* Modern Buttons */
.btn-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

/* Alert Modern */
.alert-modern {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.animate-slide-down {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Search and Filter Card */
.search-filter-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.search-filter-form {
    width: 100%;
}

.search-input-group {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.search-input-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select {
    padding: 0.625rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 150px;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table Card Modern */
.table-card-modern {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-header-modern {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-bottom: 2px solid #e5e7eb;
}

.table-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.table-header-icon {
    font-size: 1.25rem;
    color: #8b5cf6;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3e8ff;
    border-radius: 0.5rem;
}

.table-header-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.table-count-badge {
    padding: 0.25rem 0.75rem;
    background: #8b5cf6;
    color: white;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.table-container-modern {
    overflow-x: auto;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
}

.table-modern thead {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.table-modern th {
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.table-th-content {
    display: flex;
    align-items: center;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.table-row-modern:hover {
    background: #f9fafb;
    transform: scale(1.001);
}

.table-modern td {
    padding: 1.25rem 1.5rem;
    font-size: 0.875rem;
}

/* Status Badges */
.status-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
}

.status-active-modern {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.status-inactive-modern {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.action-edit {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.action-edit:hover {
    background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.action-delete {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.action-delete:hover {
    background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
    padding: 3rem 1.5rem;
}

.empty-state-content {
    text-align: center;
    max-width: 400px;
    margin: 0 auto;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 2rem;
}

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .search-input-group {
        flex-direction: column;
    }
    
    .search-input-wrapper {
        min-width: 100%;
    }
    
    .filter-select {
        width: 100%;
    }
    
    .resource-cell {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .teacher-cell {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush
@endsection

