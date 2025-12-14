@extends('frontend.layouts.app')

@section('title', 'Our Team Members')

@section('content')
<!-- Hero Section -->
<div class="bg-indigo-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
                Our Team Members
            </h1>
            <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                Meet the dedicated professionals who make Naf Academy a leading educational institution.
            </p>
        </div>
    </div>
</div>

<!-- Team Members Section -->
@if($teams->count() > 0)
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($teams as $team)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6 text-center">
                    <!-- Circular Image Container -->
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <img class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg hover:shadow-xl transition-shadow duration-300" 
                                 src="{{ asset('storage/' . $team->image_path) }}" 
                                 alt="{{ $team->name }}"
                                 onerror="this.src='{{ asset('images/team.jpg') }}'">
                            <!-- Optional: Add a subtle ring around the image -->
                            <div class="absolute inset-0 rounded-full border-2 border-indigo-100"></div>
                        </div>
                    </div>
                    
                    <!-- Team Member Info -->
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $team->name }}</h3>
                    <p class="text-indigo-600 font-medium mb-4">{{ $team->position }}</p>
                    
                    <!-- Bio -->
                    @if($team->bio)
                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $team->bio }}</p>
                    @endif
                    
                    <!-- Skills -->
                    @if($team->skills_array && count($team->skills_array) > 0)
                    <div class="flex flex-wrap gap-2 justify-center">
                        @foreach($team->skills_array as $skill)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ trim($skill) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@else
<!-- No Team Members -->
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-1.656-.895-3.107-2.222-3.858M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-1.656.895-3.107 2.222-3.858m0 0a5.002 5.002 0 019.556 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Team Members</h3>
            <p class="mt-2 text-gray-500">There are currently no team members to display.</p>
        </div>
    </div>
</div>
@endif

<!-- Call to Action Section -->
<div class="bg-indigo-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Ready to start your learning journey?</span>
            <span class="block text-indigo-200">Join Naf Academy today.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    View Pricing Plans
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-400">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
