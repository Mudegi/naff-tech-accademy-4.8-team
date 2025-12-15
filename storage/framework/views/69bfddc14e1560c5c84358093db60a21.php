

<?php $__env->startSection('content'); ?>
<div class="dashboard-page">
    <div class="welcome-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>My Academic Marks</h1>
            <p>View your UACE/UCE examination results uploaded by your teachers</p>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 1.5rem;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Aggregate Points Summary -->
    <?php if($aggregatePoints > 0): ?>
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-calculator" style="color: white;"></i>
            </div>
            <div class="stat-content">
                <h3 style="color: rgba(255,255,255,0.9);">Aggregate Points</h3>
                <p class="stat-number" style="color: white; font-size: 2rem;"><?php echo e(number_format($aggregatePoints, 1)); ?></p>
                <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem; margin-top: 0.5rem;">Based on best 3 principal passes</p>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-check-circle" style="color: white;"></i>
            </div>
            <div class="stat-content">
                <h3 style="color: rgba(255,255,255,0.9);">Principal Passes</h3>
                <p class="stat-number" style="color: white; font-size: 2rem;"><?php echo e($principalPasses); ?></p>
                <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem; margin-top: 0.5rem;">Subjects with E or better</p>
            </div>
        </div>
        <?php if(!in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
        <div class="stat-card" style="background: white; border: 2px solid #e5e7eb;">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>Course Recommendations</h3>
                <p class="stat-number" style="color: #667eea; font-size: 1.5rem;">Available</p>
                <a href="<?php echo e(route('student.course-recommendations.index')); ?>" class="stat-link" style="color: #667eea; font-weight: 600;">
                    View Recommendations <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Marks by Academic Level -->
    <?php $__empty_1 = true; $__currentLoopData = $marks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level => $levelMarks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="marks-section" style="background: white; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: #1a1a1a;">
                    <i class="fas fa-book" style="color: #667eea; margin-right: 0.5rem;"></i>
                    <?php echo e($level); ?> Results
                </h2>
                <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                    <?php echo e($levelMarks->count()); ?> <?php echo e(Str::plural('Subject', $levelMarks->count())); ?>

                </span>
            </div>

            <div class="marks-table-container" style="overflow-x: auto;">
                <table class="marks-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;">Subject</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;">Paper</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #374151; font-size: 0.875rem;">Grade</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #374151; font-size: 0.875rem;">Points</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #374151; font-size: 0.875rem;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $levelMarks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                <td style="padding: 0.75rem; color: #1a1a1a; font-weight: 500;"><?php echo e($mark->subject_name); ?></td>
                                <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;"><?php echo e($mark->paper_name ?? 'N/A'); ?></td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span class="grade-badge" style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem; background: #dbeafe; color: #1e40af;">
                                        <?php echo e($mark->grade); ?>

                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span style="font-weight: 600; color: #059669; font-size: 1rem;"><?php echo e($mark->points ?? 0); ?></span>
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <div style="display: flex; gap: 0.25rem; justify-content: center; flex-wrap: wrap;">
                                        <?php if($mark->is_principal_pass): ?>
                                            <span class="badge-small" style="background: #fef3c7; color: #92400e; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">Principal</span>
                                        <?php endif; ?>
                                        <?php if($mark->is_essential): ?>
                                            <span class="badge-small" style="background: #fee2e2; color: #991b1b; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">Essential</span>
                                        <?php endif; ?>
                                        <?php if($mark->is_relevant): ?>
                                            <span class="badge-small" style="background: #dbeafe; color: #1e40af; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">Relevant</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty-state" style="background: white; border-radius: 0.5rem; padding: 3rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
            <h3 style="color: #6b7280; margin-bottom: 0.5rem;">No Marks Available Yet</h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Your teachers haven't uploaded your UACE or UCE examination results yet.</p>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 640px) {
    .marks-table-container {
        font-size: 0.875rem;
    }
    .marks-table th,
    .marks-table td {
        padding: 0.5rem;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/marks/index.blade.php ENDPATH**/ ?>