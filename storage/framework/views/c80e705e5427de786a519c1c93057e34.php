

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner school-dashboard">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-school text-blue-600 mr-3"></i>
                School Dashboard
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">School Dashboard</span>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success mb-6 animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-error mb-6 animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- School Info Hero Card -->
    <div class="school-hero-card mb-8">
        <div class="school-hero-content">
            <div class="school-hero-left">
                <div class="school-logo-wrapper">
                    <?php if($school->logo): ?>
                        <img src="<?php echo e(asset('storage/' . $school->logo)); ?>" alt="<?php echo e($school->name); ?>" class="school-logo">
                    <?php else: ?>
                        <div class="school-logo-placeholder">
                            <i class="fas fa-school"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="school-hero-info">
                    <h2 class="school-hero-title"><?php echo e($school->name); ?></h2>
                    <p class="school-hero-subtitle">
                        <i class="fas fa-envelope mr-2"></i>
                        <?php echo e($school->email); ?>

                    </p>
                    <?php if($school->phone_number): ?>
                    <p class="school-hero-role">
                        <i class="fas fa-phone mr-2"></i>
                        <?php echo e($school->phone_number); ?>

                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="school-hero-right">
                <div class="school-status-badge <?php echo e($school->status === 'active' ? 'status-active' : 'status-inactive'); ?>">
                    <i class="fas fa-circle mr-2"></i>
                    <?php echo e(ucfirst($school->status)); ?>

                </div>
                <div class="school-stats-mini">
                    <div class="mini-stat">
                        <div class="mini-stat-value"><?php echo e($stats['total_students']); ?></div>
                        <div class="mini-stat-label">Students</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value"><?php echo e($stats['total_staff']); ?></div>
                        <div class="mini-stat-label">Staff</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value"><?php echo e($stats['total_classes']); ?></div>
                        <div class="mini-stat-label">Classes</div>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.school.settings')); ?>" class="school-settings-btn">
                    <i class="fas fa-cog mr-2"></i>
                    Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card-modern stat-card-primary">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['total_staff']); ?></div>
                <div class="stat-label-modern">Total Staff</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-info"><?php echo e($stats['directors']); ?> Directors</span>
                    <span class="stat-badge-secondary"><?php echo e($stats['heads_of_department']); ?> HODs</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-success">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-success">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['total_students']); ?></div>
                <div class="stat-label-modern">Total Students</div>
                <div class="stat-sublabel-modern">
                    <span class="stat-badge-success">Active</span>
                </div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-warning">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-warning">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['total_subjects']); ?></div>
                <div class="stat-label-modern">Subjects</div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-info">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-info">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['total_classes']); ?></div>
                <div class="stat-label-modern">Classes</div>
            </div>
        </div>
    </div>

    <!-- Staff Breakdown -->
    <div class="stats-grid">
        <div class="stat-card-modern stat-card-secondary">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-secondary">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['directors']); ?></div>
                <div class="stat-label-modern">Directors of Studies</div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-accent">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-accent">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['heads_of_department']); ?></div>
                <div class="stat-label-modern">Heads of Department</div>
            </div>
        </div>

        <div class="stat-card-modern stat-card-teal">
            <div class="stat-card-header">
                <div class="stat-icon-wrapper stat-icon-teal">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value-modern"><?php echo e($stats['teachers']); ?></div>
                <div class="stat-label-modern">Subject Teachers</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="section-title">Quick Actions</h3>
            </div>
        </div>
        <div class="quick-actions-grid">
            <a href="<?php echo e(route('admin.school.staff.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Manage Staff</div>
                    <div class="quick-action-desc">Add or manage staff members</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.school.settings')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-green">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">School Settings</div>
                    <div class="quick-action-desc">Update school information</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.grade-scales.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-indigo">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Grading Scales</div>
                    <div class="quick-action-desc">Customize O-Level & A-Level grading</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.subjects.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-purple">
                    <i class="fas fa-book"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Manage Subjects</div>
                    <div class="quick-action-desc">View and manage subjects</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.classes.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-slate">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Manage Classes</div>
                    <div class="quick-action-desc">Create, edit and track classes</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.school.departments.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-orange">
                    <i class="fas fa-building"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Manage Departments</div>
                    <div class="quick-action-desc">Create and manage departments</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.users.student-parent-list')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-pink">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Student-Parent Accounts</div>
                    <div class="quick-action-desc">View auto-generated parent accounts</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.school.subscriptions.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-red">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Subscriptions</div>
                    <div class="quick-action-desc">Manage subscription and payments</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo e(route('admin.school.resources.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-indigo">
                    <i class="fas fa-video"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Resources & Videos</div>
                    <div class="quick-action-desc">Manage learning resources and videos</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <?php if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies()): ?>
            <a href="<?php echo e(route('admin.school.students.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-teal">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Manage Students</div>
                    <div class="quick-action-desc">Create and manage student accounts</div>
                </div>
                <div class="quick-action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Staff Members -->
    <?php if($recentStaff->count() > 0): ?>
    <div class="dashboard-section-card">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3 class="section-title">Recent Staff Members</h3>
            </div>
            <a href="<?php echo e(route('admin.school.staff.index')); ?>" class="view-all-link">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="table-cell-content">
                                <div class="table-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="table-name"><?php echo e($staff->name); ?></span>
                            </div>
                        </td>
                        <td><?php echo e($staff->email); ?></td>
                        <td>
                            <span class="table-badge table-badge-blue">
                                <?php echo e(ucfirst(str_replace('_', ' ', $staff->account_type))); ?>

                            </span>
                        </td>
                        <td>
                            <span class="table-badge <?php echo e($staff->is_active ? 'table-badge-success' : 'table-badge-danger'); ?>">
                                <?php echo e($staff->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* School Dashboard Styles */
.school-dashboard {
    padding: 0 0 32px 0;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

/* School Hero Card */
.school-hero-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.school-hero-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 20s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

.school-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.school-hero-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.school-logo-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
}

.school-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 1rem;
}

.school-logo-placeholder {
    font-size: 2rem;
    color: white;
}

.school-hero-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.school-hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.school-hero-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
    display: flex;
    align-items: center;
}

.school-hero-role {
    font-size: 0.875rem;
    opacity: 0.8;
    margin: 0;
    display: flex;
    align-items: center;
}

.school-hero-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.school-status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    backdrop-filter: blur(10px);
}

.school-status-badge.status-active {
    background: rgba(16, 185, 129, 0.3);
    border: 1px solid rgba(16, 185, 129, 0.5);
}

.school-status-badge.status-inactive {
    background: rgba(239, 68, 68, 0.3);
    border: 1px solid rgba(239, 68, 68, 0.5);
}

.school-stats-mini {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.mini-stat {
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 70px;
}

.mini-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.mini-stat-label {
    font-size: 0.75rem;
    opacity: 0.9;
}

.school-settings-btn {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
    color: white;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.school-settings-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-modern {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.stat-card-modern:hover::before {
    width: 100%;
    opacity: 0.1;
}

.stat-card-primary::before { background: #3b82f6; }
.stat-card-success::before { background: #10b981; }
.stat-card-warning::before { background: #f59e0b; }
.stat-card-info::before { background: #06b6d4; }
.stat-card-secondary::before { background: #8b5cf6; }
.stat-card-accent::before { background: #ec4899; }
.stat-card-teal::before { background: #14b8a6; }

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-icon-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-icon-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.stat-icon-secondary { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-icon-accent { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
.stat-icon-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

.stat-trend {
    color: #10b981;
    font-size: 0.875rem;
}

.stat-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.stat-value-modern {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    line-height: 1;
}

.stat-label-modern {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.stat-sublabel-modern {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.25rem;
}

.stat-badge-active,
.stat-badge-success,
.stat-badge-info,
.stat-badge-secondary {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-badge-active { background: #dbeafe; color: #1e40af; }
.stat-badge-success { background: #d1fae5; color: #065f46; }
.stat-badge-info { background: #dbeafe; color: #1e40af; }
.stat-badge-secondary { background: #e9d5ff; color: #6b21a8; }

/* Dashboard Section Card */
.dashboard-section-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.section-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-icon {
    font-size: 1.25rem;
    color: #3b82f6;
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.view-all-link {
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: color 0.2s;
}

.view-all-link:hover {
    color: #2563eb;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
    background: white;
    gap: 1rem;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    border-color: #3b82f6;
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

.quick-action-icon-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.quick-action-icon-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.quick-action-icon-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.quick-action-icon-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.quick-action-icon-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.quick-action-icon-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.quick-action-icon-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
.quick-action-icon-slate { background: linear-gradient(135deg, #1f2937 0%, #374151 100%); }

.quick-action-content {
    flex: 1;
}

.quick-action-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.quick-action-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

.quick-action-arrow {
    color: #9ca3af;
    transition: all 0.3s ease;
}

.quick-action-card:hover .quick-action-arrow {
    color: #3b82f6;
    transform: translateX(4px);
}

/* Modern Table */
.table-container {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: #f9fafb;
}

.modern-table th {
    padding: 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.modern-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.modern-table tbody tr:hover {
    background: #f9fafb;
}

.modern-table td {
    padding: 1rem;
    font-size: 0.875rem;
    color: #1a1a1a;
}

.table-cell-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.table-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.table-name {
    font-weight: 500;
    color: #1a1a1a;
}

.table-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.table-badge-blue {
    background: #dbeafe;
    color: #1e40af;
}

.table-badge-success {
    background: #d1fae5;
    color: #065f46;
}

.table-badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .school-hero-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .school-hero-right {
        align-items: flex-start;
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
}

@media (max-width: 768px) {
    .school-hero-title {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .table-container {
        overflow-x: scroll;
    }
    
    .modern-table {
        min-width: 600px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/school/dashboard.blade.php ENDPATH**/ ?>