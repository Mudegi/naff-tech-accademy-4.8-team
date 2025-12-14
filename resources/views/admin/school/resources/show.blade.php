@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Resource Details</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="{{ route('admin.school.resources.index') }}">Resources</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">{{ $resource->title }}</span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $resource->title }}</h2>
                <div class="flex items-center space-x-2 mt-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                        {{ $resource->grade_level }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $resource->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $resource->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.school.resources.edit', $resource->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>

        @if($resource->description)
            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $resource->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Subject</h3>
                <p class="text-gray-900">{{ $resource->subject->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Topic</h3>
                <p class="text-gray-900">{{ $resource->topic->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Term</h3>
                <p class="text-gray-900">{{ $resource->term->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Class</h3>
                <p class="text-gray-900">{{ $resource->classRoom->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Assigned Teacher</h3>
                <p class="text-gray-900">{{ $resource->teacher->name ?? 'Not Assigned' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Created By</h3>
                <p class="text-gray-900">{{ $resource->creator->name ?? 'N/A' }}</p>
            </div>
        </div>

        @if($resource->tags)
            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $resource->tags) as $tag)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                            {{ trim($tag) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($resource->learning_outcomes)
            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Learning Outcomes</h3>
                <p class="text-gray-600 whitespace-pre-line">{{ $resource->learning_outcomes }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            @if($resource->video_url)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Video URL</h3>
                    <a href="{{ $resource->video_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        {{ $resource->video_url }}
                    </a>
                </div>
            @endif

            @if($resource->google_drive_link)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Google Drive Link</h3>
                    <a href="{{ $resource->google_drive_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        {{ $resource->google_drive_link }}
                    </a>
                </div>
            @endif

            @if($resource->notes_file_path)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Notes File</h3>
                    <a href="{{ asset('storage/' . $resource->notes_file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-file mr-1"></i> Download Notes
                    </a>
                </div>
            @endif

            @if($resource->assessment_tests_path)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Assessment Tests</h3>
                    <a href="{{ asset('storage/' . $resource->assessment_tests_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-file-pdf mr-1"></i> Download Assessment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

