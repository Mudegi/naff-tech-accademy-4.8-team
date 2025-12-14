@extends('layouts.dashboard')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-clipboard-list"></i> My Assignments</h2>
                <p class="text-muted">Manage all your assignments</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('teacher.standalone-assignments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Assignment
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($assignments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Due Date</th>
                                <th>Submissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->title }}</strong>
                                        @if($assignment->assignment_file_path)
                                            <br><small class="text-muted">
                                                <i class="fas fa-paperclip"></i> Has attachment
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->classRoom->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($assignment->due_date)
                                            {{ $assignment->due_date->format('M d, Y') }}
                                            @if($assignment->isOverdue())
                                                <br><span class="badge bg-danger">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No deadline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $assignment->submissions->count() }} submitted</span>
                                    </td>
                                    <td>
                                        @if($assignment->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.standalone-assignments.show', $assignment->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.standalone-assignments.submissions', $assignment->id) }}" 
                                           class="btn btn-sm btn-primary" title="View Submissions">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="{{ route('teacher.standalone-assignments.edit', $assignment->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('teacher.standalone-assignments.destroy', $assignment->id) }}" 
                                              method="POST" style="display:inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $assignments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #ddd;"></i>
                    <h4 class="mt-3">No Assignments Yet</h4>
                    <p class="text-muted">Create your first assignment to get started!</p>
                    <a href="{{ route('teacher.standalone-assignments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
