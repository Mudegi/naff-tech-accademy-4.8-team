@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner cutoffs-page">
    <!-- Page Title -->
    <div class="page-title-section">
        <h1 class="dashboard-title">University Cut-Offs</h1>
    </div>

    <!-- Action Buttons -->
    <div class="cutoffs-actions-bar">
        <a href="{{ route('admin.university-cut-offs.export', request()->query()) }}" class="dashboard-btn export-btn">
            <i class="fas fa-file-download"></i> <span class="btn-text">Export to Excel</span>
        </a>
        <a href="{{ route('admin.university-cut-offs.import') }}" class="dashboard-btn import-btn">
            <i class="fas fa-file-upload"></i> <span class="btn-text">Import from CSV/Excel</span>
        </a>
        <a href="{{ route('admin.university-cut-offs.create') }}" class="dashboard-btn dashboard-btn-primary create-btn">
            <i class="fas fa-plus"></i> <span class="btn-text">Add New Cut-Off</span>
        </a>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="dashboard-alert dashboard-alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Info Banner for Export/Import -->
    <div class="dashboard-alert" style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.375rem;">
        <div style="display: flex; align-items-start; gap: 0.75rem;">
            <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 0.25rem; font-size: 1.25rem;"></i>
            <div style="flex: 1;">
                <strong style="color: #1e40af; font-size: 1rem;">Export & Import Workflow</strong>
                <div style="color: #1e40af; margin-top: 0.5rem; font-size: 0.875rem; line-height: 1.6;">
                    <p style="margin: 0 0 0.5rem 0;">
                        <strong>Step 1:</strong> Click <i class="fas fa-file-download"></i> "Export to Excel" to download all university cut-offs including Essential Subjects.
                    </p>
                    <p style="margin: 0 0 0.5rem 0;">
                        <strong>Step 2:</strong> Edit the Excel file offline - you can update cut-off points, modify essential subjects (comma-separated), or add new courses.
                    </p>
                    <p style="margin: 0;">
                        <strong>Step 3:</strong> Click <i class="fas fa-file-upload"></i> "Import from Excel/CSV" to upload your edited file. The system will update existing records or create new ones.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="dashboard-card" style="margin-bottom: 20px;">
        <form action="{{ route('admin.university-cut-offs.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-item">
                    <label>Search</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="Search by university, course, or faculty">
                </div>
                <div class="filter-item">
                    <label>University</label>
                    <select name="university" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Universities</option>
                        @foreach($universities as $university)
                            <option value="{{ $university }}" {{ request('university') == $university ? 'selected' : '' }}>{{ $university }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label>Academic Year</label>
                    <select name="academic_year" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Per Page</label>
                    <select name="per_page" class="filter-input" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="filter-item">
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'university', 'academic_year', 'status', 'per_page']))
                        <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary" style="margin-left: 10px;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Cut-Offs Table -->
    <div class="dashboard-table-container">
        @if($cutOffs->count() > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>University</th>
                        <th>Program Name</th>
                        <th>Program Code</th>
                        <th>Degree Type</th>
                        <th>Min Principal Passes</th>
                        <th>Cut-Off Points</th>
                        <th>Essential Subjects</th>
                        <th>Academic Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cutOffs as $index => $cutOff)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: #6b7280;">
                                {{ $cutOffs->firstItem() + $index }}
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1a1a1a;">{{ $cutOff->university_name }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #374151;">{{ $cutOff->course_name }}</div>
                                @if($cutOff->faculty)
                                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 2px;">{{ $cutOff->faculty }}</div>
                                @endif
                            </td>
                            <td>
                                @if($cutOff->course_code)
                                    <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; font-family: monospace;">
                                        {{ $cutOff->course_code }}
                                    </span>
                                @else
                                    <span style="color: #d1d5db;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-transform: capitalize;">
                                    {{ $cutOff->degree_type }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                                    {{ $cutOff->minimum_principal_passes }}
                                </span>
                            </td>
                            <td>
                                @if($cutOff->cut_off_points_male && $cutOff->cut_off_points_female)
                                    {{-- Gender-specific cut-offs --}}
                                    <div style="font-size: 0.875rem;">
                                        <div><strong style="color: #3b82f6;"><i class="fas fa-mars"></i> Male:</strong> {{ number_format($cutOff->cut_off_points_male, 1) }}</div>
                                        <div><strong style="color: #ec4899;"><i class="fas fa-venus"></i> Female:</strong> {{ number_format($cutOff->cut_off_points_female, 1) }}</div>
                                    </div>
                                @elseif($cutOff->cut_off_points)
                                    {{-- Single cut-off for all --}}
                                    <span style="font-weight: 600; color: #059669; font-size: 1.125rem;">{{ number_format($cutOff->cut_off_points, 1) }}</span>
                                @else
                                    {{-- No cut-off specified --}}
                                    <span style="color: #9ca3af; font-style: italic;">Not specified</span>
                                @endif
                            </td>
                            <td>
                                @if($cutOff->essential_subjects && count($cutOff->essential_subjects) > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; max-width: 220px;">
                                        @foreach($cutOff->essential_subjects as $subject)
                                            <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; white-space: nowrap;">
                                                <i class="fas fa-check-circle" style="font-size: 0.625rem; margin-right: 0.25rem;"></i>{{ $subject }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #9ca3af; font-style: italic; font-size: 0.875rem;">No requirements</span>
                                @endif
                            </td>
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                                    {{ $cutOff->academic_year }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.university-cut-offs.edit', $cutOff->id) }}" class="action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.university-cut-offs.destroy', $cutOff->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this cut-off?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: center; align-items: center;">
                {{ $cutOffs->links() }}
            </div>
        @else
            <div class="empty-state" style="text-align: center; padding: 3rem;">
                <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <h3 style="color: #6b7280; margin-bottom: 0.5rem;">No Cut-Offs Found</h3>
                <p style="color: #9ca3af; margin-bottom: 1.5rem;">Get started by adding your first university cut-off.</p>
                <a href="{{ route('admin.university-cut-offs.create') }}" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-plus"></i> Add New Cut-Off
                </a>
            </div>
        @endif
    </div>
</div>

<style>
/* Pagination Styling */
.pagination-wrapper {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.pagination-wrapper .pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.pagination-wrapper .page-item {
    display: inline-block;
}

.pagination-wrapper .page-link {
    display: inline-block;
    min-width: 40px;
    padding: 0.625rem 0.875rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    text-align: center;
    transition: all 0.2s ease;
    background: white;
}

.pagination-wrapper .page-link:hover:not(.disabled) {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
}

.pagination-wrapper .page-item.disabled .page-link,
.pagination-wrapper .page-item.disabled .page-link:hover {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f9fafb;
    color: #9ca3af;
    border-color: #e5e7eb;
}

.pagination-wrapper .page-item:first-child .page-link,
.pagination-wrapper .page-item:last-child .page-link {
    font-weight: 600;
}

/* Table Improvements */
.dashboard-table-container {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.dashboard-table {
    width: 100%;
    border-collapse: collapse;
}

.dashboard-table thead {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.dashboard-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e5e7eb;
}

.dashboard-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
    color: #1f2937;
}

.dashboard-table tbody tr {
    transition: background-color 0.15s ease;
}

.dashboard-table tbody tr:hover {
    background-color: #f9fafb;
}

.dashboard-table tbody tr:last-child td {
    border-bottom: none;
}

/* Action Buttons */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    background: white;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.action-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.action-btn.action-btn-danger {
    color: #dc2626;
    border-color: #fecaca;
}

.action-btn.action-btn-danger:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* Filter Form Improvements */
.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-item label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.filter-input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #1f2937;
    background: white;
    transition: all 0.2s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Page Layout */
.cutoffs-page {
    max-width: 100%;
    overflow-x: hidden;
}

/* Page Title Section */
.page-title-section {
    margin-bottom: 1rem;
}

.page-title-section .dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

/* Action Buttons Bar */
.cutoffs-actions-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.cutoffs-actions-bar .dashboard-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
    transition: all 0.2s ease;
    text-decoration: none;
    border-radius: 0.5rem;
}

.export-btn {
    background: #059669 !important;
    color: white !important;
    border: none !important;
}

.export-btn:hover {
    background: #047857 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
}

.import-btn {
    background: #10b981 !important;
    color: white !important;
    border: none !important;
}

.import-btn:hover {
    background: #059669 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.create-btn {
    background: #667eea !important;
    color: white !important;
    border: none !important;
}

.create-btn:hover {
    background: #5568d3 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Responsive Buttons */
@media (max-width: 1200px) {
    .cutoffs-actions-bar .dashboard-btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.9375rem;
    }
}

@media (max-width: 768px) {
    .page-title-section .dashboard-title {
        font-size: 1.5rem;
    }
    
    .cutoffs-actions-bar {
        width: 100%;
    }
    
    .cutoffs-actions-bar .dashboard-btn {
        flex: 1;
        justify-content: center;
        min-width: 0;
    }
}

@media (max-width: 640px) {
    .page-title-section .dashboard-title {
        font-size: 1.25rem;
    }
    
    .cutoffs-actions-bar {
        flex-direction: column;
    }
    
    .cutoffs-actions-bar .dashboard-btn {
        width: 100%;
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 480px) {
    .cutoffs-actions-bar .btn-text {
        display: none;
    }
    
    .cutoffs-actions-bar {
        flex-direction: row;
        justify-content: space-between;
    }
    
    .cutoffs-actions-bar .dashboard-btn {
        flex: 1;
        padding: 0.75rem 0.5rem;
    }
}

/* Dashboard Content */
.dashboard-content-inner {
    padding: 1.5rem;
    max-width: 100%;
}

/* Card Styling */
.dashboard-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-table {
        font-size: 0.8125rem;
    }
    
    .dashboard-table th,
    .dashboard-table td {
        padding: 0.75rem 0.5rem;
    }
    
    .dashboard-content-inner {
        padding: 1rem;
    }
}

@media (max-width: 768px) {
    .dashboard-content-inner {
        padding: 0.75rem;
    }
    
    .dashboard-card {
        padding: 1rem;
        border-radius: 0.5rem;
    }
    
    .dashboard-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -0.75rem;
        padding: 0 0.75rem;
    }
    
    .dashboard-table {
        min-width: 1200px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .pagination-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.5rem 0;
    }
    
    .pagination-wrapper .pagination {
        gap: 0.25rem;
        min-width: max-content;
    }
    
    .pagination-wrapper .page-link {
        min-width: 36px;
        padding: 0.5rem 0.625rem;
        font-size: 0.8125rem;
    }
}

@media (max-width: 640px) {
    .dashboard-alert {
        font-size: 0.875rem;
        padding: 0.875rem !important;
    }
    
    .dashboard-alert i {
        font-size: 1rem !important;
    }
    
    .dashboard-alert strong {
        font-size: 0.875rem !important;
    }
    
    .dashboard-alert p {
        font-size: 0.8125rem !important;
    }
}

/* Table Scroll Indicator */
.dashboard-table-container::after {
    content: '← Scroll horizontally to see more →';
    display: none;
    text-align: center;
    padding: 0.75rem;
    font-size: 0.8125rem;
    color: #6b7280;
    font-style: italic;
    background: linear-gradient(to bottom, transparent, #f9fafb);
}

@media (max-width: 768px) {
    .dashboard-table-container::after {
        display: block;
    }
}
</style>
@endsection

