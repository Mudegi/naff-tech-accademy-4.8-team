@extends('layouts.student-dashboard')

@section('title', $resource->title)

@section('content')
<div class="video-page">
    <!-- Header Section -->
    <div class="video-header">
        <a href="{{ url()->previous() }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
        <h1 class="video-title">{{ $resource->title }}</h1>
    </div>

    <!-- Video Section -->
    <div class="video-section">
        @if($driveFileId)
            <div class="video-wrapper" id="videoWrapper">
                <div class="video-overlay"></div>
                <iframe 
                    src="https://drive.google.com/file/d/{{ $driveFileId }}/preview" 
                    allow="autoplay; encrypted-media"
                    frameborder="0"
                    allowfullscreen
                    id="videoFrame"
                    class="video-frame"
                ></iframe>
            </div>
        @elseif($videoId)
            <div class="video-wrapper" id="videoWrapper">
                <div class="video-overlay"></div>
                <iframe 
                    src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&controls=1&showinfo=0" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    id="videoFrame"
                    class="video-frame"
                ></iframe>
            </div>
        @else
            <div class="no-video">
                <i class="fas fa-video-slash"></i>
                <p>No video available</p>
            </div>
        @endif
    </div>

    <!-- Learning Outcomes Section -->
    @if($resource->learning_outcomes)
        <div class="learning-outcomes-section">
            <div class="section-header">
                <i class="fas fa-graduation-cap"></i>
                <h2>What You'll Learn</h2>
            </div>
            
            <div class="learning-outcomes-content">
                @php
                    // Try to decode as JSON first, then fallback to comma-separated
                    $decoded = json_decode($resource->learning_outcomes, true);
                    if (is_array($decoded)) {
                        $learningOutcomes = $decoded;
                    } else {
                        // Split by comma and clean up
                        $learningOutcomes = array_filter(array_map('trim', explode(',', $resource->learning_outcomes)));
                    }
                @endphp
                
                @if(count($learningOutcomes) > 0)
                    <div class="outcomes-list">
                        @foreach($learningOutcomes as $index => $outcome)
                            <div class="outcome-item">
                                <div class="outcome-number">{{ $index + 1 }}</div>
                                <div class="outcome-text">{{ $outcome }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-outcomes">No learning outcomes specified for this resource.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Notes Files Section -->
    @if($resource->notes_file_path)
        <div class="notes-files-section">
            <div class="section-header">
                <i class="fas fa-file-alt"></i>
                <h2>Study Materials (Online Reading)</h2>
            </div>
            
            <div class="notes-content">
                @php
                    $fileExtension = strtolower(pathinfo($resource->notes_file_path, PATHINFO_EXTENSION));
                    $fileName = basename($resource->notes_file_path);
                    $fileUrl = Storage::url($resource->notes_file_path);
                @endphp
                
                <div class="file-info">
                    <div class="file-icon">
                        @if($fileExtension == 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif(in_array($fileExtension, ['doc', 'docx']))
                            <i class="fas fa-file-word"></i>
                        @elseif(in_array($fileExtension, ['ppt', 'pptx']))
                            <i class="fas fa-file-powerpoint"></i>
                        @elseif(in_array($fileExtension, ['xls', 'xlsx']))
                            <i class="fas fa-file-excel"></i>
                        @else
                            <i class="fas fa-file"></i>
                        @endif
                    </div>
                    <div class="file-details">
                        <h3 class="file-name">{{ $fileName }}</h3>
                        <p class="file-type">{{ strtoupper($fileExtension) }} Document - Online Reading Only</p>
                    </div>
                </div>

                <div class="file-preview">
                    @if($fileExtension == 'pdf')
                        <div class="pdf-container">
                            <iframe 
                                src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1" 
                                width="100%" 
                                height="600px" 
                                frameborder="0"
                                class="preview-frame"
                            ></iframe>
                            <div class="pdf-overlay"></div>
                        </div>
                    @elseif(in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                        <div class="office-preview">
                            <iframe 
                                src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" 
                                width="100%" 
                                height="600px" 
                                frameborder="0"
                                class="preview-frame"
                            ></iframe>
                        </div>
                    @else
                        <div class="unsupported-format">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Preview not available for this file type. This file can only be accessed through the admin panel.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Content Section -->
    <div class="content-section">
        <div class="description">
            {!! $resource->description !!}
        </div>

        @if($resource->notes)
            <div class="additional-resources">
                <h2>Additional Resources</h2>
                <div class="resources-content">
                    {!! $resource->notes !!}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.video-page {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Header Styles */
.video-header {
    margin-bottom: 1.5rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
}

.back-button:hover {
    background-color: #f3f4f6;
}

.video-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

/* Video Section Styles */
.video-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.video-wrapper {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
}

.video-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.video-overlay {
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 60px;
    background: white;
    z-index: 1000;
    pointer-events: none;
}

.no-video {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: #6b7280;
}

.no-video i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

/* Content Section Styles */
.content-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.description {
    color: #374151;
    line-height: 1.6;
}

.description p {
    margin-bottom: 1rem;
}

.additional-resources {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.additional-resources h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 1rem;
}

.resources-content {
    background: #f9fafb;
    border-radius: 0.375rem;
    padding: 1rem;
    color: #374151;
    line-height: 1.6;
}

/* Mobile Styles */
@media (max-width: 640px) {
    .video-page {
        padding: 0.75rem;
    }

    .video-title {
        font-size: 1.25rem;
    }

    .content-section {
        padding: 1rem;
    }

    .description, .resources-content {
        font-size: 0.875rem;
    }

    .additional-resources h2 {
        font-size: 1.125rem;
    }
}

/* Prevent text selection */
.video-wrapper {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Learning Outcomes Styles */
.learning-outcomes-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #3b82f6;
}

.section-header i {
    font-size: 1.5rem;
    color: #3b82f6;
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.learning-outcomes-content {
    padding: 0;
}

.outcomes-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.outcome-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    border-left: 4px solid #3b82f6;
    transition: all 0.3s ease;
}

.outcome-item:hover {
    background: #e0e7ff;
    transform: translateX(5px);
}

.outcome-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.outcome-text {
    flex: 1;
    font-size: 1rem;
    line-height: 1.5;
    color: #374151;
    margin: 0;
}

.no-outcomes {
    text-align: center;
    color: #6b7280;
    font-style: italic;
    padding: 2rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 2px dashed #d1d5db;
}

/* Mobile Styles for Learning Outcomes */
@media (max-width: 640px) {
    .learning-outcomes-section {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .section-header {
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .section-header i {
        font-size: 1.25rem;
    }

    .section-header h2 {
        font-size: 1.125rem;
    }

    .outcome-item {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.75rem;
    }

    .outcome-number {
        align-self: flex-start;
    }

    .outcome-text {
        font-size: 0.875rem;
    }

    .no-outcomes {
        padding: 1.5rem;
        font-size: 0.875rem;
    }

    /* Notes Files Section Styles */
    .notes-files-section {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .notes-content {
        margin-top: 1rem;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .file-icon {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e5e7eb;
        border-radius: 0.375rem;
        color: #6b7280;
        font-size: 1.5rem;
    }

    .file-icon .fa-file-pdf {
        color: #dc2626;
    }

    .file-icon .fa-file-word {
        color: #2563eb;
    }

    .file-icon .fa-file-powerpoint {
        color: #dc2626;
    }

    .file-icon .fa-file-excel {
        color: #059669;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
    }

    .file-type {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .file-actions {
        flex-shrink: 0;
    }

    .file-preview {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #f8fafc;
    }

    .preview-frame {
        display: block;
        border: none;
    }

    .pdf-container {
        position: relative;
        width: 100%;
        height: 600px;
    }

    .pdf-overlay {
    position: absolute;
    top: 0;
    right: 0;
    width: 30%;
    height: 9%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 10;
    pointer-events: auto;
}

    .office-preview {
        background: #f8fafc;
        padding: 1rem;
        text-align: center;
    }

    .unsupported-format {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        text-align: center;
        color: #6b7280;
    }

    .unsupported-format i {
        font-size: 2rem;
        color: #f59e0b;
        margin-bottom: 1rem;
    }

    .unsupported-format p {
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    /* Mobile Styles for Notes Files */
    @media (max-width: 640px) {
        .notes-files-section {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .file-info {
            flex-direction: column;
            gap: 0.75rem;
            text-align: center;
        }

        .file-icon {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1.25rem;
        }

        .file-name {
            font-size: 1rem;
        }

        .file-type {
            font-size: 0.8rem;
        }

        .preview-frame {
            height: 400px;
        }
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoFrame = document.getElementById('videoFrame');
    let watchDuration = 0;
    let isCompleted = false;
    let trackingInterval;

    // Function to track video progress
    function trackVideoProgress() {
        if (videoFrame) {
            try {
                // Get video element from iframe
                const video = videoFrame.contentWindow.document.querySelector('video');
                if (video) {
                    // Update watch duration
                    watchDuration = Math.floor(video.currentTime);
                    console.log('Current watch duration:', watchDuration);
                    
                    // Check if video is completed (watched 90% or more)
                    isCompleted = video.currentTime >= (video.duration * 0.9);
                    console.log('Is completed:', isCompleted);

                    // Send tracking data to server
                    console.log('Sending tracking data...');
                    fetch('{{ route("student.videos.track") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            resource_id: '{{ $resource->id }}',
                            watch_duration: watchDuration,
                            completed: isCompleted
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Tracking response:', data);
                    })
                    .catch(error => {
                        console.error('Tracking error:', error);
                    });
                } else {
                    console.log('No video element found in iframe');
                }
            } catch (error) {
                console.error('Error tracking video:', error);
            }
        } else {
            console.log('No video frame found');
        }
    }

    // Start tracking when video is loaded
    if (videoFrame) {
        console.log('Video frame found, setting up tracking...');
        videoFrame.addEventListener('load', function() {
            console.log('Video frame loaded, starting tracking interval...');
            // Start tracking every 30 seconds
            trackingInterval = setInterval(trackVideoProgress, 30000);
            // Also track immediately
            trackVideoProgress();
        });
    }

    // Clean up when page is unloaded
    window.addEventListener('beforeunload', function() {
        console.log('Page unloading, sending final tracking data...');
        if (trackingInterval) {
            clearInterval(trackingInterval);
        }
        // Send final tracking data
        trackVideoProgress();
    });
});
</script>
@endpush
@endsection 