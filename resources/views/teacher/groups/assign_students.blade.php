@extends('layouts.dashboard')

@section('content')
<div class="assign-students-page">
    <div class="page-header">
        <h1>Assign Students to Group: {{ $group->name }}</h1>
        <p>Select students from your assigned classes to add to this group.</p>
    </div>

    <form method="POST" action="{{ route('teacher.groups.assign.students.submit', $group) }}">
        @csrf
        <div class="students-list">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @if($group->members->contains($student->id)) checked disabled @endif></td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->schoolClass->name ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success">Assign Selected Students</button>
        <a href="{{ route('teacher.groups.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
