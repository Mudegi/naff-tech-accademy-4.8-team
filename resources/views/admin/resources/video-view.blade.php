@extends('layouts.dashboard')

@section('content')
<div class="video-fullscreen-container">
    <div class="video-wrapper">
        <div class="video-preview" style="position:relative;">
            <iframe
                id="main-video-iframe"
                width="100%"
                height="100%"
                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&controls=1&showinfo=0"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                style="border-radius: 12px; background: #000;"
            ></iframe>
            <div class="yt-overlay"></div>
            <div class="video-preview-overlay" id="video-preview-overlay" style="display:none;"></div>
        </div>
    </div>
</div>

<style>
.video-fullscreen-container {
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #111;
}
.video-wrapper {
    width: 80vw;
    height: 80vh;
    max-width: 1280px;
    max-height: 720px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.video-preview {
    width: 100%;
    height: 100%;
    position: relative;
}
.yt-overlay {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 60px;
    background: white;
    z-index: 1000;
    border-top-right-radius: 12px;
}
.video-preview::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 180px;
    height: 60px;
    background: white;
    z-index: 1000;
    border-bottom-right-radius: 12px;
}
.video-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.85);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #333;
    cursor: pointer;
    border-radius: 12px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.querySelector('.video-preview');
    const overlay = document.getElementById('video-preview-overlay');
    const iframe = document.getElementById('main-video-iframe');

    preview.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        overlay.style.display = 'flex';
        overlay.innerHTML = 'Right-click is disabled';
    });

    overlay.addEventListener('click', function() {
        overlay.style.display = 'none';
    });

    // Hide overlay when video is played (using postMessage API for YouTube iframe)
    window.addEventListener('message', function(event) {
        if (typeof event.data === 'string' && event.data.indexOf('"event":"play"') !== -1) {
            overlay.style.display = 'none';
        }
    });
});
</script>
@endsection 