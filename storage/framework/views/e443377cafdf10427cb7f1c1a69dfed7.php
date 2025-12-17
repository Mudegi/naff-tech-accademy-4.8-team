

<?php $__env->startSection('title', 'My Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-clipboard-list"></i> My Assignments</h2>
                <p class="text-muted">Manage all your assignments</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?php echo e(route('teacher.standalone-assignments.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Assignment
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if($assignments->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Due Date</th>
                                <th>Submissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($assignment->title); ?></strong>
                                        <?php if($assignment->assignment_file_path): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-paperclip"></i> Has attachment
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($assignment->subject->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($assignment->classRoom->name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($assignment->due_date): ?>
                                            <?php echo e($assignment->due_date->format('M d, Y')); ?>

                                            <?php if($assignment->isOverdue()): ?>
                                                <br><span class="badge bg-danger">Overdue</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No deadline</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($assignment->submissions->count()); ?> submitted</span>
                                    </td>
                                    <td>
                                        <?php if($assignment->is_active): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('teacher.standalone-assignments.show', $assignment->id)); ?>" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" 
                                           class="btn btn-sm btn-primary" title="View Submissions">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="<?php echo e(route('teacher.standalone-assignments.edit', $assignment->id)); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('teacher.standalone-assignments.destroy', $assignment->id)); ?>" 
                                              method="POST" style="display:inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <?php echo e($assignments->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #ddd;"></i>
                    <h4 class="mt-3">No Assignments Yet</h4>
                    <p class="text-muted">Create your first assignment to get started!</p>
                    <a href="<?php echo e(route('teacher.standalone-assignments.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/standalone-assignments/index.blade.php ENDPATH**/ ?>