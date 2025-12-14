@extends('frontend.layouts.app')

@section('title', $welcomeLinks->meta_title ?? 'About Us - Naf Academy')

@section('meta')
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $welcomeLinks->meta_title ?? 'About Us - Naf Academy' }}">
    <meta name="description" content="{{ $welcomeLinks->meta_description ?? 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.' }}">
    <meta name="keywords" content="{{ $welcomeLinks->meta_keywords ?? 'about Naf Academy, Uganda education, tech education, online learning, academic resources' }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $welcomeLinks->og_title ?? 'About Us - Naf Academy' }}">
    <meta property="og:description" content="{{ $welcomeLinks->og_description ?? 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.' }}">
    <meta property="og:image" content="{{ asset($welcomeLinks->og_image ?? 'images/og-image.jpg') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $welcomeLinks->twitter_title ?? 'About Us - Naf Academy' }}">
    <meta property="twitter:description" content="{{ $welcomeLinks->twitter_description ?? 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.' }}">
    <meta property="twitter:image" content="{{ asset($welcomeLinks->twitter_image ?? 'images/og-image.jpg') }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/about') }}">
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@@type": "ListItem",
                "position": 2,
                "name": "About Us",
                "item": "{{ url('/about') }}"
            }
        ]
    }
    </script>
@endsection

@section('content')
<!-- Hero Section -->
<div class="relative bg-indigo-800">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover" src="{{ $welcomeLinks->getImageUrl('about_image') ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80' }}" alt="About Naf Academy">
        <div class="absolute inset-0 bg-indigo-800 mix-blend-multiply"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                {{ $welcomeLinks->about_title ?? 'About Naf Academy' }}
            </h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl mx-auto">
                {{ $welcomeLinks->about_description ?? 'Empowering the next generation of tech leaders through innovative education and hands-on learning experiences.' }}
            </p>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Our Mission</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                {{ $welcomeLinks->mission_title ?? 'Empowering Through Education' }}
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                {{ $welcomeLinks->mission_description ?? 'We are committed to providing high-quality education that prepares students for success in the digital age.' }}
            </p>
        </div>

        <div class="mt-10">
            <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                <!-- Vision -->
                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="ml-16">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $welcomeLinks->vision_title ?? 'Our Vision' }}</h3>
                        <p class="mt-2 text-base text-gray-500">
                            {{ $welcomeLinks->vision_description ?? 'To be the leading provider of quality education in Uganda, empowering students with the skills and knowledge needed for success in the digital age.' }}
                        </p>
                    </div>
                </div>

                <!-- Values -->
                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div class="ml-16">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $welcomeLinks->values_title ?? 'Our Values' }}</h3>
                        <p class="mt-2 text-base text-gray-500">
                            {{ $welcomeLinks->values_description ?? 'Excellence, Innovation, Integrity, and Student Success are the core values that guide everything we do.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Section -->
@if($teamMembers->isNotEmpty())
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Our Team</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                {{ $welcomeLinks->team_title ?? 'Meet Our Expert Instructors' }}
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                {{ $welcomeLinks->team_description ?? 'Our team of experienced educators is dedicated to providing the best learning experience for our students.' }}
            </p>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($teamMembers as $member)
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-200"></div>
                    <div class="relative bg-white p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                                <img class="h-12 w-12 rounded-full" src="{{ $member->image }}" alt="{{ $member->name }}">
                        </div>
                        <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $member->name }}</h3>
                                <p class="text-gray-500">{{ $member->position }}</p>
                            </div>
                        </div>
                        @if($member->bio)
                        <p class="mt-4 text-sm text-gray-500">{{ $member->bio }}</p>
                        @endif
                        <div class="mt-4 flex space-x-4">
                            @if($member->email)
                            <a href="mailto:{{ $member->email }}" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Email</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </a>
                            @endif
                            @if($member->linkedin)
                            <a href="{{ $member->linkedin }}" class="text-gray-400 hover:text-gray-500" target="_blank">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.779 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" />
                                </svg>
                            </a>
                            @endif
                            @if($member->twitter)
                            <a href="{{ $member->twitter }}" class="text-gray-400 hover:text-gray-500" target="_blank">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- CTA Section -->
<div class="bg-indigo-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">{{ $welcomeLinks->cta_title ?? 'Ready to start your journey?' }}</span>
            <span class="block text-indigo-200">{{ $welcomeLinks->cta_description ?? 'Join Naf Academy today.' }}</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-full text-indigo-600 bg-white hover:bg-indigo-50 transition duration-150">
                    Get started
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-full text-white bg-indigo-500 hover:bg-indigo-600 transition duration-150">
                    Contact us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection