@extends('layouts.dashboard')

@section('content')
@php
    $user = Auth::user();
    $isSuperAdmin = $user->isSuperAdmin();
    $userPermissions = [];
    if ($user) {
        $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
        $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
        $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
    }
@endphp
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Permissions</h1>
        @if($isSuperAdmin)
            <a href="{{ route('admin.permissions.create') }}" class="dashboard-btn dashboard-btn-primary">Add New Permission</a>
        @endif
    </div>

    <!-- Search and Filter Section -->
    <div class="dashboard-filters" style="margin-bottom: 20px;">
        <form action="{{ route('admin.permissions.index') }}" method="GET" class="filter-form">
            <div class="filter-group" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search permissions..." class="dashboard-input" style="flex: 1;">
                <select name="per_page" class="dashboard-input" style="width: 120px;">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
                <button type="submit" class="dashboard-btn dashboard-btn-primary">Search</button>
                @if(request()->hasAny(['search', 'per_page']))
                    <a href="{{ route('admin.permissions.index') }}" class="dashboard-btn dashboard-btn-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="dashboard-table-container">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('admin.permissions.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                            Name
                            @if(request('sort') == 'name')
                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('admin.permissions.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                            Description
                            @if(request('sort') == 'description')
                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->description }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($isSuperAdmin)
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('Are you sure you want to delete this permission?');" style="display:inline;">
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
                        <td colspan="3" class="text-center">No permissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="dashboard-pagination">
        {{ $permissions->appends(request()->query())->links('vendor.pagination.simple-default') }}
    </div>
</div>

<style>
.sort-link {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.sort-link:hover {
    color: inherit;
}

.filter-form {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.dashboard-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.dashboard-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 5px;
}

.page-item {
    display: inline-block;
}

.page-link {
    display: block;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    transition: all 0.2s;
}

.page-link:hover {
    background-color: #f8f9fa;
    border-color: #ddd;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #ddd;
}
</style>
@endsection 