@extends('layouts.app')

@section('title', $child->name . ' - Performance Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('parent.dashboard') }}" class="text-decoration-none text-muted mb-2 d-block">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-user-graduate me-2"></i>{{ $child->name }}
            </h1>
            <p class="text-muted mb-0">
                Comprehensive Performance Report
                @if($child->student)
                    | {{ $child->student->class_name ?? 'N/A' }} | {{ $child->student->level ?? 'N/A' }}
                @endif
            </p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
            <button class="btn btn-primary" onclick="alert('Report download coming soon!')">
                <i class="fas fa-download me-1"></i>Download PDF
            </button>
        </div>
    </div>

    <!-- Overall Performance Summary -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #667eea !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Overall Average
                    </div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        {{ $overallMetrics['overall_average'] }}%
                    </div>
                    <span class="badge bg-{{ $overallMetrics['overall_average'] >= 70 ? 'success' : ($overallMetrics['overall_average'] >= 50 ? 'warning' : 'danger') }} text-white mt-2">
                        Grade {{ $overallMetrics['letter_grade'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f093fb !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        Assignments
                    </div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        {{ $assignmentPerformance['average_grade'] }}%
                    </div>
                    <small class="text-muted">{{ $assignmentPerformance['total'] }} completed</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4facfe !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Examinations
                    </div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        {{ $examPerformance['average_grade'] }}%
                    </div>
                    <small class="text-muted">{{ $examPerformance['total'] }} exams recorded</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #00c9ff !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Group Projects
                    </div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        {{ $groupWorkPerformance['average_grade'] }}%
                    </div>
                    <small class="text-muted">{{ $groupWorkPerformance['total'] }} projects</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-chart-line me-2"></i>Performance Trends (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceTrendsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-Wise Performance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-book me-2"></i>Subject-Wise Performance
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($subjectBreakdown))
                        <div class="row">
                            @foreach($subjectBreakdown as $subject)
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 font-weight-bold">{{ $subject['subject'] }}</h6>
                                            <span class="badge bg-{{ $subject['average'] >= 70 ? 'success' : ($subject['average'] >= 50 ? 'warning' : 'danger') }} text-white">
                                                {{ $subject['letter_grade'] }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="h5 mb-0 font-weight-bold">{{ $subject['average'] }}%</span>
                                            <small class="text-muted">
                                                {{ $subject['exam_count'] }} exam(s)
                                            </small>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $subject['average'] >= 70 ? 'success' : ($subject['average'] >= 50 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $subject['average'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No subject performance data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($recentActivity))
                        <div class="timeline">
                            @foreach($recentActivity as $activity)
                            <div class="activity-item mb-3 p-3 rounded" 
                                 style="background-color: {{ $activity['status'] == 'success' ? '#d4edda' : ($activity['status'] == 'warning' ? '#fff3cd' : '#f8d7da') }}; border-left: 4px solid {{ $activity['status'] == 'success' ? '#28a745' : ($activity['status'] == 'warning' ? '#ffc107' : '#dc3545') }};">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-{{ $activity['type'] == 'assignment' ? 'clipboard-check' : 'file-alt' }} me-2"></i>
                                            <strong>{{ $activity['title'] }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $activity['status'] }} text-white">
                                            {{ $activity['percentage'] }}%
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $activity['grade'] }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No recent activity to display.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Breakdown -->
    <div class="row">
        <!-- Assignment Details -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-check me-2"></i>Assignment Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Average Grade</small>
                            <strong>{{ $assignmentPerformance['average_grade'] }}%</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Total Completed</small>
                            <strong>{{ $assignmentPerformance['total'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Graded</small>
                            <strong>{{ $assignmentPerformance['graded'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Pending Grading</small>
                            <strong class="text-warning">{{ $assignmentPerformance['pending'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-file-alt me-2"></i>Examination Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Average Grade</small>
                            <strong>{{ $examPerformance['average_grade'] }}%</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Total Exams</small>
                            <strong>{{ $examPerformance['total'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Principal Passes</small>
                            <strong class="text-success">{{ $examPerformance['principal_passes'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Work Details -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-users me-2"></i>Group Project Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Average Grade</small>
                            <strong>{{ $groupWorkPerformance['average_grade'] }}%</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Total Projects</small>
                            <strong>{{ $groupWorkPerformance['total'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Trends Chart
    const trendsCtx = document.getElementById('performanceTrendsChart');
    if (trendsCtx) {
        const trendsData = @json($performanceTrends);
        
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendsData.map(d => d.month),
                datasets: [{
                    label: 'Average Performance',
                    data: trendsData.map(d => d.average),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return 'Average: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<style>
@media print {
    .btn, .d-print-none {
        display: none !important;
    }
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700;
}

.text-gray-800 {
    color: #5a5c69;
}

.activity-item {
    transition: transform 0.2s;
}

.activity-item:hover {
    transform: translateX(5px);
}
</style>
@endsection
