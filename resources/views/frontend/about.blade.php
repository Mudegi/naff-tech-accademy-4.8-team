@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                    About Naf Academy
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-500">
                    Empowering students with quality education through innovative learning platforms.
                </p>
            </div>

            <!-- Mission and Vision -->
            <div class="mt-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="bg-indigo-50 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-indigo-900">Our Mission</h2>
                        <p class="mt-4 text-indigo-700">
                            To provide accessible, high-quality education to students worldwide through innovative digital platforms and comprehensive learning resources.
                        </p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-indigo-900">Our Vision</h2>
                        <p class="mt-4 text-indigo-700">
                            To become the leading online educational platform, transforming the way students learn and achieve their academic goals.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-16">
                <h2 class="text-3xl font-extrabold text-gray-900 text-center">Why Choose Us</h2>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mx-auto">
                            <i class="fas fa-video text-xl"></i>
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Video Lessons</h3>
                        <p class="mt-2 text-base text-gray-500">
                            High-quality video content from experienced educators.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mx-auto">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">PDF Resources</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Comprehensive study materials in PDF format.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mx-auto">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Community</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Join a community of learners and educators.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Team -->
            <div class="mt-16">
                <h2 class="text-3xl font-extrabold text-gray-900 text-center">Our Team</h2>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="text-center">
                        <img class="mx-auto h-32 w-32 rounded-full" src="https://via.placeholder.com/150" alt="Team member">
                        <h3 class="mt-6 text-lg font-medium text-gray-900">John Doe</h3>
                        <p class="text-sm text-gray-500">Founder & CEO</p>
                    </div>
                    <div class="text-center">
                        <img class="mx-auto h-32 w-32 rounded-full" src="https://via.placeholder.com/150" alt="Team member">
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Jane Smith</h3>
                        <p class="text-sm text-gray-500">Head of Education</p>
                    </div>
                    <div class="text-center">
                        <img class="mx-auto h-32 w-32 rounded-full" src="https://via.placeholder.com/150" alt="Team member">
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Mike Johnson</h3>
                        <p class="text-sm text-gray-500">Technical Director</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 