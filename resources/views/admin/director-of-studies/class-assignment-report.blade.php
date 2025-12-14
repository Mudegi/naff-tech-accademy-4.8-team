@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">Download Class Assignment Report</h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.director-of-studies.dashboard') }}">Director of Studies</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Class Assignment Report</span>
            </div>
        </div>
        <a href="{{ route('admin.director-of-studies.dashboard') }}" class="dashboard-btn dashboard-btn-secondary">Back to Dashboard</a>
    </div>

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6" style="max-width: 600px; margin: 0 auto;">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Generate Assignment Report</h2>
            <p class="text-gray-600 text-sm">
                Download a PDF report containing the best ranked assignments for each student in a selected class.
            </p>
        </div>

        <form action="{{ route('admin.director-of-studies.download-class-assignment-report') }}" method="POST">
            @csrf

            <div class="profile-form-group mb-4">
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Class <span class="text-red-500">*</span>
                </label>
                <select id="class_id" name="class_id" class="profile-input" required>
                    <option value="">Choose a class...</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} - Grade {{ $class->grade_level }} ({{ $class->term }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <div class="dashboard-alert dashboard-alert-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="profile-form-group mb-4">
                <label for="top_count" class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Top Assignments per Student
                </label>
                <select id="top_count" name="top_count" class="profile-input">
                    <option value="3" {{ old('top_count', 3) == 3 ? 'selected' : '' }}>Top 3 Assignments</option>
                    <option value="5" {{ old('top_count') == 5 ? 'selected' : '' }}>Top 5 Assignments</option>
                    <option value="10" {{ old('top_count') == 10 ? 'selected' : '' }}>Top 10 Assignments</option>
                </select>
                <small class="text-gray-500 text-xs mt-1 block">
                    Select how many top-ranked assignments to include for each student
                </small>
                @error('top_count')
                    <div class="dashboard-alert dashboard-alert-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> The report will include only students who have graded assignments. 
                            Students are sorted by their average grade (highest first).
                        </p>
                    </div>
                </div>
            </div>

            <div class="profile-form-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-download mr-2"></i> Download PDF Report
                </button>
                <a href="{{ route('admin.director-of-studies.dashboard') }}" class="dashboard-btn dashboard-btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

