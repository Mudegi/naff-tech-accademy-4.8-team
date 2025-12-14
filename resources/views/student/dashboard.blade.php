@extends('layouts.student-dashboard')

@section('content')
<style>
    .student-dashboard {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 20px;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .welcome-banner h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .welcome-banner p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 5px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .stat-card.grade {
        border-left-color: #f093fb;
    }

    .stat-card.projects {
        border-left-color: #667eea;
    }

    .stat-card.groups {
        border-left-color: #4facfe;
    }

    .stat-card.pending {
        border-left-color: #ff6b6b;
    }

    .stat-card.completed {
        border-left-color: #43e97b;
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
        display: block;
        position: relative;
        z-index: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .stat-subtext {
        font-size: 0.85rem;
        color: #999;
    }

    .dashboard-section {
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
        font-size: 1.3rem;
        color: #667eea;
    }

    .quick-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .nav-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .nav-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
        color: white;
        text-decoration: none;
    }

    .nav-card i {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
    }

    .nav-card-title {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .progress-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 4px solid #667eea;
    }

    .progress-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .progress-icon.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .progress-icon.in-progress {
        background: #dbeafe;
        color: #0284c7;
    }

    .progress-icon.approved {
        background: #bbf7d0;
        color: #15803d;
    }

    .progress-icon.submitted {
        background: #ddd6fe;
        color: #6366f1;
    }

    .progress-details {
        flex: 1;
    }

    .progress-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .progress-meta {
        font-size: 0.85rem;
        color: #666;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-submitted {
        background: #ddd6fe;
        color: #6366f1;
    }

    .status-approved {
        background: #bbf7d0;
        color: #15803d;
    }

    .status-graded {
        background: #fecaca;
        color: #dc2626;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 15px;
        display: block;
    }

    .empty-state h3 {
        color: #666;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 20px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    @media (max-width: 768px) {
        .student-dashboard {
            padding: 12px;
        }

        .welcome-banner {
            padding: 25px 15px;
        }

        .welcome-banner h1 {
            font-size: 1.8rem;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .stat-card {
            padding: 15px;
        }

        .dashboard-section {
            padding: 20px;
        }
    }
</style>

<div class="student-dashboard">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h1>👋 Welcome back, {{ Auth::user()->name }}!</h1>
        <p>Keep track of your academic progress and achievements</p>
    </div>

    <!-- School Student Dashboard Section -->
    @if(isset($isSchoolStudent) && $isSchoolStudent && isset($schoolStudentData) && $schoolStudentData)
    
    <!-- Performance Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card grade">
            <span class="stat-icon">📊</span>
            <div class="stat-label">Average Score</div>
            <div class="stat-value">{{ $schoolStudentData['stats']['average_percentage'] }}%</div>
            <div class="stat-subtext">{{ $schoolStudentData['stats']['total_marks'] }} marks earned</div>
        </div>
        
        <div class="stat-card projects">
            <span class="stat-icon">🎯</span>
            <div class="stat-label">Active Projects</div>
            <div class="stat-value">{{ $schoolStudentData['stats']['total_projects'] }}</div>
            <div class="stat-subtext">Track your progress</div>
        </div>
        
        <div class="stat-card groups">
            <span class="stat-icon">👥</span>
            <div class="stat-label">Study Groups</div>
            <div class="stat-value">{{ $schoolStudentData['stats']['total_groups'] }}</div>
            <div class="stat-subtext">Collaborate together</div>
        </div>
        
        <div class="stat-card pending">
            <span class="stat-icon">📋</span>
            <div class="stat-label">Pending Tasks</div>
            <div class="stat-value">{{ $schoolStudentData['stats']['pending_assignments'] }}</div>
            <div class="stat-subtext">Action needed</div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="fas fa-lightning-bolt"></i>
            Quick Actions
        </div>
        <div class="quick-nav">
            <a href="{{ route('student.projects.index') }}" class="nav-card">
                <i class="fas fa-project-diagram"></i>
                <div class="nav-card-title">My Projects</div>
            </a>
            <a href="{{ route('student.projects.groups.index') }}" class="nav-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-users"></i>
                <div class="nav-card-title">Manage Groups</div>
            </a>
            <a href="{{ route('student.marks.index') }}" class="nav-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-chart-line"></i>
                <div class="nav-card-title">My Marks</div>
            </a>
            <a href="{{ route('student.profile') }}" class="nav-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-user"></i>
                <div class="nav-card-title">My Profile</div>
            </a>
        </div>
    </div>

    <!-- Recent Marks Section -->
    @if($schoolStudentData['marks']->count() > 0)
    <div class="dashboard-section">
        <div class="section-title">
            <i class="fas fa-trophy"></i>
            Recent Marks & Performance
        </div>
        <div style="display: grid; gap: 12px;">
            @foreach($schoolStudentData['marks']->take(6) as $mark)
            <div class="progress-item">
                <div class="progress-icon pending" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-book"></i>
                </div>
                <div class="progress-details">
                    <div class="progress-title">{{ $mark->subject->name ?? 'Unknown Subject' }}</div>
                    <div class="progress-meta">
                        {{ $mark->class->name ?? 'Unknown Class' }}
                        @if($mark->created_at)
                            • {{ $mark->created_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>
                <div style="text-align: right;">
                    @if($mark->marks_percentage !== null)
                        <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">{{ $mark->marks_percentage }}%</div>
                    @elseif($mark->marks_grade !== null)
                        <div style="font-size: 1.5rem; font-weight: 700; color: #43e97b;">{{ $mark->marks_grade }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <a href="{{ route('student.marks.index') }}" class="btn-primary" style="margin-top: 20px;">
            <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>View All Marks
        </a>
    </div>
    @else
    <div class="dashboard-section">
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No marks yet</h3>
            <p>Your marks and grades will appear here once your teachers have evaluated your work.</p>
        </div>
    </div>
    @endif

    <!-- My Groups Section -->
    @if($schoolStudentData['myGroups']->count() > 0)
    <div class="dashboard-section">
        <div class="section-title">
            <i class="fas fa-users"></i>
            My Study Groups ({{ $schoolStudentData['myGroups']->count() }})
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            @foreach($schoolStudentData['myGroups'] as $group)
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2); transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.35)';" onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.2)';">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div>
                        <p style="font-weight: 600; font-size: 1.1rem; margin: 0 0 5px 0;">{{ $group->name }}</p>
                        <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">{{ $group->schoolClass->name ?? 'Class' }}</p>
                    </div>
                    @if($group->isLeader(Auth::user()))
                        <span style="background: rgba(255,255,255,0.3); padding: 4px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">👑 Leader</span>
                    @endif
                </div>
                <div style="display: flex; gap: 15px; margin-bottom: 12px; font-size: 0.9rem; opacity: 0.9;">
                    <span><i class="fas fa-users" style="margin-right: 5px;"></i>{{ $group->approvedMembers->count() }}/{{ $group->max_members }}</span>
                    <span><i class="fas fa-bookmark" style="margin-right: 5px;"></i>{{ $group->projects->count() }} project{{ $group->projects->count() !== 1 ? 's' : '' }}</span>
                </div>
                <a href="{{ route('student.projects.groups.show', $group) }}" style="display: inline-block; padding: 8px 12px; background: rgba(255,255,255,0.25); color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.4)';" onmouseout="this.style.background='rgba(255,255,255,0.25)';">
                    View Details →
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Groups Section -->
    @if($schoolStudentData['availableGroups']->count() > 0)
    <div class="dashboard-section">
        <div class="section-title">
            <i class="fas fa-search"></i>
            Available Groups to Join
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            @foreach($schoolStudentData['availableGroups']->take(6) as $group)
            <div style="background: white; border: 2px solid #e0e0e0; border-radius: 10px; padding: 20px; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.15)';" onmouseout="this.style.borderColor='#e0e0e0'; this.style.boxShadow='';">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <p style="font-weight: 600; color: #333; margin: 0; font-size: 1rem;">{{ $group->name }}</p>
                    @if($group->isFull())
                        <span style="background: #ff6b6b; color: white; padding: 4px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Full</span>
                    @else
                        <span style="background: #43e97b; color: white; padding: 4px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Open</span>
                    @endif
                </div>
                @if($group->description)
                    <p style="font-size: 0.9rem; color: #666; margin: 0 0 12px 0; line-height: 1.4;">{{ Str::limit($group->description, 80) }}</p>
                @endif
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: #666; margin-bottom: 12px;">
                    <span><i class="fas fa-users" style="margin-right: 5px;"></i>{{ $group->approvedMembers->count() }}/{{ $group->max_members }} members</span>
                </div>
                <form action="{{ route('student.projects.groups.join', $group) }}" method="POST">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 10px 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';" {{ $group->isFull() ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                        {{ $group->isFull() ? 'Group Full' : 'Join This Group' }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Projects Section -->
    @if($schoolStudentData['projects']->count() > 0)
    <div class="dashboard-section">
        <div class="section-title">
            <i class="fas fa-clipboard-list"></i>
            My Projects ({{ $schoolStudentData['projects']->count() }})
        </div>
        <div style="display: grid; gap: 15px;">
            @foreach($schoolStudentData['projects']->take(5) as $project)
            <div style="background: white; border-left: 5px solid #667eea; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #333; font-size: 1.1rem; font-weight: 600;">{{ $project->title }}</h4>
                        <p style="margin: 0; font-size: 0.9rem; color: #666;">Group: <strong>{{ $project->group->name }}</strong></p>
                    </div>
                    <span class="status-badge" style="
                        background: {{ $project->status === 'planning' ? '#fef3c7' : ($project->status === 'implementation' ? '#ddd6fe' : '#bbf7d0') }};
                        color: {{ $project->status === 'planning' ? '#d97706' : ($project->status === 'implementation' ? '#6366f1' : '#15803d') }};
                    ">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
                
                @if($project->planning || $project->implementation)
                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                    @if($project->planning)
                        <div style="flex: 1; background: #f0f0f0; border-radius: 6px; padding: 10px; text-align: center;">
                            <div style="font-size: 0.8rem; color: #666; font-weight: 600; margin-bottom: 4px;">Planning</div>
                            <div style="font-size: 0.9rem; font-weight: 600;">
                                @if($project->planning->status === 'draft')
                                    <span style="color: #f59e0b;">🔄 In Progress</span>
                                @elseif($project->planning->status === 'submitted')
                                    <span style="color: #3b82f6;">⏳ Submitted</span>
                                @elseif($project->planning->status === 'approved')
                                    <span style="color: #10b981;">✓ Approved</span>
                                @else
                                    <span style="color: #ef4444;">✕ Rejected</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($project->implementation)
                        <div style="flex: 1; background: #f0f0f0; border-radius: 6px; padding: 10px; text-align: center;">
                            <div style="font-size: 0.8rem; color: #666; font-weight: 600; margin-bottom: 4px;">Implementation</div>
                            <div style="font-size: 0.9rem; font-weight: 600;">
                                @if($project->implementation->status === 'in_progress')
                                    <span style="color: #f59e0b;">🔄 In Progress</span>
                                @elseif($project->implementation->status === 'submitted')
                                    <span style="color: #3b82f6;">⏳ Submitted</span>
                                @else
                                    <span style="color: #10b981;">✓ Complete</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                @endif
                
                <a href="{{ route('student.projects.show', $project) }}" class="btn-primary" style="display: inline-block; margin-top: 10px;">
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>View Project
                </a>
            </div>
            @endforeach
        </div>
        <a href="{{ route('student.projects.index') }}" class="btn-primary" style="display: inline-block; margin-top: 20px;">
            <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>View All Projects
        </a>
    </div>
    @else
    <div class="dashboard-section">
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No projects yet</h3>
            <p>Create or join a group to start your first project. Projects help you apply what you've learned in class.</p>
            <a href="{{ route('student.projects.groups.index') }}" class="btn-primary">Get Started</a>
        </div>
    </div>
    @endif

    <!-- School Info Card -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div>
                <h3 style="margin: 0 0 10px 0; opacity: 0.9; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">School Information</h3>
                <p style="margin: 0; font-size: 1.3rem; font-weight: 600;">{{ $schoolStudentData['school']->name }}</p>
            </div>
            <div>
                <h3 style="margin: 0 0 10px 0; opacity: 0.9; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Your Classes</h3>
                <p style="margin: 0; font-size: 1.1rem; font-weight: 500;">{{ $schoolStudentData['classes']->pluck('name')->join(', ') }}</p>
            </div>
        </div>
    </div>
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #333;">Academic Dashboard</h2>
        
        <!-- Quick Stats for School Student -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Total Marks</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['total_marks'] }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Average Score</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['average_percentage'] }}%</p>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Groups</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['total_groups'] }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Projects</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['total_projects'] }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Assignments</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['total_assignments'] }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; padding: 15px; border-radius: 8px;">
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Pending</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $schoolStudentData['stats']['pending_assignments'] }}</p>
            </div>
        </div>

    @endif

    <!-- Stats Overview -->
    <div class="stats-grid">
        @if((!isset($isSchoolStudent) || !$isSchoolStudent) && !in_array(session('user_type'), ['teacher', 'subject_teacher']))
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-video"></i>
            </div>
            <div class="stat-content">
                <h3>Sample Videos</h3>
                <p class="stat-number">{{ $sampleVideosCount }}</p>
                <a href="{{ route('student.sample-videos.index') }}" class="stat-link">View All Sample Videos</a>
            </div>
        </div>
        @endif

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <h3>@if(in_array(session('user_type'), ['teacher', 'subject_teacher'])) My Created Videos @else My Videos @endif</h3>
                <p class="stat-number">{{ $availableVideosCount }}</p>
                @if($hasActiveSubscription || in_array(session('user_type'), ['teacher', 'subject_teacher']) || (isset($isSchoolStudent) && $isSchoolStudent))
                    <a href="{{ route('student.my-videos') }}" class="stat-link">View My Videos</a>
                @else
                    <a href="{{ route('pricing') }}" class="stat-link">Get Subscription</a>
                @endif
            </div>
        </div>

        @if(!in_array(session('user_type'), ['teacher', 'subject_teacher']))
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>My Subjects</h3>
                <p class="stat-number">{{ $availableSubjectsCount }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-content">
                <h3>My Topics</h3>
                <p class="stat-number">{{ $availableTopicsCount }}</p>
            </div>
        </div>
        @endif

        @if(!in_array(session('user_type'), ['teacher', 'subject_teacher']))
        @php
            // Determine student's academic level based on their class
            $studentClass = Auth::user()->classes->first();
            $isALevel = false;
            $isOLevel = false;
            
            if ($studentClass) {
                $className = strtolower($studentClass->name);
                // A-Level: Form 5, Form 6, S5, S6
                if (preg_match('/(form\s*[56]|s[56])/i', $className)) {
                    $isALevel = true;
                }
                // O-Level: Form 1-4, S1-S4
                elseif (preg_match('/(form\s*[1-4]|s[1-4])/i', $className)) {
                    $isOLevel = true;
                }
            }
        @endphp
        
        @if($isALevel)
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-graduation-cap" style="color: white;"></i>
                </div>
                <div class="stat-content">
                    <h3 style="color: rgba(255,255,255,0.9);">A-Level Course Recommendations</h3>
                    <p class="stat-number" style="color: white; font-size: 14px;">Based on UACE Results</p>
                    <a href="{{ route('student.course-recommendations.index') }}" class="stat-link" style="color: white; font-weight: 600;">
                        View Recommendations <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @elseif($isOLevel)
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-graduation-cap" style="color: white;"></i>
                </div>
                <div class="stat-content">
                    <h3 style="color: rgba(255,255,255,0.9);">A-Level Combination Guide</h3>
                    <p class="stat-number" style="color: white; font-size: 14px;">Based on UCE Results</p>
                    <a href="{{ route('student.career-guidance.index') }}" class="stat-link" style="color: white; font-weight: 600;">
                        View Recommendations <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @else
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-graduation-cap" style="color: white;"></i>
                </div>
                <div class="stat-content">
                    <h3 style="color: rgba(255,255,255,0.9);">Career Guidance</h3>
                    <p class="stat-number" style="color: white; font-size: 14px;">Available</p>
                    <a href="{{ route('student.career-guidance.index') }}" class="stat-link" style="color: white; font-weight: 600;">
                        View Recommendations <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endif
        @endif
    </div>

    <!-- My Videos Section -->
    @if($hasAvailableVideosLink && $userVideos->count() > 0)
        <div class="videos-section">
            <div class="section-header">
                <h2>@if(in_array(session('user_type'), ['teacher', 'subject_teacher'])) My Created Videos @else My Videos @endif</h2>
                <a href="{{ route('student.my-videos') }}" class="view-all">View All Videos</a>
            </div>
            <div class="videos-grid">
                @foreach($userVideos as $video)
                    <div class="video-card">
                        <div class="video-thumbnail">
                            @if($video->thumbnail_path)
                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}">
                            @else
                                <div class="video-thumbnail-placeholder">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            @endif
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">{{ $video->title }}</h3>
                            <p class="video-description">{{ Str::limit($video->description, 100) }}</p>
                            <div class="video-meta">
                                @if($video->subject)
                                    <span class="video-subject">
                                        <i class="fas fa-book"></i> {{ $video->subject->name }}
                                    </span>
                                @endif
                                @if($video->term)
                                    <span class="video-term">
                                        <i class="fas fa-calendar"></i> {{ $video->term->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="video-actions">
                                <a href="{{ route('student.my-videos.show', $video->id) }}" class="video-btn">
                                    <i class="fas fa-play"></i> Watch Video
                                </a>
                                @if(in_array(session('user_type'), ['teacher', 'subject_teacher']))
                                    @if(isset($video->unreplied_comments_count) && $video->unreplied_comments_count > 0)
                                        <span class="video-notification-badge" title="Unreplied student comments">
                                            <i class="fas fa-comment-exclamation"></i> {{ $video->unreplied_comments_count }}
                                        </span>
                                    @endif
                                    @if(isset($video->replied_comments_count) && $video->replied_comments_count > 0)
                                        <span class="video-replied-badge" title="Replied student comments">
                                            <i class="fas fa-comment-check"></i> {{ $video->replied_comments_count }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="view-all-container">
                <a href="{{ route('student.my-videos') }}" class="view-all-btn">
                    <i class="fas fa-list"></i> View All My Videos
                </a>
            </div>
        </div>
    @elseif((!isset($isSchoolStudent) || !$isSchoolStudent) && $sampleVideos->count() > 0 && !in_array(session('user_type'), ['teacher', 'subject_teacher']))
        <div class="videos-section">
            <div class="section-header">
                <h2>Sample Videos</h2>
                <a href="{{ route('student.sample-videos.index') }}" class="view-all">View All Videos</a>
            </div>
            <div class="videos-grid">
                @foreach($sampleVideos as $video)
                    <div class="video-card">
                        <div class="video-thumbnail">
                            @if($video->thumbnail_path)
                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}">
                            @else
                                <div class="video-thumbnail-placeholder">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            @endif
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">{{ $video->title }}</h3>
                            <p class="video-description">{{ Str::limit($video->description, 100) }}</p>
                            <div class="video-meta">
                                @if($video->subject)
                                    <span class="video-subject">
                                        <i class="fas fa-book"></i> {{ $video->subject->name }}
                                    </span>
                                @endif
                                @if($video->term)
                                    <span class="video-term">
                                        <i class="fas fa-calendar"></i> {{ $video->term->name }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('student.sample-videos.show', $video->hash_id) }}" class="video-btn">
                                <i class="fas fa-play"></i> Watch Video
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="view-all-container">
                <a href="{{ route('student.sample-videos.index') }}" class="view-all-btn">
                    <i class="fas fa-list"></i> View All Sample Videos
                </a>
            </div>
        </div>
    @endif

    <!-- Current License Section (Students Only) -->
    @if(isset($currentLicense) && $currentLicense && $currentLicense->subscription_package_id && !in_array(session('user_type'), ['teacher', 'subject_teacher']) && (!isset($isSchoolStudent) || !$isSchoolStudent))
        <div class="license-section">
            <div class="license-content">
                <div class="license-header">
                    <h3>Current License</h3>
                    <span class="license-name">{{ optional($currentLicense->subscriptionPackage)->name ?? 'N/A' }}</span>
                </div>
                <div class="license-details">
                    <span>Expires: </span>
                    <span class="expiry-date">
                        {{ $currentLicense->end_date ? \Carbon\Carbon::parse($currentLicense->end_date)->format('M d, Y') : '-' }}
                    </span>
                </div>
                <a href="{{ route('pricing') }}" class="dashboard-btn dashboard-btn-primary">Upgrade License</a>
            </div>
        </div>
    @endif

    <!-- My Marks Section -->
    @if($studentMarks->count() > 0 && (isset($isSchoolStudent) && $isSchoolStudent))
        <div class="marks-section" style="margin-top: 2rem;">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> My Marks</h2>
                @if(count($studentMarks) > 5)
                    <a href="{{ route('student.marks.index') }}" class="view-all">View All Marks</a>
                @endif
            </div>
            
            <div class="marks-container">
                @foreach($studentMarks->take(5) as $mark)
                    @php
                        // Determine if student is Form 5-6 (uses letter grades) or Form 1-4 (uses percentage marks)
                        $student = Auth::user()->student;
                        $studentClass = $student ? $student->schoolClass : null;
                        $classLevel = $studentClass ? $studentClass->level : null;
                        $formNumber = null;
                        
                        if ($classLevel && preg_match('/\d+/', strtolower($classLevel), $matches)) {
                            $formNumber = intval($matches[0]);
                        }
                        
                        $isUpperSecondary = $formNumber && $formNumber >= 5;
                    @endphp
                    
                    <div class="mark-card" style="border-left: 4px solid #4299e1; background: white; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.25rem; color: #2d3748; font-weight: 600;">{{ $mark->subject_name }}</h4>
                                <p style="margin: 0 0 0.5rem; color: #718096; font-size: 0.875rem;">{{ $mark->paper_name ?? 'Assessment' }}</p>
                                @if($mark->remarks)
                                    <p style="margin: 0; color: #4a5568; font-size: 0.875rem; font-style: italic;">{{ Str::limit($mark->remarks, 100) }}</p>
                                @endif
                            </div>
                            <div style="text-align: right;">
                                @if($isUpperSecondary && $mark->grade)
                                    <!-- Form 5-6: Display letter grade -->
                                    <div style="font-size: 2rem; font-weight: bold; color: #2b6cb0;">
                                        {{ $mark->grade }}
                                    </div>
                                    <div style="font-size: 0.875rem; color: #718096;">
                                        ({{ number_format($mark->numeric_mark, 0) }}%)
                                    </div>
                                @else
                                    <!-- Form 1-4: Display percentage only -->
                                    <div style="font-size: 2rem; font-weight: bold; color: #2b6cb0;">
                                        {{ number_format($mark->numeric_mark, 0) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0; color: #718096; font-size: 0.75rem;">
                            {{ $mark->created_at->format('M d, Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
</div>

<style>
.dashboard-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.welcome-section {
    margin-bottom: 1.5rem;
    text-align: center;
}

.welcome-section h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.welcome-section p {
    color: #6b7280;
    font-size: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: #f3f4f6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stat-icon i {
    font-size: 1.25rem;
    color: #4b5563;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.stat-link {
    color: #2563eb;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-block;
}

.license-section {
    background: #f9fafb;
    border-radius: 0.5rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #2563eb;
}

.license-header {
    margin-bottom: 0.75rem;
}

.license-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.license-name {
    color: #2563eb;
    font-weight: 500;
}

.license-details {
    color: #4b5563;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.expiry-date {
    color: #d97706;
    font-weight: 500;
}

.activity-section {
    background: white;
    border-radius: 0.5rem;
    padding: 1.25rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-header h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
}

.view-all {
    color: #2563eb;
    font-size: 0.875rem;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #d1d5db;
}

.dashboard-alert {
    margin-bottom: 1.5rem;
    border-radius: 0.5rem;
    padding: 1rem;
}

.dashboard-alert-warning {
    background: #fff7ed;
    border: 1px solid #fdba74;
}

.alert-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    text-align: center;
}

.dashboard-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    font-size: 0.875rem;
    line-height: 1.25rem;
    text-align: center;
    -webkit-tap-highlight-color: transparent;
}

.dashboard-btn-primary {
    background-color: #2563eb;
    color: white;
}

.dashboard-btn-primary:hover {
    background-color: #1d4ed8;
}

.stat-link-disabled {
    color: #bdbdbd !important;
    background: none !important;
    text-decoration: none;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #f3f4f6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.activity-icon i {
    font-size: 1.25rem;
    color: #4b5563;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.activity-time {
    color: #6b7280;
}

.activity-status {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.activity-status.completed {
    color: #059669;
}

.activity-status.completed i {
    font-size: 0.875rem;
}

.videos-section {
    margin-bottom: 1.5rem;
}

.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.video-card {
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.video-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.video-thumbnail {
    position: relative;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    background: #f3f4f6;
}

.video-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-thumbnail-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 2rem;
}

.video-info {
    padding: 1rem;
}

.video-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.video-description {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.video-subject, .video-term {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.video-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #2563eb;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s;
}

.video-btn:hover {
    background: #1d4ed8;
}
.video-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.video-notification-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    animation: pulse 2s infinite;
}
.video-replied-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.view-all-container {
    text-align: center;
    margin-top: 1.5rem;
}

.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #f3f4f6;
    color: #374151;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.view-all-btn:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.view-all-btn i {
    font-size: 1rem;
}

@media (max-width: 640px) {
    .dashboard-page {
        padding: 0.75rem;
    }

    .welcome-section h1 {
        font-size: 1.25rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 1rem;
    }

    .license-section {
        padding: 1rem;
    }

    .dashboard-btn {
        width: 100%;
        padding: 0.875rem 1rem;
        font-size: 1rem;
    }

    .alert-content {
        padding: 0.5rem;
    }

    .videos-grid {
        grid-template-columns: 1fr;
    }
}
</style>

{{-- Remove mobile redirect logic - users with subscriptions should access their videos regardless of device --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileDropdown = document.getElementById('profileDropdown');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');
    const profileDropdownToggleName = document.getElementById('profileDropdownToggleName');

    if (profileDropdown && profileDropdownMenu) {
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) {
                profileDropdownMenu.classList.remove('show');
            }
        });
    }

    // Mobile profile dropdown functionality
    const mobileProfileBtn = document.getElementById('mobileProfileBtn');
    const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');

    if (mobileProfileBtn && mobileProfileDropdown) {
        mobileProfileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileProfileDropdown.classList.toggle('show');
        });

        // Close mobile dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileProfileBtn.contains(e.target) && !mobileProfileDropdown.contains(e.target)) {
                mobileProfileDropdown.classList.remove('show');
            }
        });
    }
});
</script>
@endpush

@endsection 