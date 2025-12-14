@extends('layouts.dashboard')

@section('content')
<div class="upload-resource-page">
    <div class="page-header">
        <h1>Upload Resource</h1>
        <p>Select the class and upload a PDF or PNG resource.</p>
    </div>
    <form method="POST" action="{{ route('teacher.resources.upload.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" class="form-control" required>
                <option value="">Select Class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="term_id">Term</label>
            <select name="term_id" id="term_id" class="form-control" required>
                <option value="">Select Term</option>
                @foreach($terms as $term)
                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="topic_name">Topic Name</label>
            <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="Enter topic name (e.g., Introduction to Algebra)" required>
            <small class="form-text text-muted">If this topic doesn't exist, it will be created automatically</small>
        </div>
        <div class="form-group">
            <label for="resource_file">Resource File (PDF or PNG)</label>
            <input type="file" name="resource_file" id="resource_file" class="form-control" accept=".pdf,.png" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Resource</button>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
