

<?php $__env->startSection('title', 'Parent Dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    body { background: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .parent-container { max-width: 1400px; margin: 0 auto; padding: 2rem 1rem; }
    .page-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-size: 1.875rem; font-weight: 700; color: #1a202c; margin: 0 0 0.5rem 0; }
    .page-subtitle { color: #718096; margin: 0; font-size: 1rem; }
    .compose-btn { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; }
    .compose-btn:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
    
    .stats-grid { display: flex; gap: 1.25rem; margin-bottom: 2.5rem; flex-wrap: wrap; }
    .stat-box { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.2s; flex: 1; min-width: 220px; max-width: 280px; position: relative; overflow: hidden; }
    .stat-box::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .stat-box:nth-child(1) { border: 1px solid #c7d2fe; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%); }
    .stat-box:nth-child(1)::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
    .stat-box:nth-child(2) { border: 1px solid #a7f3d0; background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); }
    .stat-box:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #059669); }
    .stat-box:nth-child(3) { border: 1px solid #fed7aa; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%); }
    .stat-box:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .stat-box:nth-child(4) { border: 1px solid #bfdbfe; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); }
    .stat-box:nth-child(4)::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .stat-box:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.12); transform: translateY(-4px); }
    .stat-icon { width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; margin-bottom: 1rem; }
    .stat-number { font-size: 2rem; font-weight: 700; color: #1a202c; margin: 0; }
    .stat-label { color: #718096; font-size: 0.875rem; margin-top: 0.25rem; }
    
    .children-grid { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .student-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s; border: 2px solid #e2e8f0; width: 100%; max-width: 450px; position: relative; }
    .student-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.15); transform: translateY(-4px); border-color: #6366f1; }
    
    .card-top { padding: 1.5rem; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .student-left { display: flex; gap: 1rem; align-items: center; }
    .avatar { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; flex-shrink: 0; }
    .student-details h4 { margin: 0 0 0.25rem 0; font-size: 1.125rem; font-weight: 700; color: #1a202c; }
    .student-details p { margin: 0; color: #718096; font-size: 0.875rem; }
    .view-btn { background: #6366f1; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.875rem; transition: background 0.2s; }
    .view-btn:hover { background: #4f46e5; color: white; }
    
    .card-middle { padding: 1.5rem; }
    .performance { text-align: center; margin-bottom: 1.5rem; }
    .score-display { width: 100px; height: 100px; margin: 0 auto 0.75rem; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .score-percent { font-size: 1.75rem; font-weight: 700; color: white; }
    .score-badge { display: inline-block; padding: 0.375rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.875rem; margin-top: 0.5rem; }
    
    .metrics-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .metric-item { background: linear-gradient(135deg, #f0f9ff 0%, #f7fafc 100%); padding: 1rem; border-radius: 6px; text-align: center; border: 1px solid #e0f2fe; }
    .metric-num { font-size: 1.5rem; font-weight: 700; color: #0369a1; margin-bottom: 0.25rem; }
    .metric-text { color: #718096; font-size: 0.75rem; }
    
    .alert-warn { background: #fef3c7; border-left: 3px solid #f59e0b; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.875rem; color: #92400e; }
    
    .recent-title { font-size: 0.875rem; font-weight: 700; color: #1a202c; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .activities { background: linear-gradient(135deg, #faf5ff 0%, #f7fafc 100%); border-radius: 6px; padding: 0.5rem; max-height: 200px; overflow-y: auto; border: 1px solid #e9d5ff; }
    .act-item { padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; border-left: 3px solid transparent; }
    .act-item:hover { background: white; border-left-color: #6366f1; }
    .act-item:last-child { margin-bottom: 0; }
    .act-left h5 { margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 600; color: #2d3748; }
    .act-left p { margin: 0; font-size: 0.75rem; color: #718096; }
    .act-right { text-align: right; }
    .act-score { font-weight: 700; color: #1a202c; font-size: 0.875rem; }
    .act-grade { font-size: 0.75rem; color: #718096; }
    
    .card-bottom { padding: 1rem 1.5rem; background: linear-gradient(135deg, #faf5ff 0%, #fafbfc 100%); display: flex; gap: 0.5rem; border-top: 1px solid #f3e8ff; }
    .action-btn { flex: 1; text-align: center; padding: 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 6px; color: #6366f1; font-weight: 600; text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
    .action-btn:hover { background: #6366f1; color: white; }
    
    .empty-state { background: white; padding: 3rem; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
    .empty-icon { font-size: 3.5rem; color: #cbd5e0; margin-bottom: 1rem; }
    .empty-state h3 { font-size: 1.5rem; font-weight: 700; color: #1a202c; margin-bottom: 0.5rem; }
    .empty-state p { color: #718096; max-width: 500px; margin: 0 auto 1.5rem; line-height: 1.6; }
    .empty-note { background: #eff6ff; border: 1px solid #3b82f6; padding: 1rem 1.5rem; border-radius: 8px; display: inline-block; color: #1e40af; font-size: 0.875rem; }
    
    .no-activity { text-align: center; padding: 2rem 1rem; color: #a0aec0; }
    .no-activity i { font-size: 2.5rem; color: #cbd5e0; margin-bottom: 0.5rem; }
    .no-activity p { margin: 0; color: #718096; font-size: 0.875rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="parent-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">👋 Parent Dashboard</h1>
            <p class="page-subtitle">Monitor your children's academic progress and performance</p>
        </div>
        <a href="<?php echo e(route('parent.messages.index')); ?>" class="compose-btn">
            <i class="fas fa-envelope"></i> Messages
            <span class="badge bg-danger" id="parent-unread-badge" style="display: none; margin-left: 0.5rem;">0</span>
        </a>
    </div>

    <?php if($children->isEmpty()): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-user-friends"></i></div>
            <h3>No Children Linked</h3>
            <p>Your account is not currently linked to any students. Please contact your school administrator to link your account to your child's/children's account(s).</p>
            <div class="empty-note">
                <i class="fas fa-info-circle"></i> The school will verify your relationship before linking accounts
            </div>
        </div>
    <?php else: ?>
        <!-- Statistics Overview -->
        <?php if($overallSummary): ?>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <i class="fas fa-users"></i>
                </div>
                <p class="stat-number"><?php echo e($overallSummary['total_children']); ?></p>
                <p class="stat-label">Total Children</p>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-star"></i>
                </div>
                <p class="stat-number"><?php echo e($overallSummary['performing_well']); ?></p>
                <p class="stat-label">Performing Well (≥70%)</p>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="stat-number"><?php echo e($overallSummary['needs_attention']); ?></p>
                <p class="stat-label">Needs Attention (<50%)</p>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <p class="stat-number"><?php echo e($overallSummary['total_activities']); ?></p>
                <p class="stat-label">Total Activities</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Children Cards -->
        <div class="children-grid">
            <?php $__currentLoopData = $childrenData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $student = $data['student'];
                $performance = $data['performance'];
                $recentActivity = $data['recent_activity'];
                $alerts = $data['alerts'];
                
                $circleColor = $performance['overall_average'] >= 70 ? '#10b981' : ($performance['overall_average'] >= 50 ? '#f59e0b' : '#ef4444');
                $badgeClass = $performance['overall_average'] >= 70 ? 'background: #d1fae5; color: #065f46;' : ($performance['overall_average'] >= 50 ? 'background: #fef3c7; color: #92400e;' : 'background: #fee2e2; color: #991b1b;');
            ?>
            
            <div class="student-card">
                <div class="card-top">
                    <div class="student-left">
                        <div class="avatar"><?php echo e(strtoupper(substr($student->name, 0, 1))); ?></div>
                        <div class="student-details">
                            <h4><?php echo e($student->name); ?></h4>
                            <p>
                                <i class="fas fa-graduation-cap"></i> 
                                <?php echo e(optional($student->classes->first())->name ?? 'Class not assigned'); ?>

                                <?php if($student->student): ?> · <?php echo e($student->student->level ?? 'N/A'); ?><?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('parent.children.show', $student->id)); ?>" class="view-btn">View Details</a>
                </div>

                <div class="card-middle">
                    <div class="performance">
                        <div class="score-display" style="background: <?php echo e($circleColor); ?>;">
                            <div class="score-percent"><?php echo e($performance['overall_average']); ?>%</div>
                        </div>
                        <span class="score-badge" style="<?php echo e($badgeClass); ?>"><?php echo e($performance['letter_grade']); ?></span>
                    </div>

                    <div class="metrics-row">
                        <div class="metric-item">
                            <div class="metric-num"><?php echo e($performance['total_assignments']); ?></div>
                            <div class="metric-text">Assignments</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-num"><?php echo e($performance['total_exams']); ?></div>
                            <div class="metric-text">Exams</div>
                        </div>
                    </div>

                    <?php if($performance['pending_assignments'] > 0): ?>
                    <div class="alert-warn">
                        <i class="fas fa-clock"></i> <strong><?php echo e($performance['pending_assignments']); ?></strong> assignment(s) awaiting grading
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($recentActivity)): ?>
                    <div class="recent-title"><i class="fas fa-history"></i> Recent Activity</div>
                    <div class="activities">
                        <?php $__currentLoopData = array_slice($recentActivity, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="act-item">
                            <div class="act-left">
                                <h5><?php echo e($activity['title']); ?></h5>
                                <p><?php echo e($activity['date']->diffForHumans()); ?></p>
                            </div>
                            <div class="act-right">
                                <div class="act-score"><?php echo e($activity['percentage']); ?>%</div>
                                <div class="act-grade"><?php echo e($activity['grade']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="no-activity">
                        <i class="fas fa-inbox"></i>
                        <p>No recent activity</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card-bottom">
                    <a href="<?php echo e(route('parent.child.details', $student->id)); ?>" class="action-btn">
                        <i class="fas fa-chart-bar"></i> Full Report
                    </a>
                    <a href="<?php echo e(route('parent.messages.create', ['student_id' => $student->id])); ?>" class="action-btn">
                        <i class="fas fa-comments"></i> Message Teacher
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Load unread message count
document.addEventListener('DOMContentLoaded', function() {
    fetch('/parent/messages/unread-count')
        .then(response => response.json())
        .then(data => {
            const unreadCount = data.unread_count || 0;
            const badge = document.getElementById('parent-unread-badge');
            
            if (unreadCount > 0 && badge) {
                badge.textContent = unreadCount;
                badge.style.display = 'inline-block';
            }
        })
        .catch(error => console.error('Error loading message count:', error));
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/parent/parent-dashboard.blade.php ENDPATH**/ ?>