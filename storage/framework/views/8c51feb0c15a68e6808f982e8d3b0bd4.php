<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="notifications-container">
    <div class="notifications-header">
        <h1>Notifications</h1>
        <?php if($notifications->count() > 0): ?>
            <button id="markAllReadBtn" class="mark-all-read-btn">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        <?php endif; ?>
    </div>

    <?php if($notifications->count() > 0): ?>
        <div class="notifications-list">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="notification-item <?php echo e($notification->is_read ? 'read' : 'unread'); ?> <?php echo e(in_array($notification->type, ['assignment_graded', 'assignment_feedback', 'assignment_status_updated']) ? 'assignment-notification' : ''); ?>" data-id="<?php echo e($notification->id); ?>">
                    <div class="notification-content">
                        <div class="notification-header">
                            <h3 class="notification-title">
                                <?php if(in_array($notification->type, ['assignment_graded', 'assignment_feedback', 'assignment_status_updated', 'assignment_reviewed'])): ?>
                                    <i class="fas fa-clipboard-check" style="color: #10b981; margin-right: 0.5rem;"></i>
                                <?php elseif($notification->type === 'assignment_submitted'): ?>
                                    <i class="fas fa-upload" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                                <?php elseif($notification->type === 'course_recommendation'): ?>
                                    <i class="fas fa-graduation-cap" style="color: #667eea; margin-right: 0.5rem;"></i>
                                <?php else: ?>
                                    <i class="fas fa-comment" style="color: #6b7280; margin-right: 0.5rem;"></i>
                                <?php endif; ?>
                                <?php echo e($notification->title); ?>

                            </h3>
                            <span class="notification-time"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="notification-message"><?php echo e($notification->message); ?></p>
                        <?php if($notification->comment): ?>
                            <div class="notification-comment">
                                <strong><?php echo e($notification->comment->user->name); ?>:</strong>
                                <span><?php echo e(Str::limit($notification->comment->comment, 100)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="notification-actions">
                        <?php if(in_array($notification->type, ['assignment_submitted', 'assignment_graded', 'assignment_feedback', 'assignment_status_updated', 'assignment_reviewed'])): ?>
                            <a href="<?php echo e(route('student.my-assignments.index')); ?>" class="view-video-btn">
                                <i class="fas fa-clipboard-check"></i> View My Assignments
                            </a>
                        <?php elseif($notification->type === 'course_recommendation'): ?>
                            <a href="<?php echo e($notification->link ?? route('student.course-recommendations.index')); ?>" class="view-video-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-graduation-cap"></i> View Recommendations
                            </a>
                        <?php elseif($notification->resource_id): ?>
                            <a href="<?php echo e(route('student.my-videos.show', $notification->resource_id)); ?>" class="view-video-btn">
                                <i class="fas fa-play"></i> View Video
                            </a>
                        <?php endif; ?>
                        <?php if(!$notification->is_read): ?>
                            <button class="mark-read-btn" data-id="<?php echo e($notification->id); ?>">
                                <i class="fas fa-check"></i> Mark as Read
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="notifications-pagination">
            <?php echo e($notifications->links()); ?>

        </div>
    <?php else: ?>
        <div class="no-notifications">
            <i class="fas fa-bell-slash"></i>
            <h2>No notifications yet</h2>
            <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                <p>You'll receive notifications when students comment on your videos.</p>
            <?php else: ?>
                <p>You'll receive notifications when teachers reply to your comments.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.notifications-container {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.notifications-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.mark-all-read-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #2563eb;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.mark-all-read-btn:hover {
    background: #1d4ed8;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-item {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #e5e7eb;
    transition: all 0.2s;
}

.notification-item.unread {
    border-left-color: #2563eb;
    background: #f8fafc;
}

.notification-item.assignment-notification {
    border-left-color: #10b981;
    background: #f0fdf4;
}

.notification-item.assignment-notification.unread {
    border-left-color: #059669;
    background: #ecfdf5;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
}

.notification-item:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.notification-content {
    margin-bottom: 1rem;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.notification-time {
    font-size: 0.875rem;
    color: #6b7280;
    white-space: nowrap;
}

.notification-message {
    color: #374151;
    margin-bottom: 0.75rem;
    line-height: 1.5;
}

.notification-comment {
    background: #f3f4f6;
    padding: 0.75rem;
    border-radius: 0.5rem;
    border-left: 3px solid #d1d5db;
    font-size: 0.875rem;
    color: #4b5563;
}

.notification-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.view-video-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #2563eb;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
}

.view-video-btn:hover {
    background: #1d4ed8;
}

.mark-read-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #f3f4f6;
    color: #374151;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.mark-read-btn:hover {
    background: #e5e7eb;
}

.no-notifications {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.no-notifications i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.no-notifications h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.no-notifications p {
    font-size: 1rem;
    color: #6b7280;
}

.notifications-pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .notifications-container {
        padding: 0.75rem;
    }

    .notifications-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .notifications-header h1 {
        font-size: 1.5rem;
    }

    .notification-header {
        flex-direction: column;
        gap: 0.5rem;
    }

    .notification-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .view-video-btn,
    .mark-read-btn {
        justify-content: center;
    }
}
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark individual notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            markAsRead(notificationId);
        });
    });

    // Mark all notifications as read
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            markAllAsRead();
        });
    }
});

function markAsRead(notificationId) {
    fetch(`/student/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            notificationItem.classList.remove('unread');
            notificationItem.classList.add('read');
            
            const markReadBtn = notificationItem.querySelector('.mark-read-btn');
            if (markReadBtn) {
                markReadBtn.remove();
            }
            
            // Update notification count in sidebar
            if (typeof loadNotificationCount === 'function') {
                loadNotificationCount();
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllAsRead() {
    fetch('/student/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated state
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/notifications.blade.php ENDPATH**/ ?>