<?php $__env->startSection('content'); ?>
<?php
    $user = Auth::user();
    $userPermissions = [];
    $isAdmin = false;
    
    if ($user) {
        // Check if user is admin
        $isAdmin = ($user->account_type === 'admin');
        
        // Get permissions from roles
        $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
        $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
        $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        
        // Admins get all permissions by default
        if ($isAdmin) {
            $allPermissions = DB::table('permissions')->pluck('name')->toArray();
            $userPermissions = array_unique(array_merge($userPermissions, $allPermissions));
        }
    }
?>
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Resources</h1>
        <?php if(in_array('create_resource', $userPermissions) || $isAdmin): ?>
        <a href="<?php echo e(route('admin.resources.create')); ?>" class="dashboard-btn dashboard-btn-primary">
            <i class="fas fa-plus"></i> Add Resource
        </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="dashboard-alert dashboard-alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="dashboard-alert dashboard-alert-error">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- Bulk Actions Bar (Super Admin Only) -->
    <?php if($isAdmin && !$user->school_id): ?>
    <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e9ecef;">
        <div class="bulk-actions-content" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="selection-info">
                <span id="selectedCount">0</span> resource(s) selected
            </div>
            <div class="bulk-actions-buttons">
                <button type="button" class="dashboard-btn dashboard-btn-secondary" onclick="openBulkAssignModal()">
                    <i class="fas fa-school"></i> Assign to Schools
                </button>
                <button type="button" class="dashboard-btn dashboard-btn-outline" onclick="clearSelection()">
                    <i class="fas fa-times"></i> Clear Selection
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-card">
        <form action="<?php echo e(route('admin.resources.index')); ?>" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search resources..." class="filter-input">
                </div>
                <div class="filter-group">
                    <select name="subject" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Subjects</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->id); ?>" <?php echo e(request('subject') == $subject->id ? 'selected' : ''); ?>>
                                <?php echo e($subject->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="topic" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Topics</option>
                        <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($topic->id); ?>" <?php echo e(request('topic') == $topic->id ? 'selected' : ''); ?>>
                                <?php echo e($topic->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="grade_level" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($level); ?>" <?php echo e(request('grade_level') == $level ? 'selected' : ''); ?>>
                                <?php echo e($level); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="term" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Terms</option>
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($term->id); ?>" <?php echo e(request('term') == $term->id ? 'selected' : ''); ?>>
                                <?php echo e($term->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="teacher" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Teachers</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->id); ?>" <?php echo e(request('teacher') == $teacher->id ? 'selected' : ''); ?>>
                                <?php echo e($teacher->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if(request()->hasAny(['search', 'subject', 'topic', 'grade_level', 'term', 'teacher'])): ?>
                        <a href="<?php echo e(route('admin.resources.index')); ?>" class="filter-btn filter-btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="resources-grid">
            <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="resource-card">
                    <!-- Checkbox for bulk selection (Super Admin Only) -->
                    <?php if($isAdmin && !$user->school_id): ?>
                    <div class="resource-checkbox" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                        <input type="checkbox" class="resource-select-checkbox" value="<?php echo e($resource->hash_id); ?>" id="checkbox-<?php echo e($resource->id); ?>" onchange="updateBulkActions()">
                        <label for="checkbox-<?php echo e($resource->id); ?>" style="position: absolute; top: 0; right: 0; width: 20px; height: 20px; cursor: pointer;"></label>
                    </div>
                    <?php endif; ?>
                    
                    <div class="resource-header">
                        <h3 class="resource-title"><?php echo e($resource->title); ?></h3>
                        <p class="resource-description"><?php echo e(Str::limit($resource->description, 100)); ?></p>
                    </div>
                    <div class="resource-tags">
                        <span class="resource-tag grade-level"><?php echo e($resource->grade_level); ?></span>
                        <span class="resource-tag subject"><?php echo e($resource->subject->name); ?></span>
                        <span class="resource-tag term"><?php echo e($resource->term->name); ?></span>
                        <?php if($resource->teacher): ?>
                            <span class="resource-tag teacher"><?php echo e($resource->teacher->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="resource-footer">
                        <div class="resource-actions">
                            <?php if(in_array('view_resource', $userPermissions) || $isAdmin): ?>
                            <a href="<?php echo e(route('admin.resources.show', $resource->hash_id)); ?>" class="action-btn view-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php endif; ?>
                            <?php if(in_array('edit_resource', $userPermissions) || $isAdmin): ?>
                            <a href="<?php echo e(route('admin.resources.edit', $resource->hash_id)); ?>" class="action-btn edit-btn" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                            <?php if(in_array('delete_resource', $userPermissions) || $isAdmin): ?>
                            <form action="<?php echo e(route('admin.resources.destroy', $resource->hash_id)); ?>" method="POST" class="delete-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this resource?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <span class="resource-date"><?php echo e($resource->created_at->diffForHumans()); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="no-resources">
                    <p>No resources found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-pagination">
        <?php echo e($resources->links('vendor.pagination.simple-default')); ?>

    </div>
    <form method="GET" action="<?php echo e(route('admin.resources.index')); ?>" id="perPageForm" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
        <?php $__currentLoopData = request()->except('per_page', 'page'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <label for="per_page" style="font-weight: 500;">Show per page:</label>
        <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit();" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <?php $__currentLoopData = [10, 20, 30, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $limit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($limit); ?>" <?php echo e(request('per_page', 12) == $limit ? 'selected' : ''); ?>><?php echo e($limit); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
</div>

<style>
.dashboard-content-inner {
    padding: 20px;
}

.dashboard-breadcrumbs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.dashboard-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.dashboard-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dashboard-btn i {
    margin-right: 8px;
}

.dashboard-btn-primary {
    background-color: #3498db;
    color: white;
}

.dashboard-btn-primary:hover {
    background-color: #2980b9;
}

.dashboard-alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.dashboard-alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.resource-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.resource-header {
    padding: 16px;
}

.resource-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
}

.resource-description {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.resource-tags {
    padding: 0 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.resource-tag {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.grade-level {
    background-color: #e3f2fd;
    color: #1976d2;
}

.subject {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.term {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

.teacher {
    background-color: #fff3e0;
    color: #f57c00;
}

.resource-footer {
    padding: 12px 16px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.resource-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: #666;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.view-btn:hover {
    color: #3498db;
    border-color: #3498db;
}

.edit-btn:hover {
    color: #f39c12;
    border-color: #f39c12;
}

.delete-btn:hover {
    color: #e74c3c;
    border-color: #e74c3c;
}

.delete-form {
    display: inline;
    margin: 0;
    padding: 0;
}

.resource-date {
    font-size: 12px;
    color: #666;
}

.no-resources {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #666;
}

.dashboard-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item {
    display: flex;
}

.page-link {
    display: inline-block;
    min-width: 36px;
    padding: 8px 14px;
    border-radius: 6px;
    background: #f4f6fa;
    color: #2563eb;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    transition: background 0.2s, color 0.2s, border 0.2s;
}

.page-link:hover {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

.page-item.active .page-link {
    background: #2563eb !important;
    color: #fff !important;
    border-color: #2563eb !important;
    font-weight: bold;
}

.page-item.disabled .page-link {
    background: #f4f6fa;
    color: #b0b0b0;
    border-color: #e5e7eb;
    cursor: not-allowed;
}

/* Bulk Actions Styles */
.bulk-actions-bar {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.bulk-actions-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.selection-info {
    font-weight: 500;
    color: #2c3e50;
}

.bulk-actions-buttons {
    display: flex;
    gap: 10px;
}

.dashboard-btn-outline {
    background: #f8f9fa;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

.dashboard-btn-outline:hover {
    background: #e9ecef;
}

/* Resource Card Checkbox Styles */
.resource-card {
    position: relative;
    padding: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    transition: box-shadow 0.2s;
}

.resource-checkbox {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.resource-select-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #3498db;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

/* Filter Styles */

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    gap: 10px;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    color: #2c3e50;
    background-color: white;
    transition: border-color 0.2s;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
}

.filter-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background-color: #3498db;
    color: white;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
}

.filter-btn:hover {
    background-color: #2980b9;
}

.filter-btn-secondary {
    background-color: #e74c3c;
}

.filter-btn-secondary:hover {
    background-color: #c0392b;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-group {
        flex-direction: column;
    }
}
</style>

<!-- Bulk Assignment Modal (Super Admin Only) -->
<?php if($isAdmin && !$user->school_id): ?>
<div id="bulkAssignModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 0; border: 1px solid #888; width: 90%; max-width: 600px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #2c3e50;">Assign Resources to Schools</h2>
            <button type="button" onclick="closeBulkAssignModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        
        <form id="bulkAssignForm" action="<?php echo e(route('admin.resources.bulk-assign')); ?>" method="POST" style="padding: 20px;">
            <?php echo csrf_field(); ?>
            <div id="selectedResourcesList" style="margin-bottom: 20px; max-height: 200px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                <p style="margin: 0; font-weight: 500; color: #2c3e50;">Selected Resources:</p>
                <div id="selectedResourcesItems" style="margin-top: 10px;">
                    <!-- Selected resources will be populated here -->
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="schoolSelect" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Select Schools:</label>
                <select id="schoolSelect" name="school_ids[]" multiple required style="width: 100%; min-height: 120px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                    <?php $__currentLoopData = \App\Models\School::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($school->id); ?>"><?php echo e($school->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small style="color: #6b7280; font-size: 12px;">Hold Ctrl (Cmd on Mac) to select multiple schools</small>
            </div>
            
            <div class="modal-actions" style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="button" onclick="closeBulkAssignModal()" class="dashboard-btn dashboard-btn-outline" style="background: #f8f9fa; color: #6b7280; border: 1px solid #e5e7eb;">Cancel</button>
                <button type="submit" class="dashboard-btn dashboard-btn-primary">Assign Resources</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
// Bulk selection functionality
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.resource-select-checkbox:checked');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedCount.textContent = checkboxes.length;
    
    if (checkboxes.length > 0) {
        bulkActionsBar.style.display = 'block';
    } else {
        bulkActionsBar.style.display = 'none';
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.resource-select-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

function openBulkAssignModal() {
    const selectedCheckboxes = document.querySelectorAll('.resource-select-checkbox:checked');
    const selectedResourcesItems = document.getElementById('selectedResourcesItems');
    const bulkAssignForm = document.getElementById('bulkAssignForm');
    
    // Clear previous selections
    selectedResourcesItems.innerHTML = '';
    
    // Add selected resources to the form
    selectedCheckboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'resource_ids[]';
        input.value = checkbox.value;
        
        // Find the resource title
        const card = checkbox.closest('.resource-card');
        const title = card.querySelector('.resource-title').textContent;
        
        const item = document.createElement('div');
        item.style.cssText = 'padding: 4px 0; font-size: 14px; color: #4b5563;';
        item.textContent = title;
        
        selectedResourcesItems.appendChild(item);
        bulkAssignForm.appendChild(input);
    });
    
    document.getElementById('bulkAssignModal').style.display = 'block';
}

function closeBulkAssignModal() {
    document.getElementById('bulkAssignModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('bulkAssignModal');
    if (event.target == modal) {
        closeBulkAssignModal();
    }
}
</script>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/resources/index.blade.php ENDPATH**/ ?>