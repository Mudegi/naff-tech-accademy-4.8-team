<?php $__env->startSection('title', 'Sample Videos'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if(!isset($hasActiveSubscription) || !$hasActiveSubscription): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>No active subscription found.</strong>
                            Get full access to all videos and features by purchasing a subscription.
                        </p>
                    </div>
                </div>
                <div class="ml-4">
                    <a href="<?php echo e(route('pricing')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Purchase Subscription
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Sample Videos</h1>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form id="filterForm" action="<?php echo e(route('student.sample-videos.index')); ?>" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Grade Level Filter -->
                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                        <select name="grade_level" id="grade_level" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">All Grades</option>
                            <option value="O Level" <?php echo e(request('grade_level') == 'O Level' ? 'selected' : ''); ?>>O Level</option>
                            <option value="A Level" <?php echo e(request('grade_level') == 'A Level' ? 'selected' : ''); ?>>A Level</option>
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div class="mb-4">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select name="subject_id" id="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                            <option value="">All Subjects</option>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($subject->id); ?>" <?php echo e(request('subject_id') == $subject->id ? 'selected' : ''); ?>>
                                    <?php echo e($subject->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Topic Filter -->
                    <div>
                        <label for="topic_id" class="block text-sm font-medium text-gray-700">Topic</label>
                        <select name="topic_id" id="topic_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                            <option value="">All Topics</option>
                            <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($topic->id); ?>" <?php echo e(request('topic_id') == $topic->id ? 'selected' : ''); ?>>
                                    <?php echo e($topic->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Term Filter -->
                    <div>
                        <label for="term_id" class="block text-sm font-medium text-gray-700">Term</label>
                        <select name="term_id" id="term_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                            <option value="">All Terms</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e(request('term_id') == $term->id ? 'selected' : ''); ?>>
                                    <?php echo e($term->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Class Filter -->
                    <div class="mb-4">
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select name="class_id" id="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e(request('class_id') == $class->id ? 'selected' : ''); ?>>
                                    <?php echo e($class->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Search by title or description">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="<?php echo e(route('student.sample-videos.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Reset Filters
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Videos Grid -->
    <div class="bg-white rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden transition-transform duration-200 hover:shadow-lg hover:-translate-y-1">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo e($resource->title); ?></h3>
                        <p class="text-gray-600 text-sm mb-4"><?php echo e(Str::limit($resource->description, 100)); ?></p>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <?php echo e($resource->grade_level); ?>

                            </span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <?php echo e(optional($resource->subject)->name ?? $resource->subject_name ?? 'Unknown Subject'); ?>

                            </span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                <?php echo e(optional($resource->term)->name ?? $resource->term_name ?? 'Unknown Term'); ?>

                            </span>
                            <?php if($resource->classRoom): ?>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                    <?php echo e($resource->classRoom->name); ?>

                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <a href="<?php echo e(route('student.sample-videos.show', $resource->hash_id)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-play mr-2"></i>
                                Watch Video
                            </a>
                            <span class="text-sm text-gray-500"><?php echo e($resource->created_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-video text-4xl mb-4"></i>
                        <p class="text-lg">No sample videos available at the moment.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-6">
        <?php echo e($resources->links()); ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change
        const filterForm = document.getElementById('filterForm');
        const filterInputs = filterForm.querySelectorAll('select');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // Handle subject change for topics
        const subjectSelect = document.getElementById('subject_id');
        const topicSelect = document.getElementById('topic_id');
        
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            
            if (subjectId) {
                // Load topics for the selected subject
                fetch(`/api/subjects/${subjectId}/topics`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.name;
                            topicSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/sample-videos/index.blade.php ENDPATH**/ ?>