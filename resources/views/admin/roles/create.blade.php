@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Add New Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Roles</a>
    </div>
    <div class="profile-card" style="max-width:600px;margin:0 auto;">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="profile-form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="profile-input" required>
                @error('name')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="profile-input" rows="3">{{ old('description') }}</textarea>
                @error('description')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">Create Role</button>
        </form>
    </div>
</div>
@endsection 