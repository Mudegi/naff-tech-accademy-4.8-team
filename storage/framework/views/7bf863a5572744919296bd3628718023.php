

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Edit Staff Member</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="<?php echo e(route('admin.school.staff.index')); ?>">Staff</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Edit</span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <form action="<?php echo e(route('admin.school.staff.update', $staff->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?php echo e(old('name', $staff->name)); ?>"
                           required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo e(old('email', $staff->email)); ?>"
                           required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" 
                           id="phone_number" 
                           name="phone_number" 
                           value="<?php echo e(old('phone_number', $staff->phone_number)); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="account_type" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select id="account_type" 
                            name="account_type" 
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Role</option>
                        <?php $__currentLoopData = $availableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(old('account_type', $staff->account_type) == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="department-field" style="display: none;">
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select id="department_id" 
                            name="department_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Department (Optional)</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($department->id); ?>" <?php echo e(old('department_id', $staff->department_id) == $department->id ? 'selected' : ''); ?>>
                                <?php echo e($department->name); ?><?php if($department->code): ?> (<?php echo e($department->code); ?>)<?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Assign to a department (for Head of Department and Subject Teachers)</p>
                    <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Teaching Assignment Section -->
                <div id="teaching-assignment-section" style="display: none;">
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Teaching Assignment</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="classes" class="block text-sm font-medium text-gray-700">
                                    Classes
                                </label>
                                <select id="classes" 
                                        name="classes[]" 
                                        multiple 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        style="height: 120px;">
                                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($class->id); ?>" <?php echo e(in_array($class->id, old('classes', $assignedClasses ?? [])) ? 'selected' : ''); ?>>
                                            <?php echo e($class->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple classes</p>
                                <?php $__errorArgs = ['classes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div id="subjects-field">
                                <label for="subjects" class="block text-sm font-medium text-gray-700">
                                    Subjects
                                </label>
                                <select id="subjects" 
                                        name="subjects[]" 
                                        multiple 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        style="height: 120px;">
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($subject->id); ?>" <?php echo e(in_array($subject->id, old('subjects', $assignedSubjects ?? [])) ? 'selected' : ''); ?>>
                                            <?php echo e($subject->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple subjects</p>
                                <?php $__errorArgs = ['subjects'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password (Leave blank to keep current)</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           minlength="8"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-1 text-sm text-gray-500">Minimum 8 characters (only if changing password)</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           minlength="8"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div id="show-password-field" style="display: none;">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="show_password" 
                               value="1"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Show password after update (for sharing with staff member)</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               <?php echo e(old('is_active', $staff->is_active) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="<?php echo e(route('admin.school.staff.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Staff Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide fields based on role selection
    document.getElementById('account_type').addEventListener('change', function() {
        const departmentField = document.getElementById('department-field');
        const teachingAssignmentSection = document.getElementById('teaching-assignment-section');
        const subjectsField = document.getElementById('subjects-field');
        const role = this.value;
        
        // Show department field for HOD and Subject Teacher roles
        if (role === 'head_of_department' || role === 'subject_teacher') {
            departmentField.style.display = 'block';
        } else {
            departmentField.style.display = 'none';
            document.getElementById('department_id').value = '';
        }

        // Show teaching assignment section for teachers, subject teachers, and HODs
        if (role === 'teacher' || role === 'subject_teacher' || role === 'head_of_department') {
            teachingAssignmentSection.style.display = 'block';
            
            // Show subjects field only for subject teachers and HODs
            if (role === 'subject_teacher' || role === 'head_of_department') {
                subjectsField.style.display = 'block';
            } else {
                subjectsField.style.display = 'none';
            }
        } else {
            teachingAssignmentSection.style.display = 'none';
        }
    });
    
    // Trigger on page load
    window.addEventListener('load', function() {
        const accountType = document.getElementById('account_type').value;
        const departmentField = document.getElementById('department-field');
        const teachingAssignmentSection = document.getElementById('teaching-assignment-section');
        const subjectsField = document.getElementById('subjects-field');
        
        // Show department field for HOD and Subject Teacher
        if (accountType === 'head_of_department' || accountType === 'subject_teacher') {
            departmentField.style.display = 'block';
        }

        // Show teaching assignment section for teachers, subject teachers, and HODs
        if (accountType === 'teacher' || accountType === 'subject_teacher' || accountType === 'head_of_department') {
            teachingAssignmentSection.style.display = 'block';
            
            // Show subjects field only for subject teachers and HODs
            if (accountType === 'subject_teacher' || accountType === 'head_of_department') {
                subjectsField.style.display = 'block';
            } else {
                subjectsField.style.display = 'none';
            }
        }
    });

    // Show/hide password display checkbox when password field is filled
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


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/school/staff/edit.blade.php ENDPATH**/ ?>