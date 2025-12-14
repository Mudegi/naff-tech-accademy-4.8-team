<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/footer-settings.css')); ?>">

<div class="footer-settings-container">
    <div class="footer-settings-card">
        <h2 class="section-header">Footer Settings</h2>

        <?php if(session('status') === 'success'): ?>
            <div class="alert alert-success" role="alert">
                <p class="font-bold">Success!</p>
                <p><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        <?php if(session('status') === 'error'): ?>
            <div class="alert alert-error" role="alert">
                <p class="font-bold">Error!</p>
                <p><?php echo e(session('error')); ?></p>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.settings.footer.update')); ?>" method="POST" class="space-y-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="footer-section">
                <h3 class="section-header">About Section</h3>
                <div class="form-group">
                    <label for="about_title" class="form-label">About Title</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="about_title" id="about_title" value="<?php echo e(old('about_title', $footer->about_title ?? '')); ?>" 
                            class="form-input">
                    </div>
                    <?php $__errorArgs = ['about_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="error-message"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="about_description" class="form-label">About Description</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-align-left"></i>
                        </span>
                        <textarea name="about_description" id="about_description" rows="4" 
                            class="form-input form-textarea"><?php echo e(old('about_description', $footer->about_description ?? '')); ?></textarea>
                    </div>
                    <?php $__errorArgs = ['about_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="error-message"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="section-header">Contact Information</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="contact_email" class="form-label">
                            <i class="fas fa-envelope social-icon"></i>Email Address
                        </label>
                        <input type="email" name="contact_email" id="contact_email" value="<?php echo e(old('contact_email', $footer->contact_email ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['contact_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone" class="form-label">
                            <i class="fas fa-phone social-icon"></i>Phone Number
                        </label>
                        <input type="text" name="contact_phone" id="contact_phone" value="<?php echo e(old('contact_phone', $footer->contact_phone ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['contact_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_address" class="form-label">
                        <i class="fas fa-map-marker-alt social-icon"></i>Address
                    </label>
                    <input type="text" name="contact_address" id="contact_address" value="<?php echo e(old('contact_address', $footer->contact_address ?? '')); ?>" 
                        class="form-input">
                    <?php $__errorArgs = ['contact_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="error-message"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="section-header">Social Media Links</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="facebook_url" class="form-label">
                            <i class="fab fa-facebook social-icon facebook-icon"></i>Facebook URL
                        </label>
                        <input type="url" name="facebook_url" id="facebook_url" value="<?php echo e(old('facebook_url', $footer->facebook_url ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['facebook_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="twitter_url" class="form-label">
                            <i class="fab fa-twitter social-icon twitter-icon"></i>Twitter URL
                        </label>
                        <input type="url" name="twitter_url" id="twitter_url" value="<?php echo e(old('twitter_url', $footer->twitter_url ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['twitter_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="instagram_url" class="form-label">
                            <i class="fab fa-instagram social-icon instagram-icon"></i>Instagram URL
                        </label>
                        <input type="url" name="instagram_url" id="instagram_url" value="<?php echo e(old('instagram_url', $footer->instagram_url ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['instagram_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url" class="form-label">
                            <i class="fab fa-linkedin social-icon linkedin-icon"></i>LinkedIn URL
                        </label>
                        <input type="url" name="linkedin_url" id="linkedin_url" value="<?php echo e(old('linkedin_url', $footer->linkedin_url ?? '')); ?>" 
                            class="form-input">
                        <?php $__errorArgs = ['linkedin_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });

    // Add tooltips to social media icons
    const socialIcons = document.querySelectorAll('.social-icon');
    socialIcons.forEach(icon => {
        icon.parentElement.classList.add('tooltip');
        icon.parentElement.setAttribute('data-tooltip', icon.nextSibling.textContent.trim());
    });
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/settings/footer.blade.php ENDPATH**/ ?>