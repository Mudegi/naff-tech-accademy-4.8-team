

<?php $__env->startSection('title', 'My Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm">
                <div>
                    <h1 class="h3 mb-1 text-primary">My Assignments</h1>
                    <p class="text-muted mb-0">Manage and track your standalone assignments</p>
                </div>
                <a href="<?php echo e(route('teacher.standalone-assignments.create')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Create New Assignment
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Assignments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">All Assignments</h5>
                </div>
                <div class="card-body p-0">
                    <?php if($assignments->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="border-0 fw-semibold">Title & Details</th>
                                        <th class="border-0 fw-semibold">Subject</th>
                                        <th class="border-0 fw-semibold">Class</th>
                                        <th class="border-0 fw-semibold">Due Date</th>
                                        <th class="border-0 fw-semibold">Submissions</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong class="text-dark"><?php echo e($assignment->title); ?></strong>
                                                    <?php if($assignment->assignment_file_path): ?>
                                                        <small class="text-muted">Attachment included</small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6"><?php echo e($assignment->subject->name ?? 'N/A'); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info fs-6"><?php echo e($assignment->classRoom->name ?? 'N/A'); ?></span>
                                            </td>
                                            <td>
                                                <?php if($assignment->due_date): ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold"><?php echo e($assignment->due_date->format('M d, Y')); ?></span>
                                                        <?php if($assignment->isOverdue()): ?>
                                                            <small class="text-danger">Overdue</small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">No deadline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" 
                                                   class="text-decoration-none">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success me-2"><?php echo e($assignment->submissions->count()); ?></span>
                                                        <small class="text-muted">submitted</small>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if($assignment->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="<?php echo e(route('teacher.standalone-assignments.show', $assignment->id)); ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View Submissions">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('teacher.standalone-assignments.edit', $assignment->id)); ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('teacher.standalone-assignments.destroy', $assignment->id)); ?>" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-center">
                                <?php echo e($assignments->links()); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <h4 class="text-muted mb-3">No Assignments Created Yet</h4>
                            <p class="text-muted mb-4">Start by creating your first standalone assignment to engage your students.</p>
                            <a href="<?php echo e(route('teacher.standalone-assignments.create')); ?>" class="btn btn-primary btn-lg">
                                Create Your First Assignment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/standalone-assignments/index.blade.php ENDPATH**/ ?>