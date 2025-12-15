<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex">
    <!-- Left side - Image -->
    <div class="hidden lg:block lg:w-1/2 relative">
        <img src="<?php echo e($welcomeLinks->getImageUrl('login_image') ?? 'https://images.unsplash.com/photo-1577896851231-70ef18881754?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80'); ?>" 
             alt="Students learning" 
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-blue-900/50"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white px-12">
                <h1 class="text-4xl font-bold mb-4">Welcome Back</h1>
                <p class="text-lg">Sign in to continue your learning journey</p>
            </div>
        </div>
    </div>

    <!-- Right side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 text-center">
                    Login to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="<?php echo e(route('register')); ?>" class="font-medium text-blue-600 hover:text-blue-500">
                        Register here
                    </a>
                </p>
            </div>

            <?php if(session('status')): ?>
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Success
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <?php echo e(session('status')); ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="<?php echo e(route('login')); ?>" method="POST" id="loginForm">
                <?php echo csrf_field(); ?>

                <div class="space-y-4">
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700">Email or Phone Number</label>
                        <input id="login" 
                               name="login" 
                               type="text" 
                               value="<?php echo e(old('login')); ?>"
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your email or phone number">
                        <?php $__errorArgs = ['login'];
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
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your password">
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
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="<?php echo e(route('password.request')); ?>" class="font-medium text-blue-600 hover:text-blue-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const rememberMeCheckbox = document.getElementById('remember_me');

    // Check if we have saved credentials and remember me state
    const savedLogin = localStorage.getItem('savedLogin');
    const savedPassword = localStorage.getItem('savedPassword');
    const savedRememberMe = localStorage.getItem('rememberMe');

    // Set initial state of remember me checkbox
    if (savedRememberMe === 'true') {
        rememberMeCheckbox.checked = true;
        if (savedLogin && savedPassword) {
            loginInput.value = savedLogin;
            passwordInput.value = savedPassword;
        }
    }

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        if (rememberMeCheckbox.checked) {
            // Save credentials and remember me state to localStorage
            localStorage.setItem('savedLogin', loginInput.value);
            localStorage.setItem('savedPassword', passwordInput.value);
            localStorage.setItem('rememberMe', 'true');
        } else {
            // Remove saved credentials and remember me state
            localStorage.removeItem('savedLogin');
            localStorage.removeItem('savedPassword');
            localStorage.removeItem('rememberMe');
        }
    });

    // Handle remember me checkbox change
    rememberMeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Save remember me state
            localStorage.setItem('rememberMe', 'true');
        } else {
            // Remove saved credentials and remember me state
            localStorage.removeItem('savedLogin');
            localStorage.removeItem('savedPassword');
            localStorage.removeItem('rememberMe');
        }
    });

    // Save remember me state on page unload if checked
    window.addEventListener('beforeunload', function() {
        if (rememberMeCheckbox.checked) {
            localStorage.setItem('rememberMe', 'true');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/frontend/auth/login.blade.php ENDPATH**/ ?>