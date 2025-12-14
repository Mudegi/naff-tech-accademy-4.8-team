@extends('layouts.dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/footer-settings.css') }}">

<div class="footer-settings-container">
    <div class="footer-settings-card">
        <h2 class="section-header">Footer Settings</h2>

        @if(session('status') === 'success')
            <div class="alert alert-success" role="alert">
                <p class="font-bold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('status') === 'error')
            <div class="alert alert-error" role="alert">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.settings.footer.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="footer-section">
                <h3 class="section-header">About Section</h3>
                <div class="form-group">
                    <label for="about_title" class="form-label">About Title</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="about_title" id="about_title" value="{{ old('about_title', $footer->about_title ?? '') }}" 
                            class="form-input">
                    </div>
                    @error('about_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="about_description" class="form-label">About Description</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-align-left"></i>
                        </span>
                        <textarea name="about_description" id="about_description" rows="4" 
                            class="form-input form-textarea">{{ old('about_description', $footer->about_description ?? '') }}</textarea>
                    </div>
                    @error('about_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="footer-section">
                <h3 class="section-header">Contact Information</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="contact_email" class="form-label">
                            <i class="fas fa-envelope social-icon"></i>Email Address
                        </label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $footer->contact_email ?? '') }}" 
                            class="form-input">
                        @error('contact_email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_phone" class="form-label">
                            <i class="fas fa-phone social-icon"></i>Phone Number
                        </label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $footer->contact_phone ?? '') }}" 
                            class="form-input">
                        @error('contact_phone')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_address" class="form-label">
                        <i class="fas fa-map-marker-alt social-icon"></i>Address
                    </label>
                    <input type="text" name="contact_address" id="contact_address" value="{{ old('contact_address', $footer->contact_address ?? '') }}" 
                        class="form-input">
                    @error('contact_address')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="footer-section">
                <h3 class="section-header">Social Media Links</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="facebook_url" class="form-label">
                            <i class="fab fa-facebook social-icon facebook-icon"></i>Facebook URL
                        </label>
                        <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $footer->facebook_url ?? '') }}" 
                            class="form-input">
                        @error('facebook_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="twitter_url" class="form-label">
                            <i class="fab fa-twitter social-icon twitter-icon"></i>Twitter URL
                        </label>
                        <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $footer->twitter_url ?? '') }}" 
                            class="form-input">
                        @error('twitter_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="instagram_url" class="form-label">
                            <i class="fab fa-instagram social-icon instagram-icon"></i>Instagram URL
                        </label>
                        <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $footer->instagram_url ?? '') }}" 
                            class="form-input">
                        @error('instagram_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url" class="form-label">
                            <i class="fab fa-linkedin social-icon linkedin-icon"></i>LinkedIn URL
                        </label>
                        <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $footer->linkedin_url ?? '') }}" 
                            class="form-input">
                        @error('linkedin_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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
@endsection 