<?php $__env->startSection('title', 'My Videos'); ?>

<?php $__env->startSection('content'); ?>

<div class="my-videos-container">
    <div class="my-videos-header">
        <h1><?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?> My Created Videos <?php else: ?> My Videos <?php endif; ?></h1>
    </div>
    <div class="my-videos-filters">
        <form id="filterForm" action="<?php echo e(route('student.my-videos')); ?>" method="GET">
            <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                <div class="teacher-filters">
                    <label for="filter">Filter Videos</label>
                    <select name="filter" id="filter" onchange="this.form.submit()">
                        <option value="">All My Videos</option>
                        <option value="unreplied_comments" <?php echo e(request('filter') === 'unreplied_comments' ? 'selected' : ''); ?>>Videos with Unreplied Student Comments</option>
                        <option value="replied_comments" <?php echo e(request('filter') === 'replied_comments' ? 'selected' : ''); ?>>Videos with Replied Student Comments</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="my-videos-filters-grid">
                <?php if(!isset($isSchoolStudent) || !$isSchoolStudent): ?>
                
                <div>
                    <label for="class_id">Class</label>
                    <select name="class_id" id="class_id" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>" <?php echo e(request('class_id') == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label for="subject_id">Subject</label>
                    <select name="subject_id" id="subject_id" onchange="this.form.submit()">
                        <option value="">All Subjects</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->id); ?>" <?php echo e(request('subject_id') == $subject->id ? 'selected' : ''); ?>><?php echo e($subject->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="topic_id">Topic</label>
                    <select name="topic_id" id="topic_id" onchange="this.form.submit()">
                        <option value="">All Topics</option>
                        <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($topic->id); ?>" <?php echo e(request('topic_id') == $topic->id ? 'selected' : ''); ?>><?php echo e($topic->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="term_id">Term</label>
                    <select name="term_id" id="term_id" onchange="this.form.submit()">
                        <option value="">All Terms</option>
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($term->id); ?>" <?php echo e(request('term_id') == $term->id ? 'selected' : ''); ?>><?php echo e($term->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php if(!isset($isSchoolStudent) || !$isSchoolStudent): ?>
                
                <div>
                    <label for="grade_level">Grade Level</label>
                    <select name="grade_level" id="grade_level" onchange="this.form.submit()">
                        <option value="">All Grades</option>
                        <option value="O Level" <?php echo e(request('grade_level') == 'O Level' ? 'selected' : ''); ?>>O Level</option>
                        <option value="A Level" <?php echo e(request('grade_level') == 'A Level' ? 'selected' : ''); ?>>A Level</option>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" placeholder="Search by title or description" onblur="this.form.submit()">
                </div>
            </div>
            <div class="my-videos-filters-actions">
                <a href="<?php echo e(route('student.my-videos')); ?>" class="my-videos-reset">Reset Filters</a>
            </div>
        </form>
    </div>
    <div class="my-videos-grid">
        <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="my-video-card">
                <div class="my-video-card-body">
                    <h3 class="my-video-title"><?php echo e($resource->title); ?></h3>
                    <p class="my-video-desc"><?php echo e(Str::limit($resource->description, 100)); ?></p>
                    <div class="my-video-tags">
                        <?php if($resource->grade_level): ?>
                            <span class="my-video-tag my-video-tag-blue"><?php echo e($resource->grade_level); ?></span>
                        <?php endif; ?>
                        <?php if($resource->subject_name ?? $resource->subject->name ?? null): ?>
                            <span class="my-video-tag my-video-tag-green"><?php echo e($resource->subject_name ?? $resource->subject->name); ?></span>
                        <?php endif; ?>
                        <?php if($resource->term_name ?? $resource->term->name ?? null): ?>
                            <span class="my-video-tag my-video-tag-purple"><?php echo e($resource->term_name ?? $resource->term->name); ?></span>
                        <?php endif; ?>
                        <?php if($resource->class_name ?? $resource->classRoom->name ?? null): ?>
                            <span class="my-video-tag my-video-tag-yellow"><?php echo e($resource->class_name ?? $resource->classRoom->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="my-video-footer">
                        <a href="<?php echo e(route('student.my-videos.show', $resource->id)); ?>" class="my-video-btn">
                            <i class="fas fa-play"></i> Watch Video
                        </a>
                        <div class="my-video-meta">
                            <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?>
                                <?php if(isset($resource->unreplied_comments_count) && $resource->unreplied_comments_count > 0): ?>
                                    <span class="unreplied-comments-badge" title="Unreplied student comments">
                                        <i class="fas fa-comment-exclamation"></i> <?php echo e($resource->unreplied_comments_count); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if(isset($resource->replied_comments_count) && $resource->replied_comments_count > 0): ?>
                                    <span class="replied-comments-badge" title="Replied student comments">
                                        <i class="fas fa-comment-check"></i> <?php echo e($resource->replied_comments_count); ?>

                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <span class="my-video-date"><?php echo e($resource->created_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="my-videos-empty">
                <i class="fas fa-video"></i>
                <p>
                    <?php if(in_array(session('user_type'), ['teacher', 'subject_teacher'])): ?> 
                        You haven't created any videos yet.
                    <?php elseif(isset($isSchoolStudent) && $isSchoolStudent): ?>
                        No videos available for your school yet. Please contact your school administrator.
                    <?php else: ?> 
                        No videos available for your preferences.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <div class="my-videos-pagination">
        <?php if($resources->hasPages()): ?>
            <nav>
                <ul class="pagination">
                    
                    <?php if($resources->onFirstPage()): ?>
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($resources->previousPageUrl()); ?>" rel="prev">Previous</a>
                        </li>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $resources->getUrlRange(1, $resources->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $resources->currentPage()): ?>
                            <li class="page-item active">
                                <span class="page-link"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($resources->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($resources->nextPageUrl()); ?>" rel="next">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Next</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<style>
.my-videos-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 16px;
}
.my-videos-header h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #22223b;
    margin-bottom: 32px;
    text-align: left;
}
.my-videos-filters {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    padding: 18px 18px 8px 18px;
    margin-bottom: 32px;
}
.teacher-filters {
    margin-bottom: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid #e5e7eb;
}
.teacher-filters label {
    font-size: 0.98rem;
    color: #374151;
    font-weight: 500;
    margin-bottom: 4px;
    display: block;
}
.teacher-filters select {
    width: 100%;
    max-width: 300px;
    padding: 7px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 1rem;
    background: #f9fafb;
    color: #22223b;
    margin-bottom: 2px;
    transition: border 0.2s;
}
.teacher-filters select:focus {
    border-color: #2563eb;
    outline: none;
}
.my-videos-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
}
.my-videos-filters label {
    font-size: 0.98rem;
    color: #374151;
    font-weight: 500;
    margin-bottom: 4px;
    display: block;
}
.my-videos-filters select,
.my-videos-filters input[type="text"] {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 1rem;
    background: #f9fafb;
    color: #22223b;
    margin-bottom: 2px;
    transition: border 0.2s;
}
.my-videos-filters select:focus,
.my-videos-filters input[type="text"]:focus {
    border-color: #2563eb;
    outline: none;
}
.my-videos-filters-actions {
    margin-top: 10px;
    text-align: right;
}
.my-videos-reset {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    color: #374151;
    border-radius: 6px;
    font-size: 0.97rem;
    text-decoration: none;
    transition: background 0.2s;
}
.my-videos-reset:hover {
    background: #e0e7ef;
}
.my-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 28px;
}
.my-video-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
    transition: box-shadow 0.2s, transform 0.2s;
    display: flex;
    flex-direction: column;
    min-height: 220px;
}
.my-video-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.10);
    transform: translateY(-2px) scale(1.01);
}
.my-video-card-body {
    padding: 22px 20px 16px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.my-video-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}
.my-video-desc {
    color: #555;
    font-size: 0.98rem;
    margin-bottom: 14px;
    min-height: 40px;
}
.my-video-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}
.my-video-tag {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 500;
    background: #f1f5f9;
    color: #374151;
}
.my-video-tag-blue { background: #dbeafe; color: #2563eb; }
.my-video-tag-green { background: #d1fae5; color: #059669; }
.my-video-tag-purple { background: #ede9fe; color: #7c3aed; }
.my-video-tag-yellow { background: #fef9c3; color: #b45309; }
.my-video-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #f3f4f6;
    padding-top: 12px;
    margin-top: auto;
}
.my-video-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}
.unreplied-comments-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
.replied-comments-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}
.my-video-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #2563eb;
    color: #fff;
    padding: 7px 18px;
    border-radius: 6px;
    font-size: 0.98rem;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s;
}
.my-video-btn:hover {
    background: #1e40af;
}
.my-video-date {
    font-size: 0.92rem;
    color: #6b7280;
}
.my-videos-empty {
    grid-column: 1/-1;
    text-align: center;
    color: #6b7280;
    padding: 48px 0;
}
.my-videos-empty i {
    font-size: 2.5rem;
    margin-bottom: 12px;
}
.my-videos-pagination {
    margin-top: 36px;
    text-align: center;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item {
    margin: 0;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s;
    background: #fff;
}

.page-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
}

.page-item.active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}

.page-item.disabled .page-link {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

.page-item.disabled .page-link:hover {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
}
@media (max-width: 700px) {
    .my-videos-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
    .my-videos-header h1 {
        font-size: 1.3rem;
        margin-bottom: 18px;
    }
    .my-videos-filters-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/my-videos.blade.php ENDPATH**/ ?>