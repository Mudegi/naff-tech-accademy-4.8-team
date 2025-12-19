

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner staff-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                Staff Management
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Staff</span>
            </div>
        </div>
        <a href="<?php echo e(route('admin.school.staff.create')); ?>" class="btn-modern btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Staff Member
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert-modern alert-success animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert-modern alert-error animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('show_credentials') && session('new_staff_credentials')): ?>
        <?php
            $credentials = session('new_staff_credentials');
        ?>
        <div class="credentials-card credentials-card-new">
            <div class="credentials-header">
                <div class="credentials-icon-wrapper">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="credentials-title-group">
                    <h3 class="credentials-title">New Staff Member Created</h3>
                    <p class="credentials-subtitle">Login Credentials</p>
                </div>
                <button type="button" onclick="this.closest('.credentials-card').remove()" class="credentials-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="credentials-body">
                <div class="credentials-info-grid">
                    <div class="credentials-info-section">
                        <h4 class="credentials-section-title">
                            <i class="fas fa-user mr-2"></i>
                            Staff Information
                        </h4>
                        <div class="credentials-info-list">
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Name:</span>
                                <span class="credentials-info-value"><?php echo e($credentials['name']); ?></span>
                            </div>
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Email:</span>
                                <span class="credentials-info-value"><?php echo e($credentials['email']); ?></span>
                            </div>
                            <?php if($credentials['phone_number']): ?>
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Phone:</span>
                                <span class="credentials-info-value"><?php echo e($credentials['phone_number']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Role:</span>
                                <span class="credentials-info-value credentials-role-badge"><?php echo e(ucfirst(str_replace('_', ' ', $credentials['account_type']))); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="credentials-login-section">
                        <h4 class="credentials-section-title">
                            <i class="fas fa-key mr-2"></i>
                            Login Credentials
                        </h4>
                        <div class="credentials-login-list">
                            <div class="credentials-login-item">
                                <span class="credentials-login-label">Email:</span>
                                <code class="credentials-code" id="new-email-display"><?php echo e($credentials['email']); ?></code>
                            </div>
                            <div class="credentials-login-item">
                                <span class="credentials-login-label">Password:</span>
                                <code class="credentials-code" id="password-display"><?php echo e($credentials['password']); ?></code>
                            </div>
                            <button type="button" onclick="copyCredentials()" class="btn-modern btn-primary btn-sm">
                                <i class="fas fa-copy mr-2"></i> Copy Credentials
                            </button>
                        </div>
                    </div>
                </div>
                <div class="credentials-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Important:</strong> Please save these credentials securely. The password cannot be retrieved later. 
                        The staff member can change their password after logging in.
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('show_updated_credentials') && session('updated_staff_credentials')): ?>
        <?php
            $credentials = session('updated_staff_credentials');
        ?>
        <div class="credentials-card credentials-card-updated">
            <div class="credentials-header">
                <div class="credentials-icon-wrapper">
                    <i class="fas fa-key"></i>
                </div>
                <div class="credentials-title-group">
                    <h3 class="credentials-title">Password Updated</h3>
                    <p class="credentials-subtitle">New Login Credentials</p>
                </div>
                <button type="button" onclick="this.closest('.credentials-card').remove()" class="credentials-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="credentials-body">
                <div class="credentials-info-grid">
                    <div class="credentials-info-section">
                        <h4 class="credentials-section-title">
                            <i class="fas fa-user mr-2"></i>
                            Staff Information
                        </h4>
                        <div class="credentials-info-list">
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Name:</span>
                                <span class="credentials-info-value"><?php echo e($credentials['name']); ?></span>
                            </div>
                            <div class="credentials-info-item">
                                <span class="credentials-info-label">Email:</span>
                                <span class="credentials-info-value"><?php echo e($credentials['email']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="credentials-login-section">
                        <h4 class="credentials-section-title">
                            <i class="fas fa-key mr-2"></i>
                            New Login Credentials
                        </h4>
                        <div class="credentials-login-list">
                            <div class="credentials-login-item">
                                <span class="credentials-login-label">Email:</span>
                                <code class="credentials-code" id="updated-email-display"><?php echo e($credentials['email']); ?></code>
                            </div>
                            <div class="credentials-login-item">
                                <span class="credentials-login-label">New Password:</span>
                                <code class="credentials-code" id="updated-password-display"><?php echo e($credentials['password']); ?></code>
                            </div>
                            <button type="button" onclick="copyUpdatedCredentials()" class="btn-modern btn-primary btn-sm">
                                <i class="fas fa-copy mr-2"></i> Copy Credentials
                            </button>
                        </div>
                    </div>
                </div>
                <div class="credentials-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Important:</strong> Please share these new credentials with the staff member securely. 
                        The password cannot be retrieved later.
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Section -->
    <div class="search-filter-card">
        <form method="GET" action="<?php echo e(route('admin.school.staff.index')); ?>" class="search-filter-form">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           value="<?php echo e(request('search')); ?>" 
                           placeholder="Search by name, email, or phone..." 
                           class="search-input">
                </div>
                <select name="account_type" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="director_of_studies" <?php echo e(request('account_type') == 'director_of_studies' ? 'selected' : ''); ?>>Director of Studies</option>
                    <option value="head_of_department" <?php echo e(request('account_type') == 'head_of_department' ? 'selected' : ''); ?>>Head of Department</option>
                    <option value="subject_teacher" <?php echo e(request('account_type') == 'subject_teacher' ? 'selected' : ''); ?>>Subject Teacher</option>
                </select>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                <?php if(request('search') || request('account_type')): ?>
                    <a href="<?php echo e(route('admin.school.staff.index')); ?>" class="btn-modern btn-secondary">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Staff Table -->
    <div class="table-card-modern">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-users table-header-icon"></i>
                <h3 class="table-header-title">All Staff Members</h3>
                <span class="table-count-badge"><?php echo e($staff->total()); ?> <?php echo e(Str::plural('Member', $staff->total())); ?></span>
            </div>
        </div>
        <div class="table-container-modern">
            <div class="table-responsive">
                <table class="table-modern">
                <thead>
                    <tr>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-user mr-2"></i>
                                Staff Member
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-envelope mr-2"></i>
                                Email
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-phone mr-2"></i>
                                Phone
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-user-tag mr-2"></i>
                                Role
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-building mr-2"></i>
                                Department
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-toggle-on mr-2"></i>
                                Status
                            </div>
                        </th>
                        <th>
                            <div class="table-th-content">
                                <i class="fas fa-cog mr-2"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row-modern">
                        <td>
                            <div class="staff-cell">
                                <div class="staff-avatar">
                                    <span><?php echo e(strtoupper(substr($member->name, 0, 2))); ?></span>
                                </div>
                                <div class="staff-name"><?php echo e($member->name); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="table-cell-text">
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                <?php echo e($member->email); ?>

                            </div>
                        </td>
                        <td>
                            <div class="table-cell-text">
                                <?php if($member->phone_number): ?>
                                    <i class="fas fa-phone mr-1 text-gray-400"></i>
                                    <?php echo e($member->phone_number); ?>

                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php
                                $roleColors = [
                                    'director_of_studies' => 'role-dos',
                                    'head_of_department' => 'role-hod',
                                    'subject_teacher' => 'role-teacher',
                                    'school_admin' => 'role-admin',
                                ];
                                $roleClass = $roleColors[$member->account_type] ?? 'role-default';
                            ?>
                            <span class="role-badge <?php echo e($roleClass); ?>">
                                <i class="fas fa-user-tag mr-1"></i>
                                <?php echo e(ucfirst(str_replace('_', ' ', $member->account_type))); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($member->department): ?>
                                <a href="<?php echo e(route('admin.school.departments.show', $member->department->id)); ?>" class="department-link-badge">
                                    <i class="fas fa-building mr-1"></i>
                                    <?php echo e($member->department->name); ?>

                                </a>
                            <?php else: ?>
                                <span class="not-assigned">
                                    <i class="fas fa-minus-circle mr-1"></i>
                                    Not Assigned
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge-modern <?php echo e($member->is_active ? 'status-active-modern' : 'status-inactive-modern'); ?>">
                                <i class="fas fa-circle mr-1"></i>
                                <?php echo e($member->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.school.staff.edit', $member->id)); ?>" class="action-btn action-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if(in_array($member->account_type, ['teacher', 'subject_teacher']) && (Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())): ?>
                                <a href="<?php echo e(route('admin.school.staff.assign-classes', $member->id)); ?>" class="action-btn action-assign" title="Assign Classes" style="background: #10b981; color: white;">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </a>
                                <?php endif; ?>
                                <?php if(Auth::user()->canManageUser($member) && $member->id !== Auth::id()): ?>
                                <form action="<?php echo e(route('admin.school.staff.destroy', $member->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="action-btn action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-content">
                                <div class="empty-state-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="empty-state-title">No Staff Members Found</h3>
                                <p class="empty-state-text">
                                    <?php if(request('search') || request('account_type')): ?>
                                        No staff members match your search criteria. Try adjusting your filters.
                                    <?php else: ?>
                                        Get started by adding your first staff member to the school.
                                    <?php endif; ?>
                                </p>
                                <a href="<?php echo e(route('admin.school.staff.create')); ?>" class="btn-modern btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Add Staff Member
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($staff->hasPages()): ?>
    <div class="pagination-wrapper">
        <?php echo e($staff->appends(request()->query())->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('styles'); ?>
<style>
/* Staff Page Styles */
.staff-page {
    padding: 1.5rem;
}

/* Credentials Card */
.credentials-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    position: relative;
    animation: slideDown 0.3s ease;
}

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

.credentials-card-new {
    border-left: 4px solid #3b82f6;
}

.credentials-card-updated {
    border-left: 4px solid #10b981;
}

.credentials-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.credentials-icon-wrapper {
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

.credentials-card-new .credentials-icon-wrapper {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.credentials-card-updated .credentials-icon-wrapper {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.credentials-title-group {
    flex: 1;
}

.credentials-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.25rem 0;
}

.credentials-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.credentials-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.credentials-close-btn:hover {
    background: #e5e7eb;
    color: #1a1a1a;
}

.credentials-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.credentials-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.credentials-info-section,
.credentials-login-section {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
}

.credentials-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.credentials-info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.credentials-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.credentials-info-item:last-child {
    border-bottom: none;
}

.credentials-info-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.credentials-info-value {
    font-size: 0.875rem;
    color: #1a1a1a;
    font-weight: 600;
}

.credentials-role-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #eff6ff;
    color: #1e40af;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.credentials-login-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.credentials-login-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.credentials-login-label {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
}

.credentials-code {
    display: block;
    padding: 0.625rem 0.875rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1a1a1a;
    word-break: break-all;
}

.credentials-warning {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 0.5rem;
    border-left: 3px solid #f59e0b;
}

.credentials-warning i {
    color: #f59e0b;
    font-size: 1.125rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.credentials-warning div {
    font-size: 0.8125rem;
    color: #92400e;
    line-height: 1.5;
}

/* Staff Cell */
.staff-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.staff-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.staff-name {
    font-weight: 600;
    color: #1a1a1a;
}

/* Role Badges */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.625rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.role-dos {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.role-hod {
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #3730a3;
}

.role-teacher {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.role-admin {
    background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
    color: #9f1239;
}

.role-default {
    background: #f3f4f6;
    color: #4b5563;
}

/* Department Link Badge */
.department-link-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #6b21a8;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.department-link-badge:hover {
    background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(139, 92, 246, 0.2);
}

/* Modern Buttons */
.btn-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
}

/* Alert Modern */
.alert-modern {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.animate-slide-down {
    animation: slideDown 0.3s ease;
}

/* Search and Filter Card */
.search-filter-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.search-filter-form {
    width: 100%;
}

.search-input-group {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.search-input-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select {
    padding: 0.625rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 150px;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table Card Modern */
.table-card-modern {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-header-modern {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-bottom: 2px solid #e5e7eb;
}

.table-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.table-header-icon {
    font-size: 1rem;
    color: #3b82f6;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
    border-radius: 0.5rem;
}

.table-header-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.table-count-badge {
    padding: 0.25rem 0.75rem;
    background: #3b82f6;
    color: white;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.table-container-modern {
    overflow-x: auto;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.table-modern thead {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.table-modern th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.table-modern th:nth-child(1) { width: 18%; }
.table-modern th:nth-child(2) { width: 22%; }
.table-modern th:nth-child(3) { width: 12%; }
.table-modern th:nth-child(4) { width: 15%; }
.table-modern th:nth-child(5) { width: 15%; }
.table-modern th:nth-child(6) { width: 10%; }
.table-modern th:nth-child(7) { width: 8%; }

.table-th-content {
    display: flex;
    align-items: center;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.table-row-modern:hover {
    background: #f9fafb;
    transform: scale(1.001);
}

.table-modern td {
    padding: 1rem 1rem;
    font-size: 0.8125rem;
}

.table-cell-text {
    display: flex;
    align-items: center;
    color: #4b5563;
}

/* Status Badges */
.status-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.625rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-active-modern {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.status-inactive-modern {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.75rem;
}

.action-edit {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.action-edit:hover {
    background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.action-delete {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.action-delete:hover {
    background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
    padding: 3rem 1.5rem;
}

.empty-state-content {
    text-align: center;
    max-width: 400px;
    margin: 0 auto;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 2rem;
}

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

/* Not Assigned */
.not-assigned {
    display: inline-flex;
    align-items: center;
    color: #9ca3af;
    font-size: 0.8125rem;
    font-style: italic;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .credentials-info-grid {
        grid-template-columns: 1fr;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .search-input-wrapper {
        min-width: 100%;
    }
    
    .filter-select {
        width: 100%;
    }
}
</style>
<?php $__env->stopPush(); ?>

<script>
function copyCredentials() {
    const email = document.getElementById('new-email-display')?.textContent || '<?php echo e(session("new_staff_credentials.email") ?? ""); ?>';
    const password = document.getElementById('password-display')?.textContent || '<?php echo e(session("new_staff_credentials.password") ?? ""); ?>';
    const text = `Login Credentials:\nEmail: ${email}\nPassword: ${password}`;
    
    navigator.clipboard.writeText(text).then(function() {
        // Show success notification
        showNotification('Credentials copied to clipboard!', 'success');
    }, function(err) {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showNotification('Credentials copied to clipboard!', 'success');
    });
}

function copyUpdatedCredentials() {
    const email = document.getElementById('updated-email-display')?.textContent || '<?php echo e(session("updated_staff_credentials.email") ?? ""); ?>';
    const password = document.getElementById('updated-password-display')?.textContent || '<?php echo e(session("updated_staff_credentials.password") ?? ""); ?>';
    const text = `Updated Login Credentials:\nEmail: ${email}\nNew Password: ${password}`;
    
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Credentials copied to clipboard!', 'success');
    }, function(err) {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showNotification('Credentials copied to clipboard!', 'success');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
        ${message}
    `;
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Hide and remove notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<?php $__env->startPush('styles'); ?>
<style>
/* Notification Styles */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    display: flex;
    align-items: center;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification-success {
    background: #10b981;
    color: white;
}

.notification-error {
    background: #ef4444;
    color: white;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/school/staff/index.blade.php ENDPATH**/ ?>