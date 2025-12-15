@extends('layouts.dashboard')

@section('title', 'Student-Parent Accounts')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">
            <i class="fas fa-users me-2"></i>Student-Parent Accounts
        </h1>
        <div class="breadcrumbs">
            <span>Admin</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Student-Parent Accounts</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4">
        <form action="{{ route('admin.users.generate-missing-parent-accounts') }}" method="POST" 
              onsubmit="return confirm('This will create parent accounts for all students who don\'t have them. Continue?');">
            @csrf
            @if(request('school_id'))
                <input type="hidden" name="school_id" value="{{ request('school_id') }}">
            @endif
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-magic"></i> Generate Missing Parents
            </button>
        </form>
        <a href="{{ route('admin.users.export-student-parent-list') }}{{ request('school_id') ? '?school_id='.request('school_id') : '' }}" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-download"></i> Export to CSV
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('generation_errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Generation Warnings</h5>
            <ul class="mb-0">
                @foreach(session('generation_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Box -->
    <div class="dashboard-info-box mb-4">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <strong>How It Works:</strong> Parent accounts are automatically created when students are added. 
            Default password is the student's phone number (or "parent123"). 
            Email format: <code>studentemail_parent@domain.com</code>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="dashboard-table-container mb-4">
        <div class="dashboard-table-header">
            <h3><i class="fas fa-search me-2"></i>Search & Filter</h3>
        </div>
        <div class="dashboard-table-body">
            <form action="{{ route('admin.users.student-parent-list') }}" method="GET" class="row g-3">
                @if(!Auth::user()->school_id && count($schools) > 0)
                <div class="col-md-4">
                    <select name="school_id" class="dashboard-select">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="search" class="dashboard-input" 
                           placeholder="Search by student name, email, or phone..." 
                           value="{{ request('search') }}">
                </div>
                @else
                <div class="col-md-8">
                    <input type="text" name="search" class="dashboard-input" 
                           placeholder="Search by student name, email, or phone..." 
                           value="{{ request('search') }}">
                </div>
                @endif
                <div class="col-md-2">
                    <select name="per_page" class="dashboard-input">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 || !request('per_page') ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 per page</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="dashboard-btn dashboard-btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student-Parent Table -->
    <div class="dashboard-table-container">
        <div class="dashboard-table-header">
            <h3>Student-Parent Accounts ({{ $students->total() }} Total)</h3>
        </div>
        <div class="dashboard-table-scroll">
            @if($students->count() > 0)
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Info</th>
                            <th>Parent Info</th>
                            <th>Access Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @if($student->parentLinks->count() > 0)
                                @foreach($student->parentLinks as $index => $link)
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ $student->parentLinks->count() }}">
                                                <span class="badge badge-secondary">{{ $student->id }}</span>
                                            </td>
                                            <td rowspan="{{ $student->parentLinks->count() }}" class="student-info-cell">
                                                <div class="student-name">
                                                    <strong>{{ $student->name }}</strong>
                                                    @if($student->school)
                                                        <br><small class="text-muted">{{ $student->school->name }}</small>
                                                    @endif
                                                </div>
                                                <div class="student-contact">
                                                    <small>
                                                        @if($student->email)<i class="fas fa-envelope"></i> {{ $student->email }}@endif
                                                        @if($student->phone_number)<br><i class="fas fa-phone"></i> {{ $student->phone_number }}@endif
                                                    </small>
                                                </div>
                                            </td>
                                        @endif
                                        <td class="parent-info-cell">
                                            <div class="parent-name">
                                                <strong>{{ $link->parent_name }}</strong>
                                                <br><span class="badge badge-info">{{ ucfirst($link->relationship) }}</span>
                                            </div>
                                            <div class="parent-contact">
                                                <small>
                                                    @if($link->parent_email)<i class="fas fa-envelope"></i> {{ $link->parent_email }}@endif
                                                    @if($link->parent_phone)<br><i class="fas fa-phone"></i> {{ $link->parent_phone }}@endif
                                                </small>
                                            </div>
                                        </td>
                                        @if($index === 0)
                                            <td rowspan="{{ $student->parentLinks->count() }}" class="access-details-cell">
                                                <div class="password-section">
                                                    <small><strong>Password:</strong></small><br>
                                                    <code class="password-code">{{ $student->phone_number ?: 'parent123' }}</code>
                                                    <button class="btn-icon copy-btn" 
                                                            onclick="copyPassword('{{ $student->phone_number ?: 'parent123' }}')"
                                                            title="Copy password">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td rowspan="{{ $student->parentLinks->count() }}">
                                                <span class="status-badge status-active">✓ Linked</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><span class="badge badge-secondary">{{ $student->id }}</span></td>
                                    <td class="student-info-cell">
                                        <div class="student-name">
                                            <strong>{{ $student->name }}</strong>
                                            @if($student->school)
                                                <br><small class="text-muted">{{ $student->school->name }}</small>
                                            @endif
                                        </div>
                                        <div class="student-contact">
                                            <small>
                                                @if($student->email)<i class="fas fa-envelope"></i> {{ $student->email }}@endif
                                                @if($student->phone_number)<br><i class="fas fa-phone"></i> {{ $student->phone_number }}@endif
                                            </small>
                                        </div>
                                    </td>
                                    <td colspan="2" class="text-center parent-missing">
                                        <em>No parent account linked</em>
                                    </td>
                                    <td><span class="status-badge status-warning">⚠ Missing</span></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <table class="dashboard-table">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 60px;">
                                <i class="fas fa-users" style="font-size: 48px; color: #ccc;"></i>
                                <p style="color: #999; margin-top: 16px; font-size: 16px;">No students found</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <!-- Pagination -->
            @if($students->total() > 0)
                <div class="dashboard-table-footer">
                    <div class="pagination-info">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </div>
                    <div class="pagination-links">
                        {{ $students->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyPassword(password) {
    const input = document.createElement('input');
    input.value = password;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    
    alert('Password copied: ' + password);
}
</script>

<style>
/* Compact Student-Parent Table Styles */
.student-info-cell,
.parent-info-cell {
    max-width: 200px;
}

.student-name,
.parent-name {
    margin-bottom: 4px;
}

.student-contact,
.parent-contact {
    color: #6b7280;
    font-size: 0.875rem;
}

.student-contact i,
.parent-contact i {
    width: 14px;
    color: #9ca3af;
}

.parent-missing {
    color: #6b7280;
    font-style: italic;
}

.access-details-cell {
    max-width: 150px;
}

.password-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.password-code {
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    font-family: monospace;
    color: #374151;
}

.copy-btn {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.copy-btn:hover {
    background: #f3f4f6;
    color: #2563eb;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-active {
    background: #dcfce7;
    color: #166534;
}

.status-warning {
    background: #fef3c7;
    color: #92400e;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .student-info-cell,
    .parent-info-cell,
    .access-details-cell {
        max-width: 150px;
    }
    
    .student-contact,
    .parent-contact {
        font-size: 0.8rem;
    }
    
    .password-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}

/* Table container improvements */
.dashboard-table-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 24px;
}

.dashboard-table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    min-width: 800px; /* Ensure minimum width for readability */
}

.dashboard-table th {
    background: #f9fafb;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

.dashboard-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

/* Badge styles */
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-secondary {
    background: #f3f4f6;
    color: #6b7280;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}
</style>

@endsection
