<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Naf Academy')); ?> - <?php echo e(auth()->user()->account_type === 'teacher' ? 'Teacher' : 'Student'); ?> Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard.css')); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Mobile Header -->
        <header class="mobile-header">
            <button id="hamburgerMenu" class="hamburger-menu" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="mobile-title"><?php echo e(auth()->user()->account_type === 'teacher' ? 'Teacher' : 'Student'); ?> Dashboard</span>
            <button class="mobile-profile" id="mobileProfileBtn">
                <i class="fas fa-user"></i>
            </button>
            <div id="mobileProfileDropdown" class="mobile-profile-dropdown">
                <a href="<?php echo e(route('student.profile')); ?>" class="dropdown-item">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="dropdown-item logout-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Signout</button>
                </form>
            </div>
        </header>

        <!-- Sidebar Overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand"><?php echo e(config('app.name')); ?></div>
                <button id="closeSidebar" class="close-sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <a href="<?php echo e(route('student.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <a href="<?php echo e(route('student.resources.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.resources.*') ? 'active' : ''); ?>">
                    <i class="fas fa-book-open"></i>
                    <span>My Resources</span>
                </a>

                <a href="<?php echo e(route('student.assignments.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.assignments.*') ? 'active' : ''); ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span>My Assignments</span>
                </a>

                <a href="<?php echo e(route('student.performance.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.performance.*') ? 'active' : ''); ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>My Performance</span>
                </a>

                

                <?php if(!Auth::user()->school_id && !in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                <a href="<?php echo e(route('student.sample-videos.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.sample-videos.*') ? 'active' : ''); ?>">
                    <i class="fas fa-video"></i>
                    <span>Sample Videos</span>
                </a>
                <?php endif; ?>

                <?php if($hasActiveSubscription || in_array(session('user_type'), ['teacher', 'subject_teacher']) || (Auth::user()->account_type === 'student' && Auth::user()->school_id) || Auth::user()->account_type === 'parent'): ?>
                    <a href="<?php echo e(Auth::user()->account_type === 'parent' ? route('parent.my-videos') : route('student.my-videos')); ?>" class="sidebar-item <?php echo e((Auth::user()->account_type === 'parent' ? request()->routeIs('parent.my-videos') : request()->routeIs('student.my-videos')) ? 'active' : ''); ?>">
                        <i class="fas fa-play-circle"></i>
                        <span>My Videos</span>
                    </a>

                    <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                    <a href="<?php echo e(route('student.assignments.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.assignments.*') ? 'active' : ''); ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Student Assignments</span>
                    </a>
                    <a href="<?php echo e(route('student.assessments.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.assessments.index') || request()->routeIs('student.assessments.show') ? 'active' : ''); ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Assessment Management</span>
                    </a>
                    <a href="<?php echo e(route('student.assessments.create')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.assessments.create') ? 'active' : ''); ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Upload Assignment</span>
                    </a>
                    <a href="<?php echo e(route('teacher.assigned-videos')); ?>" class="sidebar-item <?php echo e(request()->routeIs('teacher.assigned-videos') ? 'active' : ''); ?>">
                        <i class="fas fa-video"></i>
                        <span>Assigned Videos</span>
                    </a>
                    <?php endif; ?>

                    <?php if(!in_array(session('user_type'), ['teacher', 'subject_teacher']) && !Auth::user()->school_id): ?>
                    <a href="<?php echo e(route('student.preferences.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.preferences.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cog"></i>
                        <span>Learning Preferences</span>
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(Auth::user()->account_type === 'student' && Auth::user()->school_id): ?>
                        <a href="<?php echo e(route('student.my-videos')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.my-videos') ? 'active' : ''); ?>">
                            <i class="fas fa-play-circle"></i>
                            <span>My Videos</span>
                        </a>
                    <?php else: ?>
                        <?php if(session('impersonator_id')): ?>
                            <a href="<?php echo e(route('student.my-videos')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.my-videos') ? 'active' : ''); ?>">
                                <i class="fas fa-play-circle"></i>
                                <span>My Videos</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('pricing')); ?>" class="sidebar-item">
                                <i class="fas fa-play-circle"></i>
                                <span>My Videos</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(!Auth::user()->school_id && Auth::user()->account_type !== 'parent'): ?>
                    <a href="<?php echo e(route('pricing')); ?>" class="sidebar-item">
                        <i class="fas fa-cog"></i>
                        <span>Learning Preferences</span>
                    </a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(!in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                <a href="<?php echo e(route('student.marks.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.marks.*') ? 'active' : ''); ?>">
                    <i class="fas fa-clipboard-check"></i>
                    <span>My Marks</span>
                </a>
                <a href="<?php echo e(route('student.course-recommendations.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.course-recommendations.*', 'student.career-guidance.*') ? 'active' : ''); ?>">
                    <i class="fas fa-lightbulb"></i>
                    <span>Career Guidance</span>
                </a>
                <?php endif; ?>

                <?php if(!in_array(session('user_type'), ['teacher', 'subject_teacher']) && !Auth::user()->school_id): ?>
                <a href="<?php echo e(route('student.subscription')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.subscription') ? 'active' : ''); ?>">
                    <i class="fas fa-receipt"></i>
                    <span>My Subscription</span>
                </a>
                <?php endif; ?>

                <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher', 'student'])): ?>
                    <a href="<?php echo e(route('student.notifications')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.notifications') ? 'active' : ''); ?>">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <span id="notification-count" class="notification-badge" style="display: none;">0</span>
                    </a>
                <?php endif; ?>

                <?php if(session('user_type') === 'student'): ?>
                    <a href="<?php echo e(route('student.chat.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.chat.*') ? 'active' : ''); ?>">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                        <span id="chat-unread-count" class="notification-badge" style="display: none;">0</span>
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('student.projects.groups.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.projects.groups.*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Groups</span>
                </a>

                <a href="<?php echo e(route('student.projects.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.projects.*') && !request()->routeIs('student.projects.groups.*') ? 'active' : ''); ?>">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>

                <a href="<?php echo e(route('student.profile')); ?>" class="sidebar-item <?php echo e(request()->routeIs('student.profile') ? 'active' : ''); ?>">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <span class="user-name"><?php echo e(Auth::user()->name); ?></span>
                    <span class="user-role"><?php echo e(Auth::user()->account_type); ?></span>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="logout-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="sidebar-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Desktop Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <button id="sidebarToggle" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="header-title"><?php echo e(auth()->user()->account_type === 'teacher' ? 'Teacher' : 'Student'); ?> Dashboard</span>
                </div>
                <div class="header-right">
                    <div class="header-user profile-dropdown" id="profileDropdown">
                        <span class="header-username profile-dropdown-toggle" id="profileDropdownToggleName"><?php echo e(Auth::user()->name); ?></span>
                        <div class="user-avatar">
                            <?php if(Auth::user()->profile_photo_path): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo_path)); ?>" alt="Profile Photo" class="header-avatar profile-avatar-img profile-avatar-navbar">
                            <?php else: ?>
                                <span class="header-avatar profile-avatar-img profile-avatar-navbar" style="background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:1.1em;">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="profile-dropdown-menu" id="profileDropdownMenu">
                            <a href="<?php echo e(route('student.profile')); ?>" class="profile-dropdown-item">
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
            </header>

            <!-- Page Content -->
            <main class="dashboard-content">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <style>
    /* Base Styles */
    .dashboard-body {
        margin: 0;
        font-family: 'Figtree', sans-serif;
        background: #f4f6fa;
        min-height: 100vh;
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
        background: white;
        padding: 0 1rem;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .mobile-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .hamburger-menu, .mobile-profile {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: #4b5563;
        padding: 0.5rem;
        cursor: pointer;
    }

    /* Sidebar */
    .dashboard-sidebar {
        width: 260px;
        background: white;
        border-right: 1px solid #e5e7eb;
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
        border-bottom: 1px solid #e5e7eb;
    }

    .sidebar-brand {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2563eb;
    }

    .close-sidebar {
        display: none;
        background: none;
        border: none;
        color: #6b7280;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .sidebar-menu {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.2s;
    }

    .sidebar-item i {
        width: 1.5rem;
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    .sidebar-item.active {
        background: #f3f4f6;
        color: #2563eb;
        font-weight: 500;
    }

    .sidebar-item {
        position: relative;
    }

    .notification-badge {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: #ef4444;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0 6px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .user-info {
        margin-bottom: 1rem;
    }

    .user-name {
        display: block;
        font-weight: 500;
        color: #1a1a1a;
        margin-bottom: 0.25rem;
    }

    .user-role {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .logout-form {
        margin: 0;
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
        border-radius: 0.375rem;
        transition: background 0.2s;
    }

    .sidebar-logout i {
        margin-right: 0.75rem;
    }

    .sidebar-logout:hover {
        background: #fee2e2;
    }

    /* Main Content */
    .dashboard-main {
        flex: 1;
        margin-left: 260px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .dashboard-header {
        height: 64px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
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
        color: #4b5563;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .header-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .header-right {
        display: flex;
        align-items: center;
    }

    .header-user {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-username {
        font-weight: 500;
        color: #1a1a1a;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .dashboard-content {
        flex: 1;
        padding: 1.5rem;
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

    /* Mobile Styles */
    @media (max-width: 768px) {
        .mobile-header {
            display: flex;
        }

        .dashboard-sidebar {
            transform: translateX(-100%);
        }

        .dashboard-sidebar.open {
            transform: translateX(0);
        }

        .close-sidebar {
            display: block;
        }

        .dashboard-main {
            margin-left: 0;
            padding-top: 60px;
        }

        .dashboard-header {
            display: none;
        }

        .dashboard-content {
            padding: 1rem;
        }

        .sidebar-overlay.show {
            display: block;
        }
    }

    .mobile-profile-dropdown {
        display: none;
        position: absolute;
        top: 60px;
        right: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        min-width: 160px;
        z-index: 1100;
        flex-direction: column;
        padding: 0.5rem 0;
    }
    .mobile-profile-dropdown.show {
        display: flex;
    }
    .mobile-profile-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: #374151;
        text-decoration: none;
        font-size: 1rem;
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: background 0.2s;
    }
    .mobile-profile-dropdown .dropdown-item:hover {
        background: #f3f4f6;
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
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        cursor: pointer;
        padding: 0;
    }
    .mobile-profile-dropdown button[type="submit"]:hover {
        background: #fee2e2;
    }
    @media (max-width: 768px) {
        .mobile-profile-dropdown {
            right: 1rem;
            left: auto;
        }
    }
    </style>

    <?php $__env->startPush('scripts'); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu functionality
        const hamburgerMenu = document.getElementById('hamburgerMenu');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const mobileProfileBtn = document.getElementById('mobileProfileBtn');
        const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');

        // Mobile hamburger menu click
        if (hamburgerMenu) {
            hamburgerMenu.addEventListener('click', function() {
                sidebar.classList.add('open');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        // Desktop sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Close sidebar when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }

        // Close sidebar when close button is clicked
        if (closeSidebar) {
            closeSidebar.addEventListener('click', function() {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }

        // Mobile profile dropdown
        if (mobileProfileBtn && mobileProfileDropdown) {
            mobileProfileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileProfileDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                mobileProfileDropdown.classList.remove('active');
            });
        }

        // Load notification count for teachers and students
        <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher', 'student'])): ?>
        loadNotificationCount();
        
        // Refresh notification count every 30 seconds
        setInterval(loadNotificationCount, 30000);
        <?php endif; ?>

        // Load chat unread count for students
        <?php if(session('user_type') === 'student'): ?>
        loadChatUnreadCount();
        
        // Refresh chat unread count every 30 seconds
        setInterval(loadChatUnreadCount, 30000);
        <?php endif; ?>
    });

    function loadNotificationCount() {
        fetch('/student/notifications/count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-count');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading notification count:', error);
            });
    }

    function loadChatUnreadCount() {
        console.log('Loading chat unread count...');
        fetch('/student/chat/unread-count')
            .then(response => {
                console.log('Chat unread count response:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Chat unread count data:', data);
                const badge = document.getElementById('chat-unread-count');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                        console.log('✅ Showing chat badge with count:', data.count);
                    } else {
                        badge.style.display = 'none';
                        console.log('❌ Hiding chat badge - no unread messages');
                    }
                } else {
                    console.error('❌ Chat unread count badge element not found');
                }
            })
            .catch(error => {
                console.error('❌ Error loading chat unread count:', error);
            });
    }
    </script>
    <?php $__env->stopPush(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html> <?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/layouts/student-dashboard.blade.php ENDPATH**/ ?>