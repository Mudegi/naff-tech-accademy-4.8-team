@extends('layouts.student-dashboard')

@section('title', 'Debug My Videos')

@section('content')
<div style="padding: 20px; font-family: monospace;">
    <h1 style="color: #d32f2f;">🔍 DEBUG PAGE - My Videos Data</h1>

    <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h2>Session Info:</h2>
        <p><strong>User ID:</strong> {{ auth()->id() }}</p>
        <p><strong>User Type:</strong> {{ session('user_type') }}</p>
        <p><strong>School Student:</strong> {{ isset($isSchoolStudent) ? ($isSchoolStudent ? 'YES' : 'NO') : 'NOT SET' }}</p>
    </div>

    <div style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h2>Resources Data:</h2>
        <p><strong>Count on this page:</strong> {{ $resources->count() }}</p>
        <p><strong>Total resources:</strong> {{ $resources->total() }}</p>
        <p><strong>Current page:</strong> {{ $resources->currentPage() }}</p>
        <p><strong>Per page:</strong> {{ $resources->perPage() }}</p>
        <p><strong>Has pages:</strong> {{ $resources->hasPages() ? 'YES' : 'NO' }}</p>
    </div>

    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h2>Applied Filters:</h2>
        <p><strong>Subject ID:</strong> {{ request('subject_id') ?: 'none' }}</p>
        <p><strong>Topic ID:</strong> {{ request('topic_id') ?: 'none' }}</p>
        <p><strong>Term ID:</strong> {{ request('term_id') ?: 'none' }}</p>
        <p><strong>Search:</strong> "{{ request('search') ?: 'none' }}"</p>
    </div>

    <div style="background: #fce4ec; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h2>Filter Options Available:</h2>
        <p><strong>Subjects:</strong> {{ $subjects->count() }} available</p>
        <p><strong>Topics:</strong> {{ $topics->count() }} available</p>
        <p><strong>Terms:</strong> {{ $terms->count() }} available</p>
    </div>

    @if($resources->count() > 0)
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h2>All Resources ({{ $resources->count() }}):</h2>
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($resources as $index => $resource)
                    <div style="border: 1px solid #ddd; padding: 10px; margin: 5px 0; background: white;">
                        <strong>#{{ $index + 1 }} - ID: {{ $resource->id }}</strong><br>
                        <strong>Title:</strong> {{ $resource->title }}<br>
                        <strong>Grade Level:</strong> {{ $resource->grade_level }}<br>
                        <strong>Subject:</strong> {{ $resource->subject ? $resource->subject->name : 'N/A' }}<br>
                        <strong>School ID:</strong> {{ $resource->school_id ?: 'null (pivot assigned)' }}<br>
                        <strong>Has Drive Link:</strong> {{ !empty($resource->google_drive_link) ? 'YES' : 'NO' }}<br>
                        <strong>Created:</strong> {{ $resource->created_at->diffForHumans() }}
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div style="background: #ffebee; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #d32f2f;">❌ NO RESOURCES FOUND</h2>
            <p>This means either:</p>
            <ul>
                <li>The query returned no results</li>
                <li>All resources were filtered out</li>
                <li>The student is not recognized as a school student</li>
            </ul>
        </div>
    @endif

    <div style="background: #f3e5f5; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h2>Actions:</h2>
        <a href="{{ route('student.my-videos') }}" style="background: #2196f3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Back to My Videos</a>
        <a href="{{ route('student.my-videos') }}?debug=1" style="background: #ff9800; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Refresh Debug</a>
    </div>
</div>
@endsection