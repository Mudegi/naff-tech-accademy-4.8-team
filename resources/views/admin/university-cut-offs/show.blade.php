@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="dashboard-title">{{ $universityCutOff->course_name }}</h1>
            <div class="breadcrumbs">
                <a href="{{ route('admin.university-cut-offs.index') }}">University Cut-Offs</a> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">View</span>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.university-cut-offs.edit', $universityCutOff->id) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.university-cut-offs.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="dashboard-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- University Information -->
        <div class="info-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-university" style="color: #667eea;"></i> University Information
            </h2>
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">University Name</div>
                    <div style="font-size: 16px; font-weight: 600; color: #1a1a1a;">{{ $universityCutOff->university_name }}</div>
                </div>
                @if($universityCutOff->university_code)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">University Code</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151;">{{ $universityCutOff->university_code }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Course Information -->
        <div class="info-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-graduation-cap" style="color: #667eea;"></i> Course Information
            </h2>
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Course Name</div>
                    <div style="font-size: 16px; font-weight: 600; color: #1a1a1a;">{{ $universityCutOff->course_name }}</div>
                </div>
                @if($universityCutOff->course_code)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Course Code</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151;">{{ $universityCutOff->course_code }}</div>
                </div>
                @endif
                @if($universityCutOff->faculty)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Faculty</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151;">{{ $universityCutOff->faculty }}</div>
                </div>
                @endif
                @if($universityCutOff->department)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Department</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151;">{{ $universityCutOff->department }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Degree Type</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151; text-transform: capitalize;">{{ $universityCutOff->degree_type }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Cut-Off Format</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151; text-transform: capitalize;">
                        {{ $universityCutOff->cut_off_format ?? 'standard' }}
                        @if($universityCutOff->cut_off_format === 'makerere')
                            <span style="font-size: 12px; color: #6b7280;">({{ $universityCutOff->program_category === 'stem' ? 'STEM with gender' : 'Other programs' }})</span>
                        @elseif($universityCutOff->cut_off_format === 'kyambogo')
                            <span style="font-size: 12px; color: #6b7280;">(Single cut-off, no gender)</span>
                        @endif
                    </div>
                </div>
                @if($universityCutOff->duration_years)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Duration</div>
                    <div style="font-size: 16px; font-weight: 500; color: #374151;">{{ $universityCutOff->duration_years }} {{ Str::plural('Year', $universityCutOff->duration_years) }}</div>
                </div>
                @endif
            </div>
            @if($universityCutOff->course_description)
            <div style="margin-top: 15px;">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Description</div>
                <div style="font-size: 14px; color: #4b5563; line-height: 1.6;">{{ $universityCutOff->course_description }}</div>
            </div>
            @endif
        </div>

        <!-- Admission Requirements -->
        <div class="info-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-clipboard-check" style="color: #667eea;"></i> Admission Requirements
            </h2>
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Minimum Principal Passes</div>
                    <div style="font-size: 24px; font-weight: 700; color: #4338ca;">{{ $universityCutOff->minimum_principal_passes }}</div>
                </div>
                @if($universityCutOff->minimum_aggregate_points)
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Minimum Aggregate Points</div>
                    <div style="font-size: 24px; font-weight: 700; color: #059669;">{{ number_format($universityCutOff->minimum_aggregate_points, 1) }}</div>
                </div>
                @endif
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 8px; color: white;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">Cut-Off Points</div>
                    @if($universityCutOff->cut_off_format === 'makerere' && $universityCutOff->program_category === 'stem')
                        <div style="font-size: 18px; font-weight: 600; margin-bottom: 5px;">Male: {{ $universityCutOff->cut_off_points_male ? number_format($universityCutOff->cut_off_points_male, 1) : 'N/A' }}</div>
                        <div style="font-size: 18px; font-weight: 600;">Female: {{ $universityCutOff->cut_off_points_female ? number_format($universityCutOff->cut_off_points_female, 1) : 'N/A' }}</div>
                    @elseif($universityCutOff->cut_off_structure && is_array($universityCutOff->cut_off_structure))
                        <div style="font-size: 14px; opacity: 0.9;">Custom Structure (JSON)</div>
                        <div style="font-size: 12px; margin-top: 5px; opacity: 0.8;">
                            @foreach($universityCutOff->cut_off_structure as $key => $value)
                                <div>{{ ucfirst($key) }}: {{ is_numeric($value) ? number_format($value, 1) : $value }}</div>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size: 24px; font-weight: 700;">{{ $universityCutOff->cut_off_points ? number_format($universityCutOff->cut_off_points, 1) : 'N/A' }}</div>
                    @endif
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Academic Year</div>
                    <div style="font-size: 20px; font-weight: 600; color: #1a1a1a;">{{ $universityCutOff->academic_year }}</div>
                </div>
            </div>
        </div>

        <!-- Subject Requirements -->
        @if($universityCutOff->essential_subjects || $universityCutOff->relevant_subjects || $universityCutOff->desirable_subjects)
        <div class="info-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-book-open" style="color: #667eea;"></i> Subject Requirements
            </h2>
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                @if($universityCutOff->essential_subjects)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 10px; font-weight: 600;">
                        <i class="fas fa-star" style="color: #ef4444; margin-right: 5px;"></i> Essential Subjects
                    </div>
                    <div style="display: flex; flex-wrap: gap: 8px;">
                        @foreach($universityCutOff->essential_subjects as $subject)
                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500;">{{ $subject }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($universityCutOff->relevant_subjects)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 10px; font-weight: 600;">
                        <i class="fas fa-check" style="color: #f59e0b; margin-right: 5px;"></i> Relevant Subjects
                    </div>
                    <div style="display: flex; flex-wrap: gap: 8px;">
                        @foreach($universityCutOff->relevant_subjects as $subject)
                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500;">{{ $subject }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($universityCutOff->desirable_subjects)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 10px; font-weight: 600;">
                        <i class="fas fa-heart" style="color: #10b981; margin-right: 5px;"></i> Desirable Subjects
                    </div>
                    <div style="display: flex; flex-wrap: gap: 8px;">
                        @foreach($universityCutOff->desirable_subjects as $subject)
                            <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500;">{{ $subject }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Additional Information -->
        @if($universityCutOff->additional_requirements)
        <div class="info-section" style="margin-bottom: 30px;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle" style="color: #667eea;"></i> Additional Requirements
            </h2>
            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px;">
                <div style="font-size: 14px; color: #78350f; line-height: 1.6;">{{ $universityCutOff->additional_requirements }}</div>
            </div>
        </div>
        @endif

        <!-- Status -->
        <div class="info-section">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: {{ $universityCutOff->is_active ? '#d1fae5' : '#fee2e2' }}; border-radius: 8px;">
                <div>
                    <div style="font-size: 12px; color: {{ $universityCutOff->is_active ? '#065f46' : '#991b1b' }}; margin-bottom: 5px;">Status</div>
                    <div style="font-size: 16px; font-weight: 600; color: {{ $universityCutOff->is_active ? '#065f46' : '#991b1b' }};">
                        {{ $universityCutOff->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
                <div style="font-size: 12px; color: {{ $universityCutOff->is_active ? '#065f46' : '#991b1b' }};">
                    {{ $universityCutOff->is_active ? 'Visible in recommendations' : 'Hidden from recommendations' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

