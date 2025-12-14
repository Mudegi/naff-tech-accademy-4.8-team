@extends('layouts.dashboard')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Resource Details</h1>
        <div>
            <a href="{{ route('admin.resources.edit', $resource->hash_id) }}" class="dashboard-btn dashboard-btn-primary">Edit Resource</a>
            <a href="{{ route('admin.resources.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Resources</a>
        </div>
    </div>

    <div class="profile-card" style="max-width:900px;margin:0 auto;">
        <div class="profile-row">
            <div class="profile-col profile-col-details">
                <div class="profile-form-group">
                    <label>Title</label>
                    <p class="profile-value">{{ $resource->title }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Description</label>
                    <p class="profile-value">{{ $resource->description }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Subject</label>
                    <p class="profile-value">{{ $resource->subject ? $resource->subject->name : '-' }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Class</label>
                    <p class="profile-value">{{ $resource->classRoom ? $resource->classRoom->name : '-' }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Topic</label>
                    <p class="profile-value">{{ $resource->topic ? $resource->topic->name : '-' }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Term</label>
                    <p class="profile-value">{{ $resource->term ? $resource->term->name : '-' }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Grade Level</label>
                    <p class="profile-value">{{ $resource->grade_level }}</p>
                </div>
            </div>

            <div class="profile-col profile-col-password">
                <div class="profile-form-group">
                    <label>Video URL</label>
                    @if($resource->video_url)
                        <p class="profile-value">
                            <span class="video-url-display" style="display: none;">{{ $resource->video_url }}</span>
                        </p>
                        @php
                            $videoId = null;
                            if (preg_match('/youtu\.be\/([\w-]{11})/', $resource->video_url, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('/[?&]v=([\w-]{11})/', $resource->video_url, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('/embed\/([\w-]{11})/', $resource->video_url, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        @if($videoId)
                            <div class="video-preview" style="margin-top: 10px; position: relative;">
                                <iframe 
                                    id="youtube-iframe"
                                    width="560" 
                                    height="315" 
                                    src="https://www.youtube.com/embed/{{ $videoId }}?modestbranding=1&rel=0&showinfo=0&controls=1&disablekb=1&fs=0&iv_load_policy=3" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                                    loading="lazy"
                                    referrerpolicy="strict-origin"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @endif
                    @else
                        <p class="profile-value">-</p>
                    @endif
                </div>

                <div class="profile-form-group">
                    <label>Google Drive Link</label>
                    @if($resource->google_drive_link)
                        <p class="profile-value">
                            <span class="drive-url-display" style="display: none;">{{ $resource->google_drive_link }}</span>
                        </p>
                        @php
                            $fileId = null;
                            if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $resource->google_drive_link, $matches)) {
                                $fileId = $matches[1];
                            }
                        @endphp
                        @if($fileId)
                            <div class="drive-preview" style="margin-top: 10px; position: relative;">
                                <iframe 
                                    id="drive-iframe"
                                    width="560" 
                                    height="315" 
                                    src="https://drive.google.com/file/d/{{ $fileId }}/preview" 
                                    title="Google Drive Preview" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                                    loading="lazy"
                                    referrerpolicy="strict-origin"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @endif
                    @else
                        <p class="profile-value">-</p>
                    @endif
                </div>

                <div class="profile-form-group">
                    <label>Notes File</label>
                    @if($resource->notes_file_path)
                        <p class="profile-value">
                            <a href="{{ Storage::url($resource->notes_file_path) }}" target="_blank" class="dashboard-link">
                                {{ basename($resource->notes_file_path) }}
                            </a>
                            <span class="file-type">({{ strtoupper($resource->notes_file_type) }})</span>
                        </p>
                        @php
                            $fileExtension = strtolower($resource->notes_file_type);
                            $fileUrl = Storage::url($resource->notes_file_path);
                        @endphp
                        <div class="notes-preview" style="margin-top: 15px;">
                            @if($fileExtension == 'pdf')
                                <iframe 
                                    src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                                    width="100%" 
                                    height="400" 
                                    style="border: 1px solid #ddd; border-radius: 4px;"
                                    title="Notes File Preview">
                                </iframe>
                            @elseif(in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                                <iframe 
                                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" 
                                    width="100%" 
                                    height="400" 
                                    style="border: 1px solid #ddd; border-radius: 4px;"
                                    title="Notes File Preview">
                                </iframe>
                            @else
                                <div class="unsupported-format" style="padding: 20px; text-align: center; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                                    <i class="fas fa-exclamation-triangle" style="color: #ffc107; font-size: 24px; margin-bottom: 10px;"></i>
                                    <p style="margin: 0; color: #6c757d;">Preview not available for this file type. Click the download link above to access the file.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="profile-value">-</p>
                    @endif
                </div>

                <div class="profile-form-group">
                    <label>Assessment Tests</label>
                    @if($resource->assessment_tests_path)
                        <p class="profile-value">
                            <a href="{{ Storage::url($resource->assessment_tests_path) }}" target="_blank" class="dashboard-link">
                                {{ basename($resource->assessment_tests_path) }}
                            </a>
                            <span class="file-type">({{ strtoupper($resource->assessment_tests_type) }})</span>
                        </p>
                        <div class="assessment-preview" style="margin-top: 15px;">
                            <iframe 
                                src="{{ Storage::url($resource->assessment_tests_path) }}#toolbar=0&navpanes=0&scrollbar=1" 
                                width="100%" 
                                height="400" 
                                style="border: 1px solid #ddd; border-radius: 4px;"
                                title="Assessment Tests Preview">
                            </iframe>
                        </div>
                    @else
                        <p class="profile-value">-</p>
                    @endif
                </div>

                <div class="profile-form-group">
                    <label>Tags</label>
                    <div class="profile-value">
                        @if($resource->tags_array)
                            @foreach($resource->tags_array as $tag)
                                <span class="tag-badge">{{ $tag }}</span>
                            @endforeach
                        @else
                            <p>-</p>
                        @endif
                    </div>
                </div>

                <div class="profile-form-group">
                    <label>Status</label>
                    <p class="profile-value">
                        <span class="status-badge {{ $resource->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $resource->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>

                <div class="profile-form-group">
                    <label>Created By</label>
                    <p class="profile-value">{{ $resource->creator ? $resource->creator->name : '-' }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Created At</label>
                    <p class="profile-value">{{ $resource->created_at->format('M d, Y H:i A') }}</p>
                </div>

                <div class="profile-form-group">
                    <label>Last Updated</label>
                    <p class="profile-value">{{ $resource->updated_at->format('M d, Y H:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Learning Outcomes Section -->
    @if($resource->learning_outcomes)
        <div class="profile-card" style="max-width:900px;margin:20px auto;">
            <h2 class="section-title" style="margin-bottom: 20px; color: #2c3e50; font-size: 20px; font-weight: 600; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
                <i class="fas fa-graduation-cap" style="margin-right: 8px; color: #3498db;"></i>
                Learning Outcomes
            </h2>
            
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
                    <p class="no-outcomes">No learning outcomes specified.</p>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.profile-value {
    margin: 0;
    padding: 8px 0;
    color: #333;
}

.dashboard-link {
    color: #0d6efd;
    text-decoration: none;
}

.dashboard-link:hover {
    text-decoration: underline;
}

.file-type {
    color: #6c757d;
    font-size: 0.9em;
    margin-left: 5px;
}

.tag-badge {
    display: inline-block;
    padding: 4px 8px;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 15px;
    font-size: 0.85em;
    margin: 2px;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 500;
}

.status-active {
    background-color: #e6f4ea;
    color: #1e7e34;
}

.status-inactive {
    background-color: #fbe9e7;
    color: #d32f2f;
}

.video-url-display,
.drive-url-display {
    display: none !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    pointer-events: none !important;
}

.video-preview,
.drive-preview {
    position: relative;
    display: inline-block;
}

.video-preview iframe,
.drive-preview iframe {
    position: relative;
}

/* Learning Outcomes Styles */
.learning-outcomes-content {
    padding: 0;
}

.outcomes-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.outcome-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
    transition: all 0.3s ease;
}

.outcome-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.outcome-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    background: #3498db;
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.outcome-text {
    flex: 1;
    font-size: 16px;
    line-height: 1.5;
    color: #2c3e50;
    margin: 0;
}

.no-outcomes {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}

@media (max-width: 768px) {
    .outcome-item {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .outcome-number {
        align-self: flex-start;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const youtubeIframe = document.getElementById('youtube-iframe');
    const driveIframe = document.getElementById('drive-iframe');
    const videoContainer = document.querySelector('.video-preview');
    const driveContainer = document.querySelector('.drive-preview');
    const videoUrlDisplay = document.querySelector('.video-url-display');
    const driveUrlDisplay = document.querySelector('.drive-url-display');

    // Hide the URL displays
    if (videoUrlDisplay) {
        videoUrlDisplay.style.display = 'none';
    }
    if (driveUrlDisplay) {
        driveUrlDisplay.style.display = 'none';
    }

    // Prevent right-click and other security measures for YouTube iframe
    if (youtubeIframe && videoContainer) {
        youtubeIframe.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        videoContainer.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    }

    // Prevent right-click and other security measures for Drive iframe
    if (driveIframe && driveContainer) {
        driveIframe.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        driveContainer.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    }

    // Prevent keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'c' || e.key === 'C' || e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S') {
                e.preventDefault();
            }
        }
    });

    // Prevent inspect element
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
            e.preventDefault();
        }
    });

    // Prevent view source
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'U') {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection 