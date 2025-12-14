@extends('layouts.dashboard')

@section('title', 'Parent-Student Links')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-link me-2"></i>Parent-Student Links
            </h1>
            <p class="text-muted mb-0">Manage relationships between parent and student accounts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.parent-student.bulk-import') }}" class="btn btn-success">
                <i class="fas fa-file-upload me-1"></i>Bulk Import
            </a>
            <a href="{{ route('admin.parent-student.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Link Parent to Student
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Links</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalLinks) }}</div>
                        </div>
                        <i class="fas fa-link fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Parents</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalParents) }}</div>
                        </div>
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Linked Students</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalStudents) }}</div>
                        </div>
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.parent-student.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by parent name, student name, or phone number..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Per Page</label>
                    <select name="per_page" class="form-select">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 || !request('per_page') ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Links Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                All Parent-Student Links ({{ $links->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($links->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Parent</th>
                                <th>Student</th>
                                <th>Relationship</th>
                                <th>Primary Contact</th>
                                <th>Notifications</th>
                                <th>Linked On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($links as $link)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $link->parent_name }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $link->parent_phone ?: 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $link->student_name }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $link->student_phone ?: 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ ucfirst($link->relationship) }}
                                    </span>
                                </td>
                                <td>
                                    @if($link->is_primary)
                                        <i class="fas fa-star text-warning" title="Primary Contact"></i>
                                        <span class="text-success">Yes</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($link->receive_notifications)
                                        <i class="fas fa-bell text-success"></i>
                                        <span class="text-success">Enabled</span>
                                    @else
                                        <i class="fas fa-bell-slash text-muted"></i>
                                        <span class="text-muted">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($link->created_at)->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $link->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.parent-student.destroy', $link->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to remove this link?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $link->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.parent-student.update', $link->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Link Settings</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Relationship Type</label>
                                                            <select name="relationship" class="form-select" required>
                                                                <option value="parent" {{ $link->relationship == 'parent' ? 'selected' : '' }}>Parent</option>
                                                                <option value="guardian" {{ $link->relationship == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                                                <option value="sponsor" {{ $link->relationship == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input type="checkbox" name="is_primary" class="form-check-input" 
                                                                   id="isPrimary{{ $link->id }}" {{ $link->is_primary ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="isPrimary{{ $link->id }}">
                                                                Primary Contact
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" name="receive_notifications" class="form-check-input" 
                                                                   id="notifications{{ $link->id }}" {{ $link->receive_notifications ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="notifications{{ $link->id }}">
                                                                Receive Notifications
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $links->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-link fa-3x text-muted mb-3"></i>
                    <h5>No Parent-Student Links Found</h5>
                    <p class="text-muted">
                        @if(request('search'))
                            No links match your search criteria.
                        @else
                            Start by creating a link between a parent and student account.
                        @endif
                    </p>
                    <a href="{{ route('admin.parent-student.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create First Link
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.table td {
    vertical-align: middle;
}
</style>
@endsection
