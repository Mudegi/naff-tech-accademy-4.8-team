@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Parent Dashboard</h1>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-content">
                <h3>My Children</h3>
                <p class="stat-number">0</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <h3>Active Subscriptions</h3>
                <p class="stat-number">0</p>
            </div>
        </div>
    </div>

    @if(isset($hasActiveSubscription) && $hasActiveSubscription)
        <!-- Subscription Videos Section -->
        <div class="dashboard-section">
            <h2>Available Videos</h2>
            <div class="videos-grid">
                @forelse($subscriptionVideos as $video)
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
                                <span class="video-duration">
                                    <i class="fas fa-clock"></i> {{ $video->duration ?? 'N/A' }}
                                </span>
                                <span class="video-subject">
                                    <i class="fas fa-book"></i> {{ $video->subject->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-video"></i>
                        <p>No videos available in your subscription</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Sample Videos Section -->
        <div class="dashboard-section">
            <h2>Sample Videos</h2>
            <div class="videos-grid">
                @forelse($sampleVideos as $video)
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
                                <span class="video-duration">
                                    <i class="fas fa-clock"></i> {{ $video->duration ?? 'N/A' }}
                                </span>
                                <span class="video-subject">
                                    <i class="fas fa-book"></i> {{ $video->subject->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-video"></i>
                        <p>No sample videos available</p>
                    </div>
                @endforelse
            </div>
            <div class="subscription-cta">
                <p>Get access to all videos with a subscription</p>
                <a href="{{ route('pricing') }}" class="dashboard-btn dashboard-btn-primary">View Subscription Plans</a>
            </div>
        </div>
    @endif
</div>

<style>
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.video-card {
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.video-card:hover {
    transform: translateY(-2px);
}

.video-thumbnail {
    position: relative;
    padding-top: 56.25%; /* 16:9 aspect ratio */
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
    background: #f3f4f6;
}

.video-thumbnail-placeholder i {
    font-size: 3rem;
    color: #9ca3af;
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
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
}

.video-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.video-meta i {
    margin-right: 0.25rem;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 0.5rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.subscription-cta {
    text-align: center;
    margin-top: 2rem;
    padding: 2rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.subscription-cta p {
    margin-bottom: 1rem;
    color: #4b5563;
}
</style>
@endsection 