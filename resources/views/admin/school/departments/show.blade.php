@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner department-details-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-building text-orange-600 mr-3"></i>
                {{ $department->name }}
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.departments.index') }}">Departments</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">{{ $department->name }}</span>
            </div>
        </div>
        <a href="{{ route('admin.school.departments.edit', $department->id) }}" class="btn-modern btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit Department
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

    <!-- Department Hero Card -->
    <div class="department-hero-card">
        <div class="department-hero-content">
            <div class="department-hero-left">
                <div class="department-icon-large">
                    <i class="fas fa-building"></i>
                </div>
                <div class="department-hero-info">
                    <h2 class="department-hero-title">{{ $department->name }}</h2>
                    @if($department->code)
                        <div class="department-code-hero">
                            <i class="fas fa-code mr-2"></i>
                            {{ $department->code }}
                        </div>
                    @endif
                    @if($department->description)
                        <p class="department-description-hero">{{ $department->description }}</p>
                    @endif
                </div>
            </div>
            <div class="department-hero-right">
                <span class="status-badge-hero {{ $department->is_active ? 'status-active-hero' : 'status-inactive-hero' }}">
                    <i class="fas fa-circle mr-2"></i>
                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Department Info Cards -->
    <div class="info-cards-grid">
        <!-- Head of Department Card -->
        <div class="info-card-modern">
            <div class="info-card-header">
                <div class="info-card-icon hod-icon-modern">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 class="info-card-title">Head of Department</h3>
            </div>
            <div class="info-card-body">
                @if($department->headOfDepartment)
                    <div class="hod-profile">
                        <div class="hod-avatar">
                            <span>{{ strtoupper(substr($department->headOfDepartment->name, 0, 2)) }}</span>
                        </div>
                        <div class="hod-details">
                            <div class="hod-name">{{ $department->headOfDepartment->name }}</div>
                            <div class="hod-email">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ $department->headOfDepartment->email }}
                            </div>
                            @if($department->headOfDepartment->phone_number)
                                <div class="hod-phone">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $department->headOfDepartment->phone_number }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="info-card-action">
                        <a href="{{ route('admin.school.staff.edit', $department->headOfDepartment->id) }}" class="action-link-modern">
                            <i class="fas fa-edit mr-1"></i> Edit HOD
                        </a>
                    </div>
                @else
                    <div class="empty-hod">
                        <i class="fas fa-user-slash empty-icon"></i>
                        <p class="empty-text">No Head of Department assigned</p>
                        <a href="{{ route('admin.school.departments.edit', $department->id) }}" class="btn-modern btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Assign HOD
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="info-card-modern">
            <div class="info-card-header">
                <div class="info-card-icon stats-icon-modern">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="info-card-title">Department Statistics</h3>
            </div>
            <div class="info-card-body">
                <div class="stats-list">
                    <div class="stat-item-modern">
                        <div class="stat-item-left">
                            <i class="fas fa-users stat-icon"></i>
                            <span class="stat-label">Total Teachers</span>
                        </div>
                        <span class="stat-value-modern">{{ $teachers->count() }}</span>
                    </div>
                    <div class="stat-item-modern">
                        <div class="stat-item-left">
                            <i class="fas fa-check-circle stat-icon stat-icon-success"></i>
                            <span class="stat-label">Active Teachers</span>
                        </div>
                        <span class="stat-value-modern stat-value-success">{{ $teachers->where('is_active', true)->count() }}</span>
                    </div>
                    <div class="stat-item-modern">
                        <div class="stat-item-left">
                            <i class="fas fa-times-circle stat-icon stat-icon-danger"></i>
                            <span class="stat-label">Inactive Teachers</span>
                        </div>
                        <span class="stat-value-modern stat-value-danger">{{ $teachers->where('is_active', false)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teachers List -->
    <div class="table-card-modern">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-users table-header-icon"></i>
                <h3 class="table-header-title">Teachers in this Department</h3>
                <span class="table-count-badge">{{ $teachers->count() }} {{ Str::plural('Teacher', $teachers->count()) }}</span>
            </div>
            <button onclick="openAssignModal()" class="btn-modern btn-primary">
                <i class="fas fa-user-plus mr-2"></i> Assign Teachers
            </button>
        </div>

        <div class="table-container-modern">
            @if($teachers->count() > 0)
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>
                                <div class="table-th-content">
                                    <i class="fas fa-user mr-2"></i>
                                    Teacher
                                </div>
                            </th>
                            <th>
                                <div class="table-th-content">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Email
                                </div>
                            </th>
                            <th>
                                <div class="table-th-content">
                                    <i class="fas fa-phone mr-2"></i>
                                    Phone
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
                        @foreach($teachers as $teacher)
                        <tr class="table-row-modern">
                            <td>
                                <div class="teacher-cell">
                                    <div class="teacher-avatar">
                                        <span>{{ strtoupper(substr($teacher->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="teacher-name">{{ $teacher->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-text">
                                    <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                    {{ $teacher->email }}
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-text">
                                    @if($teacher->phone_number)
                                        <i class="fas fa-phone mr-1 text-gray-400"></i>
                                        {{ $teacher->phone_number }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-badge-modern {{ $teacher->is_active ? 'status-active-modern' : 'status-inactive-modern' }}">
                                    <i class="fas fa-circle mr-1"></i>
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.school.staff.edit', $teacher->id) }}" class="action-btn action-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.school.departments.remove-teacher', [$department->id, $teacher->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this teacher from the department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-remove" title="Remove from Department">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-content">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="empty-state-title">No Teachers Assigned</h3>
                        <p class="empty-state-text">This department doesn't have any teachers assigned yet. Start by assigning teachers to this department.</p>
                        <button onclick="openAssignModal()" class="btn-modern btn-primary">
                            <i class="fas fa-user-plus mr-2"></i> Assign Teachers
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Teachers Modal -->
<div id="assignModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="modal-title">Assign Teachers to {{ $department->name }}</h3>
            </div>
            <button onclick="closeAssignModal()" class="modal-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.school.departments.assign-teachers', $department->id) }}" method="POST" class="modal-form">
            @csrf
            <div class="modal-body">
                <p class="modal-description">Select teachers to assign to this department:</p>
                <div class="teachers-list-container">
                    @if($availableTeachers->count() > 0)
                        @foreach($availableTeachers as $teacher)
                            <label class="teacher-checkbox-item">
                                <input type="checkbox" 
                                       name="teacher_ids[]" 
                                       value="{{ $teacher->id }}"
                                       {{ $teacher->department_id == $department->id ? 'checked' : '' }}
                                       class="teacher-checkbox">
                                <div class="teacher-checkbox-content">
                                    <div class="teacher-checkbox-avatar">
                                        <span>{{ strtoupper(substr($teacher->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="teacher-checkbox-info">
                                        <div class="teacher-checkbox-name">{{ $teacher->name }}</div>
                                        <div class="teacher-checkbox-email">{{ $teacher->email }}</div>
                                        @if($teacher->department_id == $department->id)
                                            <span class="teacher-already-assigned">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Already assigned
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    @else
                        <div class="modal-empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p>No available teachers found.</p>
                            <a href="{{ route('admin.school.staff.create') }}" class="btn-modern btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Create Teacher
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeAssignModal()" class="btn-modern btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-check mr-2"></i> Assign Selected Teachers
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Department Details Page Styles */
.department-details-page {
    padding: 1.5rem;
}

/* Department Hero Card */
.department-hero-card {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.department-hero-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 20s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

.department-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.department-hero-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.department-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.department-hero-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.department-hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.department-code-hero {
    font-size: 1rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    font-family: 'Courier New', monospace;
}

.department-description-hero {
    font-size: 0.9375rem;
    opacity: 0.9;
    margin: 0;
    max-width: 600px;
}

.department-hero-right {
    display: flex;
    align-items: center;
}

.status-badge-hero {
    padding: 0.625rem 1.25rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    backdrop-filter: blur(10px);
}

.status-active-hero {
    background: rgba(16, 185, 129, 0.3);
    border: 1px solid rgba(16, 185, 129, 0.5);
}

.status-inactive-hero {
    background: rgba(239, 68, 68, 0.3);
    border: 1px solid rgba(239, 68, 68, 0.5);
}

/* Info Cards Grid */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-card-modern {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.info-card-modern:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.hod-icon-modern {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stats-icon-modern {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.info-card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.info-card-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* HOD Profile */
.hod-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.hod-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.hod-details {
    flex: 1;
}

.hod-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.hod-email,
.hod-phone {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
}

.info-card-action {
    margin-top: 0.5rem;
}

.action-link-modern {
    display: inline-flex;
    align-items: center;
    color: #3b82f6;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
}

.action-link-modern:hover {
    color: #2563eb;
}

/* Empty HOD */
.empty-hod {
    text-align: center;
    padding: 1.5rem 0;
}

.empty-icon {
    font-size: 2.5rem;
    color: #d1d5db;
    margin-bottom: 0.75rem;
}

.empty-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
}

/* Stats List */
.stats-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.stat-item-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: #3b82f6;
    background: #eff6ff;
}

.stat-icon-success {
    color: #10b981;
    background: #d1fae5;
}

.stat-icon-danger {
    color: #ef4444;
    background: #fee2e2;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.stat-value-modern {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
}

.stat-value-success {
    color: #10b981;
}

.stat-value-danger {
    color: #ef4444;
}

/* Teacher Cell */
.teacher-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.teacher-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.teacher-name {
    font-weight: 600;
    color: #1a1a1a;
}

/* Action Remove */
.action-remove {
    background: #fef3c7;
    color: #92400e;
}

.action-remove:hover {
    background: #fde68a;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(251, 191, 36, 0.2);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-container {
    background: white;
    border-radius: 1rem;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 2px solid #f3f4f6;
}

.modal-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.modal-close-btn {
    width: 36px;
    height: 36px;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.modal-close-btn:hover {
    background: #e5e7eb;
    color: #1a1a1a;
}

.modal-form {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.modal-description {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.teachers-list-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.5rem;
}

.teacher-checkbox-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background 0.2s ease;
    margin-bottom: 0.5rem;
}

.teacher-checkbox-item:hover {
    background: #f9fafb;
}

.teacher-checkbox-item:last-child {
    margin-bottom: 0;
}

.teacher-checkbox {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
    cursor: pointer;
    accent-color: #3b82f6;
}

.teacher-checkbox-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.teacher-checkbox-avatar {
    width: 36px;
    height: 36px;
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

.teacher-checkbox-info {
    flex: 1;
}

.teacher-checkbox-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.teacher-checkbox-email {
    font-size: 0.8125rem;
    color: #6b7280;
}

.teacher-already-assigned {
    display: inline-flex;
    align-items: center;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #10b981;
    font-weight: 500;
}

.modal-empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.modal-empty-state i {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.modal-empty-state p {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.5rem;
    border-top: 2px solid #f3f4f6;
}

/* Reuse styles from departments index */
.btn-modern, .alert-modern, .table-card-modern, .table-header-modern, 
.table-header-left, .table-header-icon, .table-header-title, 
.table-count-badge, .table-container-modern, .table-modern, 
.table-th-content, .table-row-modern, .table-cell-text, 
.status-badge-modern, .status-active-modern, .status-inactive-modern,
.action-buttons, .action-btn, .action-edit, .empty-state,
.empty-state-content, .empty-state-icon, .empty-state-title,
.empty-state-text, .animate-slide-down {
    /* These styles are already defined in the index page */
}

/* Responsive */
@media (max-width: 768px) {
    .department-hero-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }
    
    .info-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-container {
        max-width: 100%;
        margin: 1rem;
    }
}
</style>
@endpush

<script>
function openAssignModal() {
    document.getElementById('assignModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('assignModal');
    if (event.target == modal) {
        closeAssignModal();
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAssignModal();
    }
});
</script>
@endsection

