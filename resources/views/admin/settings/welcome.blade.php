@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="settings-container">
        <h1 class="page-title">Welcome Page Settings</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.welcome.update') }}" method="POST" class="settings-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Hero Section -->
            <div class="settings-section">
                <h2 class="section-header">Hero Section</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="hero_title" class="form-label">
                            <i class="fas fa-heading"></i> Hero Title
                        </label>
                        <input type="text" name="hero_title" id="hero_title" value="{{ old('hero_title', $welcomePage->hero_title ?? '') }}" class="form-input">
                        @error('hero_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hero_subtitle" class="form-label">
                            <i class="fas fa-heading"></i> Hero Subtitle
                        </label>
                        <input type="text" name="hero_subtitle" id="hero_subtitle" value="{{ old('hero_subtitle', $welcomePage->hero_subtitle ?? '') }}" class="form-input">
                        @error('hero_subtitle')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="grid-2">
                    @for($i = 1; $i <= 10; $i++)
                        <div class="form-group">
                            <label for="hero_image_{{ $i }}" class="form-label">
                                <i class="fas fa-image"></i> Hero Image {{ $i }}
                                <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                            </label>
                            <input type="file" name="hero_image_{{ $i }}" id="hero_image_{{ $i }}" 
                                class="form-input file-input" accept="image/*">
                            @if($welcomePage && $welcomePage->{'hero_image_' . $i})
                                <div class="current-image">
                                    <p class="current-image-label">Current Image:</p>
                                    <img src="{{ Storage::url($welcomePage->{'hero_image_' . $i}) }}" 
                                         alt="Current Hero Image {{ $i }}" class="preview-image">
                                </div>
                            @endif
                            @error('hero_image_' . $i)
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    @endfor
                </div>
            </div>

            <!-- About Section -->
            <div class="settings-section">
                <h2 class="section-header">About Section</h2>
                <div class="form-group">
                    <label for="about_title" class="form-label">
                        <i class="fas fa-heading"></i> About Title
                    </label>
                    <input type="text" name="about_title" id="about_title" value="{{ old('about_title', $welcomePage->about_title ?? '') }}" class="form-input">
                    @error('about_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="about_description" class="form-label">
                        <i class="fas fa-align-left"></i> About Description
                    </label>
                    <textarea name="about_description" id="about_description" rows="4" class="form-input form-textarea">{{ old('about_description', $welcomePage->about_description ?? '') }}</textarea>
                    @error('about_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="about_image" class="form-label">
                        <i class="fas fa-image"></i> About Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="about_image" id="about_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->about_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->about_image) }}" 
                                 alt="Current About Image" class="preview-image">
                        </div>
                    @endif
                    @error('about_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Features Section -->
            <div class="settings-section">
                <h2 class="section-header">Features Section</h2>
                <div class="form-group">
                    <label for="features_title" class="form-label">
                        <i class="fas fa-heading"></i> Features Title
                    </label>
                    <input type="text" name="features_title" id="features_title" value="{{ old('features_title', $welcomePage->features_title ?? '') }}" class="form-input">
                    @error('features_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="features_description" class="form-label">
                        <i class="fas fa-align-left"></i> Features Description
                    </label>
                    <textarea name="features_description" id="features_description" rows="4" class="form-input form-textarea">{{ old('features_description', $welcomePage->features_description ?? '') }}</textarea>
                    @error('features_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="features_image" class="form-label">
                        <i class="fas fa-image"></i> Features Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="features_image" id="features_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->features_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->features_image) }}" 
                                 alt="Current Features Image" class="preview-image">
                        </div>
                    @endif
                    @error('features_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Testimonials Section -->
            <div class="settings-section">
                <h2 class="section-header">Testimonials Section</h2>
                <div class="form-group">
                    <label for="testimonials_title" class="form-label">
                        <i class="fas fa-heading"></i> Testimonials Title
                    </label>
                    <input type="text" name="testimonials_title" id="testimonials_title" value="{{ old('testimonials_title', $welcomePage->testimonials_title ?? '') }}" class="form-input">
                    @error('testimonials_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="testimonials_description" class="form-label">
                        <i class="fas fa-align-left"></i> Testimonials Description
                    </label>
                    <textarea name="testimonials_description" id="testimonials_description" rows="4" class="form-input form-textarea">{{ old('testimonials_description', $welcomePage->testimonials_description ?? '') }}</textarea>
                    @error('testimonials_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="testimonials_image" class="form-label">
                        <i class="fas fa-image"></i> Testimonials Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="testimonials_image" id="testimonials_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->testimonials_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->testimonials_image) }}" 
                                 alt="Current Testimonials Image" class="preview-image">
                        </div>
                    @endif
                    @error('testimonials_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- CTA Section -->
            <div class="settings-section">
                <h2 class="section-header">Call to Action Section</h2>
                <div class="form-group">
                    <label for="cta_title" class="form-label">
                        <i class="fas fa-heading"></i> CTA Title
                    </label>
                    <input type="text" name="cta_title" id="cta_title" value="{{ old('cta_title', $welcomePage->cta_title ?? '') }}" class="form-input">
                    @error('cta_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cta_description" class="form-label">
                        <i class="fas fa-align-left"></i> CTA Description
                    </label>
                    <textarea name="cta_description" id="cta_description" rows="4" class="form-input form-textarea">{{ old('cta_description', $welcomePage->cta_description ?? '') }}</textarea>
                    @error('cta_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cta_image" class="form-label">
                        <i class="fas fa-image"></i> CTA Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="cta_image" id="cta_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->cta_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->cta_image) }}" 
                                 alt="Current CTA Image" class="preview-image">
                        </div>
                    @endif
                    @error('cta_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Auth Pages -->
            <div class="settings-section">
                <h2 class="section-header">Authentication Pages</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="login_image" class="form-label">
                            <i class="fas fa-image"></i> Login Page Image
                            <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                        </label>
                        <input type="file" name="login_image" id="login_image" class="form-input file-input" accept="image/*">
                        @if($welcomePage && $welcomePage->login_image)
                            <div class="current-image">
                                <p class="current-image-label">Current Image:</p>
                                <img src="{{ Storage::url($welcomePage->login_image) }}" 
                                     alt="Current Login Image" class="preview-image">
                            </div>
                        @endif
                        @error('login_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="register_image" class="form-label">
                            <i class="fas fa-image"></i> Register Page Image
                            <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                        </label>
                        <input type="file" name="register_image" id="register_image" class="form-input file-input" accept="image/*">
                        @if($welcomePage && $welcomePage->register_image)
                            <div class="current-image">
                                <p class="current-image-label">Current Image:</p>
                                <img src="{{ Storage::url($welcomePage->register_image) }}" 
                                     alt="Current Register Image" class="preview-image">
                            </div>
                        @endif
                        @error('register_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Meta Tags Section -->
            <div class="settings-section">
                <h2 class="section-header">Meta Tags</h2>
                <div class="form-group">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $welcomePage->meta_title ?? '') }}" class="form-input">
                    </div>
                    @error('meta_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-align-left"></i>
                        </span>
                        <textarea name="meta_description" id="meta_description" rows="4" class="form-input form-textarea">{{ old('meta_description', $welcomePage->meta_description ?? '') }}</textarea>
                    </div>
                    @error('meta_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-tags"></i>
                        </span>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $welcomePage->meta_keywords ?? '') }}" class="form-input">
                    </div>
                    @error('meta_keywords')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Social Media Section -->
            <div class="settings-section">
                <h2 class="section-header">Social Media</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="og_title" class="form-label">
                            <i class="fab fa-facebook"></i> Open Graph Title
                        </label>
                        <input type="text" name="og_title" id="og_title" value="{{ old('og_title', $welcomePage->og_title ?? '') }}" class="form-input">
                        @error('og_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="og_image" class="form-label">
                            <i class="fas fa-image"></i> Open Graph Image
                            <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                        </label>
                        <input type="file" name="og_image" id="og_image" class="form-input file-input" accept="image/*">
                        @if($welcomePage && $welcomePage->og_image)
                            <div class="current-image">
                                <p class="current-image-label">Current Image:</p>
                                <img src="{{ Storage::url($welcomePage->og_image) }}" 
                                     alt="Current OG Image" class="preview-image">
                            </div>
                        @endif
                        @error('og_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="og_description" class="form-label">
                        <i class="fas fa-align-left"></i> Open Graph Description
                    </label>
                    <textarea name="og_description" id="og_description" rows="4" class="form-input form-textarea">{{ old('og_description', $welcomePage->og_description ?? '') }}</textarea>
                    @error('og_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="twitter_title" class="form-label">
                            <i class="fab fa-twitter"></i> Twitter Title
                        </label>
                        <input type="text" name="twitter_title" id="twitter_title" value="{{ old('twitter_title', $welcomePage->twitter_title ?? '') }}" class="form-input">
                        @error('twitter_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="twitter_image" class="form-label">
                            <i class="fas fa-image"></i> Twitter Image
                            <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                        </label>
                        <input type="file" name="twitter_image" id="twitter_image" class="form-input file-input" accept="image/*">
                        @if($welcomePage && $welcomePage->twitter_image)
                            <div class="current-image">
                                <p class="current-image-label">Current Image:</p>
                                <img src="{{ Storage::url($welcomePage->twitter_image) }}" 
                                     alt="Current Twitter Image" class="preview-image">
                            </div>
                        @endif
                        @error('twitter_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="twitter_description" class="form-label">
                        <i class="fas fa-align-left"></i> Twitter Description
                    </label>
                    <textarea name="twitter_description" id="twitter_description" rows="4" class="form-input form-textarea">{{ old('twitter_description', $welcomePage->twitter_description ?? '') }}</textarea>
                    @error('twitter_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Mission Section -->
            <div class="settings-section">
                <h2 class="section-header">Mission Section</h2>
                <div class="form-group">
                    <label for="mission_title" class="form-label">
                        <i class="fas fa-heading"></i> Mission Title
                    </label>
                    <input type="text" name="mission_title" id="mission_title" value="{{ old('mission_title', $welcomePage->mission_title ?? '') }}" class="form-input">
                    @error('mission_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mission_description" class="form-label">
                        <i class="fas fa-align-left"></i> Mission Description
                    </label>
                    <textarea name="mission_description" id="mission_description" rows="4" class="form-input form-textarea">{{ old('mission_description', $welcomePage->mission_description ?? '') }}</textarea>
                    @error('mission_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mission_image" class="form-label">
                        <i class="fas fa-image"></i> Mission Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="mission_image" id="mission_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->mission_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->mission_image) }}" 
                                 alt="Current Mission Image" class="preview-image">
                        </div>
                    @endif
                    @error('mission_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Team Section -->
            <div class="settings-section">
                <h2 class="section-header">Team Section</h2>
                <div class="form-group">
                    <label for="team_title" class="form-label">
                        <i class="fas fa-heading"></i> Team Title
                    </label>
                    <input type="text" name="team_title" id="team_title" value="{{ old('team_title', $welcomePage->team_title ?? '') }}" class="form-input">
                    @error('team_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="team_description" class="form-label">
                        <i class="fas fa-align-left"></i> Team Description
                    </label>
                    <textarea name="team_description" id="team_description" rows="4" class="form-input form-textarea">{{ old('team_description', $welcomePage->team_description ?? '') }}</textarea>
                    @error('team_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="team_image" class="form-label">
                        <i class="fas fa-image"></i> Team Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="team_image" id="team_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->team_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->team_image) }}" 
                                 alt="Current Team Image" class="preview-image">
                        </div>
                    @endif
                    @error('team_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Values Section -->
            <div class="settings-section">
                <h2 class="section-header">Values Section</h2>
                <div class="form-group">
                    <label for="values_title" class="form-label">
                        <i class="fas fa-heading"></i> Values Title
                    </label>
                    <input type="text" name="values_title" id="values_title" value="{{ old('values_title', $welcomePage->values_title ?? '') }}" class="form-input">
                    @error('values_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="values_description" class="form-label">
                        <i class="fas fa-align-left"></i> Values Description
                    </label>
                    <textarea name="values_description" id="values_description" rows="4" class="form-input form-textarea">{{ old('values_description', $welcomePage->values_description ?? '') }}</textarea>
                    @error('values_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="values_image" class="form-label">
                        <i class="fas fa-image"></i> Values Image
                        <small class="image-requirements">(Min: 1200x600px, Max: 5MB)</small>
                    </label>
                    <input type="file" name="values_image" id="values_image" class="form-input file-input" accept="image/*">
                    @if($welcomePage && $welcomePage->values_image)
                        <div class="current-image">
                            <p class="current-image-label">Current Image:</p>
                            <img src="{{ Storage::url($welcomePage->values_image) }}" 
                                 alt="Current Values Image" class="preview-image">
                        </div>
                    @endif
                    @error('values_image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.settings-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.settings-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.section-header {
    font-size: 18px;
    font-weight: 600;
    color: #444;
    margin-bottom: 20px;
}

.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #555;
    margin-bottom: 8px;
}

.form-label i {
    margin-right: 8px;
    color: #666;
}

.form-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus {
    border-color: #4a90e2;
    outline: none;
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-group-icon {
    position: absolute;
    left: 12px;
    color: #666;
}

.input-group .form-input {
    padding-left: 35px;
}

.error-message {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}

.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.form-actions {
    margin-top: 30px;
    text-align: right;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary {
    background-color: #4a90e2;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #357abd;
}

.btn i {
    margin-right: 8px;
}

/* File upload and image preview styles */
.file-input {
    padding: 8px 12px;
    border: 2px dashed #ddd;
    border-radius: 4px;
    background-color: #fafafa;
    cursor: pointer;
    transition: border-color 0.2s, background-color 0.2s;
}

.file-input:hover {
    border-color: #4a90e2;
    background-color: #f0f8ff;
}

.image-requirements {
    display: block;
    font-size: 12px;
    color: #666;
    font-weight: normal;
    margin-top: 4px;
}

.current-image {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.current-image-label {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.preview-image {
    max-width: 200px;
    max-height: 120px;
    border-radius: 4px;
    border: 1px solid #ddd;
    object-fit: cover;
}

@media (max-width: 768px) {
    .grid-2 {
        grid-template-columns: 1fr;
    }
    
    .container {
        padding: 10px;
    }
    
    .settings-container {
        padding: 15px;
    }
    
    .preview-image {
        max-width: 150px;
        max-height: 90px;
    }
}
</style>
@endsection 