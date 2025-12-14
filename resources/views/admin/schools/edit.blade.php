@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Edit School</h1>
        <div class="breadcrumbs">
            <a href="{{ route('admin.schools.index') }}">Schools</a> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Edit</span>
        </div>
    </div>

    @if($errors->any())
        <div class="dashboard-alert dashboard-alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.schools.update', $school->id) }}" method="POST" enctype="multipart/form-data" class="form-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')

        <!-- School Information Section -->
        <div class="form-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-school" style="color: #667eea;"></i> School Information
            </h2>

            @if($school->logo)
            <div style="margin-bottom: 20px; padding: 15px; background: #f9fafb; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #e5e7eb;">
                <div>
                    <p style="margin: 0; font-weight: 500; color: #374151;">Current Logo</p>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #6b7280;">Upload a new logo to replace this one</p>
                </div>
            </div>
            @endif

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-building" style="margin-right: 8px; color: #667eea;"></i> School Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $school->name) }}" required class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-envelope" style="margin-right: 8px; color: #667eea;"></i> Email <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $school->email) }}" required class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                <div class="form-group">
                    <label for="phone_number" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-phone" style="margin-right: 8px; color: #667eea;"></i> Phone Number
                    </label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $school->phone_number) }}" class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                <div class="form-group">
                    <label for="website" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-globe" style="margin-right: 8px; color: #667eea;"></i> Website
                    </label>
                    <input type="url" name="website" id="website" value="{{ old('website', $school->website) }}" placeholder="https://example.com" class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="address" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #667eea;"></i> Address
                    </label>
                    <textarea name="address" id="address" rows="3" class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">{{ old('address', $school->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="logo" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-image" style="margin-right: 8px; color: #667eea;"></i> Logo
                    </label>
                    <input type="file" name="logo" id="logo" accept="image/*" class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                    <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-toggle-on" style="margin-right: 8px; color: #667eea;"></i> Status <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s; background: white;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                        <option value="active" {{ old('status', $school->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $school->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $school->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subscription_package_id" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                        <i class="fas fa-box" style="margin-right: 8px; color: #667eea;"></i> Subscription Package
                    </label>
                    <select name="subscription_package_id" id="subscription_package_id" class="form-input" style="width: 100%; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s; background: white;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                        <option value="">No Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('subscription_package_id', $school->subscription_package_id) == $package->id ? 'selected' : '' }}>
                                {{ $package->name }} - {{ number_format($package->price, 2) }} {{ $package->currency ?? 'UGX' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 15px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.schools.index') }}" class="dashboard-btn dashboard-btn-secondary" style="padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dashboard-btn dashboard-btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fas fa-save"></i> Update School
            </button>
        </div>
    </form>
</div>
@endsection

