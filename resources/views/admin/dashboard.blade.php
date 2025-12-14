@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    @php
        $user = Auth::user();
        $currentSchoolContext = null;
        // Show school context switcher for admins without school_id (both super admin and regular admin)
        if ($user->account_type === 'admin' && !$user->school_id) {
            $schoolId = session('admin_school_context');
            if ($schoolId) {
                $currentSchoolContext = \App\Models\School::find($schoolId);
            }
        }
    @endphp
    
    @if($user->account_type === 'admin' && !$user->school_id)
    <!-- School Context Switcher -->
    <div class="school-context-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-school" style="font-size: 1.5rem;"></i>
            <div>
                <div style="font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem;">
                    @if($currentSchoolContext)
                        Working in: <strong>{{ $currentSchoolContext->name }}</strong>
                    @else
                        <strong>Global Context</strong> - All Schools
                    @endif
                </div>
                <div style="font-size: 0.875rem; opacity: 0.9;">
                    @if($currentSchoolContext)
                        All operations will be for this school
                    @else
                        Select a school to work within its context
                    @endif
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; position: relative;">
            <div style="position: relative;">
                <button id="schoolContextBtn" style="padding: 0.5rem 1rem; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.25); color: white; font-size: 0.875rem; cursor: pointer; min-width: 200px; text-align: left; display: flex; align-items: center; justify-content: space-between; font-weight: 500;">
                    <span id="schoolContextBtnText">@if($currentSchoolContext){{ $currentSchoolContext->name }}@else Select School...@endif</span>
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.75rem;"></i>
                </button>
                <div id="schoolContextDropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 0.5rem; background: white; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.15); min-width: 300px; z-index: 1000; max-height: 400px; overflow: hidden; flex-direction: column;">
                    <div style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <input type="text" id="schoolSearchInput" placeholder="Search schools..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#d1d5db'">
                    </div>
                    <div id="schoolContextList" style="max-height: 300px; overflow-y: auto;">
                        <a href="{{ route('admin.school-context.switch') }}" class="school-context-option" data-school-id="" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; transition: background 0.2s; border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            <div style="font-weight: 600; color: #667eea;">🌐 Global (All Schools)</div>
                            <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Work across all schools</div>
                        </a>
                        @foreach(\App\Models\School::orderBy('name')->get() as $school)
                        <a href="{{ route('admin.school-context.switch', $school->id) }}" class="school-context-option" data-school-id="{{ $school->id }}" data-school-name="{{ $school->name }}" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; transition: background 0.2s; border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            <div style="font-weight: 500;">🏫 {{ $school->name }}</div>
                            @if($currentSchoolContext && $currentSchoolContext->id == $school->id)
                            <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem; font-weight: 500;">
                                <i class="fas fa-check-circle"></i> Currently Active
                            </div>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @if($currentSchoolContext)
            <a href="{{ route('admin.school-context.switch') }}" style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); color: white; border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; border: 1px solid rgba(255,255,255,0.3); transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </div>
    </div>
    <style>
        .school-context-option:hover {
            background: #f3f4f6 !important;
        }
        #schoolContextList::-webkit-scrollbar {
            width: 6px;
        }
        #schoolContextList::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        #schoolContextList::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        #schoolContextList::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <script>
        (function() {
            const btn = document.getElementById('schoolContextBtn');
            const dropdown = document.getElementById('schoolContextDropdown');
            const searchInput = document.getElementById('schoolSearchInput');
            const schoolOptions = document.querySelectorAll('.school-context-option');
            
            // Toggle dropdown
            btn?.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = dropdown.style.display === 'flex';
                dropdown.style.display = isOpen ? 'none' : 'flex';
                if (!isOpen) {
                    searchInput.focus();
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
            
            // Live search functionality
            searchInput?.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                schoolOptions.forEach(option => {
                    const schoolName = option.getAttribute('data-school-name') || '';
                    const text = option.textContent.toLowerCase();
                    
                    if (searchTerm === '' || text.includes(searchTerm) || schoolName.toLowerCase().includes(searchTerm)) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
            
            // Prevent dropdown from closing when clicking inside
            dropdown?.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        })();
    </script>
    @endif
    
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> <span class="breadcrumb-active">Dashboard</span>
        </div>
    </div>
    <!-- Stats Overview -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">✉️</div>
            <div class="stat-info">
                <div class="stat-value">{{ $contactMessages->count() }}</div>
                <div class="stat-label">Total Messages</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">👥</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">📚</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalSubjects }}</div>
                <div class="stat-label">Subjects</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">📝</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalTopics }}</div>
                <div class="stat-label">Topics</div>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="stat-cards mt-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">👨‍🎓</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalStudents }}</div>
                <div class="stat-label">Students</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">👨‍🏫</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalInstructors }}</div>
                <div class="stat-label">Instructors</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-red">👨‍💼</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalAdmins }}</div>
                <div class="stat-label">Admins</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-indigo">📎</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalResources }}</div>
                <div class="stat-label">Resources</div>
            </div>
        </div>
    </div>

    <!-- Assignment Stats -->
    <div class="stat-cards mt-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">📋</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalStudentAssignments }}</div>
                <div class="stat-label">Student Submissions</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">📝</div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalTeacherAssignments }}</div>
                <div class="stat-label">Teacher Assignments</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">⏳</div>
            <div class="stat-info">
                <div class="stat-value">{{ $submittedAssignments }}</div>
                <div class="stat-label">Pending Review</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">✅</div>
            <div class="stat-info">
                <div class="stat-value">{{ $gradedAssignments }}</div>
                <div class="stat-label">Graded</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-table-container mb-4">
        <div class="dashboard-table-header">
            <h3>Assignment Management</h3>
        </div>
        <div class="quick-actions">
            <a href="{{ route('admin.assignments.index') }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-clipboard-list"></i> View Student Submissions
            </a>
            <a href="{{ route('admin.teacher-assignments.index') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-tasks"></i> View Teacher Assignments
            </a>
        </div>
    </div>

    <!-- Parent Portal Management -->
    <div class="dashboard-table-container mb-4">
        <div class="dashboard-table-header">
            <h3>Parent Portal Management</h3>
        </div>
        <div class="quick-actions">
            <a href="{{ route('admin.users.student-parent-list') }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-users"></i> Student-Parent Accounts
            </a>
            <a href="{{ route('admin.parent-student.bulk-import') }}" class="dashboard-btn dashboard-btn-secondary">
                <i class="fas fa-file-upload"></i> Bulk Import Links
            </a>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="dashboard-table-container">
        <div class="dashboard-table-header">
            <h3>Recent Contact Messages</h3>
            <a href="#" class="dashboard-btn dashboard-btn-primary">View All</a>
        </div>
        <div class="dashboard-table-scroll">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contactMessages as $message)
                    <tr>
                        <td>{{ $message->id }}</td>
                        <td class="font-bold">{{ $message->name }}</td>
                        <td>{{ $message->email }}</td>
                        <td>{{ $message->subject }}</td>
                        <td>{{ Str::limit($message->message, 50) }}</td>
                        <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="#" class="dashboard-btn dashboard-btn-primary dashboard-btn-xs">View</a>
                            <a href="#" class="dashboard-btn dashboard-btn-secondary dashboard-btn-xs">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.quick-actions {
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.quick-actions .dashboard-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.quick-actions .dashboard-btn-primary {
    background: #3b82f6;
    color: white;
}

.quick-actions .dashboard-btn-primary:hover {
    background: #2563eb;
    color: white;
}

.quick-actions .dashboard-btn-secondary {
    background: #6b7280;
    color: white;
}

.quick-actions .dashboard-btn-secondary:hover {
    background: #4b5563;
    color: white;
}

@media (max-width: 640px) {
    .quick-actions {
        flex-direction: column;
    }
    
    .quick-actions .dashboard-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection 