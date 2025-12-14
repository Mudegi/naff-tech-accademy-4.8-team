@extends('frontend.layouts.app')

@section('title', $subject->name . ' Course at Naf Academy - Learn ' . $subject->name . ' Online')

@section('meta')
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $subject->name }} Course - Naf Academy">
    <meta name="description" content="{{ $subject->content }}">
    <meta name="keywords" content="{{ $subject->name }}, online course, education, learning, {{ strtolower($subject->name) }} course, {{ strtolower($subject->name) }} learning, {{ strtolower($subject->name) }} education, Naf Academy">

    <!-- Question-based Meta Descriptions -->
    <meta name="description" content="Want to learn {{ $subject->name }}? Our {{ $subject->duration }} course covers everything from basics to advanced topics. Join Naf Academy now!">
    <meta name="description" content="Looking for the best {{ $subject->name }} course? Discover our comprehensive curriculum with {{ $subject->total_topics }} topics and {{ $subject->total_resources }} learning resources.">
    <meta name="description" content="How to learn {{ $subject->name }}? Start your journey with our structured course featuring hands-on projects and expert guidance.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $subject->name }} Course - Naf Academy">
    <meta property="og:description" content="{{ $subject->content }}">
    <meta property="og:image" content="{{ asset('images/courses/' . $subject->slug . '.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $subject->name }} Course - Naf Academy">
    <meta property="twitter:description" content="{{ $subject->content }}">
    <meta property="twitter:image" content="{{ asset('images/courses/' . $subject->slug . '.jpg') }}">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Course",
        "name": "{{ $subject->name }} Course",
        "description": "{{ $subject->content }}",
        "provider": {
            "@type": "Organization",
            "name": "Naf Academy",
            "sameAs": "{{ url('/') }}"
        },
        "hasCourseInstance": {
            "@type": "CourseInstance",
            "courseMode": "online",
            "duration": "{{ $subject->duration }}",
            "educationalLevel": "Beginner to Advanced",
            "numberOfCredits": "{{ $subject->total_topics }} topics"
        },
        "learningResourceType": "Online Course",
        "educationalLevel": "All Levels",
        "teaches": [
            @foreach($subject->objectives_array as $index => $objective)
                "{{ $objective }}"@if(!$loop->last),@endif
            @endforeach
        ],
        "coursePrerequisites": [
            @foreach($subject->prerequisites_array as $index => $prerequisite)
                "{{ $prerequisite }}"@if(!$loop->last),@endif
            @endforeach
        ],
        "educationalCredentialAwarded": "Course Completion Certificate",
        "assesses": [
            @foreach($subject->assessment_methods_array as $index => $method)
                "{{ $method }}"@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>

    <!-- Additional SEO Meta Tags -->
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta name="robots" content="index, follow">
    <meta name="author" content="Naf Academy">
    <meta name="revisit-after" content="7 days">
    
    <!-- Course-specific Meta Tags -->
    <meta name="course:level" content="All Levels">
    <meta name="course:duration" content="{{ $subject->duration }}">
    <meta name="course:topics" content="{{ $subject->total_topics }}">
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Subjects",
                "item": "{{ route('subjects') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $subject->name }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>
    <meta name="course:resources" content="{{ $subject->total_resources }}">
    <meta name="course:passing-score" content="{{ $subject->passing_score }}%">
@endsection

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                {{ $subject->name }}
            </h1>
            <p class="mt-4 text-lg text-gray-500">
                {{ $subject->description }}
            </p>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Course Overview -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Overview</h2>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Content</h3>
                                <p class="mt-2 text-gray-600">{{ $subject->content }}</p>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Objectives</h3>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    @foreach($subject->objectives_array as $objective)
                                        <li>{{ $objective }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Prerequisites</h3>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    @foreach($subject->prerequisites_array as $prerequisite)
                                        <li>{{ $prerequisite }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Details</h2>
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $subject->duration }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Topics</h3>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $subject->total_topics }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Resources</h3>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $subject->total_resources }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Passing Score</h3>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $subject->passing_score }}%</p>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Learning Outcomes</h3>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    @foreach($subject->learning_outcomes_array as $outcome)
                                        <li>{{ $outcome }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Assessment Methods</h3>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    @foreach($subject->assessment_methods_array as $method)
                                        <li>{{ $method }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topics Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Course Topics</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($subject->topics as $topic)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ $topic->name }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $topic->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500">No topics available for this subject yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 