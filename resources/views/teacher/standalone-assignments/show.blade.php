@extends('layouts.dashboard')

@section('title', 'Assignment Details')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-file-alt"></i> Assignment Details</h2>
                <p class="text-muted">{{ $assignment->title }}</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('teacher.standalone-assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Assignments
                </a>
                <a href="{{ route('teacher.standalone-assignments.submissions', $assignment->id) }}" class="btn btn-primary ml-2">
                    <i class="fas fa-users"></i> View Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Assignment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Title:</strong> {{ $assignment->title }}</p>
                            <p><strong>Subject:</strong> {{ $assignment->subject->name ?? 'N/A' }}</p>
                            <p><strong>Class:</strong> {{ $assignment->classRoom->name ?? 'N/A' }}</p>
                            <p><strong>Term:</strong> {{ $assignment->term->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No deadline' }}</p>
                            <p><strong>Total Marks:</strong> {{ $assignment->total_marks }}</p>
                            <p><strong>Status:</strong> 
                                @if($assignment->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Submissions:</strong> {{ $assignment->submissions->count() }}</p>
                        </div>
                    </div>
                    
                    @if($assignment->description)
                        <div class="mt-3">
                            <strong>Description:</strong>
                            <p>{{ $assignment->description }}</p>
                        </div>
                    @endif
                    
                    @if($assignment->instructions)
                        <div class="mt-3">
                            <strong>Instructions:</strong>
                            <p>{{ $assignment->instructions }}</p>
                        </div>
                    @endif
                    
                    @if($assignment->assignment_file_path)
                        <div class="mt-3">
                            <strong>Attachment:</strong>
                            <a href="{{ Storage::url($assignment->assignment_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Submissions</h5>
                </div>
                <div class="card-body">
                    @if($assignment->submissions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($assignment->submissions->take(5) as $submission)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $submission->student->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $submission->submitted_at->diffForHumans() }}</small>
                                        </div>
                                        <div>
                                            @if($submission->grade)
                                                <span class="badge bg-success">{{ $submission->grade }}</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($assignment->submissions->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('teacher.standalone-assignments.submissions', $assignment->id) }}" class="btn btn-sm btn-outline-primary">
                                    View All Submissions
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">No submissions yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection