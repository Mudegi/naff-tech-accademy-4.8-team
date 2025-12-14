@extends('layouts.dashboard')

@section('content')
<style>
    .resource-form {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .form-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    .back-button {
        background-color: #666;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }
    .back-button:hover {
        background-color: #555;
    }
    .error-message {
        background-color: #fee;
        border: 1px solid #fcc;
        color: #c00;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .form-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 20px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
    }
    .form-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .form-input:focus {
        border-color: #4a90e2;
        outline: none;
    }
    .form-textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-height: 100px;
    }
    .form-select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .video-preview, .drive-preview {
        margin-top: 20px;
        width: 100%;
        max-width: 560px;
        position: relative;
    }
    .video-preview .video-overlay, .drive-preview .video-overlay {
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 60px;
        background: white;
        z-index: 1000;
        pointer-events: none;
        border-top-right-radius: 4px;
    }
    .video-preview iframe, .drive-preview iframe {
        width: 100%;
        height: 315px;
        border: none;
        border-radius: 4px;
    }
    .submit-button {
        background-color: #4a90e2;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    .submit-button:hover {
        background-color: #357abd;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Learning Outcomes Styles */
    .learning-outcome-row {
        margin-bottom: 10px;
    }
    
    .outcome-input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .outcome-input {
        flex: 1;
    }
    
    .add-outcome-btn, .remove-outcome-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .add-outcome-btn {
        background-color: #28a745;
        color: white;
        margin-top: 10px;
    }
    
    .add-outcome-btn:hover {
        background-color: #218838;
    }
    
    .remove-outcome-btn {
        background-color: #dc3545;
        color: white;
        padding: 8px 10px;
    }
    
    .remove-outcome-btn:hover {
        background-color: #c82333;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
        .video-preview, .drive-preview {
            max-width: 100vw;
        }
        .video-preview iframe, .drive-preview iframe {
            height: 220px;
        }
        .video-preview .video-overlay, .drive-preview .video-overlay {
            width: 60px;
            height: 36px;
        }
    }
    .video-preview::after, .video-preview::before, .drive-preview::after { display: none !important; content: none !important; }
</style>

<div class="resource-form">
    <div class="form-header">
        <h1 class="form-title">Add New Resource</h1>
        <a href="{{ route('admin.resources.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Resources
        </a>
    </div>

    @if(session('success'))
        <div class="error-message" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <!-- Basic Information -->
                <div>
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <select name="grade_level" id="grade_level" class="form-select" required>
                            <option value="">Select Grade Level</option>
                            <option value="O Level" {{ old('grade_level') == 'O Level' ? 'selected' : '' }}>O Level</option>
                            <option value="A Level" {{ old('grade_level') == 'A Level' ? 'selected' : '' }}>A Level</option>
                        </select>
                    </div>
                </div>

                <!-- Relationships -->
                <div>
                    <div class="form-group">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select">
                            <option value="">Select Class (Optional)</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="topic_id" class="form-label">Topic</label>
                        <select name="topic_id" id="topic_id" class="form-select" required>
                            <option value="">Select Topic</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="term_id" class="form-label">Term</label>
                        <select name="term_id" id="term_id" class="form-select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacher_id" class="form-label">Teacher (Optional)</label>
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">Select Teacher (Optional)</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $user = Auth::user();
                        $currentSchoolContext = null;
                        if ($user->account_type === 'admin' && !$user->school_id) {
                            $contextSchoolId = session('admin_school_context');
                            if ($contextSchoolId) {
                                $currentSchoolContext = \App\Models\School::find($contextSchoolId);
                            }
                        }
                    @endphp
                    
                    @if($user->account_type === 'admin' && !$user->school_id && !$currentSchoolContext)
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> School Assignment Options:</strong><br>
                        • Select <strong>Primary School</strong> to assign this video to one school<br>
                        • OR use <strong>Additional Schools</strong> below to assign to multiple schools<br>
                        • Leave both empty only if this should be a global video for ALL schools
                    </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="school_id" class="form-label">Primary School</label>
                        <select name="school_id" id="school_id" class="form-select">
                            <option value="">None - Select for single school assignment</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Choose ONE school for direct assignment.</small>
                    </div>
                    
                    @if($user->account_type === 'admin' && !$user->school_id && !$currentSchoolContext)
                    <div class="form-group">
                        <label for="school_ids" class="form-label">Additional Schools (Multi-Select)</label>
                        <select name="school_ids[]" id="school_ids" class="form-select" multiple size="5" style="height: auto; min-height: 120px;">
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ in_array($school->id, old('school_ids', [])) ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select MULTIPLE schools. Use this to share the video across several schools.</small>
                    </div>
                    @elseif($currentSchoolContext)
                    <div class="alert alert-primary">
                        <strong><i class="fas fa-school"></i> School Context Active:</strong> {{ $currentSchoolContext->name }}<br>
                        <small>This video will be automatically assigned to this school.</small>
                    </div>
                    <input type="hidden" name="school_id" value="{{ $currentSchoolContext->id }}">
                    @endif
                </div>
            </div>

            <!-- Resource Content -->
            <div class="form-group">
                <label for="video_url" class="form-label">Video URL</label>
                <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}" class="form-input" placeholder="https://youtube.com/...">
                <div class="video-preview">
                    @php
                        $videoId = null;
                        $videoUrl = old('video_url');
                        if ($videoUrl) {
                            if (preg_match('/youtu\.be\/([\w-]{11})/', $videoUrl, $m)) $videoId = $m[1];
                            elseif (preg_match('/[?&]v=([\w-]{11})/', $videoUrl, $m)) $videoId = $m[1];
                            elseif (preg_match('/embed\/([\w-]{11})/', $videoUrl, $m)) $videoId = $m[1];
                        }
                    @endphp
                    @if($videoId)
                        <div style="position:relative;">
                            <div class="video-overlay"></div>
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&controls=1&showinfo=0" allowfullscreen></iframe>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="google_drive_link" class="form-label">Google Drive Link</label>
                <input type="url" name="google_drive_link" id="google_drive_link" value="{{ old('google_drive_link') }}" class="form-input" placeholder="https://drive.google.com/file/d/...">
                <small class="form-text text-muted">Paste the full Google Drive file link here</small>
                <div class="drive-preview">
                    @php
                        $driveFileId = null;
                        $driveUrl = old('google_drive_link');
                        if ($driveUrl) {
                            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $driveUrl, $m)) $driveFileId = $m[1];
                            elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $driveUrl, $m)) $driveFileId = $m[1];
                            elseif (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $driveUrl, $m)) $driveFileId = $m[1];
                        }
                    @endphp
                    @if($driveFileId)
                        <div style="position:relative;">
                            <div class="video-overlay"></div>
                            <iframe src="https://drive.google.com/file/d/{{ $driveFileId }}/preview" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="notes_file" class="form-label">Notes File (PDF/PPT/Excel)</label>
                <input type="file" name="notes_file" id="notes_file" class="form-input">
            </div>

            <div class="form-group">
                <label for="assessment_tests" class="form-label">Assessment Tests (PDF only)</label>
                <input type="file" name="assessment_tests" id="assessment_tests" class="form-input" accept=".pdf">
                <small class="form-text text-muted">Only PDF files are allowed for assessment tests</small>
                <div id="assessment-preview" style="margin-top: 15px; display: none;">
                    <iframe 
                        id="assessment-iframe"
                        width="100%" 
                        height="400" 
                        style="border: 1px solid #ddd; border-radius: 4px;"
                        title="Assessment Tests Preview">
                    </iframe>
                </div>
            </div>

            <div class="form-group">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags') }}" class="form-input" placeholder="tag1, tag2, tag3">
            </div>

            <div class="form-group">
                <label for="learning_outcomes" class="form-label">Learning Outcomes</label>
                <textarea name="learning_outcomes" id="learning_outcomes" class="form-textarea" rows="4" placeholder="Enter learning outcomes separated by commas. Example: Understand basic algebra, Solve quadratic equations, Apply mathematical concepts">{{ old('learning_outcomes') }}</textarea>
                <small class="form-text text-muted">Enter each learning outcome separated by a comma. You can also use line breaks for better organization.</small>
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" name="visible_as_sample" id="visible_as_sample" value="1" {{ old('visible_as_sample') == '1' ? 'checked' : '' }}>
                <label for="visible_as_sample">Visible as Sample</label>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-button">
                    <i class="fas fa-save"></i> Save Resource
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTeacherDropdown(subjectId, classId, selectedId = null) {
        $('#teacher_id').html('<option value="">Loading...</option>');
        $.ajax({
            url: '{{ route('admin.api.teachers') }}',
            data: { subject_id: subjectId, class_id: classId },
            success: function(teachers) {
                let options = '<option value="">Select Teacher (Optional)</option>';
                teachers.forEach(function(teacher) {
                    options += `<option value="${teacher.id}" ${selectedId == teacher.id ? 'selected' : ''}>${teacher.name}</option>`;
                });
                $('#teacher_id').html(options).trigger('change');
            },
            error: function() {
                $('#teacher_id').html('<option value="">Select Teacher (Optional)</option>');
            }
        });
    }
    $('#teacher_id').select2({
        placeholder: 'Select Teacher (Optional)',
        allowClear: true,
        width: '100%'
    });
    $('#subject_id, #class_id').on('change', function() {
        const subjectId = $('#subject_id').val();
        const classId = $('#class_id').val();
        updateTeacherDropdown(subjectId, classId);
    });
    // On page load, if subject/class is selected, filter teachers
    const initialSubject = $('#subject_id').val();
    const initialClass = $('#class_id').val();
    const initialTeacher = $('#teacher_id').val();
    if (initialSubject || initialClass) {
        updateTeacherDropdown(initialSubject, initialClass, initialTeacher);
    }

    // Assessment tests preview functionality
    $('#assessment_tests').on('change', function(e) {
        const file = e.target.files[0];
        const preview = $('#assessment-preview');
        const iframe = $('#assessment-iframe');
        
        if (file && file.type === 'application/pdf') {
            const url = URL.createObjectURL(file);
            iframe.attr('src', url + '#toolbar=0&navpanes=0&scrollbar=1');
            preview.show();
        } else if (file) {
            alert('Please select a PDF file for assessment tests.');
            $(this).val('');
            preview.hide();
        } else {
            preview.hide();
        }
    });
});
</script>
@endpush
@endsection 