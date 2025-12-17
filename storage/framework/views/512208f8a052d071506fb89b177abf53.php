

<?php $__env->startSection('content'); ?>
<div class="upload-resource-page">
    <div class="page-header">
        <h1>Upload Resource</h1>
        <p>Select the class and upload a PDF or PNG resource.</p>
    </div>
    <form method="POST" action="<?php echo e(route('teacher.resources.upload.submit')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="term_id">Term</label>
            <select name="term_id" id="term_id" class="form-control" required>
                <option value="">Select Term</option>
                <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($term->id); ?>"><?php echo e($term->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="topic_name">Topic Name</label>
            <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="Enter topic name (e.g., Introduction to Algebra)" required>
            <small class="form-text text-muted">If this topic doesn't exist, it will be created automatically</small>
        </div>
        <div class="form-group">
            <label for="resource_file">Resource File (PDF or PNG)</label>
            <input type="file" name="resource_file" id="resource_file" class="form-control" accept=".pdf,.png" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Resource</button>
        <a href="<?php echo e(route('teacher.dashboard')); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/resources/upload.blade.php ENDPATH**/ ?>