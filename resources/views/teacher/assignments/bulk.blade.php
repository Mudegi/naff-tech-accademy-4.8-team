@extends('layouts.dashboard')

@section('content')
<div class="bulk-grading-page">
    <div class="page-header">
        <h1>Bulk Grade Assignments</h1>
        <p>Select multiple assignments and enter grades for them.</p>
    </div>

    <form method="POST" action="{{ route('teacher.assignments.bulk.submit') }}">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Student</th>
                    <th>Resource</th>
                    <th>Submitted At</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td><input type="checkbox" name="assignments[]" value="{{ $assignment->id }}"></td>
                    <td>{{ $assignment->student->name }}</td>
                    <td>{{ $assignment->resource->title }}</td>
                    <td>{{ $assignment->submitted_at }}</td>
                    <td><input type="number" name="grades[{{ $assignment->id }}]" min="0" max="100" class="form-control"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Apply Grades</button>
        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
