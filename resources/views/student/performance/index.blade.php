@extends('layouts.student-dashboard')

@section('content')
<div class="performance-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h1 class="page-title">My Performance Overview</h1>
                <p class="page-subtitle">Comprehensive view of your academic progress across all activities</p>
            </div>
        </div>
    </div>

    <!-- Overall Performance Card -->
    <div class="overall-card">
        <div class="overall-header">
            <h2><i class="fas fa-trophy"></i> Overall Performance</h2>
        </div>
        <div class="overall-stats">
            <div class="stat-box primary">
                <div class="stat-value">{{ $overallMetrics['overall_average'] }}%</div>
                <div class="stat-label">Overall Average</div>
                <div class="stat-grade">Grade: {{ $overallMetrics['letter_grade'] }}</div>
            </div>
            <div class="stat-box {{ $overallMetrics['trend_direction'] === 'improving' ? 'success' : ($overallMetrics['trend_direction'] === 'declining' ? 'danger' : 'secondary') }}">
                <div class="stat-icon">
                    @if($overallMetrics['trend_direction'] === 'improving')
                        <i class="fas fa-arrow-up"></i>
                    @elseif($overallMetrics['trend_direction'] === 'declining')
                        <i class="fas fa-arrow-down"></i>
                    @else
                        <i class="fas fa-arrows-alt-h"></i>
                    @endif
                </div>
                <div class="stat-value">{{ abs($overallMetrics['trend']) }}%</div>
                <div class="stat-label">
                    {{ ucfirst($overallMetrics['trend_direction']) }}
                </div>
            </div>
            <div class="stat-box info">
                <div class="stat-value">{{ $overallMetrics['total_activities'] }}</div>
                <div class="stat-label">Total Activities</div>
                <div class="stat-description">Assignments + Exams + Projects</div>
            </div>
        </div>
    </div>

    <!-- Performance Breakdown -->
    <div class="breakdown-grid">
        <!-- Assignments -->
        <div class="breakdown-card">
            <div class="card-header assignments">
                <i class="fas fa-clipboard-check"></i>
                <h3>Assignments</h3>
            </div>
            <div class="card-body">
                <div class="metric">
                    <span class="metric-label">Average Grade:</span>
                    <span class="metric-value">{{ $assignmentPerformance['average_grade'] }}%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Total Completed:</span>
                    <span class="metric-value">{{ $assignmentPerformance['total'] }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">On-Time Rate:</span>
                    <span class="metric-value">{{ $assignmentPerformance['on_time_rate'] }}%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Graded:</span>
                    <span class="metric-value success">{{ $assignmentPerformance['graded'] }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Pending:</span>
                    <span class="metric-value warning">{{ $assignmentPerformance['pending'] }}</span>
                </div>
            </div>
        </div>

        <!-- Exams -->
        <div class="breakdown-card">
            <div class="card-header exams">
                <i class="fas fa-file-alt"></i>
                <h3>Exams & Tests</h3>
            </div>
            <div class="card-body">
                <div class="metric">
                    <span class="metric-label">Average Grade:</span>
                    <span class="metric-value">{{ $examPerformance['average_grade'] }}%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Total Exams:</span>
                    <span class="metric-value">{{ $examPerformance['total'] }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Principal Passes:</span>
                    <span class="metric-value">{{ $examPerformance['principal_passes'] }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Aggregate Points:</span>
                    <span class="metric-value">{{ $examPerformance['aggregate_points'] }}</span>
                </div>
            </div>
        </div>

        <!-- Group Work -->
        <div class="breakdown-card">
            <div class="card-header projects">
                <i class="fas fa-users"></i>
                <h3>Group Work & Projects</h3>
            </div>
            <div class="card-body">
                <div class="metric">
                    <span class="metric-label">Average Grade:</span>
                    <span class="metric-value">{{ $groupWorkPerformance['average_grade'] }}%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Total Projects:</span>
                    <span class="metric-value">{{ $groupWorkPerformance['total'] }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Active Groups:</span>
                    <span class="metric-value">{{ $groupWorkPerformance['groups_active'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trends Chart -->
    <div class="trends-section">
        <h2><i class="fas fa-chart-area"></i> Performance Trends (Last 6 Months)</h2>
        <div class="chart-container">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Subject Breakdown -->
    <div class="subject-section">
        <h2><i class="fas fa-book"></i> Subject-Wise Performance</h2>
        @if(count($subjectBreakdown) > 0)
            <div class="subject-grid">
                @foreach($subjectBreakdown as $subject)
                    <div class="subject-card">
                        <div class="subject-header">
                            <h4>{{ $subject['subject'] }}</h4>
                            <span class="subject-grade grade-{{ strtolower($subject['letter_grade']) }}">
                                {{ $subject['letter_grade'] }}
                            </span>
                        </div>
                        <div class="subject-average">{{ $subject['average'] }}%</div>
                        <div class="subject-stats">
                            <span><i class="fas fa-clipboard"></i> {{ $subject['assignment_count'] }} assignments</span>
                            <span><i class="fas fa-file-alt"></i> {{ $subject['exam_count'] }} exams</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $subject['average'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No subject performance data available yet</p>
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
        <h2><i class="fas fa-clock"></i> Recent Activity</h2>
        @if(count($recentActivity) > 0)
            <div class="activity-timeline">
                @foreach($recentActivity as $activity)
                    <div class="activity-item {{ $activity['status'] }}">
                        <div class="activity-icon">
                            @if($activity['type'] === 'assignment')
                                <i class="fas fa-clipboard-check"></i>
                            @else
                                <i class="fas fa-file-alt"></i>
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-header">
                                <h4>{{ $activity['title'] }}</h4>
                                <span class="activity-date">{{ $activity['date']->diffForHumans() }}</span>
                            </div>
                            <div class="activity-details">
                                <span class="activity-grade">Grade: {{ $activity['grade'] }} ({{ $activity['percentage'] }}%)</span>
                                <span class="activity-letter-grade">{{ $activity['letter_grade'] }}</span>
                            </div>
                        </div>
                        <div class="activity-status-icon">
                            @if($activity['status'] === 'success')
                                <i class="fas fa-check-circle"></i>
                            @elseif($activity['status'] === 'warning')
                                <i class="fas fa-exclamation-circle"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No recent activity</p>
            </div>
        @endif
    </div>

    <!-- Parent/Guardian Info Box -->
    <div class="info-card parent-info">
        <div class="info-header">
            <i class="fas fa-users"></i>
            <h3>For Parents & Guardians</h3>
        </div>
        <div class="info-content">
            <p>This dashboard shows your child's complete academic performance across all activities:</p>
            <ul>
                <li><strong>Real-time Progress:</strong> Updated automatically as grades are posted</li>
                <li><strong>Comprehensive View:</strong> Includes assignments, exams, group work, and projects</li>
                <li><strong>Early Intervention:</strong> Spot areas needing support before final exams</li>
                <li><strong>Trending Indicators:</strong> See if performance is improving, declining, or stable</li>
                <li><strong>Subject Analysis:</strong> Identify strengths and areas for improvement</li>
            </ul>
            <p class="highlight">
                <i class="fas fa-lightbulb"></i> 
                This continuous assessment helps ensure your investment in education delivers visible results!
            </p>
        </div>
    </div>
</div>

<style>
.performance-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    margin: 0.5rem 0 0 0;
    opacity: 0.95;
    font-size: 1.1rem;
}

.overall-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.overall-header h2 {
    margin: 0 0 1.5rem 0;
    color: #333;
    font-size: 1.5rem;
}

.overall-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-box {
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
}

.stat-box.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-box.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stat-box.danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
}

.stat-box.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.stat-box.info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
    font-weight: 500;
}

.stat-grade {
    margin-top: 0.5rem;
    font-size: 1.1rem;
    opacity: 0.95;
}

.stat-description {
    margin-top: 0.3rem;
    font-size: 0.85rem;
    opacity: 0.85;
}

.breakdown-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.breakdown-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.card-header {
    padding: 1.25rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
}

.card-header.assignments {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-header.exams {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-header.projects {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.card-body {
    padding: 1.5rem;
}

.metric {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.metric:last-child {
    border-bottom: none;
}

.metric-label {
    color: #666;
    font-size: 0.95rem;
}

.metric-value {
    font-weight: 600;
    font-size: 1.1rem;
    color: #333;
}

.metric-value.success {
    color: #28a745;
}

.metric-value.warning {
    color: #ffc107;
}

.trends-section, .subject-section, .activity-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.trends-section h2, .subject-section h2, .activity-section h2 {
    margin: 0 0 1.5rem 0;
    color: #333;
    font-size: 1.5rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

.subject-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.subject-card {
    padding: 1.5rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.subject-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.subject-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.subject-header h4 {
    margin: 0;
    color: #333;
    font-size: 1.1rem;
}

.subject-grade {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1rem;
}

.grade-a { background: #d4edda; color: #155724; }
.grade-b\+ { background: #d1ecf1; color: #0c5460; }
.grade-b { background: #d1ecf1; color: #0c5460; }
.grade-c { background: #fff3cd; color: #856404; }
.grade-d { background: #f8d7da; color: #721c24; }
.grade-f { background: #f5c6cb; color: #721c24; }

.subject-average {
    font-size: 2.5rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.subject-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s ease;
}

.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 12px;
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.activity-item.success {
    background: #d4edda;
    border-color: #28a745;
}

.activity-item.warning {
    background: #fff3cd;
    border-color: #ffc107;
}

.activity-item.danger {
    background: #f8d7da;
    border-color: #dc3545;
}

.activity-item:hover {
    transform: translateX(8px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.activity-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #666;
}

.activity-content {
    flex: 1;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.activity-header h4 {
    margin: 0;
    color: #333;
    font-size: 1rem;
}

.activity-date {
    font-size: 0.85rem;
    color: #666;
}

.activity-details {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.activity-grade {
    font-size: 0.9rem;
    color: #666;
}

.activity-letter-grade {
    padding: 0.25rem 0.75rem;
    background: rgba(0,0,0,0.1);
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.85rem;
}

.activity-status-icon {
    font-size: 1.5rem;
}

.activity-item.success .activity-status-icon {
    color: #28a745;
}

.activity-item.warning .activity-status-icon {
    color: #ffc107;
}

.activity-item.danger .activity-status-icon {
    color: #dc3545;
}

.parent-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #e8f5e9 100%);
    border: 2px solid #4caf50;
}

.info-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #2e7d32;
}

.info-header i {
    font-size: 1.5rem;
}

.info-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.info-content {
    color: #1b5e20;
}

.info-content ul {
    margin: 1rem 0;
    padding-left: 1.5rem;
}

.info-content li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.highlight {
    background: rgba(76, 175, 80, 0.2);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #999;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .performance-container {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 2rem;
    }
    
    .breakdown-grid, .subject-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_column($performanceTrends, 'month')),
                datasets: [{
                    label: 'Average Performance (%)',
                    data: @json(array_column($performanceTrends, 'average')),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8
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
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 }
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
@endsection
