@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="margin-bottom: 2rem;">
        <h1 class="dashboard-title">University Details</h1>
        <div class="breadcrumbs" style="margin-top: 0.5rem;">
            <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
            <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
            <a href="{{ route('admin.universities.index') }}" style="color: #667eea; text-decoration: none;">Universities</a>
            <span style="color: #9ca3af; margin: 0 0.5rem;">/</span>
            <span style="color: #6b7280;">{{ $university->name }}</span>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('admin.universities.edit', $university) }}" class="dashboard-btn dashboard-btn-primary">
            <i class="fas fa-edit"></i> Edit University
        </a>
        <a href="{{ route('admin.university-cut-offs.import') }}?university_id={{ $university->id }}" class="dashboard-btn" style="background: #10b981; color: white;">
            <i class="fas fa-download"></i> Import Cut-Offs
        </a>
    </div>

    <!-- University Information Card -->
    <div class="dashboard-card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                    {{ $university->name }}
                </h2>
                @if($university->code)
                    <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                        {{ $university->code }}
                    </span>
                @endif
            </div>
            <div>
                @if($university->is_active)
                    <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-times-circle"></i> Inactive
                    </span>
                @endif
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Base URL</div>
                @if($university->base_url)
                    <a href="{{ $university->base_url }}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 500;">
                        {{ $university->base_url }} <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                    </a>
                @else
                    <span style="color: #9ca3af;">Not set</span>
                @endif
            </div>

            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">URL Pattern</div>
                @if($university->url_pattern)
                    <div style="background: #f9fafb; padding: 0.75rem; border-radius: 0.375rem; font-family: monospace; font-size: 0.875rem; color: #374151; word-break: break-all;">
                        {{ $university->url_pattern }}
                    </div>
                    <small style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        <i class="fas fa-info-circle"></i> Use {year} and {nextYear} placeholders
                    </small>
                @else
                    <span style="color: #9ca3af;">Not configured</span>
                @endif
            </div>

            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Scraper Type</div>
                <span style="background: #f3f4f6; color: #374151; padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; text-transform: capitalize;">
                    {{ str_replace('_', ' ', $university->scraper_type) }}
                </span>
            </div>

            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Cut-Off Format</div>
                <span style="background: #fef3c7; color: #92400e; padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; text-transform: capitalize;">
                    {{ $university->cut_off_format }}
                </span>
            </div>

            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Created</div>
                <div style="color: #374151; font-weight: 500;">
                    {{ $university->created_at->format('F d, Y') }}
                </div>
            </div>

            <div>
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Last Updated</div>
                <div style="color: #374151; font-weight: 500;">
                    {{ $university->updated_at->format('F d, Y') }}
                </div>
            </div>
        </div>

        @if($university->notes)
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Notes</div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.375rem; color: #374151; white-space: pre-wrap;">
                    {{ $university->notes }}
                </div>
            </div>
        @endif
    </div>

    <!-- Cut-Offs Statistics -->
    <div class="dashboard-card">
        <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
            <i class="fas fa-chart-bar" style="color: #667eea; margin-right: 0.5rem;"></i> Cut-Offs Statistics
        </h3>
        @php
            $cutOffsCount = $university->cutOffs()->count();
            $activeCutOffsCount = $university->cutOffs()->where('is_active', true)->count();
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background: #f0f9ff; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #3b82f6;">
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Total Cut-Offs</div>
                <div style="font-size: 24px; font-weight: 600; color: #1e40af;">{{ $cutOffsCount }}</div>
            </div>
            <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #10b981;">
                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Active Cut-Offs</div>
                <div style="font-size: 24px; font-weight: 600; color: #065f46;">{{ $activeCutOffsCount }}</div>
            </div>
        </div>
        @if($cutOffsCount > 0)
            <div style="margin-top: 1rem;">
                <a href="{{ route('admin.university-cut-offs.index', ['university' => $university->name]) }}" class="dashboard-btn dashboard-btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-list"></i> View All Cut-Offs
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

