@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Schools</h1>
        <a href="{{ route('admin.schools.create') }}" class="dashboard-btn dashboard-btn-primary">
            <i class="fas fa-plus"></i> Add New School
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

    <!-- Filters Section -->
    <div class="dashboard-card" style="margin-bottom: 20px;">
        <form action="{{ route('admin.schools.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-item">
                    <label>Search</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="Search by name, email, or phone">
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Subscription</label>
                    <select name="subscription_status" class="filter-input" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>Active Subscription</option>
                        <option value="inactive" {{ request('subscription_status') == 'inactive' ? 'selected' : '' }}>No Active Subscription</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Per Page</label>
                    <select name="per_page" class="filter-input" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 per page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <div class="filter-buttons">
                        <button type="submit" class="filter-button">Filter</button>
                        <a href="{{ route('admin.schools.index') }}" class="clear-button">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Schools Grid -->
    <div class="schools-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        @forelse($schools as $school)
            <div class="school-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                <div class="school-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 24px;">
                            {{ strtoupper(substr($school->name, 0, 2)) }}
                        </div>
                    @endif
                    <div style="flex: 1;">
                        <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">{{ $school->name }}</h3>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ $school->email }}</p>
                    </div>
                    <span class="status-badge status-{{ $school->status }}" style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; text-transform: capitalize;">
                        {{ $school->status }}
                    </span>
                </div>

                <div class="school-details" style="margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 14px; color: #4b5563;">
                        <i class="fas fa-phone" style="width: 16px; color: #9ca3af;"></i>
                        <span>{{ $school->phone_number ?? 'N/A' }}</span>
                    </div>
                    @if($school->website)
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 14px; color: #4b5563;">
                        <i class="fas fa-globe" style="width: 16px; color: #9ca3af;"></i>
                        <a href="{{ $school->website }}" target="_blank" style="color: #2563eb; text-decoration: none;">{{ $school->website }}</a>
                    </div>
                    @endif
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 14px; color: #4b5563;">
                        <i class="fas fa-users" style="width: 16px; color: #9ca3af;"></i>
                        <span>{{ $school->users->count() }} Users</span>
                    </div>
                    @if($school->subscriptionPackage)
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #4b5563;">
                        <i class="fas fa-box" style="width: 16px; color: #9ca3af;"></i>
                        <span>{{ $school->subscriptionPackage->name }}</span>
                    </div>
                    @endif
                </div>

                <div class="school-footer" style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 12px; color: #9ca3af;">
                        Created {{ $school->created_at->diffForHumans() }}
                    </div>
                    <div class="action-buttons" style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.schools.show', $school->id) }}" class="action-btn view-btn" title="View" style="padding: 6px 12px; background: #e0e7ff; color: #4338ca; border-radius: 6px; text-decoration: none; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='#c7d2fe'" onmouseout="this.style.background='#e0e7ff'">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.schools.edit', $school->id) }}" class="action-btn edit-btn" title="Edit" style="padding: 6px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; text-decoration: none; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete-btn" title="Delete" style="padding: 6px 12px; background: #fee2e2; color: #991b1b; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-school" style="font-size: 64px; color: #d1d5db; margin-bottom: 20px;"></i>
                <h3 style="color: #6b7280; margin-bottom: 10px;">No Schools Found</h3>
                <p style="color: #9ca3af; margin-bottom: 20px;">Get started by creating your first school.</p>
                <a href="{{ route('admin.schools.create') }}" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-plus"></i> Add New School
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($schools->hasPages())
    <div class="dashboard-pagination" style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $schools->links('vendor.pagination.simple-default') }}
    </div>
    @endif
</div>

<style>
.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.status-suspended {
    background: #fef3c7;
    color: #92400e;
}
</style>
@endsection

