@extends('frontend.layouts.app')

@section('title', 'Contact Us')

@section('meta')
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $contactPage->meta_title }}">
    <meta name="description" content="{{ $contactPage->meta_description }}">
    <meta name="keywords" content="{{ $contactPage->meta_keywords }}">
    <meta name="author" content="{{ $contactPage->meta_author }}">
    <meta name="robots" content="{{ $contactPage->meta_robots }}">
    <meta name="language" content="{{ $contactPage->meta_language }}">
    <meta name="revisit-after" content="{{ $contactPage->meta_revisit_after }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $contactPage->og_title }}">
    <meta property="og:description" content="{{ $contactPage->og_description }}">
    <meta property="og:image" content="{{ asset($contactPage->og_image) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $contactPage->twitter_title }}">
    <meta property="twitter:description" content="{{ $contactPage->twitter_description }}">
    <meta property="twitter:image" content="{{ asset($contactPage->twitter_image) }}">
    
    <!-- Schema.org markup for Google+ -->
    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "EducationalOrganization",
        "name": "{{ $contactPage->schema_name }}",
        "description": "{{ $contactPage->schema_description }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $contactPage->schema_street_address }}",
            "addressLocality": "{{ $contactPage->schema_address_locality }}",
            "addressRegion": "{{ $contactPage->schema_address_region }}",
            "postalCode": "{{ $contactPage->schema_postal_code }}",
            "addressCountry": "{{ $contactPage->schema_address_country }}"
        },
        "telephone": "{{ $contactPage->schema_telephone }}",
        "email": "{{ $contactPage->schema_email }}",
        "openingHours": "{{ $contactPage->schema_opening_hours }}",
        "url": "{{ url('/') }}"
    }
    </script>
@endsection

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm font-medium text-red-800">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="max-w-lg mx-auto md:max-w-none md:grid md:grid-cols-2 md:gap-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                    {{ $contactPage->contact_title }}
                </h2>
                <div class="mt-3">
                    <p class="text-lg text-gray-500">
                        {{ $contactPage->contact_description }}
                    </p>
                </div>
                <div class="mt-9">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="ml-3 text-base text-gray-500">
                            <p>{{ $contactPage->contact_phone }}</p>
                            <p class="mt-1">{{ $contactPage->contact_phone_hours }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3 text-base text-gray-500">
                            <p>{{ $contactPage->contact_email }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 text-base text-gray-500">
                            <p>{{ $contactPage->contact_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 sm:mt-16 md:mt-0">
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                    Send us a message
                </h2>
                <div class="mt-9">
                    <form action="{{ route('contact.submit') }}" method="POST" class="grid grid-cols-1 gap-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md @error('name') border-red-500 @enderror" required>
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md @error('email') border-red-500 @enderror" required>
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <div class="mt-1">
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md @error('subject') border-red-500 @enderror" required>
                            </div>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <div class="mt-1">
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="tel" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                            </div>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="4" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md @error('message') border-red-500 @enderror" required>{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg mx-auto md:max-w-none md:grid md:grid-cols-2 md:gap-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                    {{ $contactPage->map_title }}
                </h2>
                <div class="mt-3">
                    <p class="text-lg text-gray-500">
                        {{ $contactPage->map_description }}
                    </p>
                </div>
                <div class="mt-9">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 text-base text-gray-500">
                            <p>{{ $contactPage->map_opening_hours_monday_friday }}</p>
                            <p>{{ $contactPage->map_opening_hours_saturday }}</p>
                            <p>{{ $contactPage->map_opening_hours_sunday }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 sm:mt-16 md:mt-0">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ $contactPage->map_embed_url }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 