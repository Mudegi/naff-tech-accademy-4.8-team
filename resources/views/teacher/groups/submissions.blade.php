@extends('layouts.dashboard')

@section('content')
<div class="submissions-page">
    <div class="page-header">
        <a href="{{ route('teacher.groups.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
        <h1>{{ $group->name }} - Submissions & Grading</h1>
    </div>

    <!-- Upload Group Submission Section -->
    <div class="section">
        <h2>Upload Group Submission</h2>
        <form action="{{ route('teacher.groups.submit', $group) }}" method="POST" enctype="multipart/form-data" class="upload-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="resource_id">Resource ID</label>
                    <input type="number" id="resource_id" name="resource_id" class="form-control @error('resource_id') is-invalid @enderror" placeholder="Resource ID" required>
                    @error('resource_id') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="file">Upload File</label>
                    <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                    @error('file') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Submissions List -->
    <div class="section">
        <h2>Member Submissions</h2>
        @if($assignments->isEmpty())
        <p class="empty-text">No submissions yet.</p>
        @else
        <div class="table-responsive">
            <table class="submissions-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Resource</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                    <tr>
                        <td>{{ $a->student->name ?? 'N/A' }}</td>
                        <td>{{ $a->resource->title ?? 'N/A' }}</td>
                        <td><span class="status status-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                        <td>{{ $a->submitted_at ? $a->submitted_at->format('M d, Y H:i') : '--' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Grade Group Section -->
    <div class="section">
        <h2>Grade Group</h2>
        <form action="{{ route('teacher.groups.grade', $group) }}" method="POST" class="grade-form">
            @csrf
            <div class="form-group">
                <label for="subject_name">Subject Name (optional)</label>
                <input type="text" id="subject_name" name="subject_name" class="form-control" placeholder="e.g., Mathematics">
            </div>
            <div class="form-group">
                <label for="paper_name">Paper Name (optional)</label>
                <input type="text" id="paper_name" name="paper_name" class="form-control" placeholder="e.g., Assignment 1">
            </div>

            <div class="marks-grid">
                @forelse($group->approvedMembers()->get() as $member)
                <div class="member-mark">
                    <div class="member-name">{{ $member->name }}</div>
                    <input type="hidden" name="marks[][student_id]" value="{{ $member->id }}">
                    <div class="form-group">
                        <label>Mark (%)</label>
                        <input type="number" name="marks[][numeric_mark]" class="form-control" placeholder="0-100" min="0" max="100" required>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="marks[][remarks]" class="form-control" placeholder="Optional feedback" rows="3"></textarea>
                    </div>
                </div>
                @empty
                <p class="empty-text">No approved members in this group yet.</p>
                @endforelse
            </div>

            @if($group->approvedMembers()->count() > 0)
            <button type="submit" class="btn btn-success">Save Marks</button>
            @endif
        </form>
    </div>
</div>

<style>
    .submissions-page { padding: 20px; max-width: 1200px; margin: 0 auto; }
    .page-header { margin-bottom: 30px; }
    .back-link { display: inline-block; margin-bottom: 10px; color: #007bff; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .page-header h1 { font-size: 24px; margin: 10px 0 0 0; }

    .section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .section h2 { font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }

    .upload-form { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
    .upload-form .form-group { flex: 1; min-width: 200px; }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    .form-control:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); }
    .form-control.is-invalid { border-color: #dc3545; }
    .error { color: #dc3545; font-size: 13px; display: block; margin-top: 4px; }

    .table-responsive { overflow-x: auto; }
    .submissions-table { width: 100%; border-collapse: collapse; }
    .submissions-table th { background: #f8f9fa; padding: 10px; text-align: left; border: 1px solid #ddd; font-weight: 600; }
    .submissions-table td { padding: 10px; border: 1px solid #ddd; }
    .submissions-table tbody tr:hover { background: #f8f9fa; }

    .status { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 500; }
    .status-submitted { background: #d4edda; color: #155724; }
    .status-assigned { background: #d1ecf1; color: #0c5460; }
    .status-reviewed { background: #fff3cd; color: #856404; }

    .marks-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
    .member-mark { background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff; }
    .member-name { font-weight: 600; margin-bottom: 12px; color: #333; }
    .member-mark .form-group { margin-bottom: 12px; }

    .empty-text { color: #666; font-style: italic; }

    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; }
    .btn-primary { background: #007bff; color: white; }
    .btn-primary:hover { background: #0056b3; }
    .btn-success { background: #28a745; color: white; }
    .btn-success:hover { background: #218838; }
</style>
@endsection
