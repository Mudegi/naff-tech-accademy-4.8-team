@extends('layouts.dashboard')

@section('title', 'Team Member Details')

@section('content')
<div class="dashboard-content">
    <div class="content-header">
        <div class="header-left">
            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Team
            </a>
        </div>
        <div class="header-right">
            <h1 class="content-title">Team Member Details</h1>
            <p class="content-subtitle">View team member information</p>
        </div>
    </div>

    <div class="content-body">
        <div class="team-details-container">
            <div class="team-card">
                <div class="team-image-section">
                    <img src="{{ asset('storage/' . $team->image_path) }}" 
                         alt="{{ $team->name }}"
                         onerror="handleImageError(this)">
                    <div class="status-badge {{ $team->is_active ? 'active' : 'inactive' }}">
                        {{ $team->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>

                <div class="team-info-section">
                    <h2 class="team-name">{{ $team->name }}</h2>
                    <p class="team-position">{{ $team->position }}</p>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Sort Order:</label>
                            <span>{{ $team->sort_order }}</span>
                        </div>
                        <div class="info-item">
                            <label>Created:</label>
                            <span>{{ $team->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Last Updated:</label>
                            <span>{{ $team->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="skills-section">
                        <h3>Skills</h3>
                        <div class="skills-container">
                            @foreach($team->skills_array as $skill)
                            <span class="skill-tag">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.teams.toggle-status', $team) }}" 
                              method="POST" class="inline-form">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn {{ $team->is_active ? 'btn-secondary' : 'btn-success' }}">
                                <i class="fas fa-{{ $team->is_active ? 'pause' : 'play' }}"></i>
                                {{ $team->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.teams.destroy', $team) }}" 
                              method="POST" class="inline-form"
                              onsubmit="return confirm('Are you sure you want to delete this team member?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left {
    flex: 0 0 auto;
}

.header-right {
    flex: 1;
    text-align: right;
}

.team-details-container {
    max-width: 800px;
    margin: 0 auto;
}

.team-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 0;
}

.team-image-section {
    position: relative;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.team-image-section img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.team-info-section {
    padding: 2rem;
}

.team-name {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.team-position {
    font-size: 1.25rem;
    color: #3b82f6;
    font-weight: 600;
    margin: 0 0 2rem 0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.info-item span {
    color: #6b7280;
}

.skills-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

.skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.skill-tag {
    background: #e0e7ff;
    color: #3730a3;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-outline-secondary {
    background: transparent;
    color: #6b7280;
    border: 2px solid #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

.inline-form {
    display: inline;
}

@media (max-width: 768px) {
    .team-card {
        grid-template-columns: 1fr;
    }
    
    .team-image-section {
        padding: 1rem;
    }
    
    .team-image-section img {
        width: 150px;
        height: 150px;
    }
    
    .team-info-section {
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
function handleImageError(img) {
    img.src = '{{ asset("images/team.jpg") }}';
}
</script>
@endsection
