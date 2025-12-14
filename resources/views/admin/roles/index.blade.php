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
        <h1 class="dashboard-title">Roles</h1>
        @if($isSuperAdmin)
            <a href="{{ route('admin.roles.create') }}" class="dashboard-btn dashboard-btn-primary">Add New Role</a>
        @endif
    </div>
    <div class="dashboard-table-container">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->description }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($isSuperAdmin)
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="action-btn edit-btn" title="Edit Role & Permissions">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('Are you sure you want to delete this role?');" style="display:inline;">
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
                        <td colspan="3" class="text-center">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-table th,
.dashboard-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.dashboard-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.dashboard-table tr:last-child td {
    border-bottom: none;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.action-btn {
    padding: 0.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.edit-btn {
    background: #e3f2fd;
    color: #1976d2;
}

.edit-btn:hover {
    background: #bbdefb;
}

.delete-btn {
    background: #ffebee;
    color: #d32f2f;
}

.delete-btn:hover {
    background: #ffcdd2;
}

.delete-form {
    margin: 0;
    padding: 0;
}

.dashboard-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-btn-primary {
    background: #007bff;
    color: white;
}

.dashboard-btn-primary:hover {
    background: #0056b3;
    transform: translateY(-1px);
}
</style>
@endsection 