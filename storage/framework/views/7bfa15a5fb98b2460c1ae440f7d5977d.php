<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Naf Academy')); ?> - Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/responsive.css')); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Mobile Header -->
        <header class="mobile-header">
            <?php if(auth()->guard()->check()): ?>
            <button id="hamburgerMenu" class="hamburger-menu" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <?php endif; ?>
            <span class="mobile-title">
                <?php if(Auth::check() && (Auth::user()->account_type === 'teacher' || Auth::user()->account_type === 'subject_teacher')): ?>
                    Teacher Dashboard
                <?php elseif(Auth::check() && Auth::user()->account_type === 'student'): ?>
                    Student Dashboard
                <?php elseif(Auth::check() && Auth::user()->account_type === 'parent'): ?>
                    Parent Portal
                <?php else: ?>
                    Admin Dashboard
                <?php endif; ?>
            </span>
            <?php if(auth()->guard()->check()): ?>
            <div class="mobile-profile" id="mobileProfileBtn">
                <div class="user-avatar">
                    <?php if(Auth::user()->profile_photo_path && file_exists(public_path('storage/' . Auth::user()->profile_photo_path))): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo_path)); ?>" alt="Profile Photo" class="header-avatar">
                    <?php else: ?>
                        <span class="avatar-fallback">
                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if(auth()->guard()->check()): ?>
            <div id="mobileProfileDropdown" class="mobile-profile-dropdown">
                <a href="<?php echo e(route('profile')); ?>" class="dropdown-item">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="dropdown-item logout-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Signout</button>
                </form>
            </div>
            <?php endif; ?>
        </header>
        <!-- Sidebar Overlay -->
        <?php if(auth()->guard()->check()): ?>
        <div id="sidebarOverlay" class="sidebar-overlay"></div>
        <!-- Sidebar -->
        <aside id="sidebar" class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand"><?php echo e(config('app.name')); ?></div>
                <button id="closeSidebar" class="close-sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php
                $user = Auth::user();
                $userPermissions = [];
                $isSuperAdmin = false;
                $isAdmin = false;
                
                if ($user) {
                    // Check if user is super admin or regular admin
                    $isSuperAdmin = ($user->account_type === 'admin' && !$user->school_id);
                    $isAdmin = ($user->account_type === 'admin');
                    
                    // Get permissions from roles
                    $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
                    $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
                    $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
                    
                    // Super admin and regular admin get all permissions by default
                    if ($isAdmin) {
                        // Get all permissions for admins
                        $allPermissions = DB::table('permissions')->pluck('name')->toArray();
                        $userPermissions = array_unique(array_merge($userPermissions, $allPermissions));
                    }
                }
            ?>
            <nav class="sidebar-nav">
                <?php if($user->account_type === 'teacher' || $user->account_type === 'subject_teacher'): ?>
                    <!-- Teacher Dashboard -->
                    <a href="<?php echo e(route('teacher.dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.dashboard') ? 'active' : ''); ?>">
                        <i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard
                    </a>
                    <a href="<?php echo e(route('teacher.resources.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.resources.index') ? 'active' : ''); ?>">
                        <i class="fas fa-folder-open"></i> My Resources
                    </a>
                    <a href="<?php echo e(route('teacher.resources.upload.form')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.resources.upload.form') ? 'active' : ''); ?>">
                        <i class="fas fa-upload"></i> Upload Resource
                    </a>
                <?php elseif($user->account_type === 'student'): ?>
                    <!-- Student Dashboard -->
                    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo e(route('student.resources.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('student.resources.index') ? 'active' : ''); ?>">
                        <i class="fas fa-book-open"></i> My Resources
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">📊 Dashboard</a>
                <?php endif; ?>
                <?php if((in_array('view_subject', $userPermissions) || in_array('view_topic', $userPermissions) || in_array('view_class', $userPermissions))): ?>
                    <div class="sidebar-dropdown">
                        <button class="sidebar-dropdown-btn <?php echo e(request()->routeIs('admin.subjects.*') || request()->routeIs('admin.topics.*') || request()->routeIs('admin.classes.*') ? 'active' : ''); ?>" type="button">
                            📚 Academic Settings
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sidebar-dropdown-content">
                <?php if(in_array('view_subject', $userPermissions)): ?>
                                <a href="<?php echo e(route('admin.subjects.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.subjects.*') ? 'active' : ''); ?>">Subjects</a>
                <?php endif; ?>
                <?php if(in_array('view_topic', $userPermissions)): ?>
                                <a href="<?php echo e(route('admin.topics.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.topics.*') ? 'active' : ''); ?>">Topics</a>
                <?php endif; ?>
                <?php if(in_array('view_class', $userPermissions)): ?>
                                <a href="<?php echo e(route('admin.classes.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.classes.*') ? 'active' : ''); ?>">Classes</a>
                <?php endif; ?>
                <?php if(in_array('view_term', $userPermissions)): ?>
                                <a href="<?php echo e(route('admin.terms.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.terms.*') ? 'active' : ''); ?>">
                                    <i class="fas fa-calendar-alt"></i> Terms
                                </a>
                <?php endif; ?>
                <?php if(in_array('view_resource', $userPermissions)): ?>
                                <a href="<?php echo e(route('admin.resources.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.resources.*') ? 'active' : ''); ?>">
                                    <i class="fas fa-book"></i> Resources
                                </a>
                <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(in_array('view_messages', $userPermissions)): ?>
                    <a href="#" class="sidebar-link">✉️ Messages</a>
                <?php endif; ?>
                
                <!-- Assignment Management - Only for School Admin, Director of Studies, and Head of Departments -->
                <?php if(in_array($user->account_type, ['school_admin', 'director_of_studies', 'head_of_department'])): ?>
                    <div class="sidebar-dropdown">
                        <button class="sidebar-dropdown-btn <?php echo e(request()->routeIs('admin.assignments.*') || request()->routeIs('admin.teacher-assignments.*') || request()->routeIs('student.assignments.*') ? 'active' : ''); ?>" type="button">
                            📋 Assignment Management
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sidebar-dropdown-content">
                            <a href="<?php echo e(route('admin.assignments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.assignments.*') ? 'active' : ''); ?>">
                                <i class="fas fa-clipboard-list"></i> Student Submissions
                            </a>
                            <a href="<?php echo e(route('admin.teacher-assignments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.teacher-assignments.*') ? 'active' : ''); ?>">
                                <i class="fas fa-tasks"></i> Teacher Assignments
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($user->account_type === 'teacher' || $user->account_type === 'subject_teacher'): ?>
                    <!-- Teacher Standalone Assignments -->
                    <a href="<?php echo e(route('teacher.standalone-assignments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.standalone-assignments.*') ? 'active' : ''); ?>">
                        <i class="fas fa-clipboard-list"></i> My Assignments
                    </a>
                    <!-- Teacher Marks Management -->
                    <a href="<?php echo e(route('teacher.marks.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.marks.*') ? 'active' : ''); ?>">
                        <i class="fas fa-chart-line"></i> Manage Student Marks
                    </a>
                    <!-- Teacher Parent Messages -->
                    <a href="<?php echo e(route('teacher.messages.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.messages.*') ? 'active' : ''); ?>">
                        <i class="fas fa-envelope"></i> Parent Messages
                        <span class="badge badge-danger" id="sidebar-unread-badge" style="display: none; margin-left: auto; font-size: 0.75rem;">0</span>
                    </a>
                    </a>
                    <!-- Teacher Assessments -->
                    <a href="<?php echo e(route('teacher.assessments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.assessments.*') ? 'active' : ''); ?>">
                        <i class="fas fa-clipboard-check"></i> My Assessments
                    </a>
                    <!-- Teacher Assignments -->
                    <a href="<?php echo e(route('teacher.assignments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.assignments.*') ? 'active' : ''); ?>">
                        <i class="fas fa-check-circle"></i> Review Assignments
                    </a>
                    <!-- Groups -->
                    <a href="<?php echo e(route('teacher.projects.groups.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.projects.groups.*') ? 'active' : ''); ?>">
                        <i class="fas fa-users"></i> Groups
                    </a>
                    <!-- Projects -->
                    <a href="<?php echo e(route('teacher.projects.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('teacher.projects.*') && !request()->routeIs('teacher.groups.*') ? 'active' : ''); ?>">
                        <i class="fas fa-project-diagram"></i> Projects
                    </a>
                <?php endif; ?>
                <?php if(in_array('view_users', $userPermissions)): ?>
                    <a href="#" class="sidebar-link">👥 Users</a>
                <?php endif; ?>
                
                <?php if((in_array('view_roles', $userPermissions) || in_array('view_permissions', $userPermissions) || $isSuperAdmin)): ?>
                    <div class="sidebar-dropdown">
                        <button class="sidebar-dropdown-btn <?php echo e(request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*') ? 'active' : ''); ?>" type="button">
                            🛡️ Roles & Permissions
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sidebar-dropdown-content">
                <?php if(in_array('view_roles', $userPermissions) || $isSuperAdmin): ?>
                                <a href="<?php echo e(route('admin.roles.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">Roles</a>
                <?php endif; ?>
                <?php if(in_array('view_permissions', $userPermissions) || $isSuperAdmin): ?>
                                <a href="<?php echo e(route('admin.permissions.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.permissions.*') ? 'active' : ''); ?>">Permissions</a>
                            <?php endif; ?>
                            <?php if(in_array('view_users', $userPermissions) || $isAdmin): ?>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">Users</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="sidebar-divider"></div>
                <?php if($isSuperAdmin): ?>
                    <a href="<?php echo e(route('admin.schools.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.schools.*') ? 'active' : ''); ?>">
                        <i class="fas fa-school"></i> Schools
                    </a>
                    <a href="<?php echo e(route('admin.school-subscriptions.pending')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.school-subscriptions.pending') ? 'active' : ''); ?>">
                        <i class="fas fa-check-circle"></i> Pending Approvals
                    </a>
                    <a href="<?php echo e(route('admin.university-cut-offs.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.university-cut-offs.*') ? 'active' : ''); ?>">
                        <i class="fas fa-graduation-cap"></i> University Programs
                    </a>
                <?php endif; ?>
                <?php if(in_array('view_subscription_package', $userPermissions) || $isSuperAdmin): ?>
                    <a href="<?php echo e(route('admin.subscription-packages.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.subscription-packages.*') ? 'active' : ''); ?>">
                        <i class="fas fa-box"></i> Subscription Packages
                    </a>
                    <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>">
                        <i class="fas fa-credit-card"></i> Subscriptions
                    </a>
                <?php endif; ?>
                <?php if(in_array('view_settings', $userPermissions) || $isSuperAdmin): ?>
                    <div class="sidebar-dropdown">
                        <button class="sidebar-dropdown-btn <?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>" type="button">
                            ⚙️ Settings
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sidebar-dropdown-content">
                            <a href="<?php echo e(route('admin.settings.company')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.company') ? 'active' : ''); ?>">
                                <i class="fas fa-building"></i> Company Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.flutterwave')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.flutterwave') ? 'active' : ''); ?>">
                                <i class="fas fa-credit-card"></i> Flutterwave Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.easypay')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.easypay') ? 'active' : ''); ?>">
                                <i class="fas fa-credit-card"></i> Easypay Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.sms')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.sms') ? 'active' : ''); ?>">
                                <i class="fas fa-sms"></i> SMS Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.footer')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.footer') ? 'active' : ''); ?>">
                                <i class="fas fa-shoe-prints"></i> Footer Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.contact')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.contact') ? 'active' : ''); ?>">
                                <i class="fas fa-address-book"></i> Contact Page Settings
                            </a>
                            <a href="<?php echo e(route('admin.settings.welcome')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.settings.welcome') ? 'active' : ''); ?>">
                                <i class="fas fa-home"></i> Welcome Page Settings
                            </a>
                            <a href="<?php echo e(route('admin.teams.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.teams.*') ? 'active' : ''); ?>">
                                <i class="fas fa-users"></i> Team Management
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
            <div class="sidebar-footer">
                <span class="sidebar-role">
                    <?php echo e(Auth::user() && Auth::user()->roles ? Auth::user()->roles->pluck('name')->join(', ') : ''); ?>

                </span>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="sidebar-logout">Logout</button>
                </form>
            </div>
        </aside>
        <?php endif; ?>
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Desktop Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <?php if(auth()->guard()->check()): ?>
                    <button id="sidebarToggle" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <?php endif; ?>
                    <span class="header-title">
                        <?php if(Auth::check() && (Auth::user()->account_type === 'teacher' || Auth::user()->account_type === 'subject_teacher')): ?>
                            Teacher Dashboard
                        <?php elseif(Auth::check() && Auth::user()->account_type === 'student'): ?>
                            Student Dashboard
                        <?php elseif(Auth::check() && Auth::user()->account_type === 'parent'): ?>
                            Parent Portal
                        <?php else: ?>
                            Admin Dashboard
                        <?php endif; ?>
                    </span>
                </div>
                <?php if(auth()->guard()->check()): ?>
                <div class="header-right">
                    <div class="header-user profile-dropdown" id="profileDropdown">
                        <span class="header-username profile-dropdown-toggle" id="profileDropdownToggleName"><?php echo e(Auth::user()->name); ?></span>
                        <div class="user-avatar">
                            <?php if(Auth::user()->profile_photo_path && file_exists(public_path('storage/' . Auth::user()->profile_photo_path))): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo_path)); ?>" alt="Profile Photo" class="header-avatar">
                            <?php else: ?>
                                <span class="avatar-fallback">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="profile-dropdown-menu" id="profileDropdownMenu">
                            <a href="<?php echo e(route('profile')); ?>" class="profile-dropdown-item">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="profile-dropdown-item logout">
                                    <i class="fas fa-sign-out-alt"></i> Signout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </header>
            <!-- Page Content -->
            <main class="dashboard-content">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    <style>
    /* Base Styles */
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --text-primary: #1a1a1a;
        --text-secondary: #4b5563;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --bg-tertiary: #f1f5f9;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    .dashboard-body {
        margin: 0;
        font-family: 'Figtree', sans-serif;
        background: var(--bg-secondary);
        min-height: 100vh;
        color: var(--text-primary);
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }

    /* Mobile Header */
    .mobile-header {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg-primary);
        padding: 0 1rem;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        z-index: 1000;
    }

    .mobile-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .hamburger-menu, .mobile-profile {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--text-secondary);
        padding: 0.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .hamburger-menu:hover, .mobile-profile:hover {
        color: var(--primary-color);
    }

    /* Sidebar */
    .dashboard-sidebar {
        width: 280px;
        background: var(--bg-primary);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1001;
        transition: transform 0.3s ease;
    }

    .sidebar-header {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-brand {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .close-sidebar {
        display: none;
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
        transition: color 0.2s;
    }

    .close-sidebar:hover {
        color: var(--text-primary);
    }

    .sidebar-nav {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .sidebar-link:hover {
        background: var(--bg-tertiary);
        color: var(--primary-color);
    }

    .sidebar-link.active {
        background: var(--bg-tertiary);
        color: var(--primary-color);
        font-weight: 500;
    }

    .sidebar-dropdown {
        margin: 0.25rem 0;
    }

    .sidebar-dropdown-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.875rem 1.5rem;
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .sidebar-dropdown-btn:hover {
        background: var(--bg-tertiary);
        color: var(--primary-color);
    }

    .sidebar-dropdown-btn.active {
        background: var(--bg-tertiary);
        color: var(--primary-color);
        font-weight: 500;
    }

    .sidebar-dropdown-content {
        display: none;
        padding: 0.5rem 0;
        background: var(--bg-tertiary);
    }

    .sidebar-dropdown.active .sidebar-dropdown-content {
        display: block;
    }

    .sidebar-dropdown-content .sidebar-link {
        padding-left: 3rem;
    }

    .sidebar-divider {
        height: 1px;
        background: var(--border-color);
        margin: 0.5rem 1.5rem;
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-tertiary);
    }

    .sidebar-role {
        display: block;
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }

    .sidebar-logout {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.75rem;
        background: none;
        border: none;
        color: #ef4444;
        font-weight: 500;
        cursor: pointer;
        border-radius: var(--radius-sm);
        transition: background 0.2s;
    }

    .sidebar-logout:hover {
        background: #fee2e2;
    }

    /* Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .sidebar-overlay.show {
        opacity: 1;
    }

    /* Main Content */
    .dashboard-main {
        flex: 1;
        margin-left: 280px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--bg-secondary);
    }

    /* Header Styles */
    .dashboard-header {
        height: 64px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
        transition: color 0.2s;
    }

    .sidebar-toggle:hover {
        color: var(--primary-color);
    }

    .header-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .header-right {
        display: flex;
        align-items: center;
    }

    .header-user {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        position: relative;
        user-select: none;
        padding: 0.5rem;
        border-radius: var(--radius-md);
        transition: background 0.2s;
    }

    .header-user:hover {
        background: var(--bg-tertiary);
    }

    .header-username {
        font-weight: 500;
        color: var(--text-primary);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--bg-tertiary);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        overflow: hidden;
        position: relative;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-avatar img:not([src]), 
    .user-avatar img[src=""],
    .user-avatar img[src="#"] {
        display: none;
    }

    .avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-color);
        color: white;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .mobile-profile .user-avatar {
        width: 32px;
        height: 32px;
    }

    .mobile-profile .avatar-fallback {
        font-size: 0.75rem;
    }

    .profile-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 120%;
        background: var(--bg-primary);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        min-width: 200px;
        z-index: 2000;
        flex-direction: column;
        padding: 0.5rem 0;
        border: 1px solid var(--border-color);
    }

    .profile-dropdown.show .profile-dropdown-menu {
        display: flex;
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.95rem;
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
    }

    .profile-dropdown-item:hover {
        background: var(--bg-tertiary);
        color: var(--primary-color);
    }

    .profile-dropdown-item.logout {
        color: #ef4444;
    }

    .profile-dropdown-item.logout:hover {
        background: #fee2e2;
    }

    .dashboard-content {
        flex: 1;
        padding: 2rem;
    }

    /* Mobile Styles */
    @media (max-width: 900px) {
        .mobile-header {
            display: flex;
        }

        .dashboard-header {
            display: none;
        }

        .dashboard-main {
            margin-left: 0;
            padding-top: 60px;
        }

        .dashboard-sidebar {
            transform: translateX(-100%);
        }

        .dashboard-sidebar.open-mobile {
            transform: translateX(0);
        }

        .close-sidebar {
            display: block;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .dashboard-content {
            padding: 1rem;
        }
    }

    /* Mobile Profile Dropdown */
    .mobile-profile-dropdown {
        display: none;
        position: absolute;
        top: 60px;
        right: 1rem;
        background: var(--bg-primary);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        min-width: 200px;
        z-index: 1100;
        flex-direction: column;
        padding: 0.5rem 0;
        border: 1px solid var(--border-color);
    }

    .mobile-profile-dropdown.show {
        display: flex;
    }

    .mobile-profile-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.95rem;
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
    }

    .mobile-profile-dropdown .dropdown-item:hover {
        background: var(--bg-tertiary);
        color: var(--primary-color);
    }

    .mobile-profile-dropdown .logout-form {
        margin: 0;
        width: 100%;
    }

    .mobile-profile-dropdown button[type="submit"] {
        background: none;
        border: none;
        color: #ef4444;
        font-weight: 500;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        cursor: pointer;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s;
    }

    .mobile-profile-dropdown button[type="submit"]:hover {
        background: #fee2e2;
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile dropdown functionality
        const profileDropdown = document.getElementById('profileDropdown');
        const profileDropdownMenu = document.getElementById('profileDropdownMenu');
        const profileDropdownToggleName = document.getElementById('profileDropdownToggleName');

        if (profileDropdown && profileDropdownMenu) {
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target)) {
                    profileDropdownMenu.classList.remove('show');
                }
            });
        }

        // Mobile profile dropdown functionality
        const mobileProfileBtn = document.getElementById('mobileProfileBtn');
        const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');

        if (mobileProfileBtn && mobileProfileDropdown) {
            mobileProfileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileProfileDropdown.classList.toggle('show');
            });

            // Close mobile dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!mobileProfileBtn.contains(e.target) && !mobileProfileDropdown.contains(e.target)) {
                    mobileProfileDropdown.classList.remove('show');
                }
            });
        }

        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const hamburgerMenu = document.getElementById('hamburgerMenu');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        function toggleSidebar() {
            sidebar.classList.toggle('open-mobile');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('open-mobile') ? 'hidden' : '';
        }

        if (hamburgerMenu) hamburgerMenu.addEventListener('click', toggleSidebar);
        if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);

        // Sidebar dropdowns (expand/collapse)
        const dropdownBtns = document.querySelectorAll('.sidebar-dropdown-btn');
        dropdownBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const parent = this.parentElement;
                parent.classList.toggle('active');
            });
        });

        // Set initial state based on active links
        const activeDropdowns = document.querySelectorAll('.sidebar-dropdown-content .active');
        activeDropdowns.forEach(activeLink => {
            const dropdown = activeLink.closest('.sidebar-dropdown');
            if (dropdown) {
                dropdown.classList.add('active');
            }
        });

        // Load unread message count for teachers
        <?php if(Auth::check() && (Auth::user()->account_type === 'teacher' || Auth::user()->account_type === 'subject_teacher')): ?>
        fetch('<?php echo e(route("teacher.messages.unread-count")); ?>')
            .then(response => response.json())
            .then(data => {
                const unreadCount = data.unread_count || 0;
                const sidebarBadge = document.getElementById('sidebar-unread-badge');
                
                if (unreadCount > 0 && sidebarBadge) {
                    sidebarBadge.textContent = unreadCount;
                    sidebarBadge.style.display = 'inline-block';
                }
            })
            .catch(error => console.error('Error loading message count:', error));
        <?php endif; ?>
    });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>