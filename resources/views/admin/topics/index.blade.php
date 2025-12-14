@extends('layouts.dashboard')

@section('content')
@php
    $user = Auth::user();
    $userPermissions = [];
    if ($user) {
        $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
        $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
        $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
    }
@endphp
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Topics</h1>
        @if(in_array('create_topic', $userPermissions))
            <a href="{{ route('admin.topics.create') }}" class="dashboard-btn dashboard-btn-primary">Add New Topic</a>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="dashboard-filters" style="margin-bottom: 20px;">
        <form action="{{ route('admin.topics.index') }}" method="GET" class="dashboard-filter-form">
            <div class="filter-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div class="filter-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="profile-input" placeholder="Search topics...">
                </div>
                <div class="filter-group">
                    <select name="per_page" class="profile-input">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="status" class="profile-input">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="subject_id" class="profile-input">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">Filter</button>
                    <a href="{{ route('admin.topics.index') }}" class="dashboard-btn dashboard-btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-table-container">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topics as $topic)
                    <tr>
                        <td>{{ $topic->name }}</td>
                        <td>{{ Str::limit($topic->description, 50) }}</td>
                        <td>{{ $topic->subject ? $topic->subject->name : '-' }}</td>
                        <td>
                            <span class="status-badge {{ $topic->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $topic->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if(in_array('edit_topic', $userPermissions))
                                <a href="{{ route('admin.topics.edit', $topic->hash_id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if(in_array('delete_topic', $userPermissions))
                                <form action="{{ route('admin.topics.destroy', $topic->hash_id) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('Are you sure you want to delete this topic?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No topics found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="dashboard-pagination">
        {{ $topics->appends(request()->query())->links() }}
    </div>
</div>

<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 500;
}
.status-active {
    background-color: #e6f4ea;
    color: #1e7e34;
}
.status-inactive {
    background-color: #fbe9e7;
    color: #d32f2f;
}
.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.action-btn {
    color: #666;
    text-decoration: none;
    padding: 0;
    border-radius: 4px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}
.delete-form {
    display: inline-flex;
    margin: 0;
    padding: 0;
}
.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.edit-btn:hover {
    color: #0d6efd;
    background-color: #e9ecef;
    border-color: #0d6efd;
}
.delete-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.delete-btn:hover {
    color: #dc3545;
    background-color: #e9ecef;
    border-color: #dc3545;
}
.action-btn i {
    font-size: 14px;
}
</style>
@endsection 