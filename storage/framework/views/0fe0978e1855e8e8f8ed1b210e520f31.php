

<?php $__env->startSection('content'); ?>
<div class="dashboard-page">
    <div class="welcome-section" style="margin-bottom: 1.5rem;">
        <a href="<?php echo e(route('teacher.marks.index')); ?>" class="dashboard-btn dashboard-btn-secondary" style="margin-bottom: 1rem; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Marks
        </a>
        <h1>Upload Marks for Class</h1>
        <p>Upload examination results for students in a specific class</p>
    </div>

    <?php if($errors->any()): ?>
        <div class="dashboard-alert dashboard-alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Upload Type Toggle -->
    <div style="margin-bottom: 2rem; display: flex; gap: 1rem; justify-content: center;">
        <button type="button" onclick="showBulkUpload()" id="bulkBtn" class="dashboard-btn" style="background: #667eea; color: white;">
            <i class="fas fa-file-upload"></i> Bulk Upload (Entire Class)
        </button>
        <button type="button" onclick="showSingleUpload()" id="singleBtn" class="dashboard-btn dashboard-btn-secondary">
            <i class="fas fa-user"></i> Single Student Entry
        </button>
    </div>

    <!-- Bulk Upload Form -->
    <div class="dashboard-card" id="bulkUploadForm">
        <h2 style="margin: 0 0 1.5rem 0; color: #374151; font-size: 1.5rem;">Bulk Upload - Entire Class</h2>
        <form action="<?php echo e(route('teacher.marks.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="upload_type" value="bulk">

            <div style="margin-bottom: 1.5rem;">
                <label for="class_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Select Class *</label>
                <select name="class_id" id="class_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="loadClassStudents(this.value)">
                    <option value="">-- Select a Class --</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e((old('class_id') == $c->id || ($class && $class->id == $c->id)) ? 'selected' : ''); ?>>
                            <?php echo e($c->name); ?> (<?php echo e($c->grade_level ?? 'N/A'); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if($class && $students->count() > 0): ?>
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f0f9ff; border-radius: 0.5rem; border: 1px solid #bae6fd;">
                    <h3 style="margin: 0 0 0.5rem 0; color: #1e40af;">Students in <?php echo e($class->name); ?></h3>
                    <p style="margin: 0; color: #1e3a8a; font-size: 0.875rem;">
                        Found <?php echo e($students->count()); ?> student(s). Make sure your file uses student names or registration numbers that match these students.
                    </p>
                    <details style="margin-top: 1rem;">
                        <summary style="cursor: pointer; color: #1e40af; font-weight: 600;">View Student List</summary>
                        <ul style="margin-top: 0.5rem; padding-left: 20px; color: #1e3a8a;">
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($student->name); ?> <?php if($student->student && $student->student->registration_number): ?>(<?php echo e($student->student->registration_number); ?>)<?php endif; ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </details>
                </div>
            <?php elseif($class && $students->count() == 0): ?>
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border: 1px solid #fcd34d;">
                    <p style="margin: 0; color: #92400e;">
                        <i class="fas fa-exclamation-triangle"></i> No students found in this class. Please ensure students are properly assigned to this class.
                    </p>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 1.5rem;">
                <label for="academic_level" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Level *</label>
                <select name="academic_level" id="academic_level" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">-- Select Academic Level --</option>
                    <?php $__currentLoopData = $academicLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('academic_level') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['academic_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="subject_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Subject Name *</label>
                <?php if(!empty($teachingSubjects)): ?>
                    <select name="subject_name" id="subject_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                        <option value="">-- Select Subject --</option>
                        <?php $__currentLoopData = $teachingSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject); ?>" <?php echo e(old('subject_name') == $subject ? 'selected' : ''); ?>><?php echo e($subject); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">You can only upload marks for subjects you teach</small>
                <?php else: ?>
                    <input type="text" name="subject_name" id="subject_name" value="<?php echo e(old('subject_name')); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Mathematics, Physics, Chemistry">
                    <small style="display: block; margin-top: 0.25rem; color: #f59e0b; font-size: 0.875rem;">No subjects assigned. Please contact administrator.</small>
                <?php endif; ?>
                <?php $__errorArgs = ['subject_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="paper_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Paper Name (Optional)</label>
                <input type="text" name="paper_name" id="paper_name" value="<?php echo e(old('paper_name')); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Paper 1, Paper 2">
                <?php $__errorArgs = ['paper_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="academic_year" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Year</label>
                <input type="number" name="academic_year" id="academic_year" value="<?php echo e(old('academic_year', date('Y'))); ?>" min="2000" max="<?php echo e(date('Y') + 1); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                <?php $__errorArgs = ['academic_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">                <label for="exam_type_single" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Exam Type *</label>
                <select name="exam_type" id="exam_type_single" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="toggleOtherExamTypeSingle(this)">
                    <option value="">-- Select Exam Type --</option>
                    <option value="Beginning of Term" <?php echo e(old('exam_type') == 'Beginning of Term' ? 'selected' : ''); ?>>Beginning of Term Exams</option>
                    <option value="Mid Term" <?php echo e(old('exam_type') == 'Mid Term' ? 'selected' : ''); ?>>Mid Term Exams</option>
                    <option value="End of Term" <?php echo e(old('exam_type') == 'End of Term' ? 'selected' : ''); ?>>End of Term Exams</option>
                    <option value="Mock" <?php echo e(old('exam_type') == 'Mock' ? 'selected' : ''); ?>>Mock Exams</option>
                    <option value="Other" <?php echo e(old('exam_type') == 'Other' ? 'selected' : ''); ?>>Other (Specify)</option>
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">This helps track student progress across different exam periods</small>
                <?php $__errorArgs = ['exam_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div id="exam_type_other_field_single" style="margin-bottom: 1.5rem; display: <?php echo e(old('exam_type') == 'Other' ? 'block' : 'none'); ?>;">
                <label for="exam_type_other_single" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Specify Exam Type</label>
                <input type="text" name="exam_type_other" id="exam_type_other_single" value="<?php echo e(old('exam_type_other')); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Pre-Mock, Weekly Test">
                <?php $__errorArgs = ['exam_type_other'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">                <label for="exam_type" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Exam Type *</label>
                <select name="exam_type" id="exam_type" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="toggleOtherExamType(this)">
                    <option value="">-- Select Exam Type --</option>
                    <option value="Beginning of Term" <?php echo e(old('exam_type') == 'Beginning of Term' ? 'selected' : ''); ?>>Beginning of Term Exams</option>
                    <option value="Mid Term" <?php echo e(old('exam_type') == 'Mid Term' ? 'selected' : ''); ?>>Mid Term Exams</option>
                    <option value="End of Term" <?php echo e(old('exam_type') == 'End of Term' ? 'selected' : ''); ?>>End of Term Exams</option>
                    <option value="Mock" <?php echo e(old('exam_type') == 'Mock' ? 'selected' : ''); ?>>Mock Exams</option>
                    <option value="Other" <?php echo e(old('exam_type') == 'Other' ? 'selected' : ''); ?>>Other (Specify)</option>
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">This helps track student progress across different exam periods</small>
                <?php $__errorArgs = ['exam_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div id="exam_type_other_field" style="margin-bottom: 1.5rem; display: <?php echo e(old('exam_type') == 'Other' ? 'block' : 'none'); ?>;">
                <label for="exam_type_other" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Specify Exam Type</label>
                <input type="text" name="exam_type_other" id="exam_type_other" value="<?php echo e(old('exam_type_other')); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Pre-Mock, Weekly Test">
                <?php $__errorArgs = ['exam_type_other'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="marks_file" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Marks File (CSV or Excel) *</label>
                <input type="file" name="marks_file" id="marks_file" accept=".csv,.xlsx,.xls" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                <p style="margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                    <a href="<?php echo e(route('teacher.marks.template', ['format' => 'csv'])); ?>" style="color: #2563eb; text-decoration: underline;">Download CSV Template</a> | 
                    <a href="<?php echo e(route('teacher.marks.template', ['format' => 'excel'])); ?>" style="color: #2563eb; text-decoration: underline;">Download Excel Template</a>
                </p>
                <?php $__errorArgs = ['marks_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; display: block;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 0.5rem 0; color: #374151;">File Format Instructions:</h4>
                <p style="margin: 0 0 1rem 0; color: #6b7280; font-size: 0.875rem;">
                    Your file should have the following columns (in order):<br>
                    <strong>1. Student Name/Registration Number</strong> - The student's full name or registration number<br>
                    <strong>2. Grade</strong> - The grade (A, B, C, D, E, O, F, or Distinction 1, Credit 3, Pass 7, etc.)<br>
                    <strong>3. Numeric Mark</strong> - Optional numeric mark (0-100)<br>
                    <strong>4. Principal Pass</strong> - Yes or No<br>
                    <strong>5. Remarks</strong> - Optional remarks
                </p>
                
                <h4 style="margin: 1rem 0 0.5rem 0; color: #374151;">Example Format:</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; background: white; border: 1px solid #d1d5db;">
                        <thead>
                            <tr style="background: #e5e7eb;">
                                <th style="padding: 0.5rem; border: 1px solid #d1d5db; text-align: left;">Student Name</th>
                                <th style="padding: 0.5rem; border: 1px solid #d1d5db; text-align: left;">Grade</th>
                                <th style="padding: 0.5rem; border: 1px solid #d1d5db; text-align: left;">Numeric Mark</th>
                                <th style="padding: 0.5rem; border: 1px solid #d1d5db; text-align: left;">Principal Pass</th>
                                <th style="padding: 0.5rem; border: 1px solid #d1d5db; text-align: left;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">John Doe</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">A</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">85</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Yes</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Excellent performance</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Mary Smith</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">B</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">78</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Yes</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Good work</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Peter Ouma</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">C</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">65</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">No</td>
                                <td style="padding: 0.5rem; border: 1px solid #d1d5db;">Needs improvement</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.75rem; font-style: italic;">
                    Note: Your Excel/CSV file should look exactly like this table above. First row should be the column headers.
                </p>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-upload"></i> Upload Marks
                </button>
                <a href="<?php echo e(route('teacher.marks.index')); ?>" class="dashboard-btn dashboard-btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Single Student Upload Form -->
    <div class="dashboard-card" id="singleUploadForm" style="display: none;">
        <h2 style="margin: 0 0 1.5rem 0; color: #374151; font-size: 1.5rem;">Single Student Entry</h2>
        <form action="<?php echo e(route('teacher.marks.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="upload_type" value="single">

            <div style="margin-bottom: 1.5rem;">
                <label for="single_class_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Select Class *</label>
                <select name="class_id" id="single_class_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" onchange="loadStudentsForSingle(this.value)">
                    <option value="">-- Select a Class --</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?> (<?php echo e($c->grade_level ?? 'N/A'); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="student_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Select Student *</label>
                <select name="student_id" id="student_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">-- Select a class first --</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="single_academic_level" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Level *</label>
                <select name="academic_level" id="single_academic_level" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">-- Select Academic Level --</option>
                    <?php $__currentLoopData = $academicLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="single_subject_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Subject Name *</label>
                <?php if(!empty($teachingSubjects)): ?>
                    <select name="subject_name" id="single_subject_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                        <option value="">-- Select Subject --</option>
                        <?php $__currentLoopData = $teachingSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject); ?>"><?php echo e($subject); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small style="display: block; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">You can only upload marks for subjects you teach</small>
                <?php else: ?>
                    <input type="text" name="subject_name" id="single_subject_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Mathematics, Physics">
                    <small style="display: block; margin-top: 0.25rem; color: #f59e0b; font-size: 0.875rem;">No subjects assigned. Please contact administrator.</small>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="single_paper_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Paper Name (Optional)</label>
                <input type="text" name="paper_name" id="single_paper_name" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="e.g., Paper 1, Paper 2">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="single_academic_year" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Academic Year</label>
                <input type="number" name="academic_year" id="single_academic_year" value="<?php echo e(date('Y')); ?>" min="2000" max="<?php echo e(date('Y') + 1); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="grade" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Grade *</label>
                    <input type="text" name="grade" id="grade" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="A, B, C, D, E">
                </div>

                <div>
                    <label for="numeric_mark" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Numeric Mark</label>
                    <input type="number" name="numeric_mark" id="numeric_mark" min="0" max="100" step="0.1" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="0-100">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_principal_pass" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #374151;">Principal Pass</span>
                </label>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="remarks" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" placeholder="Enter any remarks about this result"></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">
                    <i class="fas fa-save"></i> Save Mark
                </button>
                <a href="<?php echo e(route('teacher.marks.index')); ?>" class="dashboard-btn dashboard-btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function loadClassStudents(classId) {
    if (classId) {
        window.location.href = '<?php echo e(route("teacher.marks.create")); ?>?class_id=' + classId;
    }
}

function showBulkUpload() {
    document.getElementById('bulkUploadForm').style.display = 'block';
    document.getElementById('singleUploadForm').style.display = 'none';
    document.getElementById('bulkBtn').style.background = '#667eea';
    document.getElementById('bulkBtn').style.color = 'white';
    document.getElementById('singleBtn').style.background = '#e5e7eb';
    document.getElementById('singleBtn').style.color = '#374151';
}

function showSingleUpload() {
    document.getElementById('bulkUploadForm').style.display = 'none';
    document.getElementById('singleUploadForm').style.display = 'block';
    document.getElementById('bulkBtn').style.background = '#e5e7eb';
    document.getElementById('bulkBtn').style.color = '#374151';
    document.getElementById('singleBtn').style.background = '#667eea';
    document.getElementById('singleBtn').style.color = 'white';
}

async function loadStudentsForSingle(classId) {
    const studentSelect = document.getElementById('student_id');
    
    if (!classId) {
        studentSelect.innerHTML = '<option value="">-- Select a class first --</option>';
        return;
    }

    studentSelect.innerHTML = '<option value="">Loading students...</option>';
    
    try {
        const response = await fetch(`/teacher/marks/students/${classId}`);
        const students = await response.json();
        
        if (students.length === 0) {
            studentSelect.innerHTML = '<option value="">No students found in this class</option>';
            return;
        }
        
        studentSelect.innerHTML = '<option value="">-- Select a Student --</option>';
        students.forEach(student => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = student.name + (student.registration_number ? ` (${student.registration_number})` : '');
            studentSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading students:', error);
        studentSelect.innerHTML = '<option value="">Error loading students</option>';
    }
}

function toggleOtherExamType(selectElement) {
    const otherField = document.getElementById('exam_type_other_field');
    const otherInput = document.getElementById('exam_type_other');
    
    if (selectElement.value === 'Other') {
        otherField.style.display = 'block';
        otherInput.required = true;
    } else {
        otherField.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

function toggleOtherExamTypeSingle(selectElement) {
    const otherField = document.getElementById('exam_type_other_field_single');
    const otherInput = document.getElementById('exam_type_other_single');
    
    if (selectElement.value === 'Other') {
        otherField.style.display = 'block';
        otherInput.required = true;
    } else {
        otherField.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\naff-tech-accademy-4.8-team\resources\views/teacher/marks/create.blade.php ENDPATH**/ ?>