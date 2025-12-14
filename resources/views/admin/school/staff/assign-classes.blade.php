@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner assign-classes-page">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-chalkboard-teacher text-blue-600 mr-3"></i>
                Assign Classes to {{ $staff->name }}
            </h1>
            <div class="breadcrumbs">
                <span>Home</span> <span class="breadcrumb-sep">/</span> 
                <span>School</span> <span class="breadcrumb-sep">/</span> 
                <span><a href="{{ route('admin.school.staff.index') }}" class="breadcrumb-link">Staff</a></span> <span class="breadcrumb-sep">/</span> 
                <span class="breadcrumb-active">Assign Classes</span>
            </div>
        </div>
        <a href="{{ route('admin.school.staff.index') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Staff
        </a>
    </div>

    @if (session('success'))
        <div class="alert-modern alert-success animate-slide-down">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-modern alert-error animate-slide-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Staff Info Card -->
    <div class="table-card-modern mb-4">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-user table-header-icon"></i>
                <h3 class="table-header-title">Teacher Information</h3>
            </div>
        </div>
        <div class="staff-info-content">
            <div class="staff-info-grid">
                <div class="staff-info-item">
                    <div class="staff-info-label">
                        <i class="fas fa-user-circle"></i>
                        Name
                    </div>
                    <div class="staff-info-value">{{ $staff->name }}</div>
                </div>
                <div class="staff-info-item">
                    <div class="staff-info-label">
                        <i class="fas fa-envelope"></i>
                        Email
                    </div>
                    <div class="staff-info-value">{{ $staff->email }}</div>
                </div>
                <div class="staff-info-item">
                    <div class="staff-info-label">
                        <i class="fas fa-user-tag"></i>
                        Role
                    </div>
                    <div class="staff-info-value">
                        <span class="role-badge role-teacher">
                            {{ ucfirst(str_replace('_', ' ', $staff->account_type)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Assignment Form -->
    <div class="table-card-modern">
        <div class="table-header-modern">
            <div class="table-header-left">
                <i class="fas fa-chalkboard table-header-icon"></i>
                <h3 class="table-header-title">Select Classes</h3>
                <span class="table-count-badge">{{ $classes->count() }} {{ Str::plural('Class', $classes->count()) }} Available</span>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.school.staff.update-classes', $staff->id) }}" class="assign-classes-form">
            @csrf
            
            @if($classes->count() > 0)
                <div class="form-section">
                    <div class="form-controls-header">
                        <label class="form-controls-label">
                            <i class="fas fa-list-check"></i>
                            Select classes to assign to this teacher:
                        </label>
                        <div class="form-controls-buttons">
                            <button type="button" onclick="selectAll()" class="control-btn control-btn-select">
                                <i class="fas fa-check-double mr-1"></i>
                                Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="control-btn control-btn-deselect">
                                <i class="fas fa-times mr-1"></i>
                                Deselect All
                            </button>
                        </div>
                    </div>
                    
                    <div class="classes-grid-container">
                        <div class="classes-grid" id="classesGrid">
                            @foreach($classes as $class)
                                <label class="class-card {{ in_array($class->id, $assignedClassIds) ? 'class-card-selected' : '' }}">
                                    <input 
                                        type="checkbox" 
                                        name="classes[]" 
                                        value="{{ $class->id }}"
                                        {{ in_array($class->id, $assignedClassIds) ? 'checked' : '' }}
                                        class="class-checkbox"
                                        onchange="updateCardState(this)"
                                    >
                                    <div class="class-card-content">
                                        <div class="class-card-header">
                                            <div class="class-card-icon">
                                                <i class="fas fa-chalkboard"></i>
                                            </div>
                                            <div class="class-card-check">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="class-card-body">
                                            <h4 class="class-card-name">{{ $class->name }}</h4>
                                            @if($class->term)
                                                <p class="class-card-term">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ $class->term }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="selected-count-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="selectedCount">0</span> of {{ $classes->count() }} classes selected
                    </div>
                </div>
            @else
                <div class="empty-classes-state">
                    <div class="empty-classes-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h3 class="empty-classes-title">No Classes Available</h3>
                    <p class="empty-classes-text">
                        Please create classes first before assigning them to teachers.
                    </p>
                    <a href="{{ route('admin.school.classes.index') }}" class="btn-modern btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create Classes
                    </a>
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('admin.school.staff.index') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                @if($classes->count() > 0)
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save mr-2"></i> Save Assignments
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.class-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        updateCardState(checkbox);
    });
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.class-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        updateCardState(checkbox);
    });
    updateSelectedCount();
}

function updateCardState(checkbox) {
    const card = checkbox.closest('.class-card');
    if (checkbox.checked) {
        card.classList.add('class-card-selected');
    } else {
        card.classList.remove('class-card-selected');
    }
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.class-checkbox:checked').length;
    const total = document.querySelectorAll('.class-checkbox').length;
    const countElement = document.getElementById('selectedCount');
    if (countElement) {
        countElement.textContent = checked;
    }
}

// Initialize count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endpush

@push('styles')
<style>
.assign-classes-page {
    padding: 1.5rem;
}

/* Staff Info Card */
.staff-info-content {
    padding: 1.5rem;
}

.staff-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.staff-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.staff-info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.staff-info-label i {
    color: #3b82f6;
    font-size: 0.875rem;
}

.staff-info-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
}

/* Form Section */
.form-section {
    padding: 1.5rem;
}

.form-controls-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
    flex-wrap: wrap;
    gap: 1rem;
}

.form-controls-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a1a1a;
}

.form-controls-label i {
    color: #3b82f6;
}

.form-controls-buttons {
    display: flex;
    gap: 0.75rem;
}

.control-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.control-btn-select {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.control-btn-select:hover {
    background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.control-btn-deselect {
    background: #f3f4f6;
    color: #4b5563;
}

.control-btn-deselect:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

/* Classes Grid */
.classes-grid-container {
    margin-bottom: 1.5rem;
}

.classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    max-height: 500px;
    overflow-y: auto;
    padding: 0.5rem;
    border: 2px solid #f3f4f6;
    border-radius: 0.75rem;
    background: #fafafa;
}

.class-card {
    position: relative;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

.class-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.class-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    border-color: #3b82f6;
}

.class-card:hover::before {
    transform: scaleX(1);
}

.class-card-selected {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.class-card-selected::before {
    transform: scaleX(1);
}

.class-checkbox {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.class-card-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.class-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.class-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    transition: all 0.3s ease;
}

.class-card-selected .class-card-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    transform: scale(1.1);
}

.class-card-check {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #10b981;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

.class-card-selected .class-card-check {
    opacity: 1;
    transform: scale(1);
}

.class-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.class-card-name {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    transition: color 0.3s ease;
}

.class-card-selected .class-card-name {
    color: #1e40af;
}

.class-card-term {
    font-size: 0.8125rem;
    color: #6b7280;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.class-card-term i {
    color: #9ca3af;
    font-size: 0.75rem;
}

/* Selected Count Info */
.selected-count-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #0369a1;
    margin-top: 1rem;
}

.selected-count-info i {
    color: #0284c7;
}

.selected-count-info #selectedCount {
    font-weight: 700;
    color: #0c4a6e;
}

/* Empty State */
.empty-classes-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-classes-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 3rem;
}

.empty-classes-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.75rem;
}

.empty-classes-text {
    font-size: 0.9375rem;
    color: #6b7280;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Form Actions */
.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.5rem;
    border-top: 2px solid #f3f4f6;
    background: linear-gradient(135deg, #fafafa 0%, #f9fafb 100%);
}

/* Scrollbar Styling */
.classes-grid::-webkit-scrollbar {
    width: 8px;
}

.classes-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.classes-grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.classes-grid::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 768px) {
    .staff-info-grid {
        grid-template-columns: 1fr;
    }
    
    .classes-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .form-controls-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-controls-buttons {
        width: 100%;
    }
    
    .control-btn {
        flex: 1;
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .form-actions .btn-modern {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush
@endsection
