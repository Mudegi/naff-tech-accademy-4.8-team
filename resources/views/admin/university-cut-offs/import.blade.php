@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container" style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <!-- Breadcrumbs -->
    <div class="dashboard-breadcrumbs" style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
        <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
        <a href="{{ route('admin.university-cut-offs.index') }}" style="color: #667eea; text-decoration: none;">University Cut-Offs</a>
        <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
        <span style="color: #6b7280;">Import</span>
    </div>

    <!-- Header -->
    <div class="dashboard-header" style="margin-bottom: 2rem;">
        <h1 class="dashboard-title">Import University Cut-Offs</h1>
        <p class="dashboard-subtitle">Automatically fetch and import cut-off points from university websites</p>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 2rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="dashboard-alert dashboard-alert-error" style="margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('import_results'))
        @php $results = session('import_results'); @endphp
        <div class="dashboard-alert" style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 2rem; border-radius: 0.375rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                <strong style="color: #1e40af;">Import Results:</strong>
            </div>
            <div style="color: #1e40af; margin-left: 1.5rem;">
                <div>✓ Successfully imported/updated: <strong>{{ $results['success'] }}</strong> cut-offs</div>
                @if(isset($results['skipped']) && $results['skipped'] > 0)
                    <div style="color: #f59e0b; margin-top: 0.5rem;">⚠ Skipped: <strong>{{ $results['skipped'] }}</strong> rows (empty or invalid data)</div>
                @endif
                @if($results['failed'] > 0)
                    <div style="color: #dc2626; margin-top: 0.5rem;">✗ Failed: <strong>{{ $results['failed'] }}</strong> rows</div>
                @endif
            </div>
            @if(!empty($results['errors']))
                <details style="margin-top: 1rem; margin-left: 1.5rem;">
                    <summary style="cursor: pointer; color: #dc2626; font-weight: 500;">View Errors ({{ count($results['errors']) }})</summary>
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem; color: #991b1b; font-size: 0.875rem;">
                        @foreach(array_slice($results['errors'], 0, 20) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if(count($results['errors']) > 20)
                            <li>... and {{ count($results['errors']) - 20 }} more errors</li>
                        @endif
                    </ul>
                </details>
            @endif
        </div>
    @endif

    <!-- Import Banner -->
    <div class="import-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem; padding: 2rem; margin-bottom: 2rem; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-globe"></i>
            </div>
            <div style="flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.5rem 0;">Automatic Web Scraping</h2>
                <p style="margin: 0; opacity: 0.9;">Fetch cut-off points directly from university websites - no file upload needed!</p>
            </div>
        </div>
    </div>

    <!-- Import Form -->
    <form action="{{ route('admin.university-cut-offs.import.store') }}" method="POST" class="form-card" style="background: white; border-radius: 0.5rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" id="importForm">
        @csrf

        <!-- Import Type Selection -->
        <div style="margin-bottom: 2rem;">
            <label class="form-label" style="margin-bottom: 1rem; display: block;">
                <i class="fas fa-cog" style="margin-right: 8px; color: #667eea;"></i> Import Method
            </label>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer; flex: 1; min-width: 200px; transition: all 0.2s;" id="label-university">
                    <input type="radio" name="import_type" value="university" checked onchange="toggleImportType()" style="margin-right: 0.75rem;">
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">Select University</div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Use configured university</div>
                    </div>
                </label>
                <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer; flex: 1; min-width: 200px; transition: all 0.2s;" id="label-custom">
                    <input type="radio" name="import_type" value="custom_url" onchange="toggleImportType()" style="margin-right: 0.75rem;">
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">Custom URL</div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Paste any PDF or HTML URL</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- University Selection (Default) -->
        <div id="university-selection" class="import-section">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="university_id" class="form-label">
                        <i class="fas fa-university" style="margin-right: 8px; color: #667eea;"></i> University <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="university_id" id="university_id" class="form-input" onchange="updateUniversityInfo()">
                        <option value="">-- Select University --</option>
                        @if(isset($universities) && $universities->count() > 0)
                            @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" 
                                        data-url="{{ $uni->url_pattern ?? $uni->base_url }}" 
                                        data-format="{{ $uni->cut_off_format }}"
                                        data-code="{{ $uni->code }}">
                                    {{ $uni->name }} @if($uni->code)({{ $uni->code }})@endif
                                </option>
                            @endforeach
                        @endif
                        @foreach($supportedUniversities as $key => $uni)
                            @if(strpos($key, 'uni_') === false && ($uni['scraper_available'] ?? false))
                                <option value="{{ $key }}" 
                                        data-url="{{ $uni['url'] }}" 
                                        data-format="{{ $uni['format'] }}"
                                        data-code="{{ $uni['code'] ?? '' }}">
                                    {{ $uni['name'] }} @if(!empty($uni['code']))({{ $uni['code'] }})@endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;" id="university-info">
                        Select a configured university from the list
                    </small>
                </div>

                <div class="form-group">
                    <label for="academic_year" class="form-label">
                        <i class="fas fa-calendar" style="margin-right: 8px; color: #667eea;"></i> Academic Year <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y')) }}" required min="2000" max="{{ date('Y') + 1 }}" class="form-input">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        The academic year for which to import cut-off points
                    </small>
                </div>
            </div>
        </div>

        <!-- Custom URL Selection -->
        <div id="custom-url-selection" class="import-section" style="display: none;">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="custom_url" class="form-label">
                        <i class="fas fa-link" style="margin-right: 8px; color: #667eea;"></i> URL <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="url" name="custom_url" id="custom_url" value="{{ old('custom_url') }}" class="form-input" placeholder="https://example.com/cut-off-points.pdf">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        Paste the URL to the PDF or HTML page containing cut-off points. The system will auto-detect the format.
                    </small>
                </div>

                <div class="form-group">
                    <label for="custom_university_name" class="form-label">
                        <i class="fas fa-university" style="margin-right: 8px; color: #667eea;"></i> University Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="custom_university_name" id="custom_university_name" value="{{ old('custom_university_name') }}" class="form-input" placeholder="e.g., Makerere University">
                </div>

                <div class="form-group">
                    <label for="custom_university_code" class="form-label">
                        <i class="fas fa-code" style="margin-right: 8px; color: #667eea;"></i> University Code (Optional)
                    </label>
                    <input type="text" name="custom_university_code" id="custom_university_code" value="{{ old('custom_university_code') }}" class="form-input" placeholder="e.g., MAK" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="custom_cut_off_format" class="form-label">
                        <i class="fas fa-file-alt" style="margin-right: 8px; color: #667eea;"></i> Cut-Off Format
                    </label>
                    <select name="custom_cut_off_format" id="custom_cut_off_format" class="form-input">
                        <option value="standard">Standard (Single cut-off)</option>
                        <option value="makerere">Makerere (STEM with gender-specific)</option>
                        <option value="kyambogo">Kyambogo</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="academic_year_custom" class="form-label">
                        <i class="fas fa-calendar" style="margin-right: 8px; color: #667eea;"></i> Academic Year <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="academic_year" id="academic_year_custom" value="{{ old('academic_year', date('Y')) }}" required min="2000" max="{{ date('Y') + 1 }}" class="form-input">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="save_as_university" value="1" style="margin-right: 0.5rem;">
                        <span style="color: #4b5563;">Save this URL as a new university for future use</span>
                    </label>
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                        If checked, this university and URL will be saved to your university list for easy access later
                    </small>
                </div>
            </div>
        </div>

        <!-- University Info Box -->
        <div id="university-details" style="display: none; background: #f9fafb; border-left: 4px solid #667eea; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="fas fa-info-circle" style="color: #667eea; margin-top: 0.25rem;"></i>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;" id="selected-university-name"></div>
                    <div style="font-size: 0.875rem; color: #6b7280;">
                        <div><strong>Source URL:</strong> <a href="#" id="source-url" target="_blank" style="color: #667eea; text-decoration: none;"></a></div>
                        <div style="margin-top: 0.25rem;"><strong>Format:</strong> <span id="format-type" style="text-transform: capitalize;"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="instructions-box" style="background: #f9fafb; border-left: 4px solid #10b981; padding: 1.5rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-lightbulb" style="color: #10b981;"></i> How It Works
            </h3>
            <div style="color: #4b5563; line-height: 1.75;">
                <ol style="margin-left: 1.5rem; margin-bottom: 1rem;">
                    <li>Select a university from the dropdown above</li>
                    <li>Choose the academic year for the cut-off points</li>
                    <li>Click "Import Cut-Offs" to automatically fetch data from the university's website</li>
                    <li>The system will parse the webpage, extract all cut-off points, and import them into the database</li>
                </ol>
                <div style="background: white; padding: 1rem; border-radius: 0.25rem; margin-top: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #059669; font-weight: 500; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle"></i>
                        <strong>Benefits:</strong>
                    </div>
                    <ul style="margin-left: 1.5rem; color: #4b5563;">
                        <li>No manual file download or upload needed</li>
                        <li>Always gets the latest data from the source</li>
                        <li>Automatically updates existing records or creates new ones</li>
                        <li>Handles program categorization (STEM/Other) automatically</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" style="display: none; background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="spinner" style="border: 3px solid #f3f4f6; border-top: 3px solid #f59e0b; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite;"></div>
                <div style="color: #78350f; font-weight: 500;">
                    Fetching data from university website... This may take a few moments.
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary" id="submitBtn">
                <i class="fas fa-download"></i> Import Cut-Offs
            </button>
        </div>
    </form>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function toggleImportType() {
    const importType = document.querySelector('input[name="import_type"]:checked').value;
    const universitySection = document.getElementById('university-selection');
    const customSection = document.getElementById('custom-url-selection');
    const universityId = document.getElementById('university_id');
    const customUrl = document.getElementById('custom_url');
    const customName = document.getElementById('custom_university_name');
    
    // Update radio button styles
    const labelUni = document.getElementById('label-university');
    const labelCustom = document.getElementById('label-custom');
    if (labelUni && labelCustom) {
        labelUni.style.borderColor = importType === 'university' ? '#667eea' : '#e5e7eb';
        labelUni.style.backgroundColor = importType === 'university' ? '#f0f4ff' : 'transparent';
        labelCustom.style.borderColor = importType === 'custom_url' ? '#667eea' : '#e5e7eb';
        labelCustom.style.backgroundColor = importType === 'custom_url' ? '#f0f4ff' : 'transparent';
    }
    
    if (importType === 'university') {
        if (universitySection) universitySection.style.display = 'block';
        if (customSection) customSection.style.display = 'none';
        if (universityId) universityId.required = true;
        if (customUrl) customUrl.required = false;
        if (customName) customName.required = false;
    } else {
        if (universitySection) universitySection.style.display = 'none';
        if (customSection) customSection.style.display = 'block';
        if (universityId) universityId.required = false;
        if (customUrl) customUrl.required = true;
        if (customName) customName.required = true;
    }
}

function updateUniversityInfo() {
    const select = document.getElementById('university_id');
    if (!select) return;
    
    const selectedOption = select.options[select.selectedIndex];
    const detailsBox = document.getElementById('university-details');
    const sourceUrl = document.getElementById('source-url');
    const formatType = document.getElementById('format-type');
    const universityName = document.getElementById('selected-university-name');
    
    if (select.value && selectedOption.dataset.url) {
        const url = selectedOption.dataset.url;
        const format = selectedOption.dataset.format || 'standard';
        const name = selectedOption.text.replace(/ \(.*?\)$/, '');
        
        if (universityName) universityName.textContent = name;
        if (sourceUrl) {
            sourceUrl.textContent = url;
            sourceUrl.href = url;
        }
        if (formatType) formatType.textContent = format;
        if (detailsBox) detailsBox.style.display = 'block';
    } else {
        if (detailsBox) detailsBox.style.display = 'none';
    }
}

// Sync academic year fields
document.addEventListener('DOMContentLoaded', function() {
    const academicYear = document.getElementById('academic_year');
    const academicYearCustom = document.getElementById('academic_year_custom');
    
    if (academicYear && academicYearCustom) {
        academicYear.addEventListener('input', function() {
            academicYearCustom.value = this.value;
        });
        academicYearCustom.addEventListener('input', function() {
            academicYear.value = this.value;
        });
    }
    
    // Initialize toggle
    toggleImportType();
});

document.getElementById('importForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
    }
    if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
    }
});
</script>
@endsection
