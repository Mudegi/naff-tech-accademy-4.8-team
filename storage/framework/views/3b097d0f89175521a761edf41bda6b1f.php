

<?php $__env->startSection('content'); ?>
<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 1.5rem;">
        <h1>Manage Student Marks</h1>
        <p>Upload marks for students in your classes</p>
    </div>

    <?php if(session('success')): ?>
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 1.5rem;">
            <?php echo e(session('success')); ?>

            <?php if(session('import_results')): ?>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.3);">
                    <strong>Import Summary:</strong><br>
                    ✅ Successfully imported: <?php echo e(session('import_results')['success'] ?? 0); ?> marks<br>
                    <?php if((session('import_results')['failed'] ?? 0) > 0): ?>
                        ❌ Failed: <?php echo e(session('import_results')['failed'] ?? 0); ?> marks
                        <?php if(!empty(session('import_results')['errors'])): ?>
                            <details style="margin-top: 0.5rem;">
                                <summary style="cursor: pointer; text-decoration: underline;">View Errors</summary>
                                <ul style="margin-top: 0.5rem; padding-left: 20px;">
                                    <?php $__currentLoopData = array_slice(session('import_results')['errors'], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li style="font-size: 0.875rem;"><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(count(session('import_results')['errors']) > 10): ?>
                                        <li style="font-size: 0.875rem; color: #fbbf24;">... and <?php echo e(count(session('import_results')['errors']) - 10); ?> more errors</li>
                                    <?php endif; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="dashboard-card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="margin: 0;">Your Classes</h2>
            <a href="<?php echo e(route('teacher.marks.create')); ?>" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-upload"></i> Upload Marks
            </a>
        </div>

        <?php if($classes->count() > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; background: white;">
                        <h3 style="margin: 0 0 0.5rem 0; color: #1f2937;"><?php echo e($class->name); ?></h3>
                        <p style="margin: 0 0 0.5rem 0; color: #6b7280; font-size: 0.875rem;"><?php echo e($class->grade_level ?? 'N/A'); ?></p>
                        <a href="<?php echo e(route('teacher.marks.create', ['class_id' => $class->id])); ?>" class="dashboard-btn dashboard-btn-secondary" style="width: 100%; text-align: center;">
                            <i class="fas fa-upload"></i> Upload Marks
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                <p>You are not assigned to any classes yet.</p>
                <p style="font-size: 0.875rem;">Please contact your administrator to be assigned to classes.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($recentUploads->count() > 0): ?>
        <div class="dashboard-card">
            <h2 style="margin: 0 0 1rem 0;">Recent Mark Uploads</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Student</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Subject</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Exam Type</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Grade</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Points</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Academic Year</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Date</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #374151;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentUploads->flatten()->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 0.75rem; color: #1f2937;"><?php echo e($mark->user->name ?? 'N/A'); ?></td>
                                <td style="padding: 0.75rem; color: #6b7280;">
                                    <?php echo e($mark->subject_name); ?>

                                    <?php if($mark->paper_name): ?>
                                        <span style="font-size: 0.75rem; color: #9ca3af;">(<?php echo e($mark->paper_name); ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                                    <?php if($mark->exam_type): ?>
                                        <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: 
                                            <?php if($mark->exam_type == 'End of Term'): ?> #dcfce7; color: #166534;
                                            <?php elseif($mark->exam_type == 'Mid Term'): ?> #fef3c7; color: #92400e;
                                            <?php elseif($mark->exam_type == 'Beginning of Term'): ?> #e0e7ff; color: #3730a3;
                                            <?php elseif($mark->exam_type == 'Mock'): ?> #fce7f3; color: #9f1239;
                                            <?php else: ?> #f3f4f6; color: #374151;
                                            <?php endif; ?>
                                        ">
                                            <?php echo e($mark->exam_type); ?>

                                            <?php if($mark->exam_type == 'Other' && $mark->exam_type_other): ?>
                                                <span style="font-size: 0.75rem;"> (<?php echo e($mark->exam_type_other); ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #9ca3af;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem; background: #dbeafe; color: #1e40af;">
                                        <?php echo e($mark->grade); ?>

                                    </span>
                                </td>
                                <td style="padding: 0.75rem; color: #6b7280;"><?php echo e($mark->points ?? 'N/A'); ?></td>
                                <td style="padding: 0.75rem; color: #6b7280;"><?php echo e($mark->academic_year ?? 'N/A'); ?></td>
                                <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;"><?php echo e($mark->created_at->format('M d, Y')); ?></td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?php echo e(route('teacher.marks.edit', $mark->id)); ?>" class="dashboard-btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #f59e0b; color: white;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('teacher.marks.destroy', $mark->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this mark? This action cannot be undone.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dashboard-btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #ef4444; color: white; border: none; cursor: pointer;" title="Delete">
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
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/marks/index.blade.php ENDPATH**/ ?>