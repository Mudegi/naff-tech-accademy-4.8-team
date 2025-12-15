

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">
            <i class="fas fa-user-edit mr-2 text-blue-600"></i>Edit Student
        </h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="<?php echo e(route('admin.school.students.index')); ?>" class="hover:text-blue-600 transition-colors">Students</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Edit</span>
        </div>
    </div>

    <form action="<?php echo e(route('admin.school.students.update', $studentUser->id)); ?>" method="POST" class="student-edit-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="form-sections-container">
            <!-- Personal Information Section -->
            <div class="form-section-card">
                <div class="form-section-header">
                    <div class="form-section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="form-section-title">Personal Information</h3>
                        <p class="form-section-subtitle">Update student's personal details</p>
                    </div>
                </div>
                <div class="form-section-content">
                    <div class="form-grid form-grid-3">
                        <div class="form-field-group">
                            <label for="first_name" class="form-label">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?php echo e(old('first_name', $studentUser->student->first_name ?? '')); ?>"
                                   required
                                   class="form-input">
                            <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="middle_name" class="form-label">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>Middle Name
                            </label>
                            <input type="text" 
                                   id="middle_name" 
                                   name="middle_name" 
                                   value="<?php echo e(old('middle_name', $studentUser->student->middle_name ?? '')); ?>"
                                   class="form-input">
                            <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="last_name" class="form-label">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?php echo e(old('last_name', $studentUser->student->last_name ?? '')); ?>"
                                   required
                                   class="form-input">
                            <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section-card">
                <div class="form-section-header">
                    <div class="form-section-icon form-section-icon-green">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <div>
                        <h3 class="form-section-title">Contact Information</h3>
                        <p class="form-section-subtitle">Student's contact details</p>
                    </div>
                </div>
                <div class="form-section-content">
                    <div class="form-grid form-grid-2">
                        <div class="form-field-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>Email Address
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email', $studentUser->email)); ?>"
                                   class="form-input">
                            <p class="form-hint">
                                <i class="fas fa-info-circle mr-1"></i>Optional, but recommended
                            </p>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="phone_number" class="form-label">
                                <i class="fas fa-phone mr-1 text-gray-400"></i>Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="<?php echo e(old('phone_number', $studentUser->phone_number)); ?>"
                                   required
                                   class="form-input">
                            <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information Section -->
            <div class="form-section-card">
                <div class="form-section-header">
                    <div class="form-section-icon form-section-icon-purple">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="form-section-title">Academic Information</h3>
                        <p class="form-section-subtitle">Student's academic details and enrollment</p>
                    </div>
                </div>
                <div class="form-section-content">
                    <div class="form-grid form-grid-3">
                        <div class="form-field-group">
                            <label for="registration_number" class="form-label">
                                <i class="fas fa-id-card mr-1 text-gray-400"></i>Registration Number
                            </label>
                            <input type="text" 
                                   id="registration_number" 
                                   name="registration_number" 
                                   value="<?php echo e(old('registration_number', $studentUser->student->registration_number ?? '')); ?>"
                                   class="form-input">
                            <p class="form-hint">
                                <i class="fas fa-info-circle mr-1"></i>Unique student registration number
                            </p>
                            <?php $__errorArgs = ['registration_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="class" class="form-label">
                                <i class="fas fa-users mr-1 text-gray-400"></i>Class
                            </label>
                            <select id="class" 
                                    name="class" 
                                    class="form-input form-select">
                                <option value="">Select Class (Optional)</option>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($class->name); ?>" <?php echo e(old('class', $studentUser->student->class ?? '') == $class->name ? 'selected' : ''); ?>>
                                        <?php echo e($class->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['class'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="level" class="form-label">
                                <i class="fas fa-layer-group mr-1 text-gray-400"></i>Academic Level <span class="text-red-500">*</span>
                            </label>
                            <select id="level"
                                    name="level"
                                    required
                                    onchange="toggleCombinationField()"
                                    class="form-input form-select">
                                <option value="">Select Level</option>
                                <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e(old('level', $studentUser->student->level ?? '') === $value ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="form-hint">
                                <i class="fas fa-info-circle mr-1"></i>O Level corresponds to UCE, A Level corresponds to UACE.
                            </p>
                            <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group" id="combination-group" style="display: none;">
                            <label for="combination" class="form-label">
                                <i class="fas fa-book mr-1 text-gray-400"></i>Subject Combination 
                                <span id="combination-required" style="display: none;" class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="combination" 
                                   name="combination" 
                                   value="<?php echo e(old('combination', $studentUser->student->combination ?? '')); ?>"
                                   placeholder="e.g., PCM/ICT, BCM/ICT, PEM/ICT"
                                   class="form-input">
                            <p class="form-hint">
                                <i class="fas fa-info-circle mr-1"></i>Enter the subject combination in short form (e.g., PCM/ICT for Physics, Chemistry, Mathematics with ICT as subsidiary).
                            </p>
                            <?php $__errorArgs = ['combination'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-field-group">
                            <label for="date_of_birth" class="form-label">
                                <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>Date of Birth
                            </label>
                            <input type="date" 
                                   id="date_of_birth" 
                                   name="date_of_birth" 
                                   value="<?php echo e(old('date_of_birth', $studentUser->student->date_of_birth ? $studentUser->student->date_of_birth->format('Y-m-d') : '')); ?>"
                                   class="form-input">
                            <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Reset Section -->
            <div class="form-section-card">
                <div class="form-section-header">
                    <div class="form-section-icon form-section-icon-orange">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h3 class="form-section-title">Password Reset</h3>
                        <p class="form-section-subtitle">Optional - Leave blank to keep current password</p>
                    </div>
                </div>
                <div class="form-section-content">
                    <div class="form-field-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock mr-1 text-gray-400"></i>New Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="text" 
                                   id="password" 
                                   name="password" 
                                   class="form-input">
                            <button type="button" onclick="generatePassword()" class="password-generate-btn">
                                <i class="fas fa-sync-alt mr-1"></i> Generate
                            </button>
                        </div>
                        <p class="form-hint">
                            <i class="fas fa-info-circle mr-1"></i>Leave blank to keep current password
                        </p>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div id="show-password-field" style="display: none;" class="mt-4">
                        <label class="form-checkbox-label">
                            <input type="checkbox" 
                                   name="show_password" 
                                   value="1"
                                   class="form-checkbox">
                            <span class="ml-2 text-sm text-gray-700">
                                <i class="fas fa-eye mr-1 text-gray-400"></i>Show password after update (for sharing with student)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="form-section-card">
                <div class="form-section-header">
                    <div class="form-section-icon form-section-icon-teal">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <div>
                        <h3 class="form-section-title">Account Status</h3>
                        <p class="form-section-subtitle">Control student's access to the system</p>
                    </div>
                </div>
                <div class="form-section-content">
                    <label class="form-checkbox-label-large">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               <?php echo e(old('is_active', $studentUser->is_active) ? 'checked' : ''); ?>

                               class="form-checkbox">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">
                                <i class="fas fa-user-check mr-1 text-green-600"></i>Active Account
                            </span>
                            <span class="block text-xs text-gray-500 mt-1">Student can log in and access the system</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?php echo e(route('admin.school.students.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Student
                </button>
            </div>
        </form>
</div>

<style>
/* Enhanced Student Edit Form Styles */
.student-edit-form {
    margin-top: 1.5rem;
}

.form-sections-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Form Section Cards */
.form-section-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.form-section-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.form-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e5e7eb;
}

.form-section-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #ffffff;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    flex-shrink: 0;
}

.form-section-icon-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.form-section-icon-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.form-section-icon-orange {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.form-section-icon-teal {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    line-height: 1.4;
}

.form-section-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0.25rem 0 0 0;
}

.form-section-content {
    padding: 1.5rem;
}

/* Form Grid */
.form-grid {
    display: grid;
    gap: 1.5rem;
}

.form-grid-2 {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.form-grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

@media (max-width: 768px) {
    .form-grid-2,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }
}

/* Form Fields */
.form-field-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9375rem;
    color: #1f2937;
    background: #ffffff;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.form-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: #ffffff;
}

.form-input:hover:not(:focus) {
    border-color: #cbd5e1;
}

.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.form-hint {
    display: flex;
    align-items: center;
    font-size: 0.8125rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.form-error {
    font-size: 0.8125rem;
    color: #dc2626;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.form-error::before {
    content: "⚠";
    font-size: 0.875rem;
}

/* Password Input Wrapper */
.password-input-wrapper {
    display: flex;
    gap: 0.75rem;
    align-items: stretch;
}

.password-input-wrapper .form-input {
    flex: 1;
}

.password-generate-btn {
    padding: 0.75rem 1.25rem;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
}

.password-generate-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.4);
    transform: translateY(-1px);
}

.password-generate-btn:active {
    transform: translateY(0);
}

/* Checkboxes */
.form-checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.form-checkbox-label:hover {
    background: #f9fafb;
}

.form-checkbox-label-large {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
    background: #fafbfc;
}

.form-checkbox-label-large:hover {
    background: #f3f4f6;
    border-color: #cbd5e1;
}

.form-checkbox {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    accent-color: #2563eb;
}

.form-checkbox:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    transform: translateY(-2px);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background: #ffffff;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #cbd5e1;
    color: #1f2937;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .form-section-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn {
        width: 100%;
    }

    .password-input-wrapper {
        flex-direction: column;
    }

    .password-generate-btn {
        width: 100%;
    }
}

/* Animation for form sections */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section-card {
    animation: fadeInUp 0.4s ease-out;
}

.form-section-card:nth-child(1) { animation-delay: 0.1s; }
.form-section-card:nth-child(2) { animation-delay: 0.2s; }
.form-section-card:nth-child(3) { animation-delay: 0.3s; }
.form-section-card:nth-child(4) { animation-delay: 0.4s; }
.form-section-card:nth-child(5) { animation-delay: 0.5s; }
</style>

<script>
function toggleCombinationField() {
    const levelSelect = document.getElementById('level');
    const classSelect = document.getElementById('class');
    const combinationGroup = document.getElementById('combination-group');
    const combinationInput = document.getElementById('combination');
    const combinationRequired = document.getElementById('combination-required');
    
    const level = levelSelect.value;
    const className = classSelect.value || '';
    const isS5OrS6 = /S\.?[56]/i.test(className);
    const isALevel = level === 'A Level';
    
    // Show combination field if A Level or class is S.5/S.6
    if (isALevel || isS5OrS6) {
        combinationGroup.style.display = 'block';
        combinationInput.required = true;
        combinationRequired.style.display = 'inline';
    } else {
        combinationGroup.style.display = 'none';
        combinationInput.required = false;
        combinationRequired.style.display = 'none';
        // Don't clear value on edit, just hide the field
    }
}

// Also check when class changes
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class');
    if (classSelect) {
        classSelect.addEventListener('change', toggleCombinationField);
    }
    // Initial check on page load
    toggleCombinationField();
});

function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('password').value = password;
    
    // Show the "show password" checkbox when password is entered
    const showPasswordField = document.getElementById('show-password-field');
    if (password.length > 0) {
        showPasswordField.style.display = 'block';
    }
}

document.getElementById('password').addEventListener('input', function() {
    const showPasswordField = document.getElementById('show-password-field');
    if (this.value.length > 0) {
        showPasswordField.style.display = 'block';
    } else {
        showPasswordField.style.display = 'none';
        document.querySelector('input[name="show_password"]').checked = false;
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/school/students/edit.blade.php ENDPATH**/ ?>