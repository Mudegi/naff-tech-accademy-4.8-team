@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Video Player</h1>
        <a href="{{ url()->previous() }}" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="dashboard-card">
        <div class="video-container">
            <div class="drive-preview">
                <iframe 
                    src="{{ $driveUrl }}" 
                    width="100%" 
                    height="600" 
                    allow="autoplay; encrypted-media"
                    frameborder="0"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-content-inner {
    padding: 20px;
}

.dashboard-breadcrumbs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.dashboard-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.dashboard-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dashboard-btn i {
    margin-right: 8px;
}

.dashboard-btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.dashboard-btn-secondary:hover {
    background-color: #7f8c8d;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.video-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.drive-preview {
    width: 100%;
    position: relative;
}

.drive-preview::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 60px;
    background: white;
    z-index: 1000;
}

.drive-preview iframe {
    width: 100%;
    border: none;
    border-radius: 4px;
}
</style>
@endsection 