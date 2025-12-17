<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', config('app.name', 'Naf Academy')); ?></title>

    <?php echo $__env->yieldContent('meta'); ?>

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
    <link rel="stylesheet" href="<?php echo e(asset('css/responsive.css')); ?>">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav x-data="{ isOpen: false, isProfileOpen: false }" class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo and Primary Nav -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo e(route('home')); ?>" class="text-2xl font-bold text-indigo-600">
                            Naf Academy
                        </a>
                    </div>
                    <!-- Desktop Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-gray-900 font-medium">
                            Home
                        </a>
                        <a href="<?php echo e(route('subjects')); ?>" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Subjects
                        </a>
                        <a href="<?php echo e(route('about')); ?>" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            About Us
                        </a>
                        <?php if(!auth()->check() || auth()->user()->account_type !== 'parent'): ?>
                        <a href="<?php echo e(route('pricing')); ?>" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Pricing
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('contact')); ?>" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-indigo-500 font-medium">
                            Contact
                        </a>
                    </div>
                </div>

                <!-- Right side buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(auth()->guard()->check()): ?>
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-500 hover:text-gray-900">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-500 hover:text-gray-900">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>" alt="Profile">
                                <span class="hidden md:block"><?php echo e(auth()->user()->name); ?></span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <?php if(auth()->user()->account_type === 'parent'): ?>
                                        <a href="<?php echo e(route('parent.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                        </a>
                                        <a href="<?php echo e(route('parent.my-videos')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-play-circle mr-2"></i>My Videos
                                        </a>
                                        <a href="<?php echo e(route('parent.messages.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-envelope mr-2"></i>Messages
                                        </a>
                                        <a href="<?php echo e(route('parent.profile')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-user-cog mr-2"></i>Profile & Settings
                                        </a>
                                    <?php elseif(auth()->user()->account_type === 'student'): ?>
                                        <a href="<?php echo e(route('student.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Dashboard</a>
                                    <?php elseif(auth()->user()->account_type === 'admin'): ?>
                                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Dashboard</a>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-gray-500 hover:text-gray-900 px-3 py-2 font-medium text-sm">
                            Login
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-150 font-medium text-sm">
                            Register
                        </a>
                    <?php endif; ?>
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
                <a href="<?php echo e(route('home')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-indigo-700 bg-indigo-50 border-l-4 border-indigo-500">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href="<?php echo e(route('subjects')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-book mr-2"></i> Subjects
                </a>
                <a href="<?php echo e(route('about')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-info-circle mr-2"></i> About Us
                </a>
                <?php if(!auth()->check() || auth()->user()->account_type !== 'parent'): ?>
                <a href="<?php echo e(route('pricing')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-tags mr-2"></i> Pricing
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('contact')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-envelope mr-2"></i> Contact
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 px-2">
                <?php if(auth()->guard()->check()): ?>
                    <div class="flex items-center px-3 py-2 bg-gray-50 rounded-md">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12 rounded-full border-2 border-indigo-500" src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>" alt="Profile">
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="text-base font-medium text-gray-800 truncate"><?php echo e(auth()->user()->name); ?></div>
                            <div class="text-sm font-medium text-gray-500 truncate"><?php echo e(auth()->user()->email); ?></div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <?php if(auth()->user()->account_type === 'student' || auth()->user()->account_type === 'parent'): ?>
                            <a href="<?php echo e(route('student.dashboard')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        <?php elseif(auth()->user()->account_type === 'admin'): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-3 py-3 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="block w-full text-left px-3 py-3 rounded-md text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <div class="space-y-2 px-2">
                        <a href="<?php echo e(route('login')); ?>" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Login</a>
                        <a href="<?php echo e(route('register')); ?>" class="block px-4 py-2 text-base font-medium text-indigo-600 hover:text-white hover:bg-indigo-600 font-semibold rounded transition">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content with padding for fixed navbar -->
    <main class="pt-20">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4"><?php echo e($footerContent->about_title ?? 'About Us'); ?></h3>
                    <p class="text-gray-300">
                        <?php echo e($footerContent->about_description ?? 'Empowering the next generation through quality education.'); ?>

                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo e(route('home')); ?>" class="text-gray-300 hover:text-white transition duration-150">Home</a></li>
                        <li><a href="<?php echo e(route('subjects')); ?>" class="text-gray-300 hover:text-white transition duration-150">Subjects</a></li>
                        <li><a href="<?php echo e(route('about')); ?>" class="text-gray-300 hover:text-white transition duration-150">About Us</a></li>
                        <?php if(!auth()->check() || auth()->user()->account_type !== 'parent'): ?>
                        <li><a href="<?php echo e(route('pricing')); ?>" class="text-gray-300 hover:text-white transition duration-150">Pricing</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo e(route('contact')); ?>" class="text-gray-300 hover:text-white transition duration-150">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                    <ul class="space-y-2">
                        <?php if(isset($footerContent) && $footerContent->contact_email): ?>
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                <a href="mailto:<?php echo e($footerContent->contact_email); ?>" class="text-gray-300 hover:text-white transition duration-150">
                                    <?php echo e($footerContent->contact_email); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(isset($footerContent) && $footerContent->contact_phone): ?>
                            <li class="flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                <a href="tel:<?php echo e($footerContent->contact_phone); ?>" class="text-gray-300 hover:text-white transition duration-150">
                                    <?php echo e($footerContent->contact_phone); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(isset($footerContent) && $footerContent->contact_address): ?>
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span class="text-gray-300"><?php echo e($footerContent->contact_address); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <?php if(isset($footerContent) && $footerContent->facebook_url): ?>
                            <a href="<?php echo e($footerContent->facebook_url); ?>" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(isset($footerContent) && $footerContent->twitter_url): ?>
                            <a href="<?php echo e($footerContent->twitter_url); ?>" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(isset($footerContent) && $footerContent->instagram_url): ?>
                            <a href="<?php echo e($footerContent->instagram_url); ?>" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(isset($footerContent) && $footerContent->linkedin_url): ?>
                            <a href="<?php echo e($footerContent->linkedin_url); ?>" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-center text-gray-300">
                <p>&copy; <?php echo e(date('Y')); ?> Naf Academy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <?php echo $__env->yieldContent('scripts'); ?>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
<?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/frontend/layouts/app.blade.php ENDPATH**/ ?>