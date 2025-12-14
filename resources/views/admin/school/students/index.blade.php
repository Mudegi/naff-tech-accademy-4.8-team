@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner students-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-user-graduate text-blue-600 mr-3"></i>
                Students
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Students</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-6 animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-6 animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Display New Student Credentials -->
    @if (session('show_credentials') && session('new_student_credentials'))
        @php $credentials = session('new_student_credentials'); @endphp
        <div class="credentials-card credentials-card-blue mb-6">
            <div class="credentials-header">
                <div class="credentials-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="credentials-title">New Student Login Credentials</h3>
                    <p class="credentials-subtitle">Please save these credentials securely</p>
                </div>
            </div>
            <div class="credentials-body">
                <div class="credentials-grid">
                    <div class="credential-item">
                        <span class="credential-label">Name:</span>
                        <span class="credential-value">{{ $credentials['name'] }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Email:</span>
                        <span class="credential-value">{{ $credentials['email'] ?? 'N/A' }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Phone:</span>
                        <span class="credential-value">{{ $credentials['phone_number'] }}</span>
                    </div>
                    @if(isset($credentials['registration_number']))
                    <div class="credential-item">
                        <span class="credential-label">Registration #:</span>
                        <span class="credential-value">{{ $credentials['registration_number'] }}</span>
                    </div>
                    @endif
                    <div class="credential-item credential-item-password">
                        <span class="credential-label">Password:</span>
                        <span class="credential-value" id="new-password">{{ $credentials['password'] }}</span>
                        <button onclick="copyPassword('new-password')" class="copy-password-btn" title="Copy password">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="credentials-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>These credentials will not be shown again. Please save them securely.</span>
                </div>
            </div>
            <div class="credentials-footer">
                <button onclick="copyNewCredentials()" class="btn-copy-credentials">
                    <i class="fas fa-copy mr-2"></i>
                    Copy All Credentials
                </button>
            </div>
        </div>
    @endif

    <!-- Display Updated Student Credentials -->
    @if (session('show_updated_credentials') && session('updated_student_credentials'))
        @php $credentials = session('updated_student_credentials'); @endphp
        <div class="credentials-card credentials-card-green mb-6">
            <div class="credentials-header">
                <div class="credentials-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h3 class="credentials-title">Updated Student Password</h3>
                    <p class="credentials-subtitle">New password has been generated</p>
                </div>
            </div>
            <div class="credentials-body">
                <div class="credentials-grid">
                    <div class="credential-item">
                        <span class="credential-label">Name:</span>
                        <span class="credential-value">{{ $credentials['name'] }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Email:</span>
                        <span class="credential-value">{{ $credentials['email'] ?? 'N/A' }}</span>
                    </div>
                    <div class="credential-item credential-item-password">
                        <span class="credential-label">New Password:</span>
                        <span class="credential-value" id="updated-password">{{ $credentials['password'] }}</span>
                        <button onclick="copyPassword('updated-password')" class="copy-password-btn" title="Copy password">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="credentials-footer">
                <button onclick="copyUpdatedCredentials()" class="btn-copy-credentials">
                    <i class="fas fa-copy mr-2"></i>
                    Copy Password
                </button>
            </div>
        </div>
    @endif

    <!-- Header Actions Card -->
    <div class="action-card mb-6">
        <div class="action-card-header">
            <div class="action-card-title">
                <i class="fas fa-filter mr-2"></i>
                Search & Filter
            </div>
        </div>
        <div class="action-card-body">
            <form method="GET" action="{{ route('admin.school.students.index') }}" class="search-form">
                <div class="search-input-group">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name, email, phone, or registration number..."
                               class="search-input">
                    </div>
                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <select name="level" class="filter-select">
                        <option value="">All Levels</option>
                        @foreach($levels as $value => $label)
                            <option value="{{ $value }}" {{ request('level') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    @if(request('search') || request('status') || request('level'))
                        <a href="{{ route('admin.school.students.index') }}" class="btn-clear">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
    <div class="action-buttons mb-6">
        <a href="{{ route('admin.school.students.import') }}" class="btn-action btn-action-green">
            <i class="fas fa-file-import mr-2"></i>
            Import CSV
        </a>
        <a href="{{ route('admin.school.students.create') }}" class="btn-action btn-action-primary">
            <i class="fas fa-plus mr-2"></i>
            Add Student
        </a>
    </div>
    @endif

    <!-- Students Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <i class="fas fa-list mr-2"></i>
                Students List
                <span class="table-count">({{ $students->total() }})</span>
            </div>
        </div>
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registration #</th>
                        <th>Class</th>
                        <th>Level</th>
                        <th>Combination</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="table-cell-content">
                                <div class="table-avatar table-avatar-student">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <div class="table-name">{{ $student->name }}</div>
                                    @if($student->student && $student->student->date_of_birth)
                                        <div class="table-meta">DOB: {{ $student->student->date_of_birth->format('M d, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="table-email">
                                <i class="fas fa-envelope table-icon"></i>
                                {{ $student->email ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div class="table-phone">
                                <i class="fas fa-phone table-icon"></i>
                                {{ $student->phone_number ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <span class="table-badge table-badge-info">
                                {{ $student->student->registration_number ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="table-badge table-badge-secondary">
                                {{ $student->student->class ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="table-badge table-badge-level">
                                {{ $student->student->level ?? 'Unspecified' }}
                            </span>
                        </td>
                        <td>
                            @if($student->student && $student->student->combination)
                                <span class="table-badge table-badge-info">
                                    {{ $student->student->combination }}
                                </span>
                            @else
                                <span class="table-badge table-badge-secondary">
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="table-badge {{ $student->is_active ? 'table-badge-success' : 'table-badge-danger' }}">
                                <i class="fas fa-circle"></i>
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                                <a href="{{ route('admin.school.students.edit', $student->id) }}" class="table-action-btn table-action-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.school.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action-btn table-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="table-empty">
                            <div class="empty-state">
                                <i class="fas fa-user-graduate empty-icon"></i>
                                <h3 class="empty-title">No Students Found</h3>
                                <p class="empty-text">
                                    @if(request('search') || request('status'))
                                        No students match your search criteria. Try adjusting your filters.
                                    @else
                                        Get started by adding your first student.
                                    @endif
                                </p>
                                @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                                <a href="{{ route('admin.school.students.create') }}" class="btn-empty-action">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Student
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
    <div class="pagination-wrapper">
        {{ $students->links() }}
    </div>
    @endif
</div>

<style>
/* Students Page Styles */
.students-page {
    padding: 0 0 32px 0;
}

/* Animations */
@keyframes slideDown {
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
    animation: slideDown 0.3s ease-out;
}

/* Credentials Card */
.credentials-card {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.credentials-card-blue {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border-left: 4px solid #3b82f6;
}

.credentials-card-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-left: 4px solid #10b981;
}

.credentials-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
}

.credentials-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.credentials-card-green .credentials-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.credentials-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.25rem 0;
}

.credentials-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.credentials-body {
    padding: 1.5rem;
}

.credentials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.credential-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 0.5rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.credential-item-password {
    position: relative;
}

.credential-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    min-width: 100px;
}

.credential-value {
    font-size: 0.875rem;
    color: #1a1a1a;
    font-weight: 500;
    flex: 1;
}

.copy-password-btn {
    padding: 0.5rem;
    background: #f3f4f6;
    border: none;
    border-radius: 0.375rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.copy-password-btn:hover {
    background: #e5e7eb;
    color: #3b82f6;
}

.credentials-warning {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #92400e;
}

.credentials-warning i {
    color: #f59e0b;
}

.credentials-footer {
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.btn-copy-credentials {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-copy-credentials:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

.credentials-card-green .btn-copy-credentials {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.credentials-card-green .btn-copy-credentials:hover {
    box-shadow: 0 8px 12px rgba(16, 185, 129, 0.4);
}

/* Action Card */
.action-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.action-card-header {
    padding: 1.25rem 1.5rem;
    background: #f9fafb;
    border-bottom: 2px solid #f3f4f6;
}

.action-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    display: flex;
    align-items: center;
}

.action-card-body {
    padding: 1.5rem;
}

/* Search Form */
.search-form {
    width: 100%;
}

.search-input-group {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 300px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-search,
.btn-clear {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
}

.btn-search {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

.btn-clear {
    background: #6b7280;
    color: white;
}

.btn-clear:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-action-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-action-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

.btn-action-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.btn-action-green:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(16, 185, 129, 0.4);
}

/* Table Card */
.table-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-card-header {
    padding: 1.25rem 1.5rem;
    background: #f9fafb;
    border-bottom: 2px solid #f3f4f6;
}

.table-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    display: flex;
    align-items: center;
}

.table-count {
    color: #6b7280;
    font-weight: 400;
    margin-left: 0.5rem;
}

.table-container {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: #f9fafb;
}

.modern-table th {
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.modern-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.modern-table tbody tr:hover {
    background: #f9fafb;
}

.modern-table td {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    color: #1a1a1a;
}

.table-cell-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.table-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.table-avatar-student {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.table-name {
    font-weight: 600;
    color: #1a1a1a;
}

.table-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.table-email,
.table-phone {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.table-icon {
    color: #9ca3af;
    font-size: 0.75rem;
}

.table-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.table-badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.table-badge-secondary {
    background: #e9d5ff;
    color: #6b21a8;
}

.table-badge-level {
    background: #fef3c7;
    color: #92400e;
}

.table-badge-success {
    background: #d1fae5;
    color: #065f46;
}

.table-badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.table-badge i {
    font-size: 0.5rem;
}

.table-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.table-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 0.375rem;
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.table-action-edit:hover {
    background: #dbeafe;
    color: #2563eb;
}

.table-action-delete:hover {
    background: #fee2e2;
    color: #dc2626;
}

/* Empty State */
.table-empty {
    padding: 3rem 1.5rem;
}

.empty-state {
    text-align: center;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.5rem 0;
}

.empty-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0 0 1.5rem 0;
}

.btn-empty-action {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-empty-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
}

/* Pagination */
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

.pagination-wrapper .pagination a,
.pagination-wrapper .pagination span {
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.pagination-wrapper .pagination a {
    background: white;
    color: #3b82f6;
    border: 1px solid #e5e7eb;
}

.pagination-wrapper .pagination a:hover {
    background: #eff6ff;
    border-color: #3b82f6;
}

.pagination-wrapper .pagination .active span {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
}

/* Alert Styles */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .credentials-grid {
        grid-template-columns: 1fr;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .search-input-wrapper {
        min-width: 100%;
    }
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
    
    .table-container {
        overflow-x: scroll;
    }
    
    .modern-table {
        min-width: 800px;
    }
}
</style>

<script>
function copyPassword(elementId) {
    const passwordElement = document.getElementById(elementId);
    const password = passwordElement.textContent;
    
    navigator.clipboard.writeText(password).then(() => {
        // Show temporary feedback
        const btn = event.target.closest('.copy-password-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#10b981';
        btn.style.color = 'white';
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}

function copyNewCredentials() {
    const credentials = {
        name: '{{ session("new_student_credentials.name") ?? "" }}',
        email: '{{ session("new_student_credentials.email") ?? "" }}',
        phone: '{{ session("new_student_credentials.phone_number") ?? "" }}',
        regNumber: '{{ session("new_student_credentials.registration_number") ?? "" }}',
        password: document.getElementById('new-password').textContent
    };
    
    const text = `Student Login Credentials\n\nName: ${credentials.name}\nEmail: ${credentials.email || 'N/A'}\nPhone: ${credentials.phone}\nRegistration Number: ${credentials.regNumber || 'N/A'}\nPassword: ${credentials.password}`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Credentials copied to clipboard!');
    });
}

function copyUpdatedCredentials() {
    const credentials = {
        name: '{{ session("updated_student_credentials.name") ?? "" }}',
        email: '{{ session("updated_student_credentials.email") ?? "" }}',
        password: document.getElementById('updated-password').textContent
    };
    
    const text = `Updated Student Password\n\nName: ${credentials.name}\nEmail: ${credentials.email || 'N/A'}\nNew Password: ${credentials.password}`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Password copied to clipboard!');
    });
}
</script>
@endsection
