@extends('layouts.dashboard')

@section('title', 'Assignment Submissions')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-users"></i> Assignment Submissions</h2>
                <p class="text-muted">{{ $assignment->title }}</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('teacher.standalone-assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Assignments
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>
                                        <strong>{{ $submission->student->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $submission->student->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $submission->submitted_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success">Submitted</span>
                                    </td>
                                    <td>
                                        @if($submission->grade)
                                            {{ $submission->grade }}/{{ $assignment->total_marks }}
                                        @else
                                            <span class="text-muted">Not graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.standalone-assignments.view-submission', [$assignment->id, $submission->id]) }}" 
                                           class="btn btn-sm btn-info" title="View Submission">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $submissions->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4>No submissions yet</h4>
                    <p class="text-muted">Students haven't submitted this assignment yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection