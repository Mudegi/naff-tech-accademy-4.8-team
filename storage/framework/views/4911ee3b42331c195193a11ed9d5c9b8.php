<?php $__env->startSection('title', 'Welcome to Naf Academy'); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero Section with Carousel -->
<section class="relative bg-gray-900 h-screen" x-data="{ activeSlide: 0 }" x-init="setInterval(() => { activeSlide = (activeSlide + 1) % 10 }, 5000)">
    <!-- Background Images -->
    <div class="absolute inset-0">
        <?php for($i = 1; $i <= 10; $i++): ?>
            <?php
                $imageField = 'hero_image_' . $i;
                $imagePath = $welcomePage && $welcomePage->$imageField ? Storage::url($welcomePage->$imageField) : null;
                $fallbackImage = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&q=80';
            ?>
            <div x-show="activeSlide === <?php echo e($i - 1); ?>" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0">
                <img src="<?php echo e($imagePath ?? $fallbackImage); ?>" class="w-full h-full object-cover" alt="Hero Image <?php echo e($i); ?>">
            </div>
        <?php endfor; ?>
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-10 flex items-center justify-center h-full">
        <div class="text-center px-4 max-w-4xl">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6"><?php echo e($welcomePage->hero_title ?? 'Welcome to Naf Academy'); ?></h1>
            <p class="text-xl md:text-2xl text-gray-200 mb-8"><?php echo e($welcomePage->hero_subtitle ?? 'Empowering the next generation of tech leaders through quality education and hands-on learning experiences.'); ?></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-full hover:bg-indigo-700 transition">Get Started</a>
                <a href="<?php echo e(route('subjects')); ?>" class="px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-full hover:bg-gray-100 transition">Explore Courses</a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <?php if($statistics && $statistics->count() > 0): ?>
                <?php $__currentLoopData = $statistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="text-5xl font-bold text-indigo-600"><?php echo e($stat->value); ?></div>
                    <div class="text-gray-600 mt-2"><?php echo e($stat->label); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div>
                    <div class="text-5xl font-bold text-indigo-600">1000+</div>
                    <div class="text-gray-600 mt-2">Active Students</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-indigo-600">50+</div>
                    <div class="text-gray-600 mt-2">Expert Instructors</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-indigo-600">100+</div>
                    <div class="text-gray-600 mt-2">Courses Available</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-indigo-600">95%</div>
                    <div class="text-gray-600 mt-2">Success Rate</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">Why Choose Us</h2>
            <p class="text-4xl font-bold text-gray-900 mt-2">Excellence in Tech Education</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-laptop-code text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Industry-Relevant Curriculum</h3>
                <p class="text-gray-600">Our courses are designed with input from industry experts to ensure you learn the most relevant skills.</p>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-chalkboard-teacher text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Expert Instructors</h3>
                <p class="text-gray-600">Learn from experienced professionals who are passionate about teaching and technology.</p>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-project-diagram text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Hands-on Projects</h3>
                <p class="text-gray-600">Apply your learning through real-world projects and build a strong portfolio.</p>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-briefcase text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Career Support</h3>
                <p class="text-gray-600">Get guidance on career development, resume building, and job placement assistance.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<?php if($subjects && $subjects->count() > 0): ?>
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">Featured Courses</h2>
            <p class="text-4xl font-bold text-gray-900 mt-2">Popular Subjects</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white border-2 border-gray-100 rounded-xl p-8 hover:shadow-xl transition">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-book text-3xl text-indigo-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4"><?php echo e($subject->name); ?></h3>
                <p class="text-gray-600 mb-6"><?php echo e(Str::limit($subject->description, 120)); ?></p>
                <a href="<?php echo e(route('subjects.show', $subject->slug)); ?>" class="text-indigo-600 font-semibold hover:text-indigo-800 inline-flex items-center">
                    Learn more
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Team Section -->
<?php if($teams && $teams->count() > 0): ?>
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">Our Team</h2>
            <p class="text-4xl font-bold text-gray-900 mt-2">Meet Our Expert Educators</p>
            <p class="text-xl text-gray-500 mt-4 max-w-2xl mx-auto">Our dedicated team of professionals is committed to providing you with the best educational experience.</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php $__currentLoopData = $teams->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl p-8 text-center hover:shadow-xl transition">
                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($team->name)); ?>&size=128&background=667eea&color=fff&bold=true" 
                     alt="<?php echo e($team->name); ?>" 
                     class="w-32 h-32 rounded-full mx-auto mb-6 border-4 border-indigo-100">
                <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo e($team->name); ?></h3>
                <p class="text-indigo-600 font-semibold mb-6"><?php echo e($team->position); ?></p>
                <div class="flex flex-wrap gap-2 justify-center">
                    <?php $__currentLoopData = $team->skills_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full"><?php echo e(trim($skill)); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo e(route('team.members')); ?>" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition shadow-lg">
                View All Members
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="text-center lg:text-left">
                <h2 class="text-4xl font-bold text-white mb-2">Ready to start your journey?</h2>
                <p class="text-xl text-indigo-100">Join Naf Academy today.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="<?php echo e(route('register')); ?>" class="px-8 py-4 bg-white text-indigo-600 font-semibold rounded-full hover:bg-gray-100 transition shadow-lg">Get started</a>
                <a href="<?php echo e(route('contact')); ?>" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-indigo-600 transition shadow-lg">Contact us</a>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/frontend/pages/home.blade.php ENDPATH**/ ?>