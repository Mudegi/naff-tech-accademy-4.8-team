@extends('layouts.dashboard')

@section('title', 'Add Team Member')

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Team
            </a>
        </div>
        <div class="header-right">
            <h1 class="content-title">Add Team Member</h1>
            <p class="content-subtitle">Create a new team member profile</p>
        </div>
    </div>

    <div class="content-body">
        <div class="form-container">
            <form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data" class="team-form">
                @csrf
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-user"></i>
                            Full Name
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               class="form-input @error('name') error @enderror" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="position" class="form-label">
                            <i class="fas fa-briefcase"></i>
                            Position
                        </label>
                        <input type="text" 
                               id="position" 
                               name="position" 
                               value="{{ old('position') }}" 
                               class="form-input @error('position') error @enderror" 
                               required>
                        @error('position')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order" class="form-label">
                            <i class="fas fa-sort-numeric-up"></i>
                            Sort Order
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', 0) }}" 
                               class="form-input @error('sort_order') error @enderror" 
                               min="0">
                        @error('sort_order')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="form-help">Lower numbers appear first</div>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">
                            <i class="fas fa-image"></i>
                            Profile Image
                        </label>
                        <div class="file-upload-area">
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*" 
                                   class="file-input @error('image') error @enderror"
                                   onchange="previewImage(this)">
                            <label for="image" class="upload-label">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <span class="upload-title">Click to upload image</span>
                                    <span class="upload-subtitle">JPG, PNG, GIF up to 2MB</span>
                                </div>
                            </label>
                        </div>
                        <div id="image-preview" class="image-preview" style="display: none;">
                            <img id="preview-img" src="" alt="Preview">
                            <button type="button" onclick="removeImage()" class="remove-image">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @error('image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="skills" class="form-label">
                        <i class="fas fa-tools"></i>
                        Skills
                    </label>
                    <textarea id="skills" 
                              name="skills" 
                              rows="4" 
                              class="form-textarea @error('skills') error @enderror" 
                              placeholder="Enter skills separated by commas (e.g., Web Development, UI/UX Design, Project Management)"
                              required>{{ old('skills') }}</textarea>
                    @error('skills')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <div class="form-help">Separate multiple skills with commas</div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="checkbox-input">
                        <label for="is_active" class="checkbox-label">
                            <i class="fas fa-check"></i>
                            Active
                        </label>
                    </div>
                    <div class="form-help">Active team members will be displayed on the website</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Team Member
                    </button>
                    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left {
    flex: 0 0 auto;
}

.header-right {
    flex: 1;
    text-align: right;
}

.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.team-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    background: white;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.error, .form-textarea.error {
    border-color: #ef4444;
}

.form-help {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: block;
}

.file-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: border-color 0.2s ease, background-color 0.2s ease;
    background: #fafafa;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.file-input {
    display: none;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    color: #6b7280;
}

.upload-icon {
    font-size: 3rem;
    color: #9ca3af;
    transition: color 0.2s ease;
}

.upload-label:hover .upload-icon {
    color: #3b82f6;
}

.upload-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.upload-title {
    font-weight: 600;
    font-size: 1.125rem;
    color: #374151;
}

.upload-subtitle {
    font-size: 0.875rem;
    opacity: 0.8;
}

.image-preview {
    position: relative;
    margin-top: 1rem;
    display: inline-block;
}

.image-preview img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.remove-image {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.75rem;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.checkbox-input {
    width: 18px;
    height: 18px;
    accent-color: #3b82f6;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-outline-secondary {
    background: transparent;
    color: #6b7280;
    border: 2px solid #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}
</style>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    
    input.value = '';
    preview.style.display = 'none';
}
</script>
@endsection

