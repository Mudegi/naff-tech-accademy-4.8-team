<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AuthController;
use App\Models\ContactMessage;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\StudentDashboardController;
use App\Http\Controllers\Frontend\SampleVideoController;
use App\Http\Controllers\Frontend\UserPreferenceController;
use App\Http\Controllers\Student\VideoPlayController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\WelcomeSettingsController;
use App\Http\Controllers\ResourceCommentController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects');
Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/team-members', [HomeController::class, 'teamMembers'])->name('team.members');

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// School Registration Routes (Public)
Route::get('/school/register', [\App\Http\Controllers\SchoolController::class, 'showRegistrationForm'])->name('school.register');
Route::post('/school/register', [\App\Http\Controllers\SchoolController::class, 'register'])->name('school.register.submit');

// School Subscription Payment (Public - for new registrations)
Route::get('/school/subscription/{id}/payment', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'showPayment'])->name('school.subscription.payment');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Temporary route for admin to view contact messages
Route::get('/admin/contact-messages/{contactMessage}', function (ContactMessage $contactMessage) {
    return view('admin.contact-messages.show', compact('contactMessage'));
})->name('admin.contact-messages.show');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'single.session'])->name('dashboard');

Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->middleware(['auth', 'single.session'])->name('profile');
Route::post('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->middleware(['auth', 'single.session'])->name('profile.update');

Route::prefix('admin')->middleware(['auth', 'single.session'])->group(function () {
    // School Context Switching (for super admin)
    Route::get('/school-context/switch/{schoolId?}', [\App\Http\Controllers\Admin\SchoolContextController::class, 'switch'])->name('admin.school-context.switch');
    Route::get('/school-context/current', [\App\Http\Controllers\Admin\SchoolContextController::class, 'getCurrentContext'])->name('admin.school-context.current');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('subjects', [AdminSubjectController::class, 'index'])->name('admin.subjects.index');
    Route::get('subjects/create', [AdminSubjectController::class, 'create'])->name('admin.subjects.create');
    Route::post('subjects', [AdminSubjectController::class, 'store'])->name('admin.subjects.store');
    Route::get('subjects/{hash_id}/edit', [AdminSubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::put('subjects/{hash_id}', [AdminSubjectController::class, 'update'])->name('admin.subjects.update');
    Route::delete('subjects/{hash_id}', [AdminSubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    // Topics CRUD
    Route::get('topics', [\App\Http\Controllers\Admin\TopicController::class, 'index'])->name('admin.topics.index');
    Route::get('topics/create', [\App\Http\Controllers\Admin\TopicController::class, 'create'])->name('admin.topics.create');
    Route::post('topics', [\App\Http\Controllers\Admin\TopicController::class, 'store'])->name('admin.topics.store');
    Route::get('topics/{hash_id}/edit', [\App\Http\Controllers\Admin\TopicController::class, 'edit'])->name('admin.topics.edit');
    Route::put('topics/{hash_id}', [\App\Http\Controllers\Admin\TopicController::class, 'update'])->name('admin.topics.update');
    Route::delete('topics/{hash_id}', [\App\Http\Controllers\Admin\TopicController::class, 'destroy'])->name('admin.topics.destroy');

    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class, ['as' => 'admin']);
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class, ['as' => 'admin']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
    Route::post('users/{user}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('admin.users.impersonate');
    Route::get('users/stop-impersonating', [\App\Http\Controllers\Admin\UserController::class, 'stopImpersonating'])->name('admin.users.stop-impersonating');
    Route::patch('users/{user}/update-status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('admin.users.update-status');
    Route::get('student-parent-list', [\App\Http\Controllers\Admin\UserController::class, 'studentParentList'])->name('admin.users.student-parent-list');
    Route::get('export-student-parent-list', [\App\Http\Controllers\Admin\UserController::class, 'exportStudentParentList'])->name('admin.users.export-student-parent-list');
    Route::post('generate-missing-parent-accounts', [\App\Http\Controllers\Admin\UserController::class, 'generateMissingParentAccounts'])->name('admin.users.generate-missing-parent-accounts');
    Route::get('roles/{id}/edit-permissions', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin.roles.edit-permissions');

    // Parent-Student Linking Management
    Route::prefix('parent-student')->name('admin.parent-student.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ParentStudentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ParentStudentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ParentStudentController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ParentStudentController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ParentStudentController::class, 'destroy'])->name('destroy');
        Route::get('/search-parents', [\App\Http\Controllers\Admin\ParentStudentController::class, 'searchParents'])->name('search-parents');
        Route::get('/search-students', [\App\Http\Controllers\Admin\ParentStudentController::class, 'searchStudents'])->name('search-students');
        
        // Bulk Import
        Route::get('/bulk-import', [\App\Http\Controllers\Admin\ParentStudentController::class, 'bulkImport'])->name('bulk-import');
        Route::post('/bulk-import', [\App\Http\Controllers\Admin\ParentStudentController::class, 'processBulkImport'])->name('process-bulk-import');
        Route::get('/download-template', [\App\Http\Controllers\Admin\ParentStudentController::class, 'downloadTemplate'])->name('download-template');
    });

    // School Management Routes
    Route::prefix('school')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\SchoolAdminController::class, 'dashboard'])->name('admin.school.dashboard');
        
        // Director of Studies Dashboard
        Route::get('/director-of-studies/dashboard', [\App\Http\Controllers\Admin\DirectorOfStudiesController::class, 'dashboard'])->name('admin.director-of-studies.dashboard');
        Route::get('/director-of-studies/class-assignment-report', [\App\Http\Controllers\Admin\DirectorOfStudiesController::class, 'showClassAssignmentReport'])->name('admin.director-of-studies.class-assignment-report');
        Route::post('/director-of-studies/class-assignment-report/download', [\App\Http\Controllers\Admin\DirectorOfStudiesController::class, 'downloadClassAssignmentReport'])->name('admin.director-of-studies.download-class-assignment-report');
        Route::get('/settings', [\App\Http\Controllers\Admin\SchoolSettingsController::class, 'index'])->name('admin.school.settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\SchoolSettingsController::class, 'update'])->name('admin.school.settings.update');
        
        // Staff Management Routes
        Route::get('/staff', [\App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('admin.school.staff.index');
        Route::get('/staff/create', [\App\Http\Controllers\Admin\StaffManagementController::class, 'create'])->name('admin.school.staff.create');
        Route::post('/staff', [\App\Http\Controllers\Admin\StaffManagementController::class, 'store'])->name('admin.school.staff.store');
        Route::get('/staff/{id}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'edit'])->name('admin.school.staff.edit');
        Route::put('/staff/{id}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'update'])->name('admin.school.staff.update');
        Route::delete('/staff/{id}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroy'])->name('admin.school.staff.destroy');
        Route::get('/staff/{id}/assign-classes', [\App\Http\Controllers\Admin\StaffManagementController::class, 'assignClasses'])->name('admin.school.staff.assign-classes');
        Route::post('/staff/{id}/assign-classes', [\App\Http\Controllers\Admin\StaffManagementController::class, 'updateClasses'])->name('admin.school.staff.update-classes');
        
        // Department Management Routes
        Route::get('/departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('admin.school.departments.index');
        Route::get('/departments/create', [\App\Http\Controllers\Admin\DepartmentController::class, 'create'])->name('admin.school.departments.create');
        Route::post('/departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('admin.school.departments.store');
        Route::get('/departments/{id}', [\App\Http\Controllers\Admin\DepartmentController::class, 'show'])->name('admin.school.departments.show');
        Route::get('/departments/{id}/edit', [\App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('admin.school.departments.edit');
        Route::put('/departments/{id}', [\App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('admin.school.departments.update');
        Route::post('/departments/{id}/assign-teachers', [\App\Http\Controllers\Admin\DepartmentController::class, 'assignTeachers'])->name('admin.school.departments.assign-teachers');
        Route::delete('/departments/{id}/teachers/{teacherId}', [\App\Http\Controllers\Admin\DepartmentController::class, 'removeTeacher'])->name('admin.school.departments.remove-teacher');
        Route::delete('/departments/{id}', [\App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('admin.school.departments.destroy');
        
        // Grade Scale Management Routes
        Route::get('/grade-scales', [\App\Http\Controllers\Admin\GradeScaleController::class, 'index'])->name('admin.grade-scales.index');
        Route::get('/grade-scales/create', [\App\Http\Controllers\Admin\GradeScaleController::class, 'create'])->name('admin.grade-scales.create');
        Route::post('/grade-scales', [\App\Http\Controllers\Admin\GradeScaleController::class, 'store'])->name('admin.grade-scales.store');
        Route::delete('/grade-scales/{level}', [\App\Http\Controllers\Admin\GradeScaleController::class, 'destroy'])->name('admin.grade-scales.destroy');
        
                // Student Management Routes
                Route::get('/students', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'index'])->name('admin.school.students.index');
                Route::get('/students/create', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'create'])->name('admin.school.students.create');
                Route::post('/students', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'store'])->name('admin.school.students.store');
                Route::get('/students/import', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'showImport'])->name('admin.school.students.import');
                Route::post('/students/import', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'import'])->name('admin.school.students.import.submit');
                Route::get('/students/import/template', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'downloadTemplate'])->name('admin.school.students.import.template');
                Route::get('/students/{id}/edit', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'edit'])->name('admin.school.students.edit');
                Route::put('/students/{id}', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'update'])->name('admin.school.students.update');
                Route::delete('/students/{id}', [\App\Http\Controllers\Admin\SchoolStudentController::class, 'destroy'])->name('admin.school.students.destroy');

                // Resource Management Routes
                Route::get('/resources', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'index'])->name('admin.school.resources.index');
                Route::get('/resources/create', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'create'])->name('admin.school.resources.create');
                Route::post('/resources', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'store'])->name('admin.school.resources.store');
                Route::get('/resources/{id}', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'show'])->name('admin.school.resources.show');
                Route::get('/resources/{id}/edit', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'edit'])->name('admin.school.resources.edit');
                Route::put('/resources/{id}', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'update'])->name('admin.school.resources.update');
                Route::delete('/resources/{id}', [\App\Http\Controllers\Admin\SchoolResourceController::class, 'destroy'])->name('admin.school.resources.destroy');

                // Class Management Routes (for Director of Studies and School Admin)
                // Schools can only view system classes and assign subjects to them
                Route::get('/classes', [\App\Http\Controllers\Admin\SchoolClassController::class, 'index'])->name('admin.school.classes.index');
                Route::get('/classes/{id}/edit', [\App\Http\Controllers\Admin\SchoolClassController::class, 'edit'])->name('admin.school.classes.edit');
                Route::put('/classes/{id}', [\App\Http\Controllers\Admin\SchoolClassController::class, 'update'])->name('admin.school.classes.update');

                // Subscription Management Routes
                Route::get('/subscriptions', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'index'])->name('admin.school.subscriptions.index');
        Route::get('/subscriptions/create', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'create'])->name('admin.school.subscriptions.create');
        Route::post('/subscriptions', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'store'])->name('admin.school.subscriptions.store');
        Route::get('/subscriptions/{id}/payment', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'showPayment'])->name('admin.school.subscriptions.payment');
        Route::post('/subscriptions/{id}/process-payment', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'processPayment'])->name('admin.school.subscriptions.process-payment');
        Route::get('/subscriptions/{id}/flutterwave-callback', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'flutterwaveCallback'])->name('admin.school.subscriptions.flutterwave-callback');
        Route::post('/subscriptions/easypay-callback', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'easypayCallback'])->name('admin.school.subscriptions.easypay-callback');
    });

    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('flutterwave', [\App\Http\Controllers\Admin\SettingsController::class, 'flutterwave'])->name('admin.settings.flutterwave');
        Route::post('flutterwave', [\App\Http\Controllers\Admin\SettingsController::class, 'updateFlutterwave'])->name('admin.settings.flutterwave.update');
        Route::get('easypay', [\App\Http\Controllers\Admin\EasypaySettingsController::class, 'index'])->name('admin.settings.easypay');
        Route::post('easypay', [\App\Http\Controllers\Admin\EasypaySettingsController::class, 'update'])->name('admin.settings.easypay.update');
        Route::get('sms', [\App\Http\Controllers\Admin\SettingsController::class, 'sms'])->name('admin.settings.sms');
        Route::post('sms', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSms'])->name('admin.settings.sms.update');
        Route::get('/company', [SettingsController::class, 'company'])->name('admin.settings.company');
        Route::post('/company', [SettingsController::class, 'updateCompany'])->name('admin.settings.company.update');
        Route::get('/footer', [\App\Http\Controllers\Admin\FooterSettingsController::class, 'index'])->name('admin.settings.footer');
        Route::put('/footer', [\App\Http\Controllers\Admin\FooterSettingsController::class, 'update'])->name('admin.settings.footer.update');
        Route::get('/contact', [\App\Http\Controllers\Admin\ContactPageSettingsController::class, 'index'])->name('admin.settings.contact');
        Route::put('/contact', [\App\Http\Controllers\Admin\ContactPageSettingsController::class, 'update'])->name('admin.settings.contact.update');
    });

    Route::resource('terms', \App\Http\Controllers\Admin\TermsController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'edit', 'update', 'show', 'destroy']
    ]);

    // School Management (Super Admin Only)
    // School Subscription Approvals (Super Admin Only)
    Route::get('school-subscription-approvals', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'pendingApprovals'])->name('admin.school-subscriptions.pending');
    Route::post('school-subscriptions/{id}/approve', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'approvePayment'])->name('admin.school-subscriptions.approve');
    Route::post('school-subscriptions/{id}/reject', [\App\Http\Controllers\Admin\SchoolSubscriptionController::class, 'rejectPayment'])->name('admin.school-subscriptions.reject');

    Route::resource('schools', \App\Http\Controllers\Admin\SchoolManagementController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']
    ]);

    // University Management (Super Admin Only) - DISABLED: Using manual program entry instead
    // Route::resource('universities', \App\Http\Controllers\Admin\UniversityController::class, [
    //     'as' => 'admin',
    //     'only' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']
    // ]);

    // University Cut-Offs Management (Super Admin Only)
    // Define custom routes BEFORE resource route to avoid conflicts
    Route::get('/university-cut-offs/import', [\App\Http\Controllers\Admin\UniversityCutOffController::class, 'showImportForm'])->name('admin.university-cut-offs.import');
    Route::post('/university-cut-offs/import', [\App\Http\Controllers\Admin\UniversityCutOffController::class, 'import'])->name('admin.university-cut-offs.import.store');
    Route::get('/university-cut-offs/download-template', [\App\Http\Controllers\Admin\UniversityCutOffController::class, 'downloadTemplate'])->name('admin.university-cut-offs.download-template');
    Route::get('/university-cut-offs/export', [\App\Http\Controllers\Admin\UniversityCutOffController::class, 'export'])->name('admin.university-cut-offs.export');
    
    Route::resource('university-cut-offs', \App\Http\Controllers\Admin\UniversityCutOffController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']
    ]);


    // Classes - READ ONLY (System-wide classes: Form 1-6)
    // Schools cannot create, edit, or delete classes
    Route::get('classes', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('admin.classes.index');
    // Route::get('classes/create', [\App\Http\Controllers\Admin\ClassController::class, 'create'])->name('admin.classes.create');
    // Route::post('classes', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('admin.classes.store');
    // Route::get('classes/{class}/edit', [\App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('admin.classes.edit');
    // Route::put('classes/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('admin.classes.update');
    // Route::delete('classes/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('admin.classes.destroy');

    // Resources CRUD
    Route::get('resources', [\App\Http\Controllers\Admin\ResourceController::class, 'index'])->name('admin.resources.index');
    Route::get('resources/create', [\App\Http\Controllers\Admin\ResourceController::class, 'create'])->name('admin.resources.create');
    Route::post('resources', [\App\Http\Controllers\Admin\ResourceController::class, 'store'])->name('admin.resources.store');

    // Assignment Management
    Route::get('assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('admin.assignments.index');
    Route::get('assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('admin.assignments.show');
    Route::get('assignments/{assignment}/download', [\App\Http\Controllers\Admin\AssignmentController::class, 'download'])->name('admin.assignments.download');
    Route::get('teacher-assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'teacherAssignments'])->name('admin.teacher-assignments.index');
    Route::get('teacher-assignments/{resource}', [\App\Http\Controllers\Admin\AssignmentController::class, 'showTeacherAssignment'])->name('admin.teacher-assignments.show');
    Route::get('teacher-assignments/{resource}/download', [\App\Http\Controllers\Admin\AssignmentController::class, 'downloadTeacherAssignment'])->name('admin.teacher-assignments.download');
    Route::get('resources/{hash_id}', [\App\Http\Controllers\Admin\ResourceController::class, 'show'])->name('admin.resources.show');
    Route::get('resources/{hash_id}/edit', [\App\Http\Controllers\Admin\ResourceController::class, 'edit'])->name('admin.resources.edit');
    Route::put('resources/{hash_id}', [\App\Http\Controllers\Admin\ResourceController::class, 'update'])->name('admin.resources.update');
    Route::delete('resources/{hash_id}', [\App\Http\Controllers\Admin\ResourceController::class, 'destroy'])->name('admin.resources.destroy');

    Route::get('resources/{hash_id}/video-view', [\App\Http\Controllers\Admin\ResourceController::class, 'videoView'])->name('admin.resources.video-view');
    Route::get('resources/{hash_id}/drive-play', [\App\Http\Controllers\Admin\ResourceController::class, 'drivePlay'])->name('admin.resources.drive-play');

    Route::get('/api/subjects/{subject}/topics', [\App\Http\Controllers\Admin\TopicController::class, 'topicsBySubject']);
    Route::get('admin/api/teachers', [\App\Http\Controllers\Admin\ResourceController::class, 'getTeachersBySubjectAndClass'])->name('admin.api.teachers');

    // Bulk Resource Assignment (Super Admin Only)
    Route::post('resources/bulk-assign', [\App\Http\Controllers\Admin\ResourceController::class, 'bulkAssign'])->name('admin.resources.bulk-assign');

    // Subscription Management

    Route::resource('subscription-packages', \App\Http\Controllers\Admin\SubscriptionPackageController::class, [
        'as' => 'admin'
    ]);

    Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    Route::get('/subscriptions/create', [\App\Http\Controllers\Admin\SubscriptionController::class, 'create'])->name('admin.subscriptions.create');
    Route::post('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
    Route::get('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('admin.subscriptions.show')->withoutMiddleware(['auth', 'single.session']);
    Route::get('/subscriptions/{subscription}/edit', [\App\Http\Controllers\Admin\SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
    Route::put('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->name('admin.subscriptions.update');
    Route::patch('/subscriptions/{subscription}/status', [\App\Http\Controllers\Admin\SubscriptionController::class, 'updateStatus'])->name('admin.subscriptions.update-status');
    Route::delete('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');

    // Welcome Settings Routes
    Route::get('/admin/settings/welcome', [WelcomeSettingsController::class, 'index'])->name('admin.settings.welcome');
    Route::put('/admin/settings/welcome', [WelcomeSettingsController::class, 'update'])->name('admin.settings.welcome.update');

    // Team Management Routes
    Route::resource('teams', \App\Http\Controllers\Admin\TeamController::class, ['as' => 'admin']);
    Route::patch('teams/{team}/toggle-status', [\App\Http\Controllers\Admin\TeamController::class, 'toggleStatus'])->name('admin.teams.toggle-status');
});

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('frontend.auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Phone Verification Routes
Route::get('/phone/verify', function () {
    return view('frontend.auth.verify-phone');
})->middleware('auth')->name('verification.phone.notice');

Route::post('/phone/verify', [AuthController::class, 'verifyPhone'])
    ->middleware('auth')
    ->name('verification.phone.verify');

Route::post('/phone/verification-notification', [AuthController::class, 'resendVerificationSMS'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.phone.send');

// Teacher Routes
Route::middleware(['auth', 'single.session', \App\Http\Middleware\CheckAccountType::class])->prefix('teacher')->name('teacher.')->group(function () {
        // Teacher Resource Upload Routes
        Route::get('/resources', [App\Http\Controllers\Teacher\TeacherResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/upload', [App\Http\Controllers\Teacher\TeacherResourceController::class, 'showUploadForm'])->name('resources.upload.form');
        Route::post('/resources/upload', [App\Http\Controllers\Teacher\TeacherResourceController::class, 'uploadResource'])->name('resources.upload.submit');

    // Teacher Standalone Assignment Routes
    Route::resource('standalone-assignments', App\Http\Controllers\Teacher\StandaloneAssignmentController::class)->names('standalone-assignments');
    Route::get('/standalone-assignments/{assignment}/download', [App\Http\Controllers\Teacher\StandaloneAssignmentController::class, 'download'])->name('standalone-assignments.download');
    Route::get('/standalone-assignments/{assignment}/submissions', [App\Http\Controllers\Teacher\StandaloneAssignmentController::class, 'submissions'])->name('standalone-assignments.submissions');
    Route::get('/standalone-assignments/{assignment}/submissions/{submission}', [App\Http\Controllers\Teacher\StandaloneAssignmentController::class, 'viewSubmission'])->name('standalone-assignments.view-submission');
    Route::post('/standalone-assignments/{assignment}/submissions/{submission}/grade', [App\Http\Controllers\Teacher\StandaloneAssignmentController::class, 'gradeSubmission'])->name('standalone-assignments.grade-submission');
    Route::get('/standalone-assignments/{assignment}/submissions/{submission}/download', [App\Http\Controllers\Teacher\StandaloneAssignmentController::class, 'downloadSubmission'])->name('standalone-assignments.download-submission');
    Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
    
    // Teacher Assignment Management Routes
    Route::get('/assignments', [App\Http\Controllers\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/view', [App\Http\Controllers\AssignmentController::class, 'view'])->name('assignments.view');
    Route::get('/assignments/{assignment}/download', [App\Http\Controllers\AssignmentController::class, 'download'])->name('assignments.download');
    Route::put('/assignments/{assignment}', [App\Http\Controllers\AssignmentController::class, 'update'])->name('assignments.update');
    Route::get('/assignments/resource/{resource}', [App\Http\Controllers\AssignmentController::class, 'byResource'])->name('assignments.by-resource');
    Route::get('/assignments/reports/student-scores', [App\Http\Controllers\AssignmentController::class, 'downloadStudentScoresReport'])->name('assignments.reports.student-scores');
    Route::get('/assignments/bulk', [App\Http\Controllers\AssignmentController::class, 'bulkForm'])->name('assignments.bulk.form');
    Route::post('/assignments/bulk', [App\Http\Controllers\AssignmentController::class, 'bulkSubmit'])->name('assignments.bulk.submit');

    // Teacher Assessment Management Routes
    Route::get('/assessments', [App\Http\Controllers\Teacher\AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/create', [App\Http\Controllers\Teacher\AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments/upload', [App\Http\Controllers\Teacher\AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{resource}', [App\Http\Controllers\Teacher\AssessmentController::class, 'show'])->name('assessments.show');
    Route::post('/assessments/{resource}/upload-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'uploadAssessment'])->name('assessments.upload-assessment');
    Route::post('/assessments/{resource}/upload-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'uploadNotes'])->name('assessments.upload-notes');
    Route::get('/assessments/{resource}/download-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'downloadAssessment'])->name('assessments.download.assessment');
    Route::get('/assessments/{resource}/download-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'downloadNotes'])->name('assessments.download.notes');
    Route::delete('/assessments/{resource}/delete-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'deleteAssessment'])->name('assessments.delete-assessment');
    Route::delete('/assessments/{resource}/delete-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'deleteNotes'])->name('assessments.delete-notes');

    // Teacher Marks Management Routes
    Route::get('/marks', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'index'])->name('marks.index');
    Route::get('/marks/create', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'create'])->name('marks.create');
    Route::post('/marks', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'store'])->name('marks.store');
    Route::get('/marks/template', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'downloadTemplate'])->name('marks.template');
    Route::get('/marks/students/{classId}', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'getClassStudents'])->name('marks.students');
    Route::get('/marks/{id}/edit', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'edit'])->name('marks.edit');
    Route::put('/marks/{id}', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'update'])->name('marks.update');
    Route::delete('/marks/{id}', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'destroy'])->name('marks.destroy');

    // Teacher Parent Messaging Routes
    Route::get('/messages', [App\Http\Controllers\Teacher\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [App\Http\Controllers\Teacher\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Teacher\MessageController::class, 'reply'])->name('messages.reply');
    Route::get('/messages/unread/count', [App\Http\Controllers\Teacher\MessageController::class, 'getUnreadCount'])->name('messages.unread-count');

    // Backwards-compat redirect: ensure old projects/groups URL works
    // Redirect /teacher/projects/groups to /teacher/groups (new canonical path)
    Route::get('/projects/groups', function () {
        return redirect()->route('teacher.groups.index');
    });

    // Teacher Projects Routes - View student projects and groups
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'index'])->name('index');
        Route::get('/group-submissions', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'groupSubmissions'])->name('group-submissions');
        Route::get('/{project}', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'show'])->name('show');
        // Grading routes
        Route::get('/{project}/grade', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'gradeForm'])->name('grade.form');
        Route::post('/{project}/grade', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'gradeSubmit'])->name('grade.submit');
        Route::get('/{project}/feedback', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'viewGradingFeedback'])->name('feedback');
        
        // Groups Routes
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'indexGroups'])->name('index');
            Route::get('/{group}', [App\Http\Controllers\Teacher\TeacherProjectController::class, 'showGroup'])->name('show');
        });
    });
});

// Student Routes

Route::middleware(['auth', 'single.session', \App\Http\Middleware\CheckAccountType::class])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    
    // Resources Routes
    Route::get('/resources', [App\Http\Controllers\Student\ResourceController::class, 'index'])->name('resources.index');
    
    // Student Standalone Assignment Routes
    Route::get('/assignments', [App\Http\Controllers\Student\StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\Student\StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [App\Http\Controllers\Student\StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('/assignments/{assignment}/download', [App\Http\Controllers\Student\StudentAssignmentController::class, 'download'])->name('assignments.download');
    Route::get('/assignment-submissions/{submission}/download', [App\Http\Controllers\Student\StudentAssignmentController::class, 'downloadSubmission'])->name('assignment-submissions.download');
    
    // Sample Videos Routes
    Route::get('/sample-videos', [App\Http\Controllers\Student\SampleVideoController::class, 'index'])->name('sample-videos.index');
    Route::get('/sample-videos/{hashId}', [App\Http\Controllers\Student\SampleVideoController::class, 'show'])->name('sample-videos.show');
    
    // Student Profile Routes
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    // ... other student routes ...

    // Student Subscription Route
    Route::get('/subscription', [App\Http\Controllers\Student\SubscriptionController::class, 'index'])->name('subscription');
    Route::get('/subscription/{subscription}/receipt', [App\Http\Controllers\Student\SubscriptionController::class, 'receipt'])->name('subscription.receipt');

    // Student Preferences Routes (no active.subscription middleware)
    Route::get('/preferences', [App\Http\Controllers\UserPreferenceController::class, 'index'])->name('preferences.index');
    Route::post('/preferences', [App\Http\Controllers\UserPreferenceController::class, 'update'])->name('preferences.update');

    // My Videos Route
    Route::get('/my-videos', [App\Http\Controllers\UserPreferenceController::class, 'myVideos'])->name('my-videos');
    Route::get('/debug-my-videos', [App\Http\Controllers\UserPreferenceController::class, 'debugMyVideos'])->name('debug-my-videos');
    Route::get('/my-videos/{resource}', [App\Http\Controllers\UserPreferenceController::class, 'showMyVideo'])->name('my-videos.show');
    
    // Teacher Assessment Upload Route
    Route::post('/videos/{resource}/upload-assessment', [App\Http\Controllers\UserPreferenceController::class, 'uploadAssessment'])->name('videos.upload-assessment');
    
    // Teacher Notes Upload Route
    Route::post('/videos/{resource}/upload-notes', [App\Http\Controllers\UserPreferenceController::class, 'uploadNotes'])->name('videos.upload-notes');
    
    // Student Assignment Upload Route
    Route::post('/videos/{resource}/upload-assignment', [App\Http\Controllers\UserPreferenceController::class, 'uploadAssignment'])->name('videos.upload-assignment');
    
    // Student Assignment Download Route
    Route::get('/my-assignments/{assignment}/download', [App\Http\Controllers\UserPreferenceController::class, 'downloadStudentAssignment'])->name('my-assignments.download');
    
    // Student Assessment Report Download Route
    Route::get('/my-assignments/{assignment}/report', [App\Http\Controllers\UserPreferenceController::class, 'downloadAssessmentReport'])->name('my-assignments.report');
    
    // Student Best 3 Assignments PDF Download Route
    Route::get('/my-assignments/best-three', [App\Http\Controllers\UserPreferenceController::class, 'downloadBestThreeAssignments'])->name('my-assignments.best-three');
    
    // Student Assignments List Route
    Route::get('/my-assignments', [App\Http\Controllers\UserPreferenceController::class, 'myAssignments'])->name('my-assignments.index');

    // Student Marks Management Routes - Students can only VIEW their marks
    // Only teachers and head of departments can upload/edit marks
    Route::resource('marks', \App\Http\Controllers\Student\StudentMarkController::class, [
        'only' => ['index']
    ]);
    // Route::get('/marks/import', [\App\Http\Controllers\Student\StudentMarkController::class, 'showImport'])->name('marks.import');
    // Route::post('/marks/import', [\App\Http\Controllers\Student\StudentMarkController::class, 'import'])->name('marks.import.store');
    // Route::get('/marks/template/download', [\App\Http\Controllers\Student\StudentMarkController::class, 'downloadTemplate'])->name('marks.template.download');

    // Course Recommendations Route (A-Level)
    Route::get('/course-recommendations', [\App\Http\Controllers\Student\CourseRecommendationController::class, 'index'])->name('course-recommendations.index');
    Route::get('/course-recommendations/download-pdf', [\App\Http\Controllers\Student\CourseRecommendationController::class, 'downloadPdf'])->name('course-recommendations.download-pdf');
    
    // O-Level Subject Combination Recommendations Routes (Career Guidance)
    Route::get('/career-guidance', [\App\Http\Controllers\Student\OLevelRecommendationController::class, 'index'])->name('career-guidance.index');
    
    // Student Performance Dashboard
    Route::get('/performance', [\App\Http\Controllers\Student\PerformanceController::class, 'index'])->name('performance.index');
    
    // O-Level Course Recommendations Routes (for university programs based on O-Level)
    Route::get('/o-level-course-recommendations', [\App\Http\Controllers\Student\OLevelCourseRecommendationController::class, 'selectSubjects'])->name('o-level-course-recommendations.select');
    Route::post('/o-level-course-recommendations', [\App\Http\Controllers\Student\OLevelCourseRecommendationController::class, 'showRecommendations'])->name('o-level-course-recommendations.show');
    Route::post('/o-level-course-recommendations/download-pdf', [\App\Http\Controllers\Student\OLevelCourseRecommendationController::class, 'downloadPdf'])->name('o-level-course-recommendations.download-pdf');

    // Teacher Assessment Management Routes
    Route::get('/assessments', [App\Http\Controllers\Teacher\AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/create', [App\Http\Controllers\Teacher\AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments/upload', [App\Http\Controllers\Teacher\AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{resource}', [App\Http\Controllers\Teacher\AssessmentController::class, 'show'])->name('assessments.show');
    Route::post('/assessments/{resource}/upload-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'uploadAssessment'])->name('assessments.upload-assessment');
    Route::post('/assessments/{resource}/upload-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'uploadNotes'])->name('assessments.upload-notes');
    Route::get('/assessments/{resource}/download-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'downloadAssessment'])->name('assessments.download.assessment');
    Route::get('/assessments/{resource}/download-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'downloadNotes'])->name('assessments.download.notes');
    Route::delete('/assessments/{resource}/delete-assessment', [App\Http\Controllers\Teacher\AssessmentController::class, 'deleteAssessment'])->name('assessments.delete-assessment');
    Route::delete('/assessments/{resource}/delete-notes', [App\Http\Controllers\Teacher\AssessmentController::class, 'deleteNotes'])->name('assessments.delete-notes');

    // Teacher Marks Management Routes
    Route::get('/teacher-marks', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'index'])->name('teacher-marks.index');
    Route::get('/teacher-marks/create', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'create'])->name('teacher-marks.create');
    Route::post('/teacher-marks', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'store'])->name('teacher-marks.store');
    Route::get('/teacher-marks/template', [App\Http\Controllers\Teacher\TeacherMarkController::class, 'downloadTemplate'])->name('teacher-marks.template');

    // Video tracking route
    Route::post('/videos/track', [App\Http\Controllers\Student\VideoPlayController::class, 'track'])->name('videos.track');
    
    // Notification routes (teachers only)
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');

    // Projects Routes
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [App\Http\Controllers\StudentProjectController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\StudentProjectController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\StudentProjectController::class, 'store'])->name('store');
        // Groups Routes (nested under projects)
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', [App\Http\Controllers\StudentGroupController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\StudentGroupController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\StudentGroupController::class, 'store'])->name('store');
            Route::get('/{group}', [App\Http\Controllers\StudentGroupController::class, 'show'])->name('show');
            Route::post('/{group}/join', [App\Http\Controllers\StudentGroupController::class, 'join'])->name('join');
            Route::post('/{group}/leave', [App\Http\Controllers\StudentGroupController::class, 'leave'])->name('leave');
            Route::post('/{group}/upload', [App\Http\Controllers\StudentGroupController::class, 'uploadSubmission'])->name('upload-submission');
            Route::delete('/{group}/members/{memberId}', [App\Http\Controllers\StudentGroupController::class, 'removeMember'])->name('remove-member');
        });

        // Catch-all project routes must come after the groups prefix to avoid matching "groups" as {project}
        Route::get('/{project}', [App\Http\Controllers\StudentProjectController::class, 'show'])->name('show');

        // Project Planning Routes
        Route::get('/{project}/planning/edit', [App\Http\Controllers\StudentProjectController::class, 'editPlanning'])->name('edit-planning');
        Route::put('/{project}/planning', [App\Http\Controllers\StudentProjectController::class, 'updatePlanning'])->name('update-planning');
        Route::post('/{project}/planning/submit', [App\Http\Controllers\StudentProjectController::class, 'submitPlanning'])->name('submit-planning');

        // Project Implementation Routes
        Route::get('/{project}/implementation/edit', [App\Http\Controllers\StudentProjectController::class, 'editImplementation'])->name('edit-implementation');
        Route::put('/{project}/implementation', [App\Http\Controllers\StudentProjectController::class, 'updateImplementation'])->name('update-implementation');
        Route::post('/{project}/submit', [App\Http\Controllers\StudentProjectController::class, 'submitProject'])->name('submit-project');
    });

    // Temporary test route for groups - REMOVE AFTER TESTING
    Route::get('/test-student-groups', function() {
        \Log::info('Test student groups route called');
        return 'Student groups route working!';
    });

    Route::get('/groups-test', [\App\Http\Controllers\StudentGroupController::class, 'index'])->name('groups-test');

    // TEMPORARY: Groups Routes without middleware for testing
    Route::prefix('test-groups')->name('test.groups.')->group(function () {
        Route::get('/', function() {
            return 'Groups working without middleware!';
        })->name('index');
    });

});

// Parent Routes
Route::middleware(['auth', 'single.session', \App\Http\Middleware\CheckAccountType::class])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/children/{studentId}', [App\Http\Controllers\Parent\DashboardController::class, 'showChild'])->name('children.show');
    Route::get('/children/{studentId}/videos', [App\Http\Controllers\Parent\DashboardController::class, 'showChildVideos'])->name('children.videos');
});

Route::post('/payment/easypay', [App\Http\Controllers\Frontend\PricingController::class, 'payWithEasypay'])->name('payment.easypay');
Route::post('/payment/flutterwave', [App\Http\Controllers\Frontend\PricingController::class, 'payWithFlutterwave'])->name('payment.flutterwave');
Route::post('/callback', [App\Http\Controllers\Frontend\PricingController::class, 'easypayCallback'])->name('easypay.callback');
Route::get('/payment/flutterwave/callback', [App\Http\Controllers\Frontend\PricingController::class, 'flutterwaveCallback'])->name('payment.flutterwave.callback');

// Resource Comments (for student videos)
Route::get('resource/{resource}/comments', [\App\Http\Controllers\ResourceCommentController::class, 'index']);
Route::post('resource/{resource}/comments', [\App\Http\Controllers\ResourceCommentController::class, 'store'])->middleware('auth');
Route::put('resource/{resource}/comments/{comment}', [\App\Http\Controllers\ResourceCommentController::class, 'update'])->middleware('auth');
Route::delete('resource/{resource}/comments/{comment}', [\App\Http\Controllers\ResourceCommentController::class, 'destroy'])->middleware('auth');
Route::post('resource/comments/{comment}/like', [\App\Http\Controllers\ResourceCommentController::class, 'like'])->middleware('auth');

// Chat Routes (students only)
Route::middleware(['auth', 'single.session', \App\Http\Middleware\CheckAccountType::class])->prefix('student')->name('student.')->group(function () {
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/search/users', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('chat.search');
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread');
    Route::post('/chat/start', [App\Http\Controllers\ChatController::class, 'startConversation'])->name('chat.start');
    Route::post('/chat/group', [App\Http\Controllers\ChatController::class, 'createGroup'])->name('chat.group');
    Route::get('/chat/{conversation}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.message');
    Route::get('/chat/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversation}/leave', [App\Http\Controllers\ChatController::class, 'leaveConversation'])->name('chat.leave');
    Route::post('/chat/{conversation}/add-members', [App\Http\Controllers\ChatController::class, 'addMembers'])->name('chat.add-members');
});

// Teacher group management routes (teacher-only)
Route::middleware(['auth', 'single.session', \App\Http\Middleware\CheckAccountType::class])->prefix('teacher')->name('teacher.')->group(function () {
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'store'])->name('store');
        Route::get('/{group}/edit', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'edit'])->name('edit');
        Route::put('/{group}', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'update'])->name('update');
        Route::delete('/{group}', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'destroy'])->name('destroy');

        Route::get('/{group}/assign', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'assignForm'])->name('assign.form');
            Route::get('/{group}/assign-students', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'assignStudentsForm'])->name('assign.students.form');
            Route::post('/{group}/assign-students', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'assignStudentsSubmit'])->name('assign.students.submit');
        Route::post('/{group}/assign', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'assignResource'])->name('assign.submit');
        Route::post('/{group}/submit', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'submitGroupAssignment'])->name('submit');
        Route::get('/{group}/submissions', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'submissions'])->name('submissions');
        Route::post('/{group}/grade', [App\Http\Controllers\Teacher\TeacherGroupController::class, 'gradeGroupSubmission'])->name('grade');
    });
});

// Parent Portal Routes
Route::middleware(['auth', 'single.session'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/child/{studentId}', [\App\Http\Controllers\Parent\DashboardController::class, 'showChild'])->name('child.details');
    Route::get('/my-videos', [\App\Http\Controllers\Parent\DashboardController::class, 'myVideos'])->name('my-videos');
    
    // Parent-Teacher Messaging
    Route::get('/messages', [\App\Http\Controllers\Parent\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [\App\Http\Controllers\Parent\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [\App\Http\Controllers\Parent\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [\App\Http\Controllers\Parent\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [\App\Http\Controllers\Parent\MessageController::class, 'reply'])->name('messages.reply');
    Route::get('/messages/unread-count', [\App\Http\Controllers\Parent\MessageController::class, 'getUnreadCount']);
    Route::get('/messages/teachers/{studentId}', [\App\Http\Controllers\Parent\MessageController::class, 'getTeachersForStudent']);
    
    // Parent Profile
    Route::get('/profile', [\App\Http\Controllers\Parent\MessageController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Parent\MessageController::class, 'updateProfile'])->name('profile.update');
});

// Stop impersonation (used by parents/admins after impersonating a user)
Route::post('/stop-impersonating', [\App\Http\Controllers\ImpersonationController::class, 'stop'])
    ->middleware(['auth', 'single.session'])
    ->name('stop-impersonating');

