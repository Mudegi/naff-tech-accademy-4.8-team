@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="header">
        <div class="header-content">
            <div>
                <h1>Edit Role</h1>
                <p>Manage role permissions and details</p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>Back to Roles
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" id="roleForm">
        @csrf
        @method('PUT')
        <div class="content-grid">
            <!-- Left Column - Role Details -->
            <div class="column">
                <div class="card">
                    <div class="card-header">
                        <h2>Role Details</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" 
                                class="form-input" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" 
                                class="form-input" 
                                rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="column permissions-column">
                <div class="card">
                    <div class="card-header">
                        <h2>Permissions</h2>
                    </div>
                    <div class="card-body">
                        <div class="search-container">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="permission-search" 
                                    placeholder="Search permissions...">
                            </div>
                        </div>
                        
                        <div id="permissions-list">
                            @php
                                $grouped = [];
                                foreach ($permissions as $permission) {
                                    $parts = explode('_', $permission->name, 2);
                                    $group = isset($parts[1]) ? $parts[1] : $parts[0];
                                    $grouped[$group][] = $permission;
                                }
                            @endphp
                            
                            @foreach($grouped as $group => $perms)
                                <div class="permission-group">
                                    <div class="group-header">
                                        <div class="group-title">
                                            <i class="fas fa-shield-alt"></i>
                                            <h3>{{ str_replace('_', ' ', ucfirst($group)) }}</h3>
                                        </div>
                                    </div>
                                    <div class="permission-items">
                                        @foreach($perms as $permission)
                                            <div class="permission-item">
                                                <input type="checkbox" 
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]" 
                                                    value="{{ $permission->id }}"
                                                    class="permission-checkbox"
                                                    data-group="{{ $group }}"
                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ str_replace('_', ' ', $permission->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-button">
                <i class="fas fa-save"></i>Update Role & Permissions
            </button>
        </div>
    </form>
</div>

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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('[Permission] Changed:', this.value, this.checked);
        });
    });
});
</script>
@endpush

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    color: #333;
}

.header p {
    margin: 0;
    color: #666;
}

.back-button {
    padding: 8px 16px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.back-button:hover {
    background: #e0e0e0;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    margin-bottom: 20px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: 100%;
}

.card-header {
    padding: 16px;
    border-bottom: 1px solid #eee;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.card-body {
    padding: 16px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
}

.form-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-input:focus {
    outline: none;
    border-color: #007bff;
}

.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.submit-button {
    padding: 12px 24px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 500;
}

.submit-button:hover {
    background: #0056b3;
}

.search-container {
    margin-bottom: 16px;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.search-box input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-box input:focus {
    outline: none;
    border-color: #007bff;
}

.permissions-column {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.permission-group {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 16px;
}

.group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.group-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.select-group-label {
    display: none;
}

.alert {
    padding: 16px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.select-all-container,
.select-all-label,
.select-group-label {
    display: none;
}

.permission-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
}

.permission-items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 8px;
}

.permission-checkbox,
.select-group,
#select-all-permissions {
    width: 16px;
    height: 16px;
    margin: 0;
    vertical-align: middle;
}
</style> 