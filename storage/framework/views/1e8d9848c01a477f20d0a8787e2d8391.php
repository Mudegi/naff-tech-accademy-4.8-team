

<?php $__env->startSection('content'); ?>
<?php
    // Determine if this is an A-Level student
    $user = Auth::user();
    $user->load('classes');
    $studentClass = $user->classes->first();
    $isALevel = false;
    
    if ($studentClass) {
        $className = strtolower($studentClass->name);
        if (preg_match('/(form\s*[56]|s[56])/i', $className)) {
            $isALevel = true;
        }
    }
?>

<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 2rem;">
        <?php if($isALevel): ?>
            <h1>A-Level Course Recommendations</h1>
            <p>Based on your UACE (A-Level) results, here are the degree courses you qualify for</p>
        <?php else: ?>
            <h1>Course Recommendations</h1>
            <p>Based on your academic results, here are the courses you may qualify for</p>
        <?php endif; ?>
    </div>

    <!-- Exam Type Selector -->
    <?php if($availableExamTypes->count() > 0): ?>
        <div class="dashboard-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
            <form method="GET" action="<?php echo e(route('student.course-recommendations.index')); ?>" id="examTypeForm">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <label for="exam_type" style="font-weight: 600; color: #374151; white-space: nowrap;">
                        <i class="fas fa-filter"></i> Select Exam Type:
                    </label>
                    <select name="exam_type" id="exam_type" onchange="document.getElementById('examTypeForm').submit()" style="flex: 1; min-width: 250px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                        <option value="">All Exams (Combined)</option>
                        <?php $__currentLoopData = $availableExamTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type); ?>" <?php echo e($selectedExamType == $type ? 'selected' : ''); ?>>
                                <?php echo e($type); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <small style="display: block; margin-top: 0.5rem; color: #6b7280;">
                    Choose which exam type's marks to use for course recommendations. This helps you see what courses you qualify for based on specific exam results.
                </small>
            </form>
        </div>
    <?php else: ?>
        <div class="dashboard-card" style="margin-bottom: 1.5rem; padding: 1.5rem; background: #fef3c7; border-left: 4px solid #f59e0b;">
            <div style="display: flex; align-items: start; gap: 0.75rem;">
                <i class="fas fa-info-circle" style="color: #d97706; font-size: 1.25rem; margin-top: 0.25rem;"></i>
                <div>
                    <strong style="color: #92400e; font-size: 1.05rem;">Exam Type Filtering Not Available Yet</strong>
                    <p style="margin: 0.5rem 0 0 0; color: #78350f; line-height: 1.6;">
                        Your marks don't have exam type information. When teachers upload new marks, they should specify the exam type (Beginning of Term, Mid Term, End of Term, Mock). Once that's done, you'll be able to:
                    </p>
                    <ul style="margin: 0.5rem 0 0 1.5rem; color: #78350f;">
                        <li>Filter course recommendations by specific exam results</li>
                        <li>Compare your performance across different exams</li>
                        <li>Track your improvement from Mid Term to End of Term</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Performance Comparison -->
    <?php if($performanceComparison): ?>
        <div class="dashboard-card" style="margin-bottom: 1.5rem; padding: 1.5rem; border-left: 4px solid <?php echo e($performanceComparison['overall_status'] == 'improved' ? '#10b981' : ($performanceComparison['overall_status'] == 'declined' ? '#ef4444' : '#6b7280')); ?>;">
            <h3 style="margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line"></i> Performance Comparison
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div style="padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Previous Exam</div>
                    <div style="font-weight: 600; color: #1f2937;"><?php echo e($performanceComparison['previous_exam_type']); ?></div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: #6b7280;"><?php echo e(number_format($performanceComparison['previous_aggregate'], 1)); ?> pts</div>
                </div>
                
                <div style="padding: 1rem; background: <?php echo e($performanceComparison['overall_status'] == 'improved' ? '#d1fae5' : ($performanceComparison['overall_status'] == 'declined' ? '#fee2e2' : '#f3f4f6')); ?>; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Current Exam</div>
                    <div style="font-weight: 600; color: #1f2937;"><?php echo e($performanceComparison['current_exam_type']); ?></div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: <?php echo e($performanceComparison['overall_status'] == 'improved' ? '#065f46' : ($performanceComparison['overall_status'] == 'declined' ? '#991b1b' : '#374151')); ?>;">
                        <?php echo e(number_format($performanceComparison['current_aggregate'], 1)); ?> pts
                    </div>
                </div>
                
                <div style="padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Change</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: <?php echo e($performanceComparison['aggregate_diff'] > 0 ? '#10b981' : ($performanceComparison['aggregate_diff'] < 0 ? '#ef4444' : '#6b7280')); ?>;">
                        <?php echo e($performanceComparison['aggregate_diff'] > 0 ? '+' : ''); ?><?php echo e(number_format($performanceComparison['aggregate_diff'], 1)); ?>

                        <i class="fas fa-<?php echo e($performanceComparison['aggregate_diff'] > 0 ? 'arrow-up' : ($performanceComparison['aggregate_diff'] < 0 ? 'arrow-down' : 'minus')); ?>"></i>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 50%;"></span>
                    <span><?php echo e($performanceComparison['improved_subjects']); ?> improved</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 50%;"></span>
                    <span><?php echo e($performanceComparison['declined_subjects']); ?> declined</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #6b7280; border-radius: 50%;"></span>
                    <span><?php echo e($performanceComparison['stable_subjects']); ?> stable</span>
                </div>
            </div>
            
            <?php if(count($performanceComparison['subjects']) > 0): ?>
                <details style="margin-top: 1rem;">
                    <summary style="cursor: pointer; font-weight: 600; color: #374151; padding: 0.5rem 0;">
                        View Subject-by-Subject Comparison
                    </summary>
                    <div style="margin-top: 1rem; overflow-x: auto;">
                        <table style="width: 100%; font-size: 0.875rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 0.5rem; text-align: left;">Subject</th>
                                    <th style="padding: 0.5rem; text-align: center;">Previous</th>
                                    <th style="padding: 0.5rem; text-align: center;">Current</th>
                                    <th style="padding: 0.5rem; text-align: center;">Change</th>
                                    <th style="padding: 0.5rem; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $performanceComparison['subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 0.5rem;"><?php echo e($subject); ?></td>
                                        <td style="padding: 0.5rem; text-align: center;"><?php echo e($data['previous_grade']); ?> (<?php echo e($data['previous_points']); ?>)</td>
                                        <td style="padding: 0.5rem; text-align: center;"><?php echo e($data['current_grade']); ?> (<?php echo e($data['current_points']); ?>)</td>
                                        <td style="padding: 0.5rem; text-align: center; color: <?php echo e($data['points_diff'] > 0 ? '#10b981' : ($data['points_diff'] < 0 ? '#ef4444' : '#6b7280')); ?>; font-weight: 600;">
                                            <?php echo e($data['points_diff'] > 0 ? '+' : ''); ?><?php echo e($data['points_diff']); ?>

                                        </td>
                                        <td style="padding: 0.5rem; text-align: center;">
                                            <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; background: <?php echo e($data['status'] == 'improved' ? '#d1fae5' : ($data['status'] == 'declined' ? '#fee2e2' : '#f3f4f6')); ?>; color: <?php echo e($data['status'] == 'improved' ? '#065f46' : ($data['status'] == 'declined' ? '#991b1b' : '#374151')); ?>;">
                                                <?php echo e(ucfirst($data['status'])); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Summary Card -->
    <div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem; padding: 2rem; margin-bottom: 2rem; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Your Aggregate Points</div>
                <div style="font-size: 2.5rem; font-weight: 700;"><?php echo e(number_format($aggregatePoints, 1)); ?></div>
            </div>
            <div>
                <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Principal Passes</div>
                <div style="font-size: 2.5rem; font-weight: 700;"><?php echo e($principalPasses); ?></div>
            </div>
            <div>
                <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Qualifying Courses</div>
                <div style="font-size: 2.5rem; font-weight: 700;"><?php echo e($qualifyingCourses->count()); ?></div>
            </div>
            <div>
                <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Academic Year</div>
                <div style="font-size: 1.5rem; font-weight: 600;"><?php echo e($currentYear); ?></div>
            </div>
        </div>
    </div>

    <?php if($qualifyingCourses->isEmpty()): ?>
        <div class="empty-state" style="background: white; border-radius: 0.5rem; padding: 3rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
            <h3 style="color: #6b7280; margin-bottom: 0.5rem;">No Course Recommendations Available</h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">
                <?php if($aggregatePoints == 0): ?>
                    <?php if($isALevel): ?>
                        You need to add your A-Level examination results first. Please contact your teacher or school administrator to upload your marks (from any exam - beginning of term, mid-term, end of term, or UACE).
                    <?php else: ?>
                        University course recommendations are available for A-Level (Form 5-6) students. Once you're in A-Level and your marks are uploaded, you'll see course recommendations here.
                    <?php endif; ?>
                <?php else: ?>
                    Your current aggregate points (<?php echo e(number_format($aggregatePoints, 1)); ?>) do not meet the minimum requirements for any courses. Consider improving your grades or checking other options.
                <?php endif; ?>
            </p>
            <?php if($aggregatePoints == 0): ?>
                <a href="<?php echo e(route('student.marks.index')); ?>" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-eye"></i> View My Marks
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Recommendations by University -->
        <?php $__currentLoopData = $groupedByUniversity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $universityName => $courses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="university-section" style="background: white; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="university-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb;">
                    <div style="width: 60px; height: 60px; border-radius: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">
                        <?php echo e(strtoupper(substr($universityName, 0, 2))); ?>

                    </div>
                    <div style="flex: 1;">
                        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1a1a1a; margin: 0;"><?php echo e($universityName); ?></h2>
                        <p style="color: #6b7280; margin: 0.25rem 0 0 0; font-size: 0.875rem;"><?php echo e($courses->count()); ?> <?php echo e(Str::plural('course', $courses->count())); ?> available</p>
                    </div>
                </div>

                <div class="courses-grid" style="display: grid; gap: 1rem;">
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="course-card" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.25rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 6px rgba(102, 126, 234, 0.1)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a1a; margin: 0 0 0.5rem 0;"><?php echo e($course->course_name); ?></h3>
                                    <?php if($course->faculty): ?>
                                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;"><?php echo e($course->faculty); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if($course->match_score > 0): ?>
                                    <span class="match-badge" style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                                        <?php echo e($course->match_score); ?>% Match
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if($course->course_description): ?>
                                <p style="color: #4b5563; font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.5;"><?php echo e(Str::limit($course->course_description, 150)); ?></p>
                            <?php endif; ?>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                                <div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Cut-Off Points</div>
                                    <?php if($course->cut_off_format === 'makerere' && $course->program_category === 'stem'): ?>
                                        <div style="font-size: 0.875rem; font-weight: 600; color: #1a1a1a;">
                                            <div>M: <?php echo e($course->cut_off_points_male ? number_format($course->cut_off_points_male, 1) : 'N/A'); ?></div>
                                            <div>F: <?php echo e($course->cut_off_points_female ? number_format($course->cut_off_points_female, 1) : 'N/A'); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div style="font-size: 1.125rem; font-weight: 600; color: #1a1a1a;"><?php echo e(number_format($course->effective_cut_off ?? $course->cut_off_points, 1)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Your Points</div>
                                    <div style="font-size: 1.125rem; font-weight: 600; color: <?php echo e($aggregatePoints >= ($course->effective_cut_off ?? $course->cut_off_points) ? '#059669' : '#dc2626'); ?>;">
                                        <?php echo e(number_format($aggregatePoints, 1)); ?>

                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Difference</div>
                                    <?php
                                        $effectiveCutOff = $course->effective_cut_off ?? $course->cut_off_points;
                                        $difference = $aggregatePoints - $effectiveCutOff;
                                    ?>
                                    <div style="font-size: 1.125rem; font-weight: 600; color: <?php echo e($difference >= 0 ? '#059669' : '#dc2626'); ?>;">
                                        <?php echo e($difference >= 0 ? '+' : ''); ?><?php echo e(number_format($difference, 1)); ?>

                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                                <?php if($course->essential_subjects): ?>
                                    <div style="flex: 1; min-width: 150px;">
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Essential Subjects</div>
                                        <div style="font-size: 0.875rem; color: #1a1a1a; font-weight: 500;"><?php echo e(implode(', ', array_slice($course->essential_subjects, 0, 3))); ?><?php echo e(count($course->essential_subjects) > 3 ? '...' : ''); ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if($course->duration_years): ?>
                                    <div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Duration</div>
                                        <div style="font-size: 0.875rem; color: #1a1a1a; font-weight: 500;"><?php echo e($course->duration_years); ?> <?php echo e(Str::plural('Year', $course->duration_years)); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if($course->additional_requirements): ?>
                                <div style="padding: 0.75rem; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 0.25rem; margin-bottom: 1rem;">
                                    <div style="font-size: 0.75rem; color: #92400e; font-weight: 600; margin-bottom: 0.25rem;">Additional Requirements</div>
                                    <div style="font-size: 0.875rem; color: #78350f;"><?php echo e($course->additional_requirements); ?></div>
                                </div>
                            <?php endif; ?>

                            <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <?php
                                    $effectiveCutOff = $course->effective_cut_off ?? $course->cut_off_points;
                                    $isQualified = $aggregatePoints >= $effectiveCutOff;
                                ?>
                                <span class="status-badge" style="padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; background: <?php echo e($isQualified ? '#d1fae5' : '#fee2e2'); ?>; color: <?php echo e($isQualified ? '#065f46' : '#991b1b'); ?>;">
                                    <?php echo e($isQualified ? '✓ Qualified' : '✗ Not Qualified'); ?>

                                </span>
                                <span class="degree-badge" style="padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; background: #e0e7ff; color: #4338ca; text-transform: capitalize;">
                                    <?php echo e($course->degree_type); ?>

                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: center; flex-wrap: wrap;">
        <?php if($qualifyingCourses->isNotEmpty()): ?>
            <a href="<?php echo e(route('student.course-recommendations.download-pdf')); ?>" class="dashboard-btn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none;">
                <i class="fas fa-file-pdf"></i> Download PDF Report
            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('student.marks.index')); ?>" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-edit"></i> Manage My Marks
        </a>
        <a href="<?php echo e(route('student.dashboard')); ?>" class="dashboard-btn dashboard-btn-primary">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
</div>

<style>
@media (max-width: 640px) {
    .summary-card {
        padding: 1.5rem;
    }
    .summary-card > div {
        grid-template-columns: 1fr;
    }
    .university-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/student/course-recommendations/index.blade.php ENDPATH**/ ?>