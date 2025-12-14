@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('teacher.projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Project
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Grade Project: {{ $project->title }}</h2>
            <p class="text-gray-600 mt-1">Group: {{ $project->group->name }}</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-red-800 font-semibold mb-2">Please fix the following errors:</h3>
                <ul class="text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.projects.grade.submit', $project) }}" class="space-y-8">
            @csrf

            <!-- Group Members Grading Section -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Grade Group Members</h3>
                
                @forelse($members as $member)
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <!-- Member Header -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                                <span class="font-bold text-blue-800">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $member->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $member->email }}</p>
                            </div>
                        </div>

                        <!-- Member Mark Input -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Numeric Mark (0-100) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100"
                                    name="marks[{{ $member->id }}][numeric_mark]"
                                    value="{{ old('marks.' . $member->id . '.numeric_mark', $existingMarks->get($member->id)?->first()?->numeric_mark) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                >
                                @error('marks.' . $member->id . '.numeric_mark')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Member Remarks (Optional)
                                </label>
                                <input 
                                    type="text"
                                    name="marks[{{ $member->id }}][member_remarks]"
                                    value="{{ old('marks.' . $member->id . '.member_remarks', $existingMarks->get($member->id)?->first()?->remarks) }}"
                                    placeholder="e.g., Excellent contribution"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    maxlength="500"
                                >
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600">No group members found.</p>
                @endforelse
            </div>

            <!-- Project-Level Feedback -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Project Feedback</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        General Remarks (Optional)
                    </label>
                    <textarea 
                        name="remarks"
                        rows="3"
                        placeholder="Add any general remarks about the project submission..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        maxlength="2000"
                    >{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teacher Feedback (Optional)
                    </label>
                    <textarea 
                        name="feedback"
                        rows="4"
                        placeholder="Provide detailed feedback on the project implementation, strengths, areas for improvement, etc."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        maxlength="2000"
                    >{{ old('feedback') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">This feedback will be visible to the group.</p>
                    @error('feedback')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                >
                    Submit Grades
                </button>
                <a 
                    href="{{ route('teacher.projects.show', $project) }}"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
