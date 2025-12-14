@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Edit Resource</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="{{ route('admin.school.resources.index') }}">Resources</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Edit</span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <form action="{{ route('admin.school.resources.update', $resource->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $resource->title) }}"
                               required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade Level *</label>
                        <select id="grade_level" 
                                name="grade_level" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Grade Level</option>
                            <option value="O Level" {{ old('grade_level', $resource->grade_level) == 'O Level' ? 'selected' : '' }}>O Level</option>
                            <option value="A Level" {{ old('grade_level', $resource->grade_level) == 'A Level' ? 'selected' : '' }}>A Level</option>
                        </select>
                        @error('grade_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $resource->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Relationships -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject *</label>
                        <select id="subject_id" 
                                name="subject_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $resource->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="topic_id" class="block text-sm font-medium text-gray-700">Topic *</label>
                        <select id="topic_id" 
                                name="topic_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Topic</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $resource->topic_id) == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="term_id" class="block text-sm font-medium text-gray-700">Term *</label>
                        <select id="term_id" 
                                name="term_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id', $resource->term_id) == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class_id" 
                                name="class_id" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Class (Optional)</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $resource->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700">Assign to Teacher</label>
                        <select id="teacher_id" 
                                name="teacher_id" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Teacher (Optional)</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $resource->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} 
                                    @if($teacher->department)
                                        - {{ $teacher->department->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Assign this resource to a teacher from your school</p>
                        @error('teacher_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Video and Drive Links -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL (YouTube)</label>
                        <input type="url" 
                               id="video_url" 
                               name="video_url" 
                               value="{{ old('video_url', $resource->video_url) }}"
                               placeholder="https://youtube.com/watch?v=..."
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="google_drive_link" class="block text-sm font-medium text-gray-700">Google Drive Link</label>
                        <input type="url" 
                               id="google_drive_link" 
                               name="google_drive_link" 
                               value="{{ old('google_drive_link', $resource->google_drive_link) }}"
                               placeholder="https://drive.google.com/file/d/..."
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Required for students to access the video</p>
                        @error('google_drive_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Files -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes_file" class="block text-sm font-medium text-gray-700">Notes File</label>
                        <input type="file" 
                               id="notes_file" 
                               name="notes_file" 
                               accept=".pdf,.ppt,.pptx,.xls,.xlsx"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">PDF, PPT, PPTX, XLS, XLSX (Max 10MB)</p>
                        @if($resource->notes_file_path)
                            <p class="mt-1 text-sm text-green-600">Current file: <a href="{{ asset('storage/' . $resource->notes_file_path) }}" target="_blank" class="underline">View</a></p>
                        @endif
                        @error('notes_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assessment_tests" class="block text-sm font-medium text-gray-700">Assessment Tests</label>
                        <input type="file" 
                               id="assessment_tests" 
                               name="assessment_tests" 
                               accept=".pdf"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">PDF only (Max 10MB)</p>
                        @if($resource->assessment_tests_path)
                            <p class="mt-1 text-sm text-green-600">Current file: <a href="{{ asset('storage/' . $resource->assessment_tests_path) }}" target="_blank" class="underline">View</a></p>
                        @endif
                        @error('assessment_tests')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tags and Learning Outcomes -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           value="{{ old('tags', $resource->tags) }}"
                           placeholder="tag1, tag2, tag3"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Comma-separated tags for searching</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="learning_outcomes" class="block text-sm font-medium text-gray-700">Learning Outcomes</label>
                    <textarea id="learning_outcomes" 
                              name="learning_outcomes" 
                              rows="4"
                              placeholder="Enter learning outcomes, one per line or separated by commas"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('learning_outcomes', $resource->learning_outcomes) }}</textarea>
                    @error('learning_outcomes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $resource->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="visible_as_sample" 
                               value="1"
                               {{ old('visible_as_sample', $resource->visible_as_sample) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Visible as Sample</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.school.resources.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Resource
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

