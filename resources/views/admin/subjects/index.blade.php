@extends('layouts.dashboard')

@section('content')
@php
    $user = Auth::user();
    $isSchoolAdmin = $user && $user->school_id;
@endphp
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">System Subjects</h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-active">Subjects</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- System Subjects Info Banner -->
    @if($isSchoolAdmin)
    <div class="dashboard-card" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border-left: 4px solid #0ea5e9; margin-bottom: 20px;">
        <div style="padding: 15px;">
            <h4 style="margin: 0 0 10px 0; color: #0c4a6e; font-size: 16px;">
                <i class="fas fa-info-circle" style="color: #0ea5e9; margin-right: 8px;"></i>
                Standard Ugandan Curriculum Subjects
            </h4>
            <p style="margin: 0; color: #374151; font-size: 14px;">
                The system comes pre-loaded with standard subjects following Uganda's O Level and A Level curriculum.
                These subjects are available to all schools and are managed centrally to ensure consistency across the education system.
                You can assign these subjects to classes to customize the curriculum for your students.
            </p>
        </div>
    </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="dashboard-filters">
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="dashboard-search-form">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subjects..." class="dashboard-input">
                </div>
                <select name="status" class="dashboard-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <select name="level" class="dashboard-select">
                    <option value="">All Levels</option>
                    @foreach($levels as $value => $label)
                        <option value="{{ $value }}" {{ request('level') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <select name="per_page" class="dashboard-select">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                @if(request()->hasAny(['search', 'status', 'level', 'per_page']))
                    <a href="{{ route('admin.subjects.index') }}" class="dashboard-btn dashboard-btn-secondary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Subjects Table -->
    <div class="dashboard-table-container">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Level</th>
                    <th>Papers</th>
                    <th>Description</th>
                    <th>Topics</th>
                    <th>Status</th>
                    @if(!$isSchoolAdmin)
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <strong>{{ $subject->name }}</strong>
                        </td>
                        <td>
                            @if($subject->level)
                                <span class="level-badge {{ $subject->level === 'A Level' ? 'level-a' : ($subject->level === 'O Level' ? 'level-o' : 'level-both') }}">
                                    {{ $subject->level }}
                                </span>
                            @else
                                <span style="color: #9ca3af;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($subject->paper_count && $subject->paper_count > 1)
                                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                    <strong style="color: #1f2937;">{{ $subject->paper_count }} Papers</strong>
                                    @if($subject->papers && count($subject->papers) > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                            @foreach(array_slice($subject->papers, 0, 3) as $paper)
                                                <span class="badge badge-primary">
                                                    {{ $paper['name'] ?? 'Paper' }}
                                                    @if(!empty($paper['code']))
                                                        <span style="font-size: 0.75rem; opacity: 0.8;">({{ $paper['code'] }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                            @if(count($subject->papers) > 3)
                                                <span class="badge badge-secondary">+{{ count($subject->papers) - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span style="color: #6b7280;">Single Paper</span>
                            @endif
                        </td>
                        <td>
                            @if($subject->description)
                                <span style="color: #6b7280; font-size: 0.875rem;">{{ Str::limit($subject->description, 60) }}</span>
                            @else
                                <span style="color: #9ca3af;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="color: #1f2937; font-weight: 600;">{{ $subject->total_topics ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $subject->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        @if(!$isSchoolAdmin)
                            <td>
                                <div class="dashboard-actions">
                                    <a href="{{ route('admin.subjects.edit', $subject->hash_id) }}" class="dashboard-btn dashboard-btn-small dashboard-btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subjects.destroy', $subject->hash_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dashboard-btn dashboard-btn-small dashboard-btn-danger" onclick="return confirm('Are you sure you want to delete this subject?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isSchoolAdmin ? '6' : '7' }}" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-book-open empty-icon"></i>
                                <h3>No subjects found</h3>
                                <p>System subjects are being loaded. Please contact the administrator if this persists.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="dashboard-pagination">
        {{ $subjects->appends(request()->query())->links() }}
    </div>
</div>

<style>
.dashboard-filters {
    background: #fff;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.search-input-group {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.dashboard-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.875rem;
}

.dashboard-select {
    padding: 0.5rem 2rem 0.5rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.875rem;
    background-color: #fff;
    min-width: 120px;
}

.dashboard-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.dashboard-btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.level-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.15rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.level-o {
    background: #dbeafe;
    color: #1d4ed8;
}

.level-a {
    background: #ede9fe;
    color: #6d28d9;
}

.level-both {
    background: #fef3c7;
    color: #92400e;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    font-weight: 500;
}

.badge-primary {
    background-color: #dbeafe;
    color: #1e40af;
}

.badge-secondary {
    background-color: #e5e7eb;
    color: #374151;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
}

.empty-state h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0;
}

.empty-icon {
    font-size: 2.5rem;
    color: #cbd5f5;
}

.dashboard-pagination {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

.dashboard-pagination .pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.dashboard-pagination .page-item {
    display: inline-block;
}

.dashboard-pagination .page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s;
}

.dashboard-pagination .page-link:hover {
    background-color: #f3f4f6;
}

.dashboard-pagination .page-item.active .page-link {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: #fff;
}

.dashboard-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    pointer-events: none;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
</style>
@endsection
