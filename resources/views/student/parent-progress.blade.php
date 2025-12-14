@extends('layouts.student-dashboard')

@section('content')
<style>
    .parent-dashboard {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 30px 20px;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .welcome-banner h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .welcome-banner p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .student-selector {
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .selector-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: block;
    }

    .selector-dropdown {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        color: #333;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .selector-dropdown:hover {
        border-color: #667eea;
    }

    .selector-dropdown:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .progress-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .progress-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-left: 5px solid;
        transition: all 0.3s ease;
    }

    .progress-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .progress-card.academic {
        border-left-color: #667eea;
    }

    .progress-card.projects {
        border-left-color: #f093fb;
    }

    .progress-card.groups {
        border-left-color: #4facfe;
    }

    .progress-card.assignments {
        border-left-color: #43e97b;
    }

    .card-icon {
        font-size: 2rem;
        margin-bottom: 12px;
    }

    .card-label {
        font-size: 0.9rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .card-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .card-subtitle {
        font-size: 0.85rem;
        color: #999;
    }

    .section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: #667eea;
        font-size: 1.3rem;
    }

    .marks-table {
        width: 100%;
        border-collapse: collapse;
    }

    .marks-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #e0e0e0;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .marks-table td {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        color: #666;
    }

    .marks-table tr:hover {
        background: #f8f9fa;
    }

    .mark-score {
        font-weight: 600;
        color: #667eea;
        font-size: 1.1rem;
    }

    .performance-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 4px solid #667eea;
    }

    .item-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .item-icon.active {
        background: #d1fae5;
        color: #10b981;
    }

    .item-icon.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .item-icon.submitted {
        background: #ddd6fe;
        color: #6366f1;
    }

    .item-details {
        flex: 1;
    }

    .item-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .item-meta {
        font-size: 0.85rem;
        color: #666;
    }

    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .empty-message i {
        font-size: 3rem;
        color: #d1d5db;
        display: block;
        margin-bottom: 15px;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }

    @media (max-width: 768px) {
        .progress-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .card-value {
            font-size: 2rem;
        }

        .marks-table {
            font-size: 0.9rem;
        }

        .marks-table th,
        .marks-table td {
            padding: 10px;
        }

        .section {
            padding: 20px;
        }
    }
</style>

<div class="parent-dashboard">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h1>👨‍👩‍👧 Student Progress Portal</h1>
        <p>Monitor your child's academic performance and development</p>
    </div>

    <!-- Student Selector -->
    @if(Auth::user()->children && Auth::user()->children->count() > 1)
    <div class="student-selector">
        <label class="selector-label">Select Student</label>
        <select class="selector-dropdown" id="studentSelector" onchange="window.location.href='?child=' + this.value">
            @foreach(Auth::user()->children as $child)
                <option value="{{ $child->id }}" {{ (request('child') == $child->id || (!request('child') && $loop->first)) ? 'selected' : '' }}>
                    {{ $child->name }} ({{ $child->student->schoolClass->name ?? 'Unknown Class' }})
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- Performance Overview -->
    <div class="progress-grid">
        <div class="progress-card academic">
            <div class="card-icon">📊</div>
            <div class="card-label">Average Performance</div>
            <div class="card-value">{{ isset($studentData) ? $studentData['average_score'] : 'N/A' }}%</div>
            <div class="card-subtitle">Academic average</div>
            @if(isset($studentData) && $studentData['average_score'])
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $studentData['average_score'] }}%"></div>
            </div>
            @endif
        </div>

        <div class="progress-card projects">
            <div class="card-icon">🎯</div>
            <div class="card-label">Active Projects</div>
            <div class="card-value">{{ isset($studentData) ? $studentData['total_projects'] : '0' }}</div>
            <div class="card-subtitle">In progress</div>
        </div>

        <div class="progress-card groups">
            <div class="card-icon">👥</div>
            <div class="card-label">Study Groups</div>
            <div class="card-value">{{ isset($studentData) ? $studentData['total_groups'] : '0' }}</div>
            <div class="card-subtitle">Collaborations</div>
        </div>

        <div class="progress-card assignments">
            <div class="card-icon">📋</div>
            <div class="card-label">Pending Tasks</div>
            <div class="card-value">{{ isset($studentData) ? $studentData['pending_tasks'] : '0' }}</div>
            <div class="card-subtitle">To complete</div>
        </div>
    </div>

    <!-- Recent Marks & Performance -->
    @if(isset($studentData) && $studentData['marks']->count() > 0)
    <div class="section">
        <div class="section-title">
            <i class="fas fa-trophy"></i>
            Latest Academic Results
        </div>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Class</th>
                    <th>Score</th>
                    <th>Grade</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentData['marks']->take(10) as $mark)
                <tr>
                    <td><strong>{{ $mark->subject->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $mark->class->name ?? 'N/A' }}</td>
                    <td class="mark-score">{{ $mark->marks_percentage ?? 'N/A' }}%</td>
                    <td>{{ $mark->marks_grade ?? 'N/A' }}</td>
                    <td>{{ $mark->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Active Projects Status -->
    @if(isset($studentData) && $studentData['projects']->count() > 0)
    <div class="section">
        <div class="section-title">
            <i class="fas fa-clipboard-list"></i>
            Project Status & Milestones
        </div>
        <div style="display: grid; gap: 12px;">
            @foreach($studentData['projects'] as $project)
            <div class="performance-item">
                <div class="item-icon {{ $project->status === 'completed' ? 'active' : ($project->planning && $project->planning->status === 'submitted' ? 'pending' : 'submitted') }}">
                    <i class="fas fa-{{ $project->status === 'completed' ? 'check-circle' : 'hourglass-half' }}"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">{{ $project->title }}</div>
                    <div class="item-meta">
                        Group: {{ $project->group->name }}
                        <span style="margin-left: 10px;">Status: <strong>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</strong></span>
                        @if($project->planning)
                            <span style="margin-left: 10px;">Planning: <strong style="color: {{ $project->planning->status === 'approved' ? '#10b981' : '#d97706' }}">{{ ucfirst($project->planning->status) }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Study Groups Overview -->
    @if(isset($studentData) && $studentData['groups']->count() > 0)
    <div class="section">
        <div class="section-title">
            <i class="fas fa-users"></i>
            Collaborative Study Groups
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
            @foreach($studentData['groups'] as $group)
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px;">
                <h4 style="margin: 0 0 10px 0; font-size: 1.1rem; font-weight: 600;">{{ $group->name }}</h4>
                <p style="margin: 0 0 8px 0; font-size: 0.9rem; opacity: 0.9;">{{ $group->schoolClass->name ?? 'Class' }}</p>
                <div style="display: flex; gap: 15px; font-size: 0.9rem; opacity: 0.9;">
                    <span><i class="fas fa-users" style="margin-right: 5px;"></i>{{ $group->approvedMembers->count() }} members</span>
                    <span><i class="fas fa-bookmark" style="margin-right: 5px;"></i>{{ $group->projects->count() }} project{{ $group->projects->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recommendations & Insights -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-lightbulb"></i>
            Insights & Recommendations
        </div>
        @if(isset($studentData))
            @if($studentData['average_score'] >= 75)
            <div class="performance-item">
                <div class="item-icon active">
                    <i class="fas fa-star"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">Excellent Performance 🌟</div>
                    <div class="item-meta">Your child is performing exceptionally well. Keep encouraging them to maintain this momentum!</div>
                </div>
            </div>
            @elseif($studentData['average_score'] >= 60)
            <div class="performance-item">
                <div class="item-icon pending">
                    <i class="fas fa-check"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">Good Progress 👍</div>
                    <div class="item-meta">Your child is making solid progress. With consistent effort, they can improve further.</div>
                </div>
            </div>
            @else
            <div class="performance-item">
                <div class="item-icon pending">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">Needs Support 🤝</div>
                    <div class="item-meta">Your child may need additional support in some areas. Consider reaching out to teachers for guidance.</div>
                </div>
            </div>
            @endif

            @if($studentData['pending_tasks'] > 3)
            <div class="performance-item">
                <div class="item-icon submitted">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">Multiple Pending Tasks 📋</div>
                    <div class="item-meta">{{ $studentData['pending_tasks'] }} assignments pending. Encourage timely submission to maintain good grades.</div>
                </div>
            </div>
            @endif

            <div class="performance-item">
                <div class="item-icon active">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="item-details">
                    <div class="item-title">Communication with Teachers</div>
                    <div class="item-meta">Feel free to contact your child's teachers for progress updates and academic support.</div>
                </div>
            </div>
        @else
        <div class="empty-message">
            <i class="fas fa-inbox"></i>
            <p>No student data available. Please select a student to view their progress.</p>
        </div>
        @endif
    </div>

    <!-- Footer Note -->
    <div style="text-align: center; color: #999; margin-top: 40px; padding: 20px 0;">
        <p>Last updated: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p style="font-size: 0.9rem;">This portal provides real-time access to your child's academic progress and school activities.</p>
    </div>
</div>

<script>
    // Auto-refresh on selection change - handled by onchange above
    // You can add more functionality here as needed
</script>
@endsection
