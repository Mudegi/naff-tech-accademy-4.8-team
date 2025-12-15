

<?php $__env->startSection('content'); ?>
<style>
    .groups-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 30px 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        text-align: center;
    }

    .page-header h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .page-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-header h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .section-count {
        background: #667eea;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .groups-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .group-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border: 2px solid #f0f0f0;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .group-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    }

    .group-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .group-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #333;
        margin: 0;
        flex: 1;
    }

    .group-badges {
        display: flex;
        gap: 8px;
    }

    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-leader {
        background: #fef3c7;
        color: #d97706;
    }

    .badge-full {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-open {
        background: #d1fae5;
        color: #065f46;
    }

    .group-description {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .group-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        padding: 15px 0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .stat {
        flex: 1;
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-label {
        display: block;
        font-size: 0.85rem;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .group-members {
        margin-bottom: 20px;
    }

    .members-list {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .member-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #667eea;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .member-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .leader-indicator {
        position: absolute;
        bottom: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        background: #fbbf24;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        border: 2px solid white;
    }

    .members-more {
        color: #667eea;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .group-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-link {
        flex: 1;
        padding: 10px 15px;
        text-align: center;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .action-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .action-secondary {
        background: #f0f0f0;
        color: #333;
    }

    .action-secondary:hover {
        background: #e0e0e0;
    }

    .quick-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .action-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .action-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .action-btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .action-btn-secondary:hover {
        background: #f8f9ff;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 30px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 30px 20px;
        }

        .page-header h1 {
            font-size: 1.6rem;
        }

        .groups-grid {
            grid-template-columns: 1fr;
        }

        .group-stats {
            gap: 15px;
        }

        .section {
            padding: 20px;
        }

        .quick-actions {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="groups-page">
    <!-- Header -->
    <div class="page-header">
        <h1>👥 Group Management</h1>
        <p>Create or join groups to collaborate on projects together</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="<?php echo e(route('student.projects.index')); ?>" class="action-btn action-btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Projects
        </a>
        <a href="<?php echo e(route('student.projects.groups.create')); ?>" class="action-btn action-btn-primary">
            <i class="fas fa-plus-circle"></i>
            Create New Group
        </a>
    </div>

    <!-- My Groups Section -->
    <?php if($myGroups->count() > 0): ?>
    <div class="section">
        <div class="section-header">
            <h2>My Groups</h2>
            <span class="section-count"><?php echo e($myGroups->count()); ?> group<?php echo e($myGroups->count() > 1 ? 's' : ''); ?></span>
        </div>

        <div class="groups-grid">
            <?php $__currentLoopData = $myGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="group-card">
                <div class="group-header">
                    <h3 class="group-name"><?php echo e($group->name); ?></h3>
                    <div class="group-badges">
                        <?php if($group->isLeader(Auth::user())): ?>
                        <span class="badge badge-leader">Leader</span>
                        <?php endif; ?>
                        <?php if($group->isFull()): ?>
                        <span class="badge badge-full">Full</span>
                        <?php else: ?>
                        <span class="badge badge-open">Open</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($group->description): ?>
                <p class="group-description"><?php echo e(Str::limit($group->description, 100)); ?></p>
                <?php endif; ?>

                <div class="group-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo e($group->approvedMembers->count()); ?></span>
                        <span class="stat-label">Members</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo e($group->max_members); ?></span>
                        <span class="stat-label">Max</span>
                    </div>
                    <?php if($group->projects->count() > 0): ?>
                    <div class="stat">
                        <span class="stat-number"><?php echo e($group->projects->count()); ?></span>
                        <span class="stat-label">Projects</span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="group-members">
                    <div class="members-list">
                        <?php $__currentLoopData = $group->approvedMembers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="member-avatar" title="<?php echo e($member->name); ?>">
                            <?php if($member->profile_photo_path): ?>
                                <img src="<?php echo e(asset('storage/' . $member->profile_photo_path)); ?>" alt="<?php echo e($member->name); ?>">
                            <?php else: ?>
                                <span><?php echo e(strtoupper(substr($member->name, 0, 2))); ?></span>
                            <?php endif; ?>
                            <?php if($group->isLeader($member)): ?>
                                <div class="leader-indicator">
                                    <i class="fas fa-crown"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($group->approvedMembers->count() > 3): ?>
                        <div class="member-more">
                            +<?php echo e($group->approvedMembers->count() - 3); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="group-actions">
                    <a href="<?php echo e(route('student.projects.groups.show', $group)); ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>

                    <?php if($group->isLeader(Auth::user()) && !$group->isFull() && $group->status === 'open'): ?>
                    <button class="btn btn-secondary" onclick="toggleGroupStatus(<?php echo e($group->id); ?>, '<?php echo e($group->status); ?>')">
                        <i class="fas fa-lock"></i> Close Group
                    </button>
                    <?php elseif($group->isLeader(Auth::user()) && $group->status === 'closed'): ?>
                    <button class="btn btn-secondary" onclick="toggleGroupStatus(<?php echo e($group->id); ?>, '<?php echo e($group->status); ?>')">
                        <i class="fas fa-lock-open"></i> Open Group
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Available Groups Section -->
    <?php if($availableGroups->count() > 0): ?>
    <div class="section">
        <div class="section-header">
            <h2>Available Groups</h2>
            <span class="section-count"><?php echo e($availableGroups->count()); ?> group<?php echo e($availableGroups->count() > 1 ? 's' : ''); ?></span>
        </div>

        <div class="groups-grid">
            <?php $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="group-card available-group">
                <div class="group-header">
                    <h3 class="group-name"><?php echo e($group->name); ?></h3>
                    <div class="group-badges">
                        <span class="badge badge-open">Open</span>
                    </div>
                </div>

                <?php if($group->description): ?>
                <p class="group-description"><?php echo e(Str::limit($group->description, 100)); ?></p>
                <?php endif; ?>

                <div class="group-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo e($group->approvedMembers->count()); ?></span>
                        <span class="stat-label">Members</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo e($group->max_members); ?></span>
                        <span class="stat-label">Max</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Created by</span>
                        <span class="stat-value"><?php echo e($group->creator->name); ?></span>
                    </div>
                </div>

                <div class="group-members">
                    <div class="members-list">
                        <?php $__currentLoopData = $group->approvedMembers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="member-avatar" title="<?php echo e($member->name); ?>">
                            <?php if($member->profile_photo_path): ?>
                                <img src="<?php echo e(asset('storage/' . $member->profile_photo_path)); ?>" alt="<?php echo e($member->name); ?>">
                            <?php else: ?>
                                <span><?php echo e(strtoupper(substr($member->name, 0, 2))); ?></span>
                            <?php endif; ?>
                            <?php if($group->isLeader($member)): ?>
                                <div class="leader-indicator">
                                    <i class="fas fa-crown"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($group->approvedMembers->count() > 3): ?>
                        <div class="member-more">
                            +<?php echo e($group->approvedMembers->count() - 3); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="group-actions">
                    <a href="<?php echo e(route('student.projects.groups.show', $group)); ?>" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <form action="<?php echo e(route('student.projects.groups.join', $group)); ?>" method="POST" class="inline-form">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Join Group
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3>No Available Groups</h3>
        <p>There are no open groups in your class that you can join at the moment.</p>
        <p>Create your own group to get started!</p>
    </div>
    <?php endif; ?>
</div>

<style>
.groups-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 1rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.action-btn-primary {
    background: #2563eb;
    color: white;
}

.action-btn-primary:hover {
    background: #1d4ed8;
}

.action-btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.action-btn-secondary:hover {
    background: #e5e7eb;
}

.section {
    margin-bottom: 3rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
}

.section-count {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.groups-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.group-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s, box-shadow 0.2s;
}

.group-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.available-group {
    border-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
}

.group-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.group-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    flex: 1;
}

.group-badges {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-leader {
    background: #fef3c7;
    color: #92400e;
}

.badge-full {
    background: #fee2e2;
    color: #dc2626;
}

.badge-open {
    background: #d1fae5;
    color: #065f46;
}

.group-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.group-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 500;
}

.stat-value {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.group-members {
    margin-bottom: 1.5rem;
}

.members-list {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.member-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: #2563eb;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.leader-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #f59e0b;
    color: white;
    border-radius: 50%;
    width: 1rem;
    height: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.5rem;
}

.member-more {
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 50%;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.group-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-success {
    background: #059669;
    color: white;
}

.btn-success:hover {
    background: #047857;
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
    color: #374151;
}

.inline-form {
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.empty-state p {
    margin-bottom: 0.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .groups-page {
        padding: 0.75rem;
    }

    .quick-actions {
        flex-direction: column;
        align-items: center;
    }

    .groups-grid {
        grid-template-columns: 1fr;
    }

    .group-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .group-actions {
        flex-direction: column;
    }

    .btn {
        justify-content: center;
    }

    .inline-form .btn {
        width: 100%;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/projects/groups/index.blade.php ENDPATH**/ ?>