@extends('layouts.dashboard')

@section('content')
<div class="groups-page">
    <!-- Header -->
    <div class="page-header">
        <h1>Groups</h1>
        <p>Manage and monitor student groups</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('teacher.groups.create') }}" class="action-btn action-btn-primary">
            <i class="fas fa-plus"></i>
            Create Group
        </a>
    </div>

    <!-- Groups Grid -->
    @if($groups->count() > 0)
    <div class="groups-grid">
        @foreach($groups as $group)
        <div class="group-card">
            <div class="group-header">
                <h3 class="group-title">{{ $group->name }}</h3>
                <span class="group-status">{{ $group->status ?? 'active' }}</span>
            </div>

            <div class="group-info">
                <p class="group-description">{{ Str::limit($group->description, 100) }}</p>
                <div class="group-meta">
                    <span class="meta-item">
                        <i class="fas fa-users"></i>
                        {{ $group->approvedMembers->count() }}/{{ $group->max_members }} members
                    </span>
                </div>
            </div>

            <div class="group-actions">
                <a href="{{ route('teacher.groups.submissions', $group) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> Submissions
                </a>
                    <a href="{{ route('teacher.groups.assign.students.form', $group) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-user-plus"></i> Assign Students
                    </a>
                <a href="{{ route('teacher.groups.assign.form', $group) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-tasks"></i> Assign
                </a>
                <a href="{{ route('teacher.groups.edit', $group) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('teacher.groups.destroy', $group) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this group?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{ $groups->links() }}
    @else
    <div class="empty-state">
        <p>No groups yet. <a href="{{ route('teacher.groups.create') }}">Create one now</a></p>
    </div>
    @endif
</div>

<style>
    .groups-page { padding: 20px; }
    .page-header { margin-bottom: 20px; }
    .page-header h1 { font-size: 24px; margin-bottom: 5px; }
    .quick-actions { margin-bottom: 20px; }
    .action-btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 500; }
    .action-btn-primary { background: #007bff; color: white; }
    .action-btn-primary:hover { background: #0056b3; }
    .groups-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-bottom: 20px; }
    .group-card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .group-title { margin: 0; font-size: 18px; font-weight: 600; }
    .group-status { background: #e7f3ff; color: #0056b3; padding: 4px 8px; border-radius: 3px; font-size: 12px; }
    .group-description { margin: 10px 0; color: #666; font-size: 14px; }
    .group-meta { margin: 10px 0; font-size: 13px; color: #666; }
    .meta-item { display: inline-block; margin-right: 15px; }
    .group-actions { display: flex; gap: 8px; margin-top: 15px; flex-wrap: wrap; }
    .btn { padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-block; font-size: 13px; }
    .btn-sm { padding: 4px 8px; font-size: 12px; }
    .btn-primary { background: #007bff; color: white; }
    .btn-primary:hover { background: #0056b3; }
    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; }
    .btn-warning { background: #ffc107; color: black; }
    .btn-warning:hover { background: #e0a800; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; }
    .empty-state { text-align: center; padding: 40px 20px; color: #666; }
</style>
@endsection
