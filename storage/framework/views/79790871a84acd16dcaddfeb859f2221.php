

<?php $__env->startSection('title', 'Assignment Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-file-alt"></i> Assignment Details</h2>
                <p class="text-muted"><?php echo e($assignment->title); ?></p>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?php echo e(route('teacher.standalone-assignments.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Assignments
                </a>
                <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" class="btn btn-primary ml-2">
                    <i class="fas fa-users"></i> View Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Assignment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Title:</strong> <?php echo e($assignment->title); ?></p>
                            <p><strong>Subject:</strong> <?php echo e($assignment->subject->name ?? 'N/A'); ?></p>
                            <p><strong>Class:</strong> <?php echo e($assignment->classRoom->name ?? 'N/A'); ?></p>
                            <p><strong>Term:</strong> <?php echo e($assignment->term->name ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> <?php echo e($assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No deadline'); ?></p>
                            <p><strong>Total Marks:</strong> <?php echo e($assignment->total_marks); ?></p>
                            <p><strong>Status:</strong> 
                                <?php if($assignment->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Submissions:</strong> <?php echo e($assignment->submissions->count()); ?></p>
                        </div>
                    </div>
                    
                    <?php if($assignment->description): ?>
                        <div class="mt-3">
                            <strong>Description:</strong>
                            <p><?php echo e($assignment->description); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($assignment->instructions): ?>
                        <div class="mt-3">
                            <strong>Instructions:</strong>
                            <p><?php echo e($assignment->instructions); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($assignment->assignment_file_path): ?>
                        <div class="mt-3">
                            <strong>Attachment:</strong>
                            <a href="<?php echo e(Storage::url($assignment->assignment_file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Submissions</h5>
                </div>
                <div class="card-body">
                    <?php if($assignment->submissions->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $assignment->submissions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo e($submission->student->name ?? 'N/A'); ?></strong>
                                            <br><small class="text-muted"><?php echo e($submission->submitted_at->diffForHumans()); ?></small>
                                        </div>
                                        <div>
                                            <?php if($submission->grade): ?>
                                                <span class="badge bg-success"><?php echo e($submission->grade); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($assignment->submissions->count() > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" class="btn btn-sm btn-outline-primary">
                                    View All Submissions
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No submissions yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/standalone-assignments/show.blade.php ENDPATH**/ ?>