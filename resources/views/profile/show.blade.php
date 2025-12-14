@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">My Profile</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Profile</span>
        </div>
    </div>
    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">{{ session('success') }}</div>
    @endif
    <div class="profile-row">
        <!-- Profile Details -->
        <div class="profile-col profile-col-details">
            <div class="profile-card">
                <div class="profile-avatar-section">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile Photo" class="profile-avatar-large profile-avatar-img">
                    @else
                        <div class="profile-avatar-large">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    @endif
                    <div class="profile-info">
                        <div class="profile-name">{{ $user->name }}</div>
                        <div class="profile-email">{{ $user->email }}</div>
                        @if($user->phone_number)
                            <div class="profile-phone">{{ $user->phone_number }}</div>
                        @endif
                    </div>
                </div>
                <hr class="profile-divider">
                <form class="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="profile-form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="profile-input" required>
                        @error('name')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="profile-input" required>
                        @error('email')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="profile-input">
                        @error('phone_number')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="profile_photo_path">Profile Photo</label>
                        <input type="file" id="profile_photo_path" name="profile_photo_path" class="profile-input">
                        @error('profile_photo_path')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
        <!-- Change Password -->
        <div class="profile-col profile-col-password">
            <div class="profile-card">
                <h2 class="profile-section-title">Change Password</h2>
                <form class="profile-form" method="POST" action="#">
                    @csrf
                    <div class="profile-form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="profile-input" placeholder="Enter current password">
                    </div>
                    <div class="profile-form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="profile-input" placeholder="Enter new password">
                    </div>
                    <div class="profile-form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="profile-input" placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 