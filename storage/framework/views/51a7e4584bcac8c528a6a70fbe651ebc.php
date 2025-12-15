<?php $__env->startSection('content'); ?>
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Edit Resource</h1>
        <a href="<?php echo e(route('admin.resources.index')); ?>" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Resources
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="dashboard-alert dashboard-alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="dashboard-alert dashboard-alert-error">
            <ul class="error-list">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="dashboard-card">
        <form action="<?php echo e(route('admin.resources.update', $resource->hash_id)); ?>" method="POST" enctype="multipart/form-data" class="resource-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h2 class="form-section-title">Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" value="<?php echo e(old('title', $resource->title)); ?>" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-textarea"><?php echo e(old('description', $resource->description)); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <select name="grade_level" id="grade_level" class="form-select" required>
                            <option value="">Select Grade Level</option>
                            <option value="O Level" <?php echo e(old('grade_level', $resource->grade_level) == 'O Level' ? 'selected' : ''); ?>>O Level</option>
                            <option value="A Level" <?php echo e(old('grade_level', $resource->grade_level) == 'A Level' ? 'selected' : ''); ?>>A Level</option>
                        </select>
                    </div>
                </div>

                <!-- Relationships -->
                <div class="form-section">
                    <h2 class="form-section-title">Relationships</h2>
                    
                    <div class="form-group">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($subject->id); ?>" <?php echo e(old('subject_id', $resource->subject_id) == $subject->id ? 'selected' : ''); ?>><?php echo e($subject->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select">
                            <option value="">Select Class (Optional)</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e(old('class_id', $resource->class_id) == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="topic_id" class="form-label">Topic</label>
                        <select name="topic_id" id="topic_id" class="form-select" required>
                            <option value="">Select Topic</option>
                            <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($topic->id); ?>" <?php echo e(old('topic_id', $resource->topic_id) == $topic->id ? 'selected' : ''); ?>><?php echo e($topic->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="term_id" class="form-label">Term</label>
                        <select name="term_id" id="term_id" class="form-select" required>
                            <option value="">Select Term</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e(old('term_id', $resource->term_id) == $term->id ? 'selected' : ''); ?>><?php echo e($term->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacher_id" class="form-label">Teacher (Optional)</label>
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">Select Teacher (Optional)</option>
                            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($teacher->id); ?>" <?php echo e(old('teacher_id', $resource->teacher_id) == $teacher->id ? 'selected' : ''); ?>><?php echo e($teacher->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <?php
                        $user = Auth::user();
                        $currentSchoolContext = null;
                        if ($user->account_type === 'admin' && !$user->school_id) {
                            $contextSchoolId = session('admin_school_context');
                            if ($contextSchoolId) {
                                $currentSchoolContext = \App\Models\School::find($contextSchoolId);
                            }
                        }
                    ?>
                    
                    <?php if($user->account_type === 'admin' && !$user->school_id && !$currentSchoolContext): ?>
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> School Assignment:</strong><br>
                        Currently assigned to: 
                        <?php if($resource->school_id): ?>
                            <strong><?php echo e($resource->school->name ?? 'Unknown'); ?></strong> (Primary)
                        <?php endif; ?>
                        <?php if($resource->schools->count() > 0): ?>
                            <?php if($resource->school_id): ?>, <?php endif; ?>
                            <strong><?php echo e($resource->schools->pluck('name')->join(', ')); ?></strong> (Additional)
                        <?php endif; ?>
                        <?php if(!$resource->school_id && $resource->schools->count() == 0): ?>
                            <strong class="text-warning">Global (All Schools)</strong>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="school_id" class="form-label">Primary School</label>
                        <select name="school_id" id="school_id" class="form-select">
                            <option value="">None - Select for single school assignment</option>
                            <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($school->id); ?>" <?php echo e(old('school_id', $resource->school_id) == $school->id ? 'selected' : ''); ?>>
                                    <?php echo e($school->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="form-text text-muted">Choose ONE school for direct assignment.</small>
                    </div>
                    
                    <?php if($user->account_type === 'admin' && !$user->school_id && !$currentSchoolContext): ?>
                    <div class="form-group">
                        <label for="school_ids" class="form-label">Additional Schools (Multi-Select)</label>
                        <select name="school_ids[]" id="school_ids" class="form-select" multiple size="5" style="height: auto; min-height: 120px;">
                            <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($school->id); ?>" <?php echo e(in_array($school->id, old('school_ids', $resource->schools->pluck('id')->toArray())) ? 'selected' : ''); ?>>
                                    <?php echo e($school->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select MULTIPLE schools. Use this to share across several schools.</small>
                    </div>
                    <?php elseif($currentSchoolContext): ?>
                    <div class="alert alert-primary">
                        <strong><i class="fas fa-school"></i> School Context Active:</strong> <?php echo e($currentSchoolContext->name); ?><br>
                        <small>Changes will apply to this school context.</small>
                    </div>
                    <input type="hidden" name="school_id" value="<?php echo e($currentSchoolContext->id); ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resource Content -->
            <div class="form-section">
                <h2 class="form-section-title">Resource Content</h2>
                
                <div class="form-group">
                    <label for="video_url" class="form-label">Video URL</label>
                    <input type="url" name="video_url" id="video_url" value="<?php echo e(old('video_url', $resource->video_url)); ?>" class="form-input" placeholder="https://youtube.com/...">
                    <div class="video-preview">
                        <?php
                            $videoId = null;
                            if ($resource->video_url) {
                                if (preg_match('/youtu\.be\/([\w-]{11})/', $resource->video_url, $m)) $videoId = $m[1];
                                elseif (preg_match('/[?&]v=([\w-]{11})/', $resource->video_url, $m)) $videoId = $m[1];
                                elseif (preg_match('/embed\/([\w-]{11})/', $resource->video_url, $m)) $videoId = $m[1];
                            }
                        ?>
                        <?php if($videoId): ?>
                            <div style="position:relative;">
                                <div class="video-overlay"></div>
                                <iframe src="https://www.youtube.com/embed/<?php echo e($videoId); ?>?rel=0&modestbranding=1&controls=1&showinfo=0" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($resource->video_url): ?>
                        <a href="<?php echo e(route('admin.resources.video-view', $resource->hash_id)); ?>" target="_blank" class="dashboard-btn dashboard-btn-primary" style="margin-top:10px;display:inline-block;">View Larger</a>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="google_drive_link" class="form-label">Google Drive Link</label>
                    <input type="url" name="google_drive_link" id="google_drive_link" value="<?php echo e(old('google_drive_link', $resource->google_drive_link)); ?>" class="form-input" placeholder="https://drive.google.com/file/d/...">
                    <small class="form-text text-muted">Paste the full Google Drive file link here</small>
                    <div class="drive-preview">
                        <?php
                            $driveFileId = null;
                            if ($resource->google_drive_link) {
                                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $m)) $driveFileId = $m[1];
                                elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $m)) $driveFileId = $m[1];
                                elseif (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $resource->google_drive_link, $m)) $driveFileId = $m[1];
                            }
                        ?>
                        <?php if($driveFileId): ?>
                            <div style="position:relative;">
                                <div class="video-overlay"></div>
                                <iframe src="https://drive.google.com/file/d/<?php echo e($driveFileId); ?>/preview" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($resource->google_drive_link): ?>
                        <a href="<?php echo e(route('admin.resources.drive-play', $resource->hash_id)); ?>" target="_blank" class="dashboard-btn dashboard-btn-primary" style="margin-top:10px;display:inline-block;">View Larger</a>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="notes_file" class="form-label">Notes File (PDF/PPT/Excel)</label>
                    <?php if($resource->notes_file_path): ?>
                        <div class="current-file">
                            <span class="file-name"><?php echo e(basename($resource->notes_file_path)); ?></span>
                            <a href="<?php echo e(asset('storage/' . $resource->notes_file_path)); ?>" target="_blank" class="file-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="notes_file" id="notes_file" class="form-file">
                    <p class="form-help">Leave empty to keep the current file</p>
                </div>

                <div class="form-group">
                    <label for="assessment_tests" class="form-label">Assessment Tests (PDF only)</label>
                    <?php if($resource->assessment_tests_path): ?>
                        <div class="current-file">
                            <span class="file-name"><?php echo e(basename($resource->assessment_tests_path)); ?></span>
                            <a href="<?php echo e(asset('storage/' . $resource->assessment_tests_path)); ?>" target="_blank" class="file-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                        <div class="assessment-preview" style="margin-top: 15px;">
                            <iframe 
                                src="<?php echo e(asset('storage/' . $resource->assessment_tests_path)); ?>#toolbar=0&navpanes=0&scrollbar=1" 
                                width="100%" 
                                height="400" 
                                style="border: 1px solid #ddd; border-radius: 4px;"
                                title="Assessment Tests Preview">
                            </iframe>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="assessment_tests" id="assessment_tests" class="form-file" accept=".pdf">
                    <p class="form-help">Leave empty to keep the current file. Only PDF files are allowed.</p>
                </div>

                <div class="form-group">
                    <label for="tags" class="form-label">Tags (comma-separated)</label>
                    <input type="text" name="tags" id="tags" value="<?php echo e(old('tags', $resource->tags)); ?>" class="form-input" placeholder="tag1, tag2, tag3">
                </div>

                <div class="form-group">
                    <label for="learning_outcomes" class="form-label">Learning Outcomes</label>
                    <?php
                        $learningOutcomesText = '';
                        if (old('learning_outcomes')) {
                            $learningOutcomesText = old('learning_outcomes');
                        } elseif ($resource->learning_outcomes) {
                            // Try to decode as JSON first, then fallback to comma-separated
                            $decoded = json_decode($resource->learning_outcomes, true);
                            if (is_array($decoded)) {
                                $learningOutcomesText = implode(', ', $decoded);
                            } else {
                                $learningOutcomesText = $resource->learning_outcomes;
                            }
                        }
                    ?>
                    <textarea name="learning_outcomes" id="learning_outcomes" class="form-textarea" rows="4" placeholder="Enter learning outcomes separated by commas. Example: Understand basic algebra, Solve quadratic equations, Apply mathematical concepts"><?php echo e($learningOutcomesText); ?></textarea>
                    <small class="form-text text-muted">Enter each learning outcome separated by a comma. You can also use line breaks for better organization.</small>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $resource->is_active) ? 'checked' : ''); ?>>
                        <span>Active</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="visible_as_sample" value="1" <?php echo e(old('visible_as_sample', $resource->visible_as_sample) ? 'checked' : ''); ?>>
                        <span>Visible as Sample</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-save"></i> Update Resource
                </button>
            </div>
        </form>
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

.dashboard-btn-primary {
    background-color: #3498db;
    color: white;
}

.dashboard-btn-primary:hover {
    background-color: #2980b9;
}

.dashboard-btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.dashboard-btn-secondary:hover {
    background-color: #7f8c8d;
}

.dashboard-alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.dashboard-alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.error-list {
    margin: 0;
    padding-left: 20px;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
}

.resource-form {
    max-width: 1200px;
    margin: 0 auto;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 20px 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    border-color: #3498db;
    outline: none;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-file {
    display: block;
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.form-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.current-file {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

.file-name {
    font-size: 14px;
    color: #2c3e50;
}

.file-download {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
}

.file-download:hover {
    text-decoration: underline;
}

.form-help {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
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

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTeacherDropdown(subjectId, classId, selectedId = null) {
        $('#teacher_id').html('<option value="">Loading...</option>');
        $.ajax({
            url: '<?php echo e(route('admin.api.teachers')); ?>',
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

    // Assessment tests preview functionality for new file selection
    $('#assessment_tests').on('change', function(e) {
        const file = e.target.files[0];
        const existingPreview = $('.assessment-preview');
        
        if (file && file.type === 'application/pdf') {
            // Hide existing preview and show new one
            existingPreview.hide();
            
            // Create new preview if it doesn't exist
            let newPreview = $('#new-assessment-preview');
            if (newPreview.length === 0) {
                newPreview = $('<div id="new-assessment-preview" style="margin-top: 15px;"><iframe id="new-assessment-iframe" width="100%" height="400" style="border: 1px solid #ddd; border-radius: 4px;" title="New Assessment Tests Preview"></iframe></div>');
                $(this).after(newPreview);
            }
            
            const url = URL.createObjectURL(file);
            $('#new-assessment-iframe').attr('src', url + '#toolbar=0&navpanes=0&scrollbar=1');
            newPreview.show();
        } else if (file) {
            alert('Please select a PDF file for assessment tests.');
            $(this).val('');
            $('#new-assessment-preview').hide();
        } else {
            $('#new-assessment-preview').hide();
            existingPreview.show();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/admin/resources/edit.blade.php ENDPATH**/ ?>