@extends('layouts.dashboard')

@section('content')
@php
    $user = Auth::user();
    $userPermissions = [];
    $isAdmin = false;
    
    if ($user) {
        // Check if user is admin
        $isAdmin = ($user->account_type === 'admin');
        
        // Get permissions from roles
        $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
        $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
        $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        
        // Admins get all permissions by default
        if ($isAdmin) {
            $allPermissions = DB::table('permissions')->pluck('name')->toArray();
            $userPermissions = array_unique(array_merge($userPermissions, $allPermissions));
        }
    }
@endphp
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Resources</h1>
        @if(in_array('create_resource', $userPermissions) || $isAdmin)
        <a href="{{ route('admin.resources.create') }}" class="dashboard-btn dashboard-btn-primary">
            <i class="fas fa-plus"></i> Add Resource
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card">
        <form action="{{ route('admin.resources.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search resources..." class="filter-input">
                </div>
                <div class="filter-group">
                    <select name="subject" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="topic" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Topics</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ request('topic') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="grade_level" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($gradeLevels as $level)
                            <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="term" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="teacher" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request()->hasAny(['search', 'subject', 'topic', 'grade_level', 'term', 'teacher']))
                        <a href="{{ route('admin.resources.index') }}" class="filter-btn filter-btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="resources-grid">
            @forelse($resources as $resource)
                <div class="resource-card">
                    <div class="resource-header">
                        <h3 class="resource-title">{{ $resource->title }}</h3>
                        <p class="resource-description">{{ Str::limit($resource->description, 100) }}</p>
                    </div>
                    <div class="resource-tags">
                        <span class="resource-tag grade-level">{{ $resource->grade_level }}</span>
                        <span class="resource-tag subject">{{ $resource->subject->name }}</span>
                        <span class="resource-tag term">{{ $resource->term->name }}</span>
                        @if($resource->teacher)
                            <span class="resource-tag teacher">{{ $resource->teacher->name }}</span>
                        @endif
                    </div>
                    <div class="resource-footer">
                        <div class="resource-actions">
                            @if(in_array('view_resource', $userPermissions) || $isAdmin)
                            <a href="{{ route('admin.resources.show', $resource->hash_id) }}" class="action-btn view-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                            @if(in_array('edit_resource', $userPermissions) || $isAdmin)
                            <a href="{{ route('admin.resources.edit', $resource->hash_id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @if(in_array('delete_resource', $userPermissions) || $isAdmin)
                            <form action="{{ route('admin.resources.destroy', $resource->hash_id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this resource?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        <span class="resource-date">{{ $resource->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="no-resources">
                    <p>No resources found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-pagination">
        {{ $resources->links('vendor.pagination.simple-default') }}
    </div>
    <form method="GET" action="{{ route('admin.resources.index') }}" id="perPageForm" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
        @foreach(request()->except('per_page', 'page') as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <label for="per_page" style="font-weight: 500;">Show per page:</label>
        <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit();" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            @foreach([10, 20, 30, 50, 100] as $limit)
                <option value="{{ $limit }}" {{ request('per_page', 12) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
            @endforeach
        </select>
    </form>
</div>

<style>
.dashboard-content-inner {
    padding: 20px;
}

.dashboard-breadcrumbs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.dashboard-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.dashboard-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dashboard-btn i {
    margin-right: 8px;
}

.dashboard-btn-primary {
    background-color: #3498db;
    color: white;
}

.dashboard-btn-primary:hover {
    background-color: #2980b9;
}

.dashboard-alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.dashboard-alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.resource-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.resource-header {
    padding: 16px;
}

.resource-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
}

.resource-description {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.resource-tags {
    padding: 0 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.resource-tag {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.grade-level {
    background-color: #e3f2fd;
    color: #1976d2;
}

.subject {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.term {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

.teacher {
    background-color: #fff3e0;
    color: #f57c00;
}

.resource-footer {
    padding: 12px 16px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.resource-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: #666;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.view-btn:hover {
    color: #3498db;
    border-color: #3498db;
}

.edit-btn:hover {
    color: #f39c12;
    border-color: #f39c12;
}

.delete-btn:hover {
    color: #e74c3c;
    border-color: #e74c3c;
}

.delete-form {
    display: inline;
    margin: 0;
    padding: 0;
}

.resource-date {
    font-size: 12px;
    color: #666;
}

.no-resources {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #666;
}

.dashboard-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item {
    display: flex;
}

.page-link {
    display: inline-block;
    min-width: 36px;
    padding: 8px 14px;
    border-radius: 6px;
    background: #f4f6fa;
    color: #2563eb;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    transition: background 0.2s, color 0.2s, border 0.2s;
}

.page-link:hover {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

.page-item.active .page-link {
    background: #2563eb !important;
    color: #fff !important;
    border-color: #2563eb !important;
    font-weight: bold;
}

.page-item.disabled .page-link {
    background: #f4f6fa;
    color: #b0b0b0;
    border-color: #e5e7eb;
    cursor: not-allowed;
}

/* Filter Styles */
.filters-form {
    margin-bottom: 20px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    gap: 10px;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    color: #2c3e50;
    background-color: white;
    transition: border-color 0.2s;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
}

.filter-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background-color: #3498db;
    color: white;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
}

.filter-btn:hover {
    background-color: #2980b9;
}

.filter-btn-secondary {
    background-color: #e74c3c;
}

.filter-btn-secondary:hover {
    background-color: #c0392b;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-group {
        flex-direction: column;
    }
}
</style>
@endsection 