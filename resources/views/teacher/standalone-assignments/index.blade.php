@extends('layouts.dashboard')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm">
                <div>
                    <h1 class="h3 mb-1 text-primary">My Assignments</h1>
                    <p class="text-muted mb-0">Manage and track your standalone assignments</p>
                </div>
                <a href="{{ route('teacher.standalone-assignments.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Create New Assignment
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assignments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">All Assignments</h5>
                </div>
                <div class="card-body p-0">
                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="border-0 fw-semibold">Title & Details</th>
                                        <th class="border-0 fw-semibold">Subject</th>
                                        <th class="border-0 fw-semibold">Class</th>
                                        <th class="border-0 fw-semibold">Due Date</th>
                                        <th class="border-0 fw-semibold">Submissions</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong class="text-dark">{{ $assignment->title }}</strong>
                                                    @if($assignment->assignment_file_path)
                                                        <small class="text-muted">Attachment included</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">{{ $assignment->subject->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info fs-6">{{ $assignment->classRoom->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($assignment->due_date)
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $assignment->due_date->format('M d, Y') }}</span>
                                                        @if($assignment->isOverdue())
                                                            <small class="text-danger">Overdue</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">No deadline</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.standalone-assignments.submissions', $assignment->id) }}" 
                                                   class="text-decoration-none">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success me-2">{{ $assignment->submissions->count() }}</span>
                                                        <small class="text-muted">submitted</small>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                @if($assignment->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('teacher.standalone-assignments.show', $assignment->id) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.standalone-assignments.submissions', $assignment->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View Submissions">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.standalone-assignments.edit', $assignment->id) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('teacher.standalone-assignments.destroy', $assignment->id) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                        
                        <!-- Pagination -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-center">
                                {{ $assignments->links() }}
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <h4 class="text-muted mb-3">No Assignments Created Yet</h4>
                            <p class="text-muted mb-4">Start by creating your first standalone assignment to engage your students.</p>
                            <a href="{{ route('teacher.standalone-assignments.create') }}" class="btn btn-primary btn-lg">
                                Create Your First Assignment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
