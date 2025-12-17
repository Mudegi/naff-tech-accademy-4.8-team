

<?php $__env->startSection('title', $assignment->title); ?>

<?php $__env->startSection('content'); ?>
<div class="assignment-detail-page">
    <div class="page-header">
        <a href="<?php echo e(route('student.assignments.index')); ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back to My Assignments</span>
        </a>
        <h1><i class="fas fa-clipboard-list"></i> <?php echo e($assignment->title); ?></h1>
        <div class="assignment-meta">
            <span class="badge badge-primary"><i class="fas fa-book"></i> <?php echo e($assignment->subject->name ?? 'N/A'); ?></span>
            <span class="badge badge-secondary"><i class="fas fa-users"></i> <?php echo e($assignment->classRoom->name ?? 'N/A'); ?></span>
            <?php if($assignment->due_date): ?>
                <span class="badge <?php echo e($assignment->isOverdue() ? 'badge-danger' : 'badge-info'); ?>">
                    <i class="fas fa-calendar"></i> Due: <?php echo e($assignment->due_date->format('M d, Y')); ?>

                </span>
            <?php endif; ?>
            <span class="badge badge-success"><i class="fas fa-star"></i> <?php echo e($assignment->total_marks); ?> marks</span>
        </div>
    </div>

    <div class="assignment-content">
        <div class="content-section">
            <h3><i class="fas fa-info-circle"></i> Description</h3>
            <p><?php echo e($assignment->description ?? 'No description provided.'); ?></p>
        </div>

        <?php if($assignment->instructions): ?>
            <div class="content-section">
                <h3><i class="fas fa-tasks"></i> Instructions</h3>
                <div class="instructions-box">
                    <?php echo nl2br(e($assignment->instructions)); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if($assignment->assignment_file_path): ?>
            <div class="content-section">
                <h3><i class="fas fa-paperclip"></i> Assignment File</h3>
                <div class="file-card">
                    <div class="file-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="file-details">
                        <p><strong><?php echo e(basename($assignment->assignment_file_path)); ?></strong></p>
                        <p class="text-muted"><?php echo e(strtoupper($assignment->assignment_file_type ?? 'FILE')); ?></p>
                    </div>
                    <a href="<?php echo e(route('student.assignments.download', $assignment->id)); ?>" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if($submission): ?>
            <div class="content-section submission-section">
                <h3><i class="fas fa-check-circle"></i> Your Submission</h3>
                <div class="submission-card submitted">
                    <div class="submission-header">
                        <span class="status-badge status-<?php echo e($submission->status); ?>">
                            <?php echo e(ucfirst($submission->status)); ?>

                        </span>
                        <span class="submission-date">
                            Submitted: <?php echo e($submission->submitted_at->format('M d, Y H:i A')); ?>

                        </span>
                    </div>
                    
                    <?php if($submission->student_comment): ?>
                        <div class="submission-detail">
                            <strong>Your Comment:</strong>
                            <p><?php echo e($submission->student_comment); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($submission->grade !== null): ?>
                        <div class="grade-display">
                            <i class="fas fa-star"></i>
                            <span class="grade-value"><?php echo e($submission->grade); ?>/<?php echo e($assignment->total_marks); ?></span>
                            <span class="grade-percentage">(<?php echo e(round(($submission->grade / $assignment->total_marks) * 100, 1)); ?>%)</span>
                        </div>
                    <?php endif; ?>

                    <?php if($submission->teacher_feedback): ?>
                        <div class="teacher-feedback">
                            <strong><i class="fas fa-comment-dots"></i> Teacher Feedback:</strong>
                            <p><?php echo e($submission->teacher_feedback); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="submission-actions">
                        <a href="<?php echo e(route('student.assignment-submissions.download', $submission->id)); ?>" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Download My Submission
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="content-section submission-section">
                <h3><i class="fas fa-upload"></i> Submit Your Work</h3>
                <?php if($assignment->isOverdue()): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>This assignment is overdue.</strong> You may still submit, but it will be marked as late.
                    </div>
                <?php endif; ?>

                <form id="submissionForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="submission_file">Upload Your Work <span class="text-danger">*</span></label>
                        <input type="file" id="submission_file" name="submission_file" 
                               accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                        <small class="form-text">Accepted formats: PDF, PNG, JPG, JPEG, DOC, DOCX (Max: 20MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="student_comment">Add a Comment (Optional)</label>
                        <textarea id="student_comment" name="student_comment" rows="3" 
                                  placeholder="Add any notes for your teacher..."></textarea>
                    </div>

                    <div id="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <span class="progress-text">Uploading...</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-upload"></i> Submit Assignment
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.assignment-detail-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.page-header {
    margin-bottom: 2rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.back-button:hover {
    background: #f3f4f6;
    color: #667eea;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 1rem;
}

.assignment-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.badge-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.badge-secondary { background: #6b7280; color: white; }
.badge-info { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-danger { background: #ef4444; color: white; }

.assignment-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.content-section {
    padding: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.content-section:last-child {
    border-bottom: none;
}

.content-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.content-section h3 i {
    color: #667eea;
}

.instructions-box {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    line-height: 1.6;
}

.file-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.file-icon {
    font-size: 2.5rem;
    color: #ef4444;
}

.file-details {
    flex: 1;
}

.file-details p {
    margin: 0.25rem 0;
}

.text-muted {
    color: #6b7280;
    font-size: 0.875rem;
}

.submission-card {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #10b981;
}

.submission-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
}

.status-submitted { background: #10b981; color: white; }
.status-reviewed { background: #f59e0b; color: white; }
.status-graded { background: #3b82f6; color: white; }

.submission-date {
    color: #6b7280;
    font-size: 0.875rem;
}

.submission-detail {
    margin: 1rem 0;
}

.grade-display {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    margin: 1rem 0;
    font-size: 1.5rem;
}

.grade-value {
    font-weight: 700;
    font-size: 2rem;
}

.grade-percentage {
    margin-left: 0.5rem;
    opacity: 0.9;
}

.teacher-feedback {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.teacher-feedback strong {
    color: #667eea;
    display: block;
    margin-bottom: 0.5rem;
}

.submission-actions {
    margin-top: 1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
}

.form-group input[type="file"],
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-group input[type="file"]:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

#upload-progress {
    margin: 1rem 0;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.3s;
}

.progress-text {
    color: #6b7280;
    font-size: 0.875rem;
}
</style>

<script>
document.getElementById('submissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const fileInput = document.getElementById('submission_file');
    const commentInput = document.getElementById('student_comment');
    const progressDiv = document.getElementById('upload-progress');
    const progressFill = progressDiv.querySelector('.progress-fill');
    const progressText = progressDiv.querySelector('.progress-text');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (!fileInput.files[0]) {
        alert('Please select a file to upload.');
        return;
    }
    
    const formData = new FormData();
    formData.append('submission_file', fileInput.files[0]);
    formData.append('student_comment', commentInput.value);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');
    
    // Show progress
    progressDiv.style.display = 'block';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
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
                alert('Assignment submitted successfully!');
                window.location.reload();
            } else {
                alert('Upload failed: ' + (response.message || 'Unknown error'));
                resetForm();
            }
        } else {
            alert('Upload failed. Please try again.');
            resetForm();
        }
    });
    
    xhr.addEventListener('error', function() {
        alert('Upload failed. Please check your connection and try again.');
        resetForm();
    });
    
    xhr.open('POST', '<?php echo e(route('student.assignments.submit', $assignment->id)); ?>');
    xhr.send(formData);
    
    function resetForm() {
        progressDiv.style.display = 'none';
        progressFill.style.width = '0%';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Submit Assignment';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/standalone-assignments/show.blade.php ENDPATH**/ ?>