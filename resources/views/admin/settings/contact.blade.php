@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="settings-container">
        <h1 class="page-title">Contact Page Settings</h1>

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

        <form action="{{ route('admin.settings.contact.update') }}" method="POST" class="settings-form">
            @csrf
            @method('PUT')

            <!-- Meta Tags Section -->
            <div class="settings-section">
                <h2 class="section-header">Meta Tags</h2>
                <div class="form-group">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $contactPage->meta_title ?? '') }}" class="form-input">
                    </div>
                    @error('meta_title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_author" class="form-label">Meta Author</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="meta_author" id="meta_author" value="{{ old('meta_author', $contactPage->meta_author ?? '') }}" class="form-input">
                    </div>
                    @error('meta_author')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-tags"></i>
                        </span>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $contactPage->meta_keywords ?? '') }}" class="form-input">
                    </div>
                    @error('meta_keywords')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-align-left"></i>
                        </span>
                        <textarea name="meta_description" id="meta_description" rows="4" class="form-input form-textarea">{{ old('meta_description', $contactPage->meta_description ?? '') }}</textarea>
                    </div>
                    @error('meta_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_robots" class="form-label">Meta Robots</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-robot"></i>
                        </span>
                        <input type="text" name="meta_robots" id="meta_robots" value="{{ old('meta_robots', $contactPage->meta_robots ?? '') }}" class="form-input" placeholder="e.g., index, follow">
                    </div>
                    @error('meta_robots')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_language" class="form-label">Meta Language</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-language"></i>
                        </span>
                        <input type="text" name="meta_language" id="meta_language" value="{{ old('meta_language', $contactPage->meta_language ?? '') }}" class="form-input" placeholder="e.g., en-US">
                    </div>
                    @error('meta_language')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_revisit_after" class="form-label">Meta Revisit After</label>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-clock"></i>
                        </span>
                        <input type="text" name="meta_revisit_after" id="meta_revisit_after" value="{{ old('meta_revisit_after', $contactPage->meta_revisit_after ?? '') }}" class="form-input" placeholder="e.g., 7 days">
                    </div>
                    @error('meta_revisit_after')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="settings-section">
                <h2 class="section-header">Contact Information</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="contact_title" class="form-label">
                            <i class="fas fa-heading"></i> Contact Title
                        </label>
                        <input type="text" name="contact_title" id="contact_title" value="{{ old('contact_title', $contactPage->contact_title ?? '') }}" class="form-input">
                        @error('contact_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_phone" class="form-label">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $contactPage->contact_phone ?? '') }}" class="form-input">
                        @error('contact_phone')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_phone_hours" class="form-label">
                            <i class="fas fa-clock"></i> Phone Hours
                        </label>
                        <input type="text" name="contact_phone_hours" id="contact_phone_hours" value="{{ old('contact_phone_hours', $contactPage->contact_phone_hours ?? '') }}" class="form-input">
                        @error('contact_phone_hours')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $contactPage->contact_email ?? '') }}" class="form-input">
                        @error('contact_email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_description" class="form-label">
                        <i class="fas fa-align-left"></i> Contact Description
                    </label>
                    <textarea name="contact_description" id="contact_description" rows="4" class="form-input form-textarea">{{ old('contact_description', $contactPage->contact_description ?? '') }}</textarea>
                    @error('contact_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="contact_address" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Address
                    </label>
                    <textarea name="contact_address" id="contact_address" rows="3" class="form-input form-textarea">{{ old('contact_address', $contactPage->contact_address ?? '') }}</textarea>
                    @error('contact_address')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Map Section -->
            <div class="settings-section">
                <h2 class="section-header">Map Settings</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="map_title" class="form-label">
                            <i class="fas fa-heading"></i> Map Title
                        </label>
                        <input type="text" name="map_title" id="map_title" value="{{ old('map_title', $contactPage->map_title ?? '') }}" class="form-input">
                        @error('map_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="map_embed_url" class="form-label">
                            <i class="fas fa-map"></i> Map Embed URL
                        </label>
                        <input type="text" name="map_embed_url" id="map_embed_url" value="{{ old('map_embed_url', $contactPage->map_embed_url ?? '') }}" class="form-input">
                        @error('map_embed_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="map_description" class="form-label">
                        <i class="fas fa-align-left"></i> Map Description
                    </label>
                    <textarea name="map_description" id="map_description" rows="4" class="form-input form-textarea">{{ old('map_description', $contactPage->map_description ?? '') }}</textarea>
                    @error('map_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="map_opening_hours_monday_friday" class="form-label">
                            <i class="fas fa-clock"></i> Monday-Friday Hours
                        </label>
                        <input type="text" name="map_opening_hours_monday_friday" id="map_opening_hours_monday_friday" 
                            value="{{ old('map_opening_hours_monday_friday', $contactPage->map_opening_hours_monday_friday ?? '') }}" class="form-input">
                        @error('map_opening_hours_monday_friday')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="map_opening_hours_saturday" class="form-label">
                            <i class="fas fa-clock"></i> Saturday Hours
                        </label>
                        <input type="text" name="map_opening_hours_saturday" id="map_opening_hours_saturday" 
                            value="{{ old('map_opening_hours_saturday', $contactPage->map_opening_hours_saturday ?? '') }}" class="form-input">
                        @error('map_opening_hours_saturday')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="map_opening_hours_sunday" class="form-label">
                            <i class="fas fa-clock"></i> Sunday Hours
                        </label>
                        <input type="text" name="map_opening_hours_sunday" id="map_opening_hours_sunday" 
                            value="{{ old('map_opening_hours_sunday', $contactPage->map_opening_hours_sunday ?? '') }}" class="form-input">
                        @error('map_opening_hours_sunday')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Schema.org Section -->
            <div class="settings-section">
                <h2 class="section-header">Schema.org Information</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="schema_name" class="form-label">
                            <i class="fas fa-building"></i> Organization Name
                        </label>
                        <input type="text" name="schema_name" id="schema_name" value="{{ old('schema_name', $contactPage->schema_name ?? '') }}" class="form-input">
                        @error('schema_name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_telephone" class="form-label">
                            <i class="fas fa-phone"></i> Schema Telephone
                        </label>
                        <input type="text" name="schema_telephone" id="schema_telephone" value="{{ old('schema_telephone', $contactPage->schema_telephone ?? '') }}" class="form-input">
                        @error('schema_telephone')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_email" class="form-label">
                            <i class="fas fa-envelope"></i> Schema Email
                        </label>
                        <input type="email" name="schema_email" id="schema_email" value="{{ old('schema_email', $contactPage->schema_email ?? '') }}" class="form-input">
                        @error('schema_email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_opening_hours" class="form-label">
                            <i class="fas fa-clock"></i> Schema Opening Hours
                        </label>
                        <input type="text" name="schema_opening_hours" id="schema_opening_hours" value="{{ old('schema_opening_hours', $contactPage->schema_opening_hours ?? '') }}" class="form-input">
                        @error('schema_opening_hours')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="schema_description" class="form-label">
                        <i class="fas fa-align-left"></i> Schema Description
                    </label>
                    <textarea name="schema_description" id="schema_description" rows="4" class="form-input form-textarea">{{ old('schema_description', $contactPage->schema_description ?? '') }}</textarea>
                    @error('schema_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="schema_street_address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Street Address
                        </label>
                        <input type="text" name="schema_street_address" id="schema_street_address" value="{{ old('schema_street_address', $contactPage->schema_street_address ?? '') }}" class="form-input">
                        @error('schema_street_address')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_address_locality" class="form-label">
                            <i class="fas fa-city"></i> City
                        </label>
                        <input type="text" name="schema_address_locality" id="schema_address_locality" value="{{ old('schema_address_locality', $contactPage->schema_address_locality ?? '') }}" class="form-input">
                        @error('schema_address_locality')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_address_region" class="form-label">
                            <i class="fas fa-map"></i> State/Region
                        </label>
                        <input type="text" name="schema_address_region" id="schema_address_region" value="{{ old('schema_address_region', $contactPage->schema_address_region ?? '') }}" class="form-input">
                        @error('schema_address_region')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_postal_code" class="form-label">
                            <i class="fas fa-mail-bulk"></i> Postal Code
                        </label>
                        <input type="text" name="schema_postal_code" id="schema_postal_code" value="{{ old('schema_postal_code', $contactPage->schema_postal_code ?? '') }}" class="form-input">
                        @error('schema_postal_code')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="schema_address_country" class="form-label">
                            <i class="fas fa-globe"></i> Country
                        </label>
                        <input type="text" name="schema_address_country" id="schema_address_country" value="{{ old('schema_address_country', $contactPage->schema_address_country ?? '') }}" class="form-input">
                        @error('schema_address_country')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
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
                        <input type="text" name="og_title" id="og_title" value="{{ old('og_title', $contactPage->og_title ?? '') }}" class="form-input">
                        @error('og_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="og_image" class="form-label">
                            <i class="fas fa-image"></i> Open Graph Image
                        </label>
                        <input type="text" name="og_image" id="og_image" value="{{ old('og_image', $contactPage->og_image ?? '') }}" class="form-input">
                        @error('og_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="og_description" class="form-label">
                        <i class="fas fa-align-left"></i> Open Graph Description
                    </label>
                    <textarea name="og_description" id="og_description" rows="4" class="form-input form-textarea">{{ old('og_description', $contactPage->og_description ?? '') }}</textarea>
                    @error('og_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="twitter_title" class="form-label">
                            <i class="fab fa-twitter"></i> Twitter Title
                        </label>
                        <input type="text" name="twitter_title" id="twitter_title" value="{{ old('twitter_title', $contactPage->twitter_title ?? '') }}" class="form-input">
                        @error('twitter_title')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="twitter_image" class="form-label">
                            <i class="fas fa-image"></i> Twitter Image
                        </label>
                        <input type="text" name="twitter_image" id="twitter_image" value="{{ old('twitter_image', $contactPage->twitter_image ?? '') }}" class="form-input">
                        @error('twitter_image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="twitter_description" class="form-label">
                        <i class="fas fa-align-left"></i> Twitter Description
                    </label>
                    <textarea name="twitter_description" id="twitter_description" rows="4" class="form-input form-textarea">{{ old('twitter_description', $contactPage->twitter_description ?? '') }}</textarea>
                    @error('twitter_description')
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
}
</style>
@endsection 