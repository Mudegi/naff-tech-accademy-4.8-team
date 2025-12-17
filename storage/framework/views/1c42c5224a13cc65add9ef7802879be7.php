

<?php $__env->startSection('title', 'View Submission'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-file-alt"></i> Submission Details</h2>
                <p class="text-muted"><?php echo e($assignment->title); ?> - <?php echo e($submission->student->name ?? 'N/A'); ?></p>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?php echo e(route('teacher.standalone-assignments.submissions', $assignment->id)); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Submission Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> <?php echo e($submission->student->name ?? 'N/A'); ?></p>
                            <p><strong>Email:</strong> <?php echo e($submission->student->email ?? 'N/A'); ?></p>
                            <p><strong>Submitted At:</strong> <?php echo e($submission->submitted_at->format('M d, Y H:i')); ?></p>
                            <p><strong>Status:</strong> 
                                <?php if($submission->grade): ?>
                                    <span class="badge bg-success">Graded</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending Grade</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <?php if($submission->grade): ?>
                                <p><strong>Grade:</strong> <?php echo e($submission->grade); ?>/<?php echo e($assignment->total_marks); ?></p>
                                <p><strong>Percentage:</strong> <?php echo e(number_format(($submission->grade / $assignment->total_marks) * 100, 1)); ?>%</p>
                            <?php endif; ?>
                            <?php if($submission->student_comment): ?>
                                <p><strong>Student Comment:</strong> <?php echo e($submission->student_comment); ?></p>
                            <?php endif; ?>
                            <?php if($submission->teacher_feedback): ?>
                                <p><strong>Teacher Feedback:</strong> <?php echo e($submission->teacher_feedback); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($submission->submission_file_path): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Submitted File</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-<?php echo e(strtolower($submission->submission_file_type) == 'pdf' ? 'pdf' : 'image'); ?> fa-2x text-primary me-3"></i>
                            <div>
                                <p class="mb-1"><strong>File:</strong> <?php echo e(basename($submission->submission_file_path)); ?></p>
                                <p class="mb-1"><strong>Type:</strong> <?php echo e(strtoupper($submission->submission_file_type)); ?></p>
                                <a href="<?php echo e(Storage::url($submission->submission_file_path)); ?>" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Download/View File
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Grade Submission</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('teacher.standalone-assignments.grade-submission', [$assignment->id, $submission->id])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label for="grade">Grade (0-<?php echo e($assignment->total_marks); ?>)</label>
                            <input type="number" class="form-control" id="grade" name="grade" 
                                   value="<?php echo e($submission->grade); ?>" min="0" max="<?php echo e($assignment->total_marks); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="teacher_feedback">Feedback (Optional)</label>
                            <textarea class="form-control" id="teacher_feedback" name="teacher_feedback" rows="3"><?php echo e($submission->teacher_feedback); ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="submitted" <?php echo e($submission->status == 'submitted' ? 'selected' : ''); ?>>Submitted</option>
                                <option value="reviewed" <?php echo e($submission->status == 'reviewed' ? 'selected' : ''); ?>>Reviewed</option>
                                <option value="graded" <?php echo e($submission->status == 'graded' ? 'selected' : ''); ?>>Graded</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Grade
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/standalone-assignments/view-submission.blade.php ENDPATH**/ ?>