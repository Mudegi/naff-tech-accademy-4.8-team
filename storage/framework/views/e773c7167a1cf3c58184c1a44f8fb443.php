<?php $__env->startSection('title', 'Pricing Plans'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">
                    Choose Your Learning Path
                </h1>
                <p class="mt-4 text-xl text-indigo-100">
                    Flexible pricing options to suit your learning needs
                </p>
            </div>
        </div>
    </div>

    <?php if(isset($currentPackage) && $currentPackage): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded mb-4 text-center">
            You are currently subscribed to the <strong><?php echo e($currentPackage->name); ?></strong> package.
        </div>
    <?php endif; ?>

    <!-- Pricing Plans Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-<?php echo e(count($packages)); ?>">
                <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden <?php echo e($loop->index === 1 ? 'transform scale-105' : ''); ?>">
                    <?php if($loop->index === 1): ?>
                    <div class="absolute top-0 right-0 -mt-4 -mr-4">
                        <span class="inline-flex rounded-full bg-green-100 px-4 py-1 text-sm font-semibold text-green-800">
                            Popular
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="px-6 py-8">
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo e($package->name); ?></h3>
                        <?php if(isset($activeSubscription) && $activeSubscription && $activeSubscription->subscription_package_id == $package->id): ?>
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full mt-2">Subscribed</span>
                        <?php endif; ?>
                        <p class="mt-4 text-gray-500"><?php echo e($package->description); ?></p>
                        <div class="mt-6">
                            <span class="text-4xl font-extrabold text-gray-900">UGX <?php echo e(number_format($package->price)); ?></span>
                            <span class="text-gray-500">/<?php echo e($package->subscription_type); ?></span>
                        </div>
                        <ul class="mt-8 space-y-4">
                            <?php $__currentLoopData = $package->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700"><?php echo e($feature); ?></span>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700"><?php echo e($package->duration_days); ?> days access</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700"><?php echo e($package->maximum_active_sessions); ?> Active Sessions</span>
                            </li>
                            <?php if($package->access_to_notices): ?>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Access to Notices</span>
                            </li>
                            <?php endif; ?>
                            <?php if($package->access_to_videos): ?>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Access to Videos</span>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <?php if($package->downloadable_content || $package->practice_questions_bank || $package->performance_analytics || 
                            $package->parent_progress_reports || $package->one_on_one_tutoring_sessions || $package->shared_resources || 
                            $package->priority_support || $package->email_support): ?>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Additional Features</h4>
                            <ul class="mt-4 space-y-4">
                                <?php if($package->downloadable_content): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Downloadable Content</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->practice_questions_bank): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Practice Questions Bank</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->performance_analytics): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Performance Analytics</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->parent_progress_reports): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Parent Progress Reports</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->one_on_one_tutoring_sessions): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">One-on-One Tutoring Sessions</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->shared_resources): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Shared Resources</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->priority_support): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Priority Support</span>
                                </li>
                                <?php endif; ?>
                                <?php if($package->email_support): ?>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700">Email Support</span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <div class="mt-8">
                            <?php if(Auth::check()): ?>
                                <?php
                                    $showEasypay = isset($easypay) && $easypay;
                                    $showFlutterwave = isset($flutterwave) && $flutterwave;
                                ?>
                                <?php if($showEasypay || $showFlutterwave): ?>
                                    <div class="mb-2 font-semibold">Choose Payment Method:</div>
                                    <?php if(isset($activeSubscription) && $activeSubscription && $activeSubscription->subscription_package_id == $package->id): ?>
                                        <button class="block w-full bg-green-100 text-green-800 text-center px-6 py-3 rounded-md font-semibold cursor-not-allowed" disabled>
                                            Subscribed
                                        </button>
                                    <?php else: ?>
                                        <?php if($showEasypay): ?>
                                            <form method="POST" action="<?php echo e(route('payment.easypay')); ?>" style="margin-bottom: 0.5rem;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="package_id" value="<?php echo e($package->id); ?>">
                                                <input type="hidden" name="amount" value="<?php echo e($package->price); ?>">
                                                <input type="hidden" name="currency" value="UGX">
                                                <input type="hidden" name="reference" value="<?php echo e(Auth::id()); ?>">
                                                <input type="hidden" name="reason" value="Payment for subscription">
                                                <input type="hidden" name="client_id" value="<?php echo e($easypay->client_id); ?>">
                                                <input type="hidden" name="secret" value="<?php echo e($easypay->secret); ?>">
                                                <input type="hidden" name="action" value="mmdeposit">
                                                <input type="hidden" name="username" value="<?php echo e($easypay->client_id); ?>">
                                                <input type="hidden" name="password" value="<?php echo e($easypay->secret); ?>">
                                                <div class="mb-2">
                                                    <label for="phone_<?php echo e($package->id); ?>" class="block text-sm font-medium text-gray-700">Mobile Money Phone Number</label>
                                                    <input type="text" name="phone" id="phone_<?php echo e($package->id); ?>" value="<?php echo e(Auth::user()->phone_number); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                                <button type="submit" class="block w-full bg-green-600 text-white text-center px-6 py-3 rounded-md font-semibold hover:bg-green-700">
                                                    Pay with Easypay
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if($showFlutterwave): ?>
                                            <form method="POST" action="<?php echo e(route('payment.flutterwave')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="package_id" value="<?php echo e($package->id); ?>">
                                                <input type="hidden" name="amount" value="<?php echo e($package->price); ?>">
                                                <input type="hidden" name="currency" value="<?php echo e($flutterwave->currency_code ?? 'UGX'); ?>">
                                                <input type="hidden" name="reference" value="<?php echo e(Auth::id()); ?>">
                                                <input type="hidden" name="reason" value="Payment for subscription">
                                                <button type="submit" class="block w-full bg-yellow-500 text-white text-center px-6 py-3 rounded-md font-semibold hover:bg-yellow-600 mt-2">
                                                    Pay with Flutterwave
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="dashboard-alert dashboard-alert-warning">No payment options are currently available. Please contact support.</div>
                                <?php endif; ?>
                            <?php else: ?>
                            <a href="<?php echo e(route('register')); ?>" class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-md font-semibold hover:bg-blue-700">
                                Get Started
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    All Plans Include
                </h2>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="text-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Comprehensive Materials</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Access to all course materials, study guides, and resources
                    </p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Expert Instructors</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Learn from experienced professionals in the field
                    </p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Progress Tracking</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Monitor your progress with detailed analytics
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Frequently Asked Questions
                </h2>
            </div>
            <div class="mt-12">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                    <div>
                        <dt class="text-lg leading-6 font-medium text-gray-900">
                            Can I switch plans later?
                        </dt>
                        <dd class="mt-2 text-base text-gray-500">
                            Yes, you can upgrade or downgrade your plan at any time. The new pricing will be prorated based on your remaining subscription period.
                        </dd>
                    </div>

                    <div>
                        <dt class="text-lg leading-6 font-medium text-gray-900">
                            What payment methods do you accept?
                        </dt>
                        <dd class="mt-2 text-base text-gray-500">
                            We accept all major credit cards, PayPal, and bank transfers. All payments are secure and encrypted.
                        </dd>
                    </div>

                    <div>
                        <dt class="text-lg leading-6 font-medium text-gray-900">
                            Is there a refund policy?
                        </dt>
                        <dd class="mt-2 text-base text-gray-500">
                            Yes, we offer a 30-day money-back guarantee if you're not satisfied with our services.
                        </dd>
                    </div>

                    <div>
                        <dt class="text-lg leading-6 font-medium text-gray-900">
                            Do you offer student discounts?
                        </dt>
                        <dd class="mt-2 text-base text-gray-500">
                            Yes, we offer special discounts for students. Please contact our support team for more information.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready to start learning?</span>
                <span class="block text-blue-200">Join Naf Academy today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        Get started
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="<?php echo e(route('contact')); ?>" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-500 hover:bg-blue-400">
                        Contact us
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/frontend/pages/pricing.blade.php ENDPATH**/ ?>