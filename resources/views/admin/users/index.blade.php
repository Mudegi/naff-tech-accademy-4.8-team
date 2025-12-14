@extends('layouts.dashboard')

@section('content')
@php
    $accountTypes = ['admin' => 'Admin', 'staff' => 'Staff', 'student' => 'Student', 'parent' => 'Parent', 'teacher' => 'Teacher'];
@endphp
<div class="dashboard-content-inner">
    @php
        $isSuperAdmin = Auth::user()->isSuperAdmin();
    @endphp
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Users</h1>
        @if($isSuperAdmin)
        <a href="{{ route('admin.users.create') }}" class="dashboard-btn">
            <i class="fas fa-plus"></i> Add New User
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card">
        <!-- Filters -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-item">
                    <label>Search</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="Search by name or email">
                </div>
                <div class="filter-item">
                    <label>Account Type</label>
                    <select name="account_type" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        @foreach($accountTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('account_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="is_active" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Per Page</label>
                    <select name="per_page" class="filter-input" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <div class="filter-buttons">
                        <button type="submit" class="filter-button">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="clear-button">Clear Filters</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="users-grid">
            @forelse($users as $user)
                <div class="user-card">
                    <div class="user-header">
                        <h3 class="user-title">{{ $user->name }}</h3>
                        <p class="user-email">{{ $user->email }}</p>
                    </div>
                    <div class="user-details">
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">{{ $user->phone_number ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">{{ ucfirst($user->account_type) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                @if($user->is_active)
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="user-footer">
                        <div class="user-actions">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn view-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($isSuperAdmin)
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="action-btn impersonate-btn" title="Impersonate">
                                    <i class="fas fa-user-secret"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-users">
                    <p>No users found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-pagination">
        {{ $users->appends(request()->query())->links() }}
    </div>
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

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.filters-form {
    margin-bottom: 24px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.filter-item label {
    font-size: 14px;
    color: #4a5568;
    margin-bottom: 4px;
}

.filter-input {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 14px;
}

.filter-button {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}

.filter-button:hover {
    background: #2980b9;
}

.filter-buttons {
    display: flex;
    gap: 8px;
}

.clear-button {
    background: #e2e8f0;
    color: #4a5568;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.clear-button:hover {
    background: #cbd5e0;
}

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.user-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.user-header {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.user-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 4px 0;
}

.user-email {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.user-details {
    padding: 16px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.detail-label {
    color: #666;
    font-size: 14px;
}

.detail-value {
    font-weight: 500;
    color: #2c3e50;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.user-footer {
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

.user-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    color: white;
    transition: background-color 0.2s;
}

.view-btn {
    background-color: #3498db;
}

.view-btn:hover {
    background-color: #2980b9;
}

.edit-btn {
    background-color: #3498db;
}

.edit-btn:hover {
    background-color: #2980b9;
}

.impersonate-btn {
    background-color: #6c757d;
}

.impersonate-btn:hover {
    background-color: #5a6268;
}

.delete-btn {
    background-color: #dc2626;
}

.delete-btn:hover {
    background-color: #b91c1c;
}

.no-users {
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

.dashboard-btn {
    background: #3498db;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: background-color 0.2s;
}

.dashboard-btn:hover {
    background: #2980b9;
}

.dashboard-btn i {
    font-size: 12px;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .users-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when search input loses focus
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('blur', function() {
            this.form.submit();
        });
    }
});
</script>
@endpush

@endsection 