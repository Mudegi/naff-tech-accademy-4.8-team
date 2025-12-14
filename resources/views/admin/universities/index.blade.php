@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="dashboard-title">Universities</h1>
            <div class="breadcrumbs" style="margin-top: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
                <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
                <span style="color: #6b7280;">Universities</span>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.university-cut-offs.import') }}" class="dashboard-btn" style="background: #10b981; color: white;">
                <i class="fas fa-download"></i> Import Cut-Offs
            </a>
            <a href="{{ route('admin.universities.create') }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-plus"></i> Add University
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Universities Table -->
    <div class="dashboard-table-container">
        @if($universities->count() > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>URL Pattern</th>
                        <th>Scraper Type</th>
                        <th>Format</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($universities as $university)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #1f2937;">{{ $university->name }}</div>
                                @if($university->base_url)
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                        <a href="{{ $university->base_url }}" target="_blank" style="color: #667eea; text-decoration: none;">
                                            <i class="fas fa-external-link-alt"></i> Website
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($university->code)
                                    <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                                        {{ $university->code }}
                                    </span>
                                @else
                                    <span style="color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($university->url_pattern)
                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.875rem; color: #4b5563;">
                                        {{ Str::limit($university->url_pattern, 50) }}
                                    </div>
                                    <small style="color: #6b7280; font-size: 0.75rem;">
                                        <i class="fas fa-info-circle"></i> Configured
                                    </small>
                                @else
                                    <span style="color: #9ca3af; font-size: 0.875rem;">Not configured</span>
                                @endif
                            </td>
                            <td>
                                <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $university->scraper_type) }}
                                </span>
                            </td>
                            <td>
                                <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; text-transform: capitalize;">
                                    {{ $university->cut_off_format }}
                                </span>
                            </td>
                            <td>
                                @if($university->is_active)
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                                        <i class="fas fa-times-circle"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.875rem; color: #6b7280;">
                                    {{ $university->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('admin.universities.show', $university) }}" class="dashboard-btn-icon" style="background: #dbeafe; color: #1e40af;" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.universities.edit', $university) }}" class="dashboard-btn-icon" style="background: #fef3c7; color: #92400e;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.universities.destroy', $university) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this university?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dashboard-btn-icon" style="background: #fee2e2; color: #991b1b; border: none; cursor: pointer;" title="Delete">
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
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $universities->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <i class="fas fa-university" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <h3 style="color: #374151; margin-bottom: 0.5rem;">No Universities Found</h3>
                <p style="color: #6b7280; margin-bottom: 1.5rem;">Get started by adding your first university.</p>
                <a href="{{ route('admin.universities.create') }}" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-plus"></i> Add University
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

