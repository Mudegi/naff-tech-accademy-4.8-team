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


    <!-- Assessment Tests Section (For Students) -->
    @if(auth()->check() && auth()->user()->account_type === 'student' && $resource->assessment_tests_path)
        <div class="assessment-tests-section">
            <div class="section-header">
                <i class="fas fa-clipboard-check"></i>
                <h2>Assessment Test</h2>
            </div>
            <div class="assessment-content">
                <div class="file-info">
                    <div class="file-icon">
                        <i class="fas fa-file-pdf" style="color: #ef4444; background: #fef2f2; padding: 0.5rem; border-radius: 0.25rem;"></i>
                    </div>
                    <div class="file-details">
                        <h3 class="file-name">{{ basename($resource->assessment_tests_path) }}</h3>
                        <p class="file-type">PDF Document - Assessment Test</p>
                    </div>
                    <div class="file-actions">
                        <a href="{{ asset('storage/' . $resource->assessment_tests_path) }}" target="_blank" class="download-btn">
                            <i class="fas fa-download"></i> Download Assessment
                        </a>
                    </div>
                </div>
                <div class="file-preview">
                    <div class="pdf-container">
                        <iframe 
                            src="{{ asset('storage/' . $resource->assessment_tests_path) }}#toolbar=0&navpanes=0&scrollbar=1" 
                            width="100%" 
                            height="400px" 
                            frameborder="0"
                            class="preview-frame"
                        ></iframe>
                    </div>
                </div>
                
                <!-- Assignment Upload Section for Students -->
                <div class="assignment-upload-section">
                    @php
                        $existingAssignment = \App\Models\StudentAssignment::where('student_id', auth()->id())
                            ->where('resource_id', $resource->id)
                            ->first();
                    @endphp
                    
                    @if($existingAssignment)
                        <!-- Show existing assignment status -->
                        <div class="submitted-assignment">
                            <h4><i class="fas fa-check-circle" style="color: #10b981;"></i> Assignment Submitted</h4>
                            <p>Your assignment has been successfully submitted and is under review.</p>
                            <div class="assignment-details">
                                <p><strong>Submitted:</strong> {{ $existingAssignment->submitted_at->format('M d, Y H:i A') }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="status-badge status-{{ $existingAssignment->status }}">
                                        {{ ucfirst($existingAssignment->status) }}
                                    </span>
                                </p>
                                
                                <!-- Download Assignment Button -->
                                <div class="assignment-actions">
                                    <a href="{{ route('student.my-assignments.download', $existingAssignment->id) }}" class="download-assignment-btn">
                                        <i class="fas fa-download"></i> Download My Assignment
                                    </a>
                                    <a href="{{ route('student.my-assignments.report', $existingAssignment->id) }}" class="download-report-btn">
                                        <i class="fas fa-file-pdf"></i> Download Report
                                    </a>
                                </div>
                                
                                @if($existingAssignment->teacher_feedback)
                                    <div class="teacher-feedback">
                                        <h5>Teacher Feedback:</h5>
                                        <p>{{ $existingAssignment->teacher_feedback }}</p>
                                    </div>
                                @endif
                                @if($existingAssignment->grade)
                                    <div class="grade-display">
                                        <h5>Grade: <span class="grade-value">{{ $existingAssignment->grade }}%</span></h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Show upload form -->
                        <div class="upload-header">
                            <h3><i class="fas fa-upload"></i> Submit Your Assignment</h3>
                            <p>Complete the assessment test and upload your answers here.</p>
                        </div>
                        
                        <form id="assignmentUploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="file" name="assignment_file" id="assignment_file" accept=".pdf,.png,.jpg,.jpeg" class="file-input">
                                <label for="assignment_file" class="file-label">
                                    <i class="fas fa-upload"></i>
                                    <span>Choose Your Completed Assignment (PDF, PNG, JPG)</span>
                                </label>
                                <p class="file-help">Accepted formats: PDF, PNG, JPG, JPEG. Maximum size: 20MB</p>
                            </div>
                            <button type="submit" class="upload-btn" disabled>
                                <i class="fas fa-upload"></i> Submit Assignment
                            </button>
                        </form>
                        
                        <div id="assignment-upload-progress" class="upload-progress" style="display: none;">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <span class="progress-text">Uploading...</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif(auth()->check() && auth()->user()->account_type === 'student')
        <!-- Show this if student but no assessment path -->
        <div class="assessment-tests-section" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0;">
            <h3>No Assignment Available</h3>
            <p>This video doesn't have an assignment uploaded yet. Check back later or contact your teacher.</p>
        </div>
    @endif

    <!-- Assessment Tests Section (For Teachers) -->
    @if(auth()->check() && auth()->user()->account_type === 'teacher' && auth()->id() == $resource->teacher_id)
        <div class="assessment-tests-section">
            <div class="section-header">
                <i class="fas fa-clipboard-check"></i>
                <h2>Assessment Tests</h2>
            </div>
            <div class="assessment-content">
                @if($resource->assessment_tests_path)
                    <div class="file-info">
                        <div class="file-icon">
                            <i class="fas fa-file-pdf" style="color: #ef4444; background: #fef2f2; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        </div>
                        <div class="file-details">
                            <h3 class="file-name">{{ basename($resource->assessment_tests_path) }}</h3>
                            <p class="file-type">PDF Document - Assessment Test</p>
                        </div>
                        <div class="file-actions">
                            <a href="{{ asset('storage/' . $resource->assessment_tests_path) }}" target="_blank" class="download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    <div class="file-preview">
                        <div class="pdf-container">
                            <iframe 
                                src="{{ asset('storage/' . $resource->assessment_tests_path) }}#toolbar=0&navpanes=0&scrollbar=1" 
                                width="100%" 
                                height="400px" 
                                frameborder="0"
                                class="preview-frame"
                            ></iframe>
                        </div>
                    </div>
                @else
                    <div class="no-assessment">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No assessment test uploaded yet.</p>
                    </div>
                @endif
                
                <!-- Upload Form for Teachers -->
                <div class="upload-assessment-form">
                    <h3>Upload Assessment Test</h3>
                    <form id="assessmentUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="assessment_tests" id="assessment_tests" accept=".pdf" class="file-input">
                            <label for="assessment_tests" class="file-label">
                                <i class="fas fa-upload"></i>
                                <span>Choose PDF File</span>
                            </label>
                            <p class="file-help">Only PDF files are allowed. Maximum size: 10MB</p>
                        </div>
                        <button type="submit" class="upload-btn" disabled>
                            <i class="fas fa-upload"></i> Upload Assessment Test
                        </button>
                    </form>
                    <div id="upload-progress" class="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <span class="progress-text">Uploading...</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notes Files Section -->
    <div class="notes-files-section">
        <div class="section-header">
            <i class="fas fa-file-alt"></i>
            <h2>Study Materials</h2>
        </div>
        @if($resource->notes_file_path)
            <div class="notes-content">
                @php
                    $fileExtension = strtolower(pathinfo($resource->notes_file_path, PATHINFO_EXTENSION));
                    $fileName = basename($resource->notes_file_path);
                    $fileUrl = asset('storage/' . $resource->notes_file_path);
                @endphp
                <div class="file-info">
                    <div class="file-icon">
                        @if($fileExtension == 'pdf')
                            <i class="fas fa-file-pdf" style="color: #ef4444; background: #fef2f2; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        @elseif(in_array($fileExtension, ['doc', 'docx']))
                            <i class="fas fa-file-word" style="color: #2563eb; background: #eff6ff; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        @elseif(in_array($fileExtension, ['ppt', 'pptx']))
                            <i class="fas fa-file-powerpoint" style="color: #dc2626; background: #fef2f2; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        @elseif(in_array($fileExtension, ['xls', 'xlsx']))
                            <i class="fas fa-file-excel" style="color: #059669; background: #f0fdf4; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        @else
                            <i class="fas fa-file" style="color: #6b7280; background: #f9fafb; padding: 0.5rem; border-radius: 0.25rem;"></i>
                        @endif
                    </div>
                    <div class="file-details">
                        <h3 class="file-name">{{ $fileName }}</h3>
                        <p class="file-type">{{ strtoupper($fileExtension) }} Document - Study Material</p>
                    </div>
                    <div class="file-actions">
                        <a href="{{ $fileUrl }}" target="_blank" class="download-btn">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <div class="file-preview">
                    @if($fileExtension == 'pdf')
                        <div class="pdf-container">
                            <iframe 
                                src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&zoom=100" 
                                width="100%" 
                                height="600px" 
                                frameborder="0"
                                class="preview-frame"
                                title="Study Material PDF Preview"
                            ></iframe>
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
        @else
            <div class="no-notes">
                <i class="fas fa-file-alt"></i>
                <p>No study materials available for this video.</p>
            </div>
        @endif
        
        <!-- Upload Form for Teachers -->
        @if(auth()->check() && auth()->user()->account_type === 'teacher' && auth()->id() == $resource->teacher_id)
            <div class="upload-notes-form">
                <h3>Upload Study Materials</h3>
                <form id="notesUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="notes_file" id="notes_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" class="file-input">
                        <label for="notes_file" class="file-label">
                            <i class="fas fa-upload"></i>
                            <span>Choose File</span>
                        </label>
                        <p class="file-help">Supported formats: PDF, Word, PowerPoint, Excel. Maximum size: 10MB</p>
                    </div>
                    <button type="submit" class="upload-btn" disabled>
                        <i class="fas fa-upload"></i> Upload Study Material
                    </button>
                </form>
                <div id="notes-upload-progress" class="upload-progress" style="display: none;">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <span class="progress-text">Uploading...</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Comment Section Start -->
    <div class="comments-section">
        <div class="comments-header">
            <i class="fas fa-comments"></i>
            <h2>Comments</h2>
        </div>
        @php $isLoggedIn = auth()->check(); $currentUserId = auth()->id(); @endphp
        @if($isLoggedIn)
        <form id="commentForm" class="comment-form">
            <textarea id="commentInput" name="comment" placeholder="Add a comment..." required></textarea>
            <button type="submit">Post Comment</button>
        </form>
        @else
        <div class="login-to-comment">Please <a href="{{ route('login') }}">log in</a> to comment.</div>
        @endif
        <div id="commentsList" class="comments-list">
            <div class="loading-comments">Loading comments...</div>
        </div>
    </div>
    <style>
    .comments-section {
        background: #f9fafb;
        border-radius: 0.5rem;
        margin-top: 2rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .comments-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .comments-header i {
        color: #3b82f6;
        font-size: 1.25rem;
    }
    .comments-header h2 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }
    .comment-form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .comment-form textarea {
        resize: vertical;
        min-height: 60px;
        max-height: 200px;
        padding: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        font-size: 1rem;
        color: #374151;
    }
    .comment-form button {
        align-self: flex-end;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1.25rem;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .comment-form button:hover {
        background: #2563eb;
    }
    .login-to-comment {
        margin-bottom: 1.5rem;
        color: #6b7280;
    }
    .login-to-comment a {
        color: #3b82f6;
        text-decoration: underline;
    }
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .comment-item {
        background: white;
        border-radius: 0.375rem;
        padding: 1rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        border: 1px solid #e5e7eb;
    }
    .comment-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .comment-author {
        font-weight: 600;
        color: #1a1a1a;
    }
    .comment-date {
        color: #6b7280;
        font-size: 0.875rem;
    }
    .user-type-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .user-type-teacher {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }
    .user-type-student {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .user-type-parent {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .user-type-admin {
        background-color: #fce7f3;
        color: #be185d;
        border: 1px solid #f9a8d4;
    }
    .user-type-staff {
        background-color: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }
    .comment-body {
        color: #374151;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .reply-btn {
        color: #3b82f6;
        background: none;
        border: none;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: underline;
        margin-top: 0.25rem;
    }
    .reply-form {
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .reply-form textarea {
        min-height: 40px;
        font-size: 0.95rem;
    }
    .reply-form button {
        align-self: flex-end;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1.25rem;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .reply-form button:hover {
        background: #2563eb;
    }
    .replies-list {
        margin-left: 2rem;
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    @media (max-width: 640px) {
        .comments-section {
            padding: 1rem;
        }
        .replies-list {
            margin-left: 1rem;
        }
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const resourceId = @json($resource->id);
        const commentsList = document.getElementById('commentsList');
        const isLoggedIn = @json($isLoggedIn);
        const currentUserId = @json($currentUserId);
        const commentForm = document.getElementById('commentForm');
        const commentInput = document.getElementById('commentInput');

        function renderReplies(replies) {
            if (!replies || !replies.length) return '';
            let html = '<div class="replies-list">';
            replies.forEach(reply => {
                html += '<div class="comment-item reply">' +
                    '<div class="comment-meta">' +
                        '<span class="comment-author">' + (reply.user ? reply.user.name : 'User') + 
                        (reply.user && reply.user.account_type ? ' <span class="user-type-badge user-type-' + reply.user.account_type + '">' + reply.user.account_type.charAt(0).toUpperCase() + reply.user.account_type.slice(1) + '</span>' : '') + '</span>' +
                        '<span class="comment-date">' + new Date(reply.created_at).toLocaleString() + '</span>';
                    if (isLoggedIn && reply.user_id === currentUserId) {
                        html += '<button class="edit-btn" data-id="' + reply.id + '">Edit</button>';
                        html += '<button class="delete-btn" data-id="' + reply.id + '">Delete</button>';
                    }
                    html += '</div>' +
                        '<div class="comment-body" data-id="' + reply.id + '">' + reply.comment.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>' +
                        '<div class="comment-actions">' +
                            '<button class="like-btn" data-id="' + reply.id + '">👍 <span class="like-count">' + (reply.likes_count || 0) + '</span></button>' +
                            '<button class="dislike-btn" data-id="' + reply.id + '">👎 <span class="dislike-count">' + (reply.dislikes_count || 0) + '</span></button>' +
                        '</div>';
                    if (isLoggedIn) {
                        html += '<button class="reply-btn" data-id="' + reply.id + '">Reply</button>' +
                            '<form class="reply-form" style="display:none;">' +
                            '<textarea name="reply" placeholder="Write a reply..."></textarea>' +
                            '<button type="submit">Post Reply</button>' +
                            '</form>';
                    }
                    // Recursively render sub-replies
                    html += renderReplies(reply.replies);
                    html += '</div>';
                });
                html += '</div>';
                return html;
            }
        function renderComments(comments) {
            if (!comments.length) {
                commentsList.innerHTML = '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                return;
            }
            commentsList.innerHTML = '';
            comments.forEach(comment => {
                const item = document.createElement('div');
                item.className = 'comment-item main-comment';
                let html = '<div class="comment-meta">' +
                    '<span class="comment-author">' + (comment.user ? comment.user.name : 'User') + 
                    (comment.user && comment.user.account_type ? ' <span class="user-type-badge user-type-' + comment.user.account_type + '">' + comment.user.account_type.charAt(0).toUpperCase() + comment.user.account_type.slice(1) + '</span>' : '') + '</span>' +
                    '<span class="comment-date">' + new Date(comment.created_at).toLocaleString() + '</span>';
                if (isLoggedIn && comment.user_id === currentUserId) {
                    html += '<button class="edit-btn" data-id="' + comment.id + '">Edit</button>';
                    html += '<button class="delete-btn" data-id="' + comment.id + '">Delete</button>';
                }
                html += '</div>' +
                    '<div class="comment-body" data-id="' + comment.id + '">' + comment.comment.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>' +
                    '<div class="comment-actions">' +
                        '<button class="like-btn" data-id="' + comment.id + '">👍 <span class="like-count">' + (comment.likes_count || 0) + '</span></button>' +
                        '<button class="dislike-btn" data-id="' + comment.id + '">👎 <span class="dislike-count">' + (comment.dislikes_count || 0) + '</span></button>' +
                    '</div>';
                if (isLoggedIn) {
                    html += '<button class="reply-btn" data-id="' + comment.id + '">Reply</button>' +
                        '<form class="reply-form" style="display:none;">' +
                        '<textarea name="reply" placeholder="Write a reply..."></textarea>' +
                        '<button type="submit">Post Reply</button>' +
                        '</form>';
                }
                // Render replies
                html += renderReplies(comment.replies);
                item.innerHTML = html;
                commentsList.appendChild(item);
            });
            attachReplyHandlers(commentsList);
        }

        function loadComments() {
            fetch(`/resource/${resourceId}/comments`)
                .then(res => res.json())
                .then(renderComments);
        }

        function attachReplyHandlers(container) {
            const replyBtns = container.querySelectorAll('.reply-btn');
            replyBtns.forEach(replyBtn => {
                const parent = replyBtn.closest('.comment-item');
                const replyForm = parent.querySelector('.reply-form');
                replyBtn.addEventListener('click', function() {
                    replyForm.style.display = replyForm.style.display === 'none' ? 'flex' : 'none';
                });
                replyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const replyText = replyForm.querySelector('textarea').value.trim();
                    if (!replyText) return;
                    
                    const submitBtn = replyForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
                    
                    fetch(`/resource/${resourceId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ comment: replyText, parent_id: replyBtn.dataset.id })
                    })
                    .then(res => res.json())
                    .then(data => {
                        replyForm.querySelector('textarea').value = '';
                        replyForm.style.display = 'none';
                        loadComments();
                    })
                    .catch(error => {
                        console.error('Error posting reply:', error);
                        alert('Error posting reply. Please try again.');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    });
                });
            });
            // Like/dislike handlers for all comments and replies
            const likeBtns = container.querySelectorAll('.like-btn');
            likeBtns.forEach(likeBtn => {
                likeBtn.addEventListener('click', function() {
                    fetch(`/resource/comments/${likeBtn.dataset.id}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ type: 'like' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        loadComments();
                    });
                });
            });
            const dislikeBtns = container.querySelectorAll('.dislike-btn');
            dislikeBtns.forEach(dislikeBtn => {
                dislikeBtn.addEventListener('click', function() {
                    fetch(`/resource/comments/${dislikeBtn.dataset.id}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ type: 'dislike' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        loadComments();
                    });
                });
            });
            
            // Delete button handlers for all comments and replies
            const deleteBtns = container.querySelectorAll('.delete-btn');
            deleteBtns.forEach(deleteBtn => {
                deleteBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
                        fetch(`/resource/${resourceId}/comments/${deleteBtn.dataset.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadComments();
                            } else {
                                alert('Error deleting comment: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting comment:', error);
                            alert('Error deleting comment. Please try again.');
                        });
                    }
                });
            });
        }

        loadComments();

        if (isLoggedIn && commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const comment = commentInput.value.trim();
                if (!comment) return;
                
                const submitBtn = commentForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
                
                fetch(`/resource/${resourceId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ comment })
                })
                .then(res => res.json())
                .then(data => {
                    commentInput.value = '';
                    loadComments();
                })
                .catch(error => {
                    console.error('Error posting comment:', error);
                    alert('Error posting comment. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }
    });
    </script>
    <!-- Comment Section End -->

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
@media (max-width: 640px) {
    .video-page {
        padding: 0.75rem;
    }
    .video-title {
        font-size: 1.25rem;
    }
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
}

/* Notes Files Styles */
.notes-files-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.notes-content {
    padding: 0;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e2e8f0;
}

.file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    background: #3b82f6;
    color: white;
    border-radius: 0.5rem;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.file-icon .fa-file-pdf {
    color: #ef4444;
    background: #fef2f2;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.file-icon .fa-file-word {
    color: #2563eb;
    background: #eff6ff;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.file-icon .fa-file-powerpoint {
    color: #dc2626;
    background: #fef2f2;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.file-icon .fa-file-excel {
    color: #059669;
    background: #f0fdf4;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.file-details {
    flex: 1;
}

.file-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
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

/* PDF overlay removed for better readability */

.no-notes {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    text-align: center;
    color: #6b7280;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 2px dashed #d1d5db;
}

.no-notes i {
    font-size: 2rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.upload-notes-form {
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    margin-top: 1rem;
}

.upload-notes-form h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 1rem 0;
}

.assignment-upload-section {
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    margin-top: 1.5rem;
}

.upload-header {
    margin-bottom: 1.5rem;
}

.upload-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.upload-header p {
    color: #6b7280;
    margin: 0;
    font-size: 0.875rem;
}

.submitted-assignment {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
}

.submitted-assignment h4 {
    color: #059669;
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.submitted-assignment p {
    color: #047857;
    margin: 0;
    font-size: 0.875rem;
}

.assignment-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #bbf7d0;
}

.assignment-details p {
    margin: 0.5rem 0;
    color: #374151;
    font-size: 0.875rem;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-submitted {
    background-color: #dbeafe;
    color: #1e40af;
}

.status-reviewed {
    background-color: #fef3c7;
    color: #92400e;
}

.status-graded {
    background-color: #d1fae5;
    color: #065f46;
}

.teacher-feedback {
    margin-top: 1rem;
    padding: 0.75rem;
    background-color: #f9fafb;
    border-radius: 0.375rem;
    border-left: 3px solid #3b82f6;
}

.teacher-feedback h5 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 0.875rem;
    font-weight: 600;
}

.teacher-feedback p {
    margin: 0;
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.5;
}

.grade-display {
    margin-top: 1rem;
    padding: 0.75rem;
    background-color: #f0fdf4;
    border-radius: 0.375rem;
    border-left: 3px solid #10b981;
}

.grade-display h5 {
    margin: 0;
    color: #1f2937;
    font-size: 0.875rem;
    font-weight: 600;
}

.grade-value {
    color: #059669;
    font-weight: 700;
    font-size: 1rem;
}

.assignment-actions {
    margin: 1rem 0;
    padding-top: 1rem;
    border-top: 1px solid #bbf7d0;
}

.download-assignment-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #3b82f6;
    color: white;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
}

.download-assignment-btn:hover {
    background: #2563eb;
    color: white;
    text-decoration: none;
}

.download-assignment-btn i {
    font-size: 1rem;
}

.download-report-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #10b981;
    color: white;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
    margin-left: 0.5rem;
}

.download-report-btn:hover {
    background: #059669;
    color: white;
    text-decoration: none;
}

.download-report-btn i {
    font-size: 1rem;
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

/* Assessment Tests Styles */
.assessment-tests-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.assessment-content {
    padding: 0;
}

.no-assessment {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    text-align: center;
    color: #6b7280;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 2px dashed #d1d5db;
    margin-bottom: 1.5rem;
}

.no-assessment i {
    font-size: 2rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.upload-assessment-form {
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.upload-assessment-form h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 1rem 0;
}

.form-group {
    margin-bottom: 1rem;
}

.file-input {
    display: none;
}

.file-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #3b82f6;
    color: white;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s;
    border: none;
}

.file-label:hover {
    background: #2563eb;
}

.file-help {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0.5rem 0 0 0;
}

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 0.375rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.upload-btn:hover:not(:disabled) {
    background: #059669;
}

.upload-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.download-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #3b82f6;
    color: white;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
}

.download-btn:hover {
    background: #2563eb;
    color: white;
    text-decoration: none;
}

.upload-progress {
    margin-top: 1rem;
    padding: 1rem;
    background: #f0f9ff;
    border-radius: 0.375rem;
    border: 1px solid #bae6fd;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e0f2fe;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: #0ea5e9;
    width: 0%;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.875rem;
    color: #0369a1;
    font-weight: 500;
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

    /* Mobile Styles for Assessment Tests */
    .assessment-tests-section {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .upload-assessment-form {
        padding: 1rem;
    }

    .file-label {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .upload-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}

.edit-btn, .like-btn, .dislike-btn {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.375rem;
    padding: 0.3rem 1rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    margin-right: 0.5rem;
    transition: background 0.2s;
}
.edit-btn:hover, .like-btn:hover, .dislike-btn:hover {
    background: #2563eb;
}
.delete-btn {
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 0.375rem;
    padding: 0.3rem 1rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    margin-right: 0.5rem;
    transition: background 0.2s;
}
.delete-btn:hover {
    background: #b91c1c;
}
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.fa-spinner {
    margin-right: 0.5rem;
}
.comment-actions {
    margin-top: 0.5rem;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}
.edit-form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.edit-textarea {
    resize: vertical;
    min-height: 60px;
    max-height: 200px;
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    font-size: 1rem;
    color: #374151;
}
.edit-form button[type="submit"] {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1.25rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    margin-right: 0.5rem;
    transition: background 0.2s;
}
.edit-form button[type="submit"]:hover {
    background: #2563eb;
}
.edit-form .cancel-edit {
    background: #e5e7eb;
    color: #374151;
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1.25rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: background 0.2s;
}
.edit-form .cancel-edit:hover {
    background: #d1d5db;
}
.reply {
    margin-left: 2rem;
    background: #f3f4f6;
    border-left: 3px solid #3b82f6;
}
.main-comment {
    margin-bottom: 1.5rem;
}
.replies-list {
    margin-top: 0.5rem;
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
                    
                    // Check if video is completed (watched 90% or more)
                    isCompleted = video.currentTime >= (video.duration * 0.9);

                    // Send tracking data to server
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
                    });
                }
            } catch (error) {
                console.error('Error tracking video:', error);
            }
        }
    }

    // Start tracking when video is loaded
    if (videoFrame) {
        videoFrame.addEventListener('load', function() {
            // Start tracking every 30 seconds
            trackingInterval = setInterval(trackVideoProgress, 30000);
        });
    }

    // Clean up when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (trackingInterval) {
            clearInterval(trackingInterval);
        }
        // Send final tracking data
        trackVideoProgress();
    });

    // Assessment Tests Upload Functionality
    const assessmentForm = document.getElementById('assessmentUploadForm');
    const fileInput = document.getElementById('assessment_tests');
    const uploadBtn = document.querySelector('.upload-btn');
    const progressDiv = document.getElementById('upload-progress');
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');

    if (fileInput && uploadBtn) {
        // Enable/disable upload button based on file selection
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.type === 'application/pdf') {
                    uploadBtn.disabled = false;
                    uploadBtn.style.background = '#10b981';
                } else {
                    alert('Please select a PDF file for assessment tests.');
                    this.value = '';
                    uploadBtn.disabled = true;
                }
            } else {
                uploadBtn.disabled = true;
            }
        });

        // Handle form submission
        if (assessmentForm) {
            assessmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                const file = fileInput.files[0];
                
                if (!file) {
                    alert('Please select a file to upload.');
                    return;
                }
                
                formData.append('assessment_tests', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show progress
                progressDiv.style.display = 'block';
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                
                // Create XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressFill.style.width = percentComplete + '%';
                        progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                    }
                });
                
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Reload the page to show the uploaded file
                            location.reload();
                        } else {
                            alert('Upload failed: ' + (response.message || 'Unknown error'));
                            resetUploadForm();
                        }
                    } else {
                        alert('Upload failed. Please try again.');
                        resetUploadForm();
                    }
                });
                
                xhr.addEventListener('error', function() {
                    alert('Upload failed. Please check your connection and try again.');
                    resetUploadForm();
                });
                
                xhr.open('POST', `/student/videos/{{ $resource->id }}/upload-assessment`);
                xhr.send(formData);
            });
        }
    }
    
    function resetUploadForm() {
        progressDiv.style.display = 'none';
        progressFill.style.width = '0%';
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Assessment Test';
        fileInput.value = '';
    }

    // Notes Upload Functionality
    const notesForm = document.getElementById('notesUploadForm');
    const notesFileInput = document.getElementById('notes_file');
    const notesUploadBtn = notesForm ? notesForm.querySelector('.upload-btn') : null;
    const notesProgressDiv = document.getElementById('notes-upload-progress');
    const notesProgressFill = notesProgressDiv ? notesProgressDiv.querySelector('.progress-fill') : null;
    const notesProgressText = notesProgressDiv ? notesProgressDiv.querySelector('.progress-text') : null;

    if (notesFileInput && notesUploadBtn) {
        // Enable/disable upload button based on file selection
        notesFileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                if (allowedTypes.includes(file.type)) {
                    notesUploadBtn.disabled = false;
                    notesUploadBtn.style.background = '#10b981';
                } else {
                    alert('Please select a supported file type (PDF, Word, PowerPoint, or Excel).');
                    this.value = '';
                    notesUploadBtn.disabled = true;
                }
            } else {
                notesUploadBtn.disabled = true;
            }
        });

        // Handle form submission
        if (notesForm) {
            notesForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                const file = notesFileInput.files[0];
                
                if (!file) {
                    alert('Please select a file to upload.');
                    return;
                }
                
                formData.append('notes_file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show progress
                notesProgressDiv.style.display = 'block';
                notesUploadBtn.disabled = true;
                notesUploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                
                // Create XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        notesProgressFill.style.width = percentComplete + '%';
                        notesProgressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                    }
                });
                
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Reload the page to show the uploaded file
                            location.reload();
                        } else {
                            alert('Upload failed: ' + (response.message || 'Unknown error'));
                            resetNotesUploadForm();
                        }
                    } else {
                        alert('Upload failed. Please try again.');
                        resetNotesUploadForm();
                    }
                });
                
                xhr.addEventListener('error', function() {
                    alert('Upload failed. Please check your connection and try again.');
                    resetNotesUploadForm();
                });
                
                xhr.open('POST', `/student/videos/{{ $resource->id }}/upload-notes`);
                xhr.send(formData);
            });
        }
    }
    
    function resetNotesUploadForm() {
        notesProgressDiv.style.display = 'none';
        notesProgressFill.style.width = '0%';
        notesUploadBtn.disabled = true;
        notesUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Study Material';
        notesFileInput.value = '';
    }

    // Assignment Upload Functionality for Students
    const assignmentForm = document.getElementById('assignmentUploadForm');
    const assignmentFileInput = document.getElementById('assignment_file');
    const assignmentUploadBtn = assignmentForm ? assignmentForm.querySelector('.upload-btn') : null;
    const assignmentProgressDiv = document.getElementById('assignment-upload-progress');
    const assignmentProgressFill = assignmentProgressDiv ? assignmentProgressDiv.querySelector('.progress-fill') : null;
    const assignmentProgressText = assignmentProgressDiv ? assignmentProgressDiv.querySelector('.progress-text') : null;
    const existingAssignmentDiv = document.getElementById('existing-assignment');

    if (assignmentFileInput && assignmentUploadBtn) {
        // Enable/disable upload button based on file selection
        assignmentFileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.type === 'application/pdf') {
                    assignmentUploadBtn.disabled = false;
                    assignmentUploadBtn.style.background = '#10b981';
                } else {
                    alert('Please select a PDF file for your assignment.');
                    this.value = '';
                    assignmentUploadBtn.disabled = true;
                }
            } else {
                assignmentUploadBtn.disabled = true;
            }
        });

        // Handle form submission
        if (assignmentForm) {
            assignmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                const file = assignmentFileInput.files[0];
                
                if (!file) {
                    alert('Please select a file to upload.');
                    return;
                }
                
                formData.append('assignment_file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show progress
                assignmentProgressDiv.style.display = 'block';
                assignmentUploadBtn.disabled = true;
                assignmentUploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                
                // Create XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        assignmentProgressFill.style.width = percentComplete + '%';
                        assignmentProgressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                    }
                });
                
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show success message and hide upload form
                            assignmentForm.style.display = 'none';
                            existingAssignmentDiv.style.display = 'block';
                            assignmentProgressDiv.style.display = 'none';
                            
                            // Show success message
                            alert('Assignment submitted successfully! Your work is now under review.');
                        } else {
                            alert('Upload failed: ' + (response.message || 'Unknown error'));
                            resetAssignmentUploadForm();
                        }
                    } else {
                        alert('Upload failed. Please try again.');
                        resetAssignmentUploadForm();
                    }
                });
                
                xhr.addEventListener('error', function() {
                    alert('Upload failed. Please check your connection and try again.');
                    resetAssignmentUploadForm();
                });
                
                xhr.open('POST', `/student/videos/{{ $resource->id }}/upload-assignment`);
                xhr.send(formData);
            });
        }
    }
    
    function resetAssignmentUploadForm() {
        assignmentProgressDiv.style.display = 'none';
        assignmentProgressFill.style.width = '0%';
        assignmentUploadBtn.disabled = true;
        assignmentUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Submit Assignment';
        assignmentFileInput.value = '';
    }
});
</script>
@endpush
@endsection 