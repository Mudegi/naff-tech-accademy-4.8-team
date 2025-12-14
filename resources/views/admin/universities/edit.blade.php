@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="margin-bottom: 2rem;">
        <h1 class="dashboard-title">Edit University</h1>
        <div class="breadcrumbs" style="margin-top: 0.5rem;">
            <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
            <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
            <a href="{{ route('admin.universities.index') }}" style="color: #667eea; text-decoration: none;">Universities</a>
            <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
            <span style="color: #6b7280;">Edit</span>
        </div>
    </div>

    @if($errors->any())
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.universities.update', $university) }}" method="POST" class="form-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 1200px;">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-university" style="color: #667eea;"></i> Basic Information
            </h2>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-building" style="margin-right: 8px; color: #667eea;"></i> University Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $university->name) }}" required class="form-input" placeholder="e.g., Makerere University">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        The full name of the university
                    </small>
                </div>

                <div class="form-group">
                    <label for="code" class="form-label">
                        <i class="fas fa-code" style="margin-right: 8px; color: #667eea;"></i> University Code
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $university->code) }}" class="form-input" placeholder="e.g., MAK" maxlength="50">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        Short code or abbreviation (optional)
                    </small>
                </div>

                <div class="form-group">
                    <label for="base_url" class="form-label">
                        <i class="fas fa-link" style="margin-right: 8px; color: #667eea;"></i> Base URL
                    </label>
                    <input type="url" name="base_url" id="base_url" value="{{ old('base_url', $university->base_url) }}" class="form-input" placeholder="https://example.ac.ug">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        Main website URL of the university
                    </small>
                </div>
            </div>
        </div>

        <!-- URL Configuration -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-globe" style="color: #667eea;"></i> URL Configuration
            </h2>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="url_pattern" class="form-label">
                    <i class="fas fa-route" style="margin-right: 8px; color: #667eea;"></i> URL Pattern
                </label>
                <input type="text" name="url_pattern" id="url_pattern" value="{{ old('url_pattern', $university->url_pattern) }}" class="form-input" placeholder="https://example.ac.ug/cut-off-points/{year}-{nextYear}.pdf">
                <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                    URL pattern for cut-off points. Use <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">{year}</code> and <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">{nextYear}</code> as placeholders for academic year.
                </small>
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="scraper_type" class="form-label">
                        <i class="fas fa-robot" style="margin-right: 8px; color: #667eea;"></i> Scraper Type <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="scraper_type" id="scraper_type" required class="form-input">
                        <option value="auto" {{ old('scraper_type', $university->scraper_type) == 'auto' ? 'selected' : '' }}>Auto-detect</option>
                        <option value="pdf" {{ old('scraper_type', $university->scraper_type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="html_table" {{ old('scraper_type', $university->scraper_type) == 'html_table' ? 'selected' : '' }}>HTML Table</option>
                        <option value="html_custom" {{ old('scraper_type', $university->scraper_type) == 'html_custom' ? 'selected' : '' }}>HTML Custom</option>
                    </select>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        How to scrape the cut-off points from the URL
                    </small>
                </div>

                <div class="form-group">
                    <label for="cut_off_format" class="form-label">
                        <i class="fas fa-file-alt" style="margin-right: 8px; color: #667eea;"></i> Cut-Off Format <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="cut_off_format" id="cut_off_format" required class="form-input">
                        <option value="standard" {{ old('cut_off_format', $university->cut_off_format) == 'standard' ? 'selected' : '' }}>Standard (Single cut-off)</option>
                        <option value="makerere" {{ old('cut_off_format', $university->cut_off_format) == 'makerere' ? 'selected' : '' }}>Makerere (STEM with gender-specific)</option>
                        <option value="kyambogo" {{ old('cut_off_format', $university->cut_off_format) == 'kyambogo' ? 'selected' : '' }}>Kyambogo</option>
                        <option value="custom" {{ old('cut_off_format', $university->cut_off_format) == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        Format of cut-off points for this university
                    </small>
                </div>
            </div>
        </div>

        <!-- Additional Settings -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-cog" style="color: #667eea;"></i> Additional Settings
            </h2>

            <div class="form-group">
                <label for="notes" class="form-label">
                    <i class="fas fa-sticky-note" style="margin-right: 8px; color: #667eea;"></i> Notes
                </label>
                <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="Any additional notes about this university...">{{ old('notes', $university->notes) }}</textarea>
                <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                    Internal notes for administrators
                </small>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $university->is_active) ? 'checked' : '' }} style="margin-right: 0.5rem; width: 18px; height: 18px;">
                    <span style="color: #374151; font-weight: 500;">Active</span>
                </label>
                <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                    Only active universities will appear in the import dropdown
                </small>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.universities.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-save"></i> Update University
            </button>
        </div>
    </form>
</div>
@endsection

