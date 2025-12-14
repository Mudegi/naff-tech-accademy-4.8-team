

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">University Cut-Offs</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="<?php echo e(route('admin.university-cut-offs.export', request()->query())); ?>" class="dashboard-btn" style="background: #059669; color: white;">
                <i class="fas fa-file-download"></i> Export to Excel
            </a>
            <a href="<?php echo e(route('admin.university-cut-offs.import')); ?>" class="dashboard-btn" style="background: #10b981; color: white;">
                <i class="fas fa-file-upload"></i> Import from Excel/CSV
            </a>
            <a href="<?php echo e(route('admin.university-cut-offs.create')); ?>" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-plus"></i> Add New Cut-Off
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="dashboard-alert dashboard-alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="dashboard-alert dashboard-alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

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
        <form action="<?php echo e(route('admin.university-cut-offs.index')); ?>" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-item">
                    <label>Search</label>
                    <input type="text" name="search" class="filter-input" value="<?php echo e(request('search')); ?>" placeholder="Search by university, course, or faculty">
                </div>
                <div class="filter-item">
                    <label>University</label>
                    <select name="university" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Universities</option>
                        <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $university): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($university); ?>" <?php echo e(request('university') == $university ? 'selected' : ''); ?>><?php echo e($university); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Academic Year</label>
                    <select name="academic_year" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>" <?php echo e(request('academic_year') == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Per Page</label>
                    <select name="per_page" class="filter-input" onchange="this.form.submit()">
                        <option value="15" <?php echo e(request('per_page', 15) == 15 ? 'selected' : ''); ?>>15</option>
                        <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                </div>
                <div class="filter-item">
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <?php if(request()->hasAny(['search', 'university', 'academic_year', 'status', 'per_page'])): ?>
                        <a href="<?php echo e(route('admin.university-cut-offs.index')); ?>" class="dashboard-btn dashboard-btn-secondary" style="margin-left: 10px;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Cut-Offs Table -->
    <div class="dashboard-table-container">
        <?php if($cutOffs->count() > 0): ?>
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
                    <?php $__currentLoopData = $cutOffs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cutOff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: #6b7280;">
                                <?php echo e($cutOffs->firstItem() + $index); ?>

                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1a1a1a;"><?php echo e($cutOff->university_name); ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #374151;"><?php echo e($cutOff->course_name); ?></div>
                                <?php if($cutOff->faculty): ?>
                                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 2px;"><?php echo e($cutOff->faculty); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($cutOff->course_code): ?>
                                    <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; font-family: monospace;">
                                        <?php echo e($cutOff->course_code); ?>

                                    </span>
                                <?php else: ?>
                                    <span style="color: #d1d5db;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-transform: capitalize;">
                                    <?php echo e($cutOff->degree_type); ?>

                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                                    <?php echo e($cutOff->minimum_principal_passes); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($cutOff->cut_off_points_male && $cutOff->cut_off_points_female): ?>
                                    
                                    <div style="font-size: 0.875rem;">
                                        <div><strong style="color: #3b82f6;"><i class="fas fa-mars"></i> Male:</strong> <?php echo e(number_format($cutOff->cut_off_points_male, 1)); ?></div>
                                        <div><strong style="color: #ec4899;"><i class="fas fa-venus"></i> Female:</strong> <?php echo e(number_format($cutOff->cut_off_points_female, 1)); ?></div>
                                    </div>
                                <?php elseif($cutOff->cut_off_points): ?>
                                    
                                    <span style="font-weight: 600; color: #059669; font-size: 1.125rem;"><?php echo e(number_format($cutOff->cut_off_points, 1)); ?></span>
                                <?php else: ?>
                                    
                                    <span style="color: #9ca3af; font-style: italic;">Not specified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($cutOff->essential_subjects && count($cutOff->essential_subjects) > 0): ?>
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; max-width: 220px;">
                                        <?php $__currentLoopData = $cutOff->essential_subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; white-space: nowrap;">
                                                <i class="fas fa-check-circle" style="font-size: 0.625rem; margin-right: 0.25rem;"></i><?php echo e($subject); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #9ca3af; font-style: italic; font-size: 0.875rem;">No requirements</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                                    <?php echo e($cutOff->academic_year); ?>

                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?php echo e(route('admin.university-cut-offs.edit', $cutOff->id)); ?>" class="action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.university-cut-offs.destroy', $cutOff->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this cut-off?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-btn action-btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: center; align-items: center;">
                <?php echo e($cutOffs->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 3rem;">
                <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <h3 style="color: #6b7280; margin-bottom: 0.5rem;">No Cut-Offs Found</h3>
                <p style="color: #9ca3af; margin-bottom: 1.5rem;">Get started by adding your first university cut-off.</p>
                <a href="<?php echo e(route('admin.university-cut-offs.create')); ?>" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-plus"></i> Add New Cut-Off
                </a>
            </div>
        <?php endif; ?>
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

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-table {
        font-size: 0.8125rem;
    }
    
    .dashboard-table th,
    .dashboard-table td {
        padding: 0.75rem 0.5rem;
    }
}

@media (max-width: 768px) {
    .dashboard-table-container {
        overflow-x: auto;
    }
    
    .dashboard-table {
        min-width: 1000px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .pagination-wrapper .pagination {
        gap: 0.25rem;
    }
    
    .pagination-wrapper .page-link {
        min-width: 36px;
        padding: 0.5rem 0.625rem;
        font-size: 0.8125rem;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/university-cut-offs/index.blade.php ENDPATH**/ ?>