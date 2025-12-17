

<?php $__env->startSection('title', 'My Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="assignments-page">
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> My Assignments</h1>
        <p>View and submit all your assignments in one place</p>
    </div>

    <?php
        // Get video-based assignments (from assessment tests)
        $user = Auth::user();
        $videoAssignments = \App\Models\StudentAssignment::with(['resource.subject', 'resource.term', 'resource.classRoom'])
            ->where('student_id', $user->id)
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        $totalAssignments = $assignments->count() + $videoAssignments->count();
    ?>

    <?php if($totalAssignments > 0): ?>
        <div class="assignments-tabs">
            <button class="tab-btn active" onclick="showTab('all')">
                All Assignments (<?php echo e($totalAssignments); ?>)
            </button>
            <button class="tab-btn" onclick="showTab('homework')">
                Homework (<?php echo e($assignments->count()); ?>)
            </button>
            <button class="tab-btn" onclick="showTab('video')">
                Video Assignments (<?php echo e($videoAssignments->count()); ?>)
            </button>
        </div>

        <div id="all-tab" class="tab-content active">
            <div class="assignments-grid">
                <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $submission = $assignment->submissions->first();
                    $isSubmitted = $submission !== null;
                    $isOverdue = $assignment->isOverdue();
                ?>
                
                <div class="assignment-card <?php echo e($isSubmitted ? 'submitted' : ''); ?> <?php echo e($isOverdue && !$isSubmitted ? 'overdue' : ''); ?>">
                    <div class="card-header">
                        <h3><?php echo e($assignment->title); ?></h3>
                        <div class="status-badges">
                            <?php if($isSubmitted): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Submitted
                                </span>
                                <?php if($submission && $submission->status === 'graded'): ?>
                                    <span class="badge badge-info">
                                        <i class="fas fa-star"></i> Graded
                                    </span>
                                <?php endif; ?>
                            <?php elseif($isOverdue): ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-exclamation-circle"></i> Overdue
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-book"></i>
                                <span><?php echo e($assignment->subject->name ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo e($assignment->classRoom->name ?? 'N/A'); ?></span>
                            </div>
                            <?php if($assignment->due_date): ?>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Due: <?php echo e($assignment->due_date->format('M d, Y')); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <i class="fas fa-star"></i>
                                <span><?php echo e($assignment->total_marks); ?> marks</span>
                            </div>
                        </div>
                        
                        <?php if($isSubmitted && $submission): ?>
                            <div class="submission-info">
                                <p><strong>Submitted:</strong> <?php echo e($submission->submitted_at->format('M d, Y H:i A')); ?></p>
                                <?php if($submission->grade !== null): ?>
                                    <p class="grade"><strong>Grade:</strong> <?php echo e($submission->grade); ?>/<?php echo e($assignment->total_marks); ?></p>
                                <?php endif; ?>
                                <?php if($submission->teacher_feedback): ?>
                                    <div class="feedback">
                                        <strong>Teacher Feedback:</strong>
                                        <p><?php echo e($submission->teacher_feedback); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($assignment->description): ?>
                            <p class="description"><?php echo e(Str::limit($assignment->description, 100)); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer">
                        <a href="<?php echo e(route('student.assignments.show', $assignment->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <?php if(!$isSubmitted && !$isOverdue): ?>
                            <span class="badge badge-info">Not submitted</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $videoAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $videoAssignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="assignment-card <?php echo e($videoAssignment->status === 'graded' ? 'submitted' : ''); ?>">
                    <div class="card-header">
                        <h3><?php echo e($videoAssignment->resource->title); ?></h3>
                        <div class="status-badges">
                            <span class="badge badge-secondary">
                                <i class="fas fa-video"></i> Video Assignment
                            </span>
                            <?php if($videoAssignment->status === 'graded'): ?>
                                <span class="badge badge-info">
                                    <i class="fas fa-star"></i> Graded
                                </span>
                            <?php elseif($videoAssignment->status === 'reviewed'): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-eye"></i> Reviewed
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Submitted
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-book"></i>
                                <span><?php echo e($videoAssignment->resource->subject->name ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo e($videoAssignment->resource->classRoom->name ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Submitted: <?php echo e($videoAssignment->submitted_at->format('M d, Y')); ?></span>
                            </div>
                            <?php if($videoAssignment->grade): ?>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <span class="grade-highlight"><?php echo e($videoAssignment->grade); ?>%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($videoAssignment->teacher_feedback): ?>
                            <div class="submission-info">
                                <div class="feedback">
                                    <strong>Teacher Feedback:</strong>
                                    <p><?php echo e($videoAssignment->teacher_feedback); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer">
                        <a href="<?php echo e(route('student.my-videos.show', $videoAssignment->resource->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Video & Details
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div id="homework-tab" class="tab-content">
            <div class="assignments-grid">
                <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $submission = $assignment->submissions->first();
                        $isSubmitted = $submission !== null;
                        $isOverdue = $assignment->isOverdue();
                    ?>
                    
                    <div class="assignment-card <?php echo e($isSubmitted ? 'submitted' : ''); ?> <?php echo e($isOverdue && !$isSubmitted ? 'overdue' : ''); ?>">
                        <div class="card-header">
                            <h3><?php echo e($assignment->title); ?></h3>
                            <div class="status-badges">
                                <span class="badge badge-primary">
                                    <i class="fas fa-pencil-alt"></i> Homework
                                </span>
                                <?php if($isSubmitted): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Submitted
                                    </span>
                                    <?php if($submission && $submission->status === 'graded'): ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-star"></i> Graded
                                        </span>
                                    <?php endif; ?>
                                <?php elseif($isOverdue): ?>
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-circle"></i> Overdue
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fas fa-book"></i>
                                    <span><?php echo e($assignment->subject->name ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo e($assignment->classRoom->name ?? 'N/A'); ?></span>
                                </div>
                                <?php if($assignment->due_date): ?>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Due: <?php echo e($assignment->due_date->format('M d, Y')); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo e($assignment->total_marks); ?> marks</span>
                                </div>
                            </div>
                            
                            <?php if($isSubmitted && $submission): ?>
                                <div class="submission-info">
                                    <p><strong>Submitted:</strong> <?php echo e($submission->submitted_at->format('M d, Y H:i A')); ?></p>
                                    <?php if($submission->grade !== null): ?>
                                        <p class="grade"><strong>Grade:</strong> <?php echo e($submission->grade); ?>/<?php echo e($assignment->total_marks); ?></p>
                                    <?php endif; ?>
                                    <?php if($submission->teacher_feedback): ?>
                                        <div class="feedback">
                                            <strong>Teacher Feedback:</strong>
                                            <p><?php echo e($submission->teacher_feedback); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($assignment->description): ?>
                                <p class="description"><?php echo e(Str::limit($assignment->description, 100)); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer">
                            <a href="<?php echo e(route('student.assignments.show', $assignment->id)); ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if(!$isSubmitted && !$isOverdue): ?>
                                <span class="badge badge-info">Not submitted</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div id="video-tab" class="tab-content">
            <div class="assignments-grid">
                <?php $__currentLoopData = $videoAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $videoAssignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="assignment-card <?php echo e($videoAssignment->status === 'graded' ? 'submitted' : ''); ?>">
                        <div class="card-header">
                            <h3><?php echo e($videoAssignment->resource->title); ?></h3>
                            <div class="status-badges">
                                <span class="badge badge-secondary">
                                    <i class="fas fa-video"></i> Video Assignment
                                </span>
                                <?php if($videoAssignment->status === 'graded'): ?>
                                    <span class="badge badge-info">
                                        <i class="fas fa-star"></i> Graded
                                    </span>
                                <?php elseif($videoAssignment->status === 'reviewed'): ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-eye"></i> Reviewed
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Submitted
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fas fa-book"></i>
                                    <span><?php echo e($videoAssignment->resource->subject->name ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo e($videoAssignment->resource->classRoom->name ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Submitted: <?php echo e($videoAssignment->submitted_at->format('M d, Y')); ?></span>
                                </div>
                                <?php if($videoAssignment->grade): ?>
                                    <div class="info-item">
                                        <i class="fas fa-star"></i>
                                        <span class="grade-highlight"><?php echo e($videoAssignment->grade); ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($videoAssignment->teacher_feedback): ?>
                                <div class="submission-info">
                                    <div class="feedback">
                                        <strong>Teacher Feedback:</strong>
                                        <p><?php echo e($videoAssignment->teacher_feedback); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer">
                            <a href="<?php echo e(route('student.my-videos.show', $videoAssignment->resource->id)); ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Video & Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Assignments Available</h3>
            <p>Your teacher hasn't assigned any work yet. Check back later!</p>
        </div>
    <?php endif; ?>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    if (tabName === 'all') {
        document.getElementById('all-tab').classList.add('active');
        document.querySelectorAll('.tab-btn')[0].classList.add('active');
    } else if (tabName === 'homework') {
        document.getElementById('homework-tab').classList.add('active');
        document.querySelectorAll('.tab-btn')[1].classList.add('active');
    } else if (tabName === 'video') {
        document.getElementById('video-tab').classList.add('active');
        document.querySelectorAll('.tab-btn')[2].classList.add('active');
    }
}
</script>

<style>
.assignments-page {
    padding: 2rem 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
}

.assignments-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e5e7eb;
    flex-wrap: wrap;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: #6b7280;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.95rem;
}

.tab-btn:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.tab-btn.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.assignments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.assignment-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.assignment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.assignment-card.submitted {
    border-left: 4px solid #10b981;
}

.assignment-card.overdue {
    border-left: 4px solid #ef4444;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
}

.card-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}

.status-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: white;
}

.badge-warning {
    background: rgba(251, 191, 36, 0.2);
    color: white;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: white;
}

.badge-info {
    background: rgba(59, 130, 246, 0.2);
    color: white;
}

.badge-primary {
    background: rgba(102, 126, 234, 0.2);
    color: white;
}

.badge-secondary {
    background: rgba(107, 114, 128, 0.2);
    color: white;
}

.card-body {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.info-item i {
    color: #667eea;
}

.grade-highlight {
    color: #10b981;
    font-weight: 700;
    font-size: 1.1rem;
}

.submission-info {
    background: #f3f4f6;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.submission-info p {
    margin: 0.5rem 0;
    font-size: 0.875rem;
}

.submission-info .grade {
    font-size: 1rem;
    color: #10b981;
    font-weight: 600;
}

.feedback {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
}

.feedback p {
    margin-top: 0.5rem;
    color: #4b5563;
}

.description {
    margin-top: 1rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.card-footer {
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
}

.empty-state h3 {
    margin: 1rem 0 0.5rem;
    color: #1a1a1a;
}

.empty-state p {
    color: #6b7280;
}

@media (max-width: 768px) {
    .assignments-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/standalone-assignments/index.blade.php ENDPATH**/ ?>