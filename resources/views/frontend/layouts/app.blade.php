<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Naf Academy'))</title>

    @yield('meta')

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Suppress Tailwind CDN warning in development
        tailwind.config = {
            corePlugins: {
                preflight: true,
            }
        }
    </script>
    
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @yield('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav x-data="{ isOpen: false, isProfileOpen: false }" class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo and Primary Nav -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                            Naf Academy
                        </a>
                    </div>
                    <!-- Desktop Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-gray-900 font-medium">
                            Home
                        </a>
                        <a href="{{ route('subjects') }}" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Subjects
                        </a>
                        <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            About Us
                        </a>
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Pricing
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Contact
                        </a>
                    </div>
                </div>

                <!-- Right side buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-500 hover:text-gray-900">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-500 hover:text-gray-900">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="Profile">
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    @if(auth()->user()->account_type === 'parent')
                                        <a href="{{ route('parent.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                        </a>
                                        <a href="{{ route('parent.messages.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-envelope mr-2"></i>Messages
                                        </a>
                                        <a href="{{ route('parent.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-user-cog mr-2"></i>Profile & Settings
                                        </a>
                                    @elseif(auth()->user()->account_type === 'student')
                                        <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Dashboard</a>
                                    @elseif(auth()->user()->account_type === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Dashboard</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 font-medium text-sm">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-150 font-medium text-sm">
                            Register
                        </a>
                    @endguest
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="isOpen = !isOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <svg class="h-6 w-6" x-show="!isOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="isOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             x-cloak 
             class="md:hidden bg-white shadow-lg" 
             style="display: none;">
            <div class="pt-2 pb-3 space-y-1 px-2">
                <a href="{{ route('home') }}" class="block px-3 py-3 rounded-md text-base font-medium text-indigo-700 bg-indigo-50 border-l-4 border-indigo-500">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href="{{ route('subjects') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-book mr-2"></i> Subjects
                </a>
                <a href="{{ route('about') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-info-circle mr-2"></i> About Us
                </a>
                <a href="{{ route('pricing') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-tags mr-2"></i> Pricing
                </a>
                <a href="{{ route('contact') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-envelope mr-2"></i> Contact
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 px-2">
                @auth
                    <div class="flex items-center px-3 py-2 bg-gray-50 rounded-md">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12 rounded-full border-2 border-indigo-500" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="Profile">
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="text-base font-medium text-gray-800 truncate">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500 truncate">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        @if(auth()->user()->account_type === 'student' || auth()->user()->account_type === 'parent')
                            <a href="{{ route('student.dashboard') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        @elseif(auth()->user()->account_type === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-3 rounded-md text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                @endauth
                @guest
                    <div class="space-y-2 px-2">
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-indigo-600 hover:text-white hover:bg-indigo-600 font-semibold rounded transition">Register</a>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content with padding for fixed navbar -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ $footerContent->about_title ?? 'About Us' }}</h3>
                    <p class="text-gray-300">
                        {{ $footerContent->about_description ?? 'Empowering the next generation through quality education.' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition duration-150">Home</a></li>
                        <li><a href="{{ route('subjects') }}" class="text-gray-300 hover:text-white transition duration-150">Subjects</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white transition duration-150">About Us</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-gray-300 hover:text-white transition duration-150">Pricing</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-white transition duration-150">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                    <ul class="space-y-2">
                        @if(isset($footerContent) && $footerContent->contact_email)
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                <a href="mailto:{{ $footerContent->contact_email }}" class="text-gray-300 hover:text-white transition duration-150">
                                    {{ $footerContent->contact_email }}
                                </a>
                            </li>
                        @endif
                        @if(isset($footerContent) && $footerContent->contact_phone)
                            <li class="flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                <a href="tel:{{ $footerContent->contact_phone }}" class="text-gray-300 hover:text-white transition duration-150">
                                    {{ $footerContent->contact_phone }}
                                </a>
                            </li>
                        @endif
                        @if(isset($footerContent) && $footerContent->contact_address)
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span class="text-gray-300">{{ $footerContent->contact_address }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        @if(isset($footerContent) && $footerContent->facebook_url)
                            <a href="{{ $footerContent->facebook_url }}" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(isset($footerContent) && $footerContent->twitter_url)
                            <a href="{{ $footerContent->twitter_url }}" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if(isset($footerContent) && $footerContent->instagram_url)
                            <a href="{{ $footerContent->instagram_url }}" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(isset($footerContent) && $footerContent->linkedin_url)
                            <a href="{{ $footerContent->linkedin_url }}" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} Naf Academy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
