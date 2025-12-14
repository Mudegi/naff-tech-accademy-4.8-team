@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('teacher.projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Project
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Project Grading Feedback</h2>
            <p class="text-gray-600 mt-1">{{ $project->title }} - {{ $project->group->name }}</p>
        </div>

        <!-- Project-Level Feedback -->
        @if($project->implementation)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">Project Feedback</h3>
                
                @if($project->implementation->feedback)
                    <div class="bg-white p-4 rounded border border-blue-100 mb-4">
                        <p class="text-gray-700">{{ $project->implementation->feedback }}</p>
                    </div>
                @else
                    <p class="text-gray-600">No feedback provided.</p>
                @endif

                @if($project->implementation->graded_at)
                    <div class="text-sm text-gray-600 mt-4 pt-4 border-t border-blue-100">
                        <p>Graded on: {{ $project->implementation->graded_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Member Marks -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Member Marks</h3>

            @forelse($marks as $userId => $memberMarks)
                @php
                    $firstMark = $memberMarks->first();
                    $member = $firstMark->user;
                    
                    // Determine if student is Form 5-6 (uses letter grades) or Form 1-4 (uses percentage marks)
                    $student = $member->student;
                    $studentClass = $student ? $student->schoolClass : null;
                    $classLevel = $studentClass ? $studentClass->level : null;
                    $formNumber = null;
                    
                    if ($classLevel && preg_match('/\d+/', strtolower($classLevel), $matches)) {
                        $formNumber = intval($matches[0]);
                    }
                    
                    $isUpperSecondary = $formNumber && $formNumber >= 5;
                @endphp
                
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <!-- Member Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                                <span class="font-bold text-green-800">{{ substr($member->name ?? 'N/A', 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $member->name ?? 'Unknown' }}</h4>
                                <p class="text-sm text-gray-600">{{ $member->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($isUpperSecondary && $firstMark->grade)
                                <!-- Form 5-6: Display letter grade -->
                                <p class="text-3xl font-bold text-blue-600">{{ $firstMark->grade }}</p>
                                <p class="text-sm text-gray-600">
                                    ({{ number_format($firstMark->numeric_mark, 0) }}%)
                                </p>
                            @else
                                <!-- Form 1-4: Display percentage only -->
                                <p class="text-3xl font-bold text-blue-600">{{ number_format($firstMark->numeric_mark, 0) }}%</p>
                            @endif
                        </div>
                    </div>

                    <!-- Member Remarks -->
                    @if($firstMark->remarks)
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Remarks:</span> {{ $firstMark->remarks }}
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-600">No marks recorded yet.</p>
            @endforelse
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8">
            <a 
                href="{{ route('teacher.projects.show', $project) }}"
                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition"
            >
                Back to Project
            </a>
            <a 
                href="{{ route('teacher.projects.grade.form', $project) }}"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
            >
                Edit Grades
            </a>
        </div>
    </div>
</div>
@endsection
