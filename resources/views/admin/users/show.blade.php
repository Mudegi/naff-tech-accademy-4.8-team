@extends('layouts.dashboard')

@section('content')
@php
    $accountTypes = ['admin' => 'Admin', 'staff' => 'Staff', 'student' => 'Student', 'parent' => 'Parent'];
@endphp
<div class="dashboard-content-inner">
    @php
        $isSuperAdmin = Auth::user()->isSuperAdmin();
    @endphp
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">User Details</h1>
        <div class="breadcrumb-actions">
            <a href="{{ route('admin.users.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
            @if($isSuperAdmin)
            <a href="{{ route('admin.users.edit', $user->id) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card">
        <div class="user-details-grid">
            <div class="user-info-section">
                <div class="user-header">
                    <div class="user-avatar">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="avatar-image">
                        @else
                            <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        @endif
                    </div>
                    <div class="user-title-section">
                        <h2 class="user-name">{{ $user->name }}</h2>
                        <p class="user-email">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="user-details">
                    <div class="detail-item">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value">{{ $user->phone_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Account Type</span>
                        <span class="detail-value">{{ $accountTypes[$user->account_type] ?? ucfirst($user->account_type) }}</span>
                    </div>
                    @if($user->account_type === 'teacher')
                        @if($user->subjects && $user->subjects->count())
                        <div class="detail-item">
                            <span class="detail-label">Subjects Taught</span>
                            <span class="detail-value">
                                {{ $user->subjects->pluck('name')->implode(', ') }}
                            </span>
                        </div>
                        @endif
                        @if($user->classes && $user->classes->count())
                        <div class="detail-item">
                            <span class="detail-label">Classes Taught</span>
                            <span class="detail-value">
                                {{ $user->classes->pluck('name')->implode(', ') }}
                            </span>
                        </div>
                        @endif
                    @endif
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            @if($user->is_active)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Created At</span>
                        <span class="detail-value">{{ $user->created_at->format('M d, Y H:i:s') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Last Updated</span>
                        <span class="detail-value">{{ $user->updated_at->format('M d, Y H:i:s') }}</span>
                    </div>
                </div>
            </div>

            <div class="user-actions-section">
                <div class="action-buttons">
                    @if($isSuperAdmin)
                    <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="action-form">
                        @csrf
                        <button type="submit" class="dashboard-btn dashboard-btn-primary action-btn">
                            <i class="fas fa-user-secret"></i> Login as User
                        </button>
                    </form>

                    <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST" class="action-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="dashboard-btn {{ $user->is_active ? 'dashboard-btn-warning' : 'dashboard-btn-success' }} action-btn">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                            {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="action-form delete-form" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dashboard-btn dashboard-btn-danger action-btn">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
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

.breadcrumb-actions {
    display: flex;
    gap: 10px;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.user-details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.user-info-section {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
}

.user-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #e2e8f0;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
    color: #64748b;
}

.user-title-section {
    flex: 1;
}

.user-name {
    font-size: 24px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.user-email {
    color: #64748b;
    margin: 0;
}

.user-details {
    display: grid;
    gap: 16px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.detail-label {
    color: #64748b;
    font-size: 14px;
}

.detail-value {
    font-weight: 500;
    color: #1e293b;
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

.user-actions-section {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-form {
    width: 100%;
}

.action-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.dashboard-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.dashboard-btn i {
    font-size: 14px;
}

.dashboard-btn-primary {
    background: #3498db;
    color: white;
}

.dashboard-btn-primary:hover {
    background: #2980b9;
}

.dashboard-btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.dashboard-btn-secondary:hover {
    background: #cbd5e0;
}

.dashboard-btn-success {
    background: #059669;
    color: white;
}

.dashboard-btn-success:hover {
    background: #047857;
}

.dashboard-btn-warning {
    background: #d97706;
    color: white;
}

.dashboard-btn-warning:hover {
    background: #b45309;
}

.dashboard-btn-danger {
    background: #dc2626;
    color: white;
}

.dashboard-btn-danger:hover {
    background: #b91c1c;
}

@media (max-width: 768px) {
    .user-details-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-breadcrumbs {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .breadcrumb-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .breadcrumb-actions .dashboard-btn {
        width: 100%;
    }
}
</style>
@endsection 