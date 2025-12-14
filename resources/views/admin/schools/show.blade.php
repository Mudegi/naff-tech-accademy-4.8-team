@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">School Details</h1>
        <div class="breadcrumb-actions">
            <a href="{{ route('admin.schools.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Schools
            </a>
            <a href="{{ route('admin.schools.edit', $school->id) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-edit"></i> Edit School
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">
            <!-- School Header -->
            <div class="school-header-section" style="text-align: center;">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" style="width: 150px; height: 150px; border-radius: 12px; object-fit: cover; border: 3px solid #e5e7eb; margin-bottom: 20px;">
                @else
                    <div style="width: 150px; height: 150px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 48px; margin: 0 auto 20px;">
                        {{ strtoupper(substr($school->name, 0, 2)) }}
                    </div>
                @endif
                <h2 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 600; color: #1f2937;">{{ $school->name }}</h2>
                <span class="status-badge status-{{ $school->status }}" style="padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; text-transform: capitalize; display: inline-block;">
                    {{ $school->status }}
                </span>
            </div>

            <!-- School Information -->
            <div class="school-info-section">
                <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;">
                    <i class="fas fa-info-circle" style="color: #667eea; margin-right: 8px;"></i> Information
                </h3>
                <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <div class="info-item">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px; font-weight: 500;">Email</div>
                        <div style="font-size: 14px; color: #1f2937; font-weight: 500;">
                            <i class="fas fa-envelope" style="margin-right: 8px; color: #9ca3af;"></i>{{ $school->email }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px; font-weight: 500;">Phone</div>
                        <div style="font-size: 14px; color: #1f2937; font-weight: 500;">
                            <i class="fas fa-phone" style="margin-right: 8px; color: #9ca3af;"></i>{{ $school->phone_number ?? 'N/A' }}
                        </div>
                    </div>
                    @if($school->website)
                    <div class="info-item">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px; font-weight: 500;">Website</div>
                        <div style="font-size: 14px; color: #1f2937; font-weight: 500;">
                            <i class="fas fa-globe" style="margin-right: 8px; color: #9ca3af;"></i>
                            <a href="{{ $school->website }}" target="_blank" style="color: #2563eb; text-decoration: none;">{{ $school->website }}</a>
                        </div>
                    </div>
                    @endif
                    @if($school->address)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px; font-weight: 500;">Address</div>
                        <div style="font-size: 14px; color: #1f2937; font-weight: 500;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #9ca3af;"></i>{{ $school->address }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-section" style="margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%); border-radius: 12px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">
                <i class="fas fa-chart-bar" style="color: #667eea; margin-right: 8px;"></i> Statistics
            </h3>
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="stat-item" style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 5px;">{{ $stats['total_staff'] }}</div>
                    <div style="font-size: 14px; color: #6b7280; font-weight: 500;">Staff Members</div>
                </div>
                <div class="stat-item" style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 5px;">{{ $stats['total_students'] }}</div>
                    <div style="font-size: 14px; color: #6b7280; font-weight: 500;">Students</div>
                </div>
                <div class="stat-item" style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: #f59e0b; margin-bottom: 5px;">{{ $stats['total_subjects'] }}</div>
                    <div style="font-size: 14px; color: #6b7280; font-weight: 500;">Subjects</div>
                </div>
                <div class="stat-item" style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: #8b5cf6; margin-bottom: 5px;">{{ $stats['total_classes'] }}</div>
                    <div style="font-size: 14px; color: #6b7280; font-weight: 500;">Classes</div>
                </div>
            </div>
        </div>

        <!-- Subscription Information -->
        @if($school->subscriptionPackage)
        <div class="subscription-section" style="margin-bottom: 30px; padding: 20px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #667eea;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px;">
                <i class="fas fa-box" style="color: #667eea; margin-right: 8px;"></i> Subscription Package
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Package Name</div>
                    <div style="font-size: 16px; font-weight: 600; color: #1f2937;">{{ $school->subscriptionPackage->name }}</div>
                </div>
                @if($school->subscription_start_date)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Start Date</div>
                    <div style="font-size: 14px; color: #1f2937;">{{ $school->subscription_start_date->format('M d, Y') }}</div>
                </div>
                @endif
                @if($school->subscription_end_date)
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">End Date</div>
                    <div style="font-size: 14px; color: #1f2937;">{{ $school->subscription_end_date->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Pending Payment Approvals -->
        @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
        <div class="pending-approvals-section" style="margin-bottom: 30px; padding: 20px; background: #fff3cd; border-radius: 12px; border-left: 4px solid #ffc107;">
            <h3 style="font-size: 18px; font-weight: 600; color: #856404; margin-bottom: 15px;">
                <i class="fas fa-clock" style="color: #ffc107; margin-right: 8px;"></i> Pending Payment Approvals ({{ $pendingApprovals->count() }})
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #fff3cd; border-bottom: 2px solid #ffc107;">
                            <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: 600; color: #856404;">Package</th>
                            <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: 600; color: #856404;">Amount</th>
                            <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: 600; color: #856404;">Transaction ID</th>
                            <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: 600; color: #856404;">Date</th>
                            <th style="padding: 12px; text-align: center; font-size: 14px; font-weight: 600; color: #856404;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingApprovals as $subscription)
                        <tr style="border-bottom: 1px solid #ffeaa7;">
                            <td style="padding: 12px; font-size: 14px; color: #1f2937;">
                                <strong>{{ $subscription->subscriptionPackage->name }}</strong>
                            </td>
                            <td style="padding: 12px; font-size: 14px; color: #1f2937;">
                                {{ number_format($subscription->amount_paid, 0) }} UGX
                            </td>
                            <td style="padding: 12px; font-size: 14px; color: #6b7280;">
                                {{ $subscription->transaction_id ?? 'N/A' }}
                            </td>
                            <td style="padding: 12px; font-size: 14px; color: #6b7280;">
                                {{ $subscription->created_at->format('M d, Y') }}
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <form action="{{ route('admin.school-subscriptions.approve', $subscription->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to approve this payment?')" 
                                            style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;"
                                            onmouseover="this.style.background='#059669'" 
                                            onmouseout="this.style.background='#10b981'">
                                        <i class="fas fa-check mr-1"></i> Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Quick Actions - Add Staff -->
        <div class="quick-actions-section" style="margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%); border-radius: 12px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 15px;">
                <i class="fas fa-user-plus" style="color: #667eea; margin-right: 8px;"></i> Quick Actions - Add Staff
            </h3>
            <div class="quick-action-buttons" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="{{ route('admin.users.create', ['school_id' => $school->id, 'account_type' => 'school_admin']) }}" class="quick-action-btn" style="padding: 15px; background: white; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s; border: 2px solid #e5e7eb;" onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
                    <i class="fas fa-user-shield" style="font-size: 24px; color: #667eea; margin-bottom: 8px; display: block;"></i>
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Add School Admin</div>
                </a>
                <a href="{{ route('admin.users.create', ['school_id' => $school->id, 'account_type' => 'director_of_studies']) }}" class="quick-action-btn" style="padding: 15px; background: white; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s; border: 2px solid #e5e7eb;" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
                    <i class="fas fa-user-tie" style="font-size: 24px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Add Director of Studies</div>
                </a>
                <a href="{{ route('admin.users.create', ['school_id' => $school->id, 'account_type' => 'head_of_department']) }}" class="quick-action-btn" style="padding: 15px; background: white; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s; border: 2px solid #e5e7eb;" onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
                    <i class="fas fa-user-graduate" style="font-size: 24px; color: #f59e0b; margin-bottom: 8px; display: block;"></i>
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Add Head of Department</div>
                </a>
                <a href="{{ route('admin.users.create', ['school_id' => $school->id, 'account_type' => 'subject_teacher']) }}" class="quick-action-btn" style="padding: 15px; background: white; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s; border: 2px solid #e5e7eb;" onmouseover="this.style.borderColor='#8b5cf6'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 24px; color: #8b5cf6; margin-bottom: 8px; display: block;"></i>
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Add Teacher</div>
                </a>
                <a href="{{ route('admin.users.create', ['school_id' => $school->id, 'account_type' => 'student']) }}" class="quick-action-btn" style="padding: 15px; background: white; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s; border: 2px solid #e5e7eb;" onmouseover="this.style.borderColor='#ec4899'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
                    <i class="fas fa-user-graduate" style="font-size: 24px; color: #ec4899; margin-bottom: 8px; display: block;"></i>
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Add Student</div>
                </a>
            </div>
        </div>

        <!-- Actions -->
        <div class="action-buttons" style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.schools.edit', $school->id) }}" class="dashboard-btn dashboard-btn-primary" style="padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                <i class="fas fa-edit"></i> Edit School
            </a>
            <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="dashboard-btn dashboard-btn-danger" style="padding: 12px 24px; border-radius: 8px; border: none; background: #ef4444; color: white; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-trash"></i> Delete School
                </button>
            </form>
        </div>
    </div>
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

