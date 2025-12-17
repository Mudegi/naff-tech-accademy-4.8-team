

<?php $__env->startSection('title', 'Assignment Submissions'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-users"></i> Assignment Submissions</h2>
                <p class="text-muted"><?php echo e($assignment->title); ?></p>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?php echo e(route('teacher.standalone-assignments.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Assignments
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if($submissions->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($submission->student->name ?? 'N/A'); ?></strong>
                                        <br><small class="text-muted"><?php echo e($submission->student->email ?? ''); ?></small>
                                    </td>
                                    <td><?php echo e($submission->submitted_at->format('M d, Y H:i')); ?></td>
                                    <td>
                                        <span class="badge bg-success">Submitted</span>
                                    </td>
                                    <td>
                                        <?php if($submission->grade): ?>
                                            <?php echo e($submission->grade); ?>/<?php echo e($assignment->total_marks); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Not graded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('teacher.standalone-assignments.view-submission', [$assignment->id, $submission->id])); ?>" 
                                           class="btn btn-sm btn-info" title="View Submission">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php echo e($submissions->links()); ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4>No submissions yet</h4>
                    <p class="text-muted">Students haven't submitted this assignment yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/standalone-assignments/submissions.blade.php ENDPATH**/ ?>