@extends('frontend.layouts.app')

@section('title', 'School Registration')

@section('content')
<div class="min-h-screen flex bg-gray-50">
    <!-- Left side - Image -->
    <div class="hidden lg:block lg:w-1/2 relative">
        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" 
             alt="School" 
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-blue-900/50"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white px-12">
                <h1 class="text-4xl font-bold mb-4">Register Your School</h1>
                <p class="text-lg">Join our platform and manage your school efficiently</p>
            </div>
        </div>
    </div>

    <!-- Right side - Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-2xl w-full space-y-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 text-center">
                    School Registration
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
                    </a>
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('school.register.submit') }}" method="POST">
                @csrf

                <!-- School Information -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">School Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700">School Name *</label>
                            <input id="school_name" 
                                   name="school_name" 
                                   type="text" 
                                   value="{{ old('school_name') }}"
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter school name">
                            @error('school_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_email" class="block text-sm font-medium text-gray-700">School Email *</label>
                            <input id="school_email" 
                                   name="school_email" 
                                   type="email" 
                                   value="{{ old('school_email') }}"
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="school@example.com">
                            @error('school_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_phone" class="block text-sm font-medium text-gray-700">School Phone</label>
                            <input id="school_phone" 
                                   name="school_phone" 
                                   type="text" 
                                   value="{{ old('school_phone') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="+256 700 000 000">
                            @error('school_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_address" class="block text-sm font-medium text-gray-700">School Address</label>
                            <textarea id="school_address" 
                                      name="school_address" 
                                      rows="3"
                                      class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Enter school address">{{ old('school_address') }}</textarea>
                            @error('school_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Information -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Administrator Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="admin_name" class="block text-sm font-medium text-gray-700">Administrator Name *</label>
                            <input id="admin_name" 
                                   name="admin_name" 
                                   type="text" 
                                   value="{{ old('admin_name') }}"
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter administrator name">
                            @error('admin_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700">Administrator Email *</label>
                            <input id="admin_email" 
                                   name="admin_email" 
                                   type="email" 
                                   value="{{ old('admin_email') }}"
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="admin@example.com">
                            @error('admin_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_phone" class="block text-sm font-medium text-gray-700">Administrator Phone</label>
                            <input id="admin_phone" 
                                   name="admin_phone" 
                                   type="text" 
                                   value="{{ old('admin_phone') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="+256 700 000 000">
                            @error('admin_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter password (min 8 characters)">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   required
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                <!-- Subscription Package Selection -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Subscription Package</h3>
                    
                    @if($packages->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($packages as $package)
                        <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition {{ old('subscription_package_id') == $package->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="subscription_package_id" 
                                   value="{{ $package->id }}" 
                                   {{ old('subscription_package_id') == $package->id ? 'checked' : '' }}
                                   required
                                   class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $package->name }}</h4>
                                    @if(old('subscription_package_id') == $package->id)
                                        <i class="fas fa-check-circle text-blue-600"></i>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ $package->description }}</p>
                                <div class="text-2xl font-bold text-blue-600 mb-3">
                                    {{ number_format($package->price, 0) }} UGX
                                </div>
                                <div class="text-sm text-gray-500 mb-3">
                                    Duration: {{ $package->duration_days }} days
                                </div>
                                @if($package->features && is_array($package->features))
                                <ul class="text-sm text-gray-600 space-y-1">
                                    @foreach(array_slice($package->features, 0, 3) as $feature)
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                    @if(count($package->features) > 3)
                                        <li class="text-gray-500">+ {{ count($package->features) - 3 }} more features</li>
                                    @endif
                                </ul>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No subscription packages available at the moment.</p>
                        <p class="text-sm mt-2">Please contact support for assistance.</p>
                    </div>
                    @endif
                    
                    @error('subscription_package_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Register School
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

