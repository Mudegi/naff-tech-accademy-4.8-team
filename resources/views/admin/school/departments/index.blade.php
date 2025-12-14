@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner departments-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-building text-orange-600 mr-3"></i>
                Departments Management
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Departments</span>
            </div>
        </div>
        <a href="{{ route('admin.school.departments.create') }}" class="btn-modern btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Department
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
        <form method="GET" action="{{ route('admin.school.departments.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search departments by name or code..." 
                           class="search-input">
                </div>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.school.departments.index') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Departments Table -->
    <div class="table-card-modern">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-list table-header-icon"></i>
                <h3 class="table-header-title">All Departments</h3>
                <span class="table-count-badge">{{ $departments->total() }} {{ Str::plural('department', $departments->total()) }}</span>
            </div>
        </div>
        <div class="table-container-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-building mr-2"></i>
                                Department
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-code mr-2"></i>
                                Code
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-user-tie mr-2"></i>
                                Head of Department
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-users mr-2"></i>
                                Teachers
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
                    @forelse($departments as $department)
                    <tr class="table-row-modern">
                        <td>
                            <div class="department-cell">
                                <div class="department-icon-wrapper">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="department-info">
                                    <div class="department-name">{{ $department->name }}</div>
                                    @if($department->description)
                                        <div class="department-description">{{ Str::limit($department->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($department->code)
                                <span class="code-badge">{{ $department->code }}</span>
                            @else
                                <span class="code-badge code-empty">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($department->headOfDepartment)
                                <div class="hod-cell">
                                    <i class="fas fa-user-tie hod-icon"></i>
                                    <span class="hod-name">{{ $department->headOfDepartment->name }}</span>
                                </div>
                            @else
                                <span class="not-assigned">
                                    <i class="fas fa-user-slash mr-1"></i>
                                    Not Assigned
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.school.departments.show', $department->id) }}" class="teachers-badge">
                                <i class="fas fa-users mr-1"></i>
                                {{ $department->teachers->count() }} {{ Str::plural('Teacher', $department->teachers->count()) }}
                            </a>
                        </td>
                        <td>
                            <span class="status-badge-modern {{ $department->is_active ? 'status-active-modern' : 'status-inactive-modern' }}">
                                <i class="fas fa-circle mr-1"></i>
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.school.departments.show', $department->id) }}" class="action-btn action-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.school.departments.edit', $department->id) }}" class="action-btn action-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.school.departments.destroy', $department->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this department? All teachers will be unassigned.');">
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
                        <td colspan="6" class="empty-state">
                            <div class="empty-state-content">
                                <div class="empty-state-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h3 class="empty-state-title">No Departments Found</h3>
                                <p class="empty-state-text">
                                    @if(request('search') || request('status'))
                                        No departments match your search criteria. Try adjusting your filters.
                                    @else
                                        Get started by creating your first department.
                                    @endif
                                </p>
                                <a href="{{ route('admin.school.departments.create') }}" class="btn-modern btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Create Department
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
    @if($departments->hasPages())
    <div class="pagination-wrapper">
        {{ $departments->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
/* Departments Page Styles */
.departments-page {
    padding: 1.5rem;
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
    color: #3b82f6;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
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
    background: #3b82f6;
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

/* Department Cell */
.department-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.department-icon-wrapper {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.department-info {
    flex: 1;
}

.department-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.department-description {
    font-size: 0.8125rem;
    color: #6b7280;
}

/* Code Badge */
.code-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    background: #eff6ff;
    color: #1e40af;
    border-radius: 0.375rem;
    font-weight: 600;
    font-size: 0.8125rem;
    font-family: 'Courier New', monospace;
}

.code-badge.code-empty {
    background: #f3f4f6;
    color: #9ca3af;
}

/* HOD Cell */
.hod-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.hod-icon {
    color: #3b82f6;
    font-size: 0.875rem;
}

.hod-name {
    font-weight: 500;
    color: #1a1a1a;
}

.not-assigned {
    color: #9ca3af;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

/* Teachers Badge */
.teachers-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.teachers-badge:hover {
    background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

/* Status Badge Modern */
.status-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
}

.status-active-modern {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive-modern {
    background: #fee2e2;
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
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
}

.action-view {
    background: #d1fae5;
    color: #065f46;
}

.action-view:hover {
    background: #a7f3d0;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.action-edit {
    background: #dbeafe;
    color: #1e40af;
}

.action-edit:hover {
    background: #bfdbfe;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
}

.action-delete {
    background: #fee2e2;
    color: #991b1b;
}

.action-delete:hover {
    background: #fecaca;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state-content {
    max-width: 400px;
    margin: 0 auto;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #f59e0b;
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
    line-height: 1.6;
}

/* Pagination Wrapper */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.pagination-wrapper .page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    color: #4b5563;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination-wrapper .page-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.pagination-wrapper .page-item.active .page-link {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination-wrapper .page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Animations */
@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slide-down 0.3s ease;
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
    
    .table-container-modern {
        overflow-x: scroll;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>
@endpush
@endsection

