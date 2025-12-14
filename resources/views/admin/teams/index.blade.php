@extends('layouts.dashboard')

@section('title', 'Team Management')

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <h1 class="content-title">Team Management</h1>
            <p class="content-subtitle">Manage your team members and their information</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Team Member
            </a>
        </div>
    </div>

    <div class="content-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($teams->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Skills</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teams as $team)
                        <tr>
                            <td>
                                <div class="team-image">
                                    <img src="{{ asset('storage/' . $team->image_path) }}" 
                                         alt="{{ $team->name }}"
                                         onerror="handleImageError(this)">
                                </div>
                            </td>
                            <td>
                                <div class="team-info">
                                    <h4 class="team-name">{{ $team->name }}</h4>
                                </div>
                            </td>
                            <td>
                                <span class="team-position">{{ $team->position }}</span>
                            </td>
                            <td>
                                <div class="skills-container">
                                    @foreach($team->skills_array as $skill)
                                    <span class="skill-tag">{{ trim($skill) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="sort-order">{{ $team->sort_order }}</span>
                            </td>
                            <td>
                                <span class="status-badge {{ $team->is_active ? 'active' : 'inactive' }}">
                                    {{ $team->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.teams.show', $team) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teams.edit', $team) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.teams.toggle-status', $team) }}" 
                                          method="POST" class="inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm {{ $team->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                title="{{ $team->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $team->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.teams.destroy', $team) }}" 
                                          method="POST" class="inline-form"
                                          onsubmit="return confirm('Are you sure you want to delete this team member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $teams->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>No Team Members Found</h3>
                <p>You haven't added any team members yet. Add your first team member to get started.</p>
                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Team Member
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.header-left {
    flex: 1;
}

.header-right {
    flex: 0 0 auto;
    margin-left: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.btn-info {
    background: #06b6d4;
    color: white;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8fafc;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

.data-table tr:hover {
    background: #f9fafb;
}

.team-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
}

.team-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-name {
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.team-position {
    color: #6b7280;
    font-size: 0.875rem;
}

.skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    max-width: 200px;
}

.skill-tag {
    background: #e0e7ff;
    color: #3730a3;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.sort-order {
    background: #f3f4f6;
    color: #374151;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.inline-form {
    display: inline;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.alert-danger {
    background-color: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}
</style>

<script>
function handleImageError(img) {
    img.src = '{{ asset("images/team.jpg") }}';
}
</script>
@endsection
