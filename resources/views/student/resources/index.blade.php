@extends('layouts.student-dashboard')

@section('content')
<style>
    .resources-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .page-header p {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }

    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .resource-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-top: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .resource-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        border-radius: 50%;
        transform: translate(40%, -40%);
    }

    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.15);
    }

    .resource-card.pdf {
        border-top-color: #ef4444;
    }

    .resource-card.image {
        border-top-color: #3b82f6;
    }

    .resource-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .resource-icon.pdf {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .resource-icon.image {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .resource-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        line-height: 1.4;
        position: relative;
        z-index: 1;
    }

    .resource-meta {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .meta-item i {
        width: 28px;
        font-size: 14px;
        color: #667eea;
    }

    .meta-label {
        font-weight: 600;
        color: #374151;
        margin-right: 6px;
    }

    .resource-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-action {
        padding: 12px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-view:hover {
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }

    .btn-download {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);
    }

    .btn-download:hover {
        box-shadow: 0 6px 20px rgba(67, 233, 123, 0.4);
        transform: translateY(-2px);
    }

    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 80px;
        color: #e5e7eb;
        margin-bottom: 20px;
        display: block;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 1rem;
        color: #6b7280;
    }

    .pagination {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .resources-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="resources-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-book-open mr-3"></i>Learning Resources</h1>
        <p>Access educational materials shared by your teachers</p>
    </div>

    <!-- Resources Grid -->
    @if($resources->count() > 0)
        <div class="resources-grid">
            @foreach($resources as $resource)
                <div class="resource-card {{ $resource->notes_file_type === 'pdf' ? 'pdf' : 'image' }}">
                    <!-- Resource Icon -->
                    <div class="resource-icon {{ $resource->notes_file_type === 'pdf' ? 'pdf' : 'image' }}">
                        @if($resource->notes_file_type === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @else
                            <i class="fas fa-image"></i>
                        @endif
                    </div>

                    <!-- Resource Title -->
                    <h3 class="resource-title">{{ $resource->title }}</h3>

                    <!-- Resource Meta -->
                    <div class="resource-meta">
                        <div class="meta-item">
                            <i class="fas fa-book"></i>
                            <span><span class="meta-label">Subject:</span>{{ $resource->subject->name ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-bookmark"></i>
                            <span><span class="meta-label">Topic:</span>{{ $resource->topic->name ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span><span class="meta-label">Class:</span>{{ $resource->classRoom->name ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><span class="meta-label">Term:</span>{{ $resource->term->name ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span><span class="meta-label">Teacher:</span>{{ $resource->teacher->name ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $resource->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="resource-actions">
                        <a href="{{ asset('storage/' . $resource->notes_file_path) }}" 
                           class="btn-action btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ asset('storage/' . $resource->notes_file_path) }}" 
                           download 
                           class="btn-action btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $resources->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No Resources Available Yet</h3>
            <p>Your teachers haven't uploaded any resources for your class yet. Check back soon!</p>
        </div>
    @endif
</div>
@endsection
