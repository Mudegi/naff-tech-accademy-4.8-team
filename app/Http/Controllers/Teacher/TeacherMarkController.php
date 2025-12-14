<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentMark;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\CourseRecommendationNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class TeacherMarkController extends Controller
{
    /**
     * Display a listing of classes the teacher can upload marks for.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        // Get classes the teacher is assigned to - direct database query
        $classIds = \DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id');
        
        $classes = SchoolClass::withoutGlobalScope('school')
            ->whereIn('id', $classIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get recent mark uploads - only marks uploaded by this teacher (not by students)
        $recentUploads = StudentMark::where('school_id', $user->school_id)
            ->where('uploaded_by', $user->id) // Only marks uploaded by this teacher
            ->whereHas('user', function($query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->latest()
            ->take(10)
            ->with('user')
            ->get()
            ->groupBy('academic_year');

        return view('teacher.marks.index', compact('classes', 'recentUploads'));
    }

    /**
     * Show the form for uploading marks for a specific class.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        $classId = $request->get('class_id');
        $class = null;
        $students = collect();

        if ($classId) {
            $class = SchoolClass::withoutGlobalScope('school')
                ->where('id', $classId)
                ->firstOrFail();

            // Verify teacher is assigned to this class - direct database query
            $isAssigned = \DB::table('class_user')
                ->where('user_id', $user->id)
                ->where('class_id', $classId)
                ->exists();
            
            if (!$isAssigned) {
                abort(403, 'You are not assigned to this class.');
            }

            // Get students in this class (matching by class name or in classes array)
            $students = User::where('account_type', 'student')
                ->where('school_id', $user->school_id)
                ->whereHas('student', function($query) use ($class) {
                    $query->where(function($q) use ($class) {
                        $q->where('class', 'LIKE', '%' . $class->name . '%')
                          ->orWhereJsonContains('classes', $class->name)
                          ->orWhereJsonContains('classes', $class->id);
                    });
                })
                ->with('student')
                ->orderBy('name')
                ->get();
        }

        // Get all classes the teacher is assigned to - direct database query
        $classIds = \DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id');
        
        $classes = SchoolClass::withoutGlobalScope('school')
            ->whereIn('id', $classIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $academicLevels = ['UACE' => 'UACE (A-Level)', 'UCE' => 'UCE (O-Level)'];
        
        // Get subjects the teacher teaches
        $teachingSubjects = $user->getTeachingSubjectNames();

        return view('teacher.marks.create', compact('classes', 'class', 'students', 'academicLevels', 'teachingSubjects'));
    }

    /**
     * Store marks for students (bulk upload or single entry).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        // Determine upload type
        $uploadType = $request->input('upload_type', 'bulk');

        if ($uploadType === 'single') {
            return $this->storeSingleMark($request, $user);
        }

        // Bulk upload validation
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'academic_level' => 'required|string|in:UACE,UCE',
            'subject_name' => 'required|string|max:255',
            'paper_name' => 'nullable|string|max:255',
            'academic_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'exam_type' => 'required|string|in:Beginning of Term,Mid Term,End of Term,Mock,Other',
            'exam_type_other' => 'required_if:exam_type,Other|nullable|string|max:255',
            'marks_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify teacher teaches this subject
        if (!$user->teachesSubject($request->subject_name)) {
            return redirect()->back()
                ->withErrors(['subject_name' => 'You are not assigned to teach this subject. You can only upload marks for subjects you teach.'])
                ->withInput();
        }

        $class = SchoolClass::where('id', $request->class_id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Verify teacher is assigned to this class
        if (!$user->classes()->where('classes.id', $request->class_id)->exists()) {
            abort(403, 'You are not assigned to this class.');
        }

        // Get students in this class
        $students = User::where('account_type', 'student')
            ->where('school_id', $user->school_id)
            ->whereHas('student', function($query) use ($class) {
                $query->where(function($q) use ($class) {
                    $q->where('class', 'LIKE', '%' . $class->name . '%')
                      ->orWhereJsonContains('classes', $class->name)
                      ->orWhereJsonContains('classes', $class->id);
                });
            })
            ->with('student')
            ->get()
            ->keyBy(function($user) {
                // Create keys for matching: name, registration number, email
                return strtolower(trim($user->name));
            });

        $file = $request->file('marks_file');
        $fileExtension = strtolower($file->getClientOriginalExtension());
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $rows = [];
            
            // Check if file is Excel or CSV
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                $rows = $this->readExcelFile($file);
            } else {
                $rows = $this->readCsvFile($file);
            }
            
            $rowNumber = 1;
            $notificationService = new CourseRecommendationNotificationService();
            $notifiedUsers = [];
            
            foreach ($rows as $row) {
                $rowNumber++;
                
                // Expected format: student_name/registration_number, grade, numeric_mark, is_principal_pass, remarks
                $studentIdentifier = trim($row[0] ?? '');
                $grade = trim($row[1] ?? '');
                $numericMark = !empty($row[2]) ? (float)$row[2] : null;
                $isPrincipalPass = !empty($row[3]) && strtolower(trim($row[3])) === 'yes';
                $remarks = trim($row[4] ?? '');

                // Validate required fields
                if (empty($studentIdentifier) || empty($grade)) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Missing student identifier or grade";
                    continue;
                }

                // Find student by name or registration number
                $studentUser = null;
                
                // Try to find by name (case-insensitive)
                $studentUser = $students->first(function($user) use ($studentIdentifier) {
                    return strtolower(trim($user->name)) === strtolower(trim($studentIdentifier)) ||
                           strtolower(trim($user->student->first_name . ' ' . $user->student->last_name ?? '')) === strtolower(trim($studentIdentifier));
                });

                // If not found by name, try registration number
                if (!$studentUser) {
                    $studentUser = $students->first(function($user) use ($studentIdentifier) {
                        return strtolower(trim($user->student->registration_number ?? '')) === strtolower(trim($studentIdentifier));
                    });
                }

                if (!$studentUser) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Student '$studentIdentifier' not found in class";
                    continue;
                }

                // Determine grade type
                $gradeType = 'letter';
                $gradeUpper = strtoupper($grade);
                if (preg_match('/distinction|credit|pass/i', $grade)) {
                    $gradeType = 'distinction_credit_pass';
                } elseif (is_numeric($grade) || is_numeric($numericMark)) {
                    $gradeType = 'numeric';
                }

                try {
                    $mark = StudentMark::create([
                        'user_id' => $studentUser->id,
                        'student_id' => $studentUser->student->id ?? null,
                        'school_id' => $user->school_id,
                        'uploaded_by' => $user->id, // Teacher uploaded this mark
                        'academic_level' => $request->academic_level,
                        'subject_name' => $request->subject_name,
                        'paper_name' => $request->paper_name ?: null,
                        'grade' => $grade,
                        'numeric_mark' => $numericMark,
                        'grade_type' => $gradeType,
                        'is_principal_pass' => $isPrincipalPass,
                        'academic_year' => $request->academic_year ?? date('Y'),
                        'exam_type' => $request->exam_type,
                        'exam_type_other' => $request->exam_type === 'Other' ? $request->exam_type_other : null,
                        'remarks' => $remarks ?: null,
                    ]);

                    $results['success']++;
                    
                    // Notify student about new marks (only once per student)
                    if (!in_array($studentUser->id, $notifiedUsers)) {
                        $notificationService->notifyOnMarksUpdate($studentUser);
                        $notifiedUsers[] = $studentUser->id;
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
                }
            }

            $message = "Import completed! {$results['success']} marks uploaded successfully.";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} marks failed to upload.";
            }

            return redirect()->route('teacher.marks.index')
                ->with('success', $message)
                ->with('import_results', $results);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template (CSV or Excel)
     */
    public function downloadTemplate(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        if ($format === 'excel') {
            return $this->downloadExcelTemplate();
        } else {
            return $this->downloadCsvTemplate();
        }
    }

    /**
     * Read CSV file
     */
    private function readCsvFile($file)
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (!empty(array_filter($row))) { // Skip completely empty rows
                $rows[] = $row;
            }
        }
        
        fclose($handle);
        return $rows;
    }

    /**
     * Read Excel file
     */
    private function readExcelFile($file)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            
            $highestRow = $worksheet->getHighestRow();
            
            // Start from row 2 to skip header
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                // Get data from columns A to E (5 columns)
                for ($col = 'A'; $col <= 'E'; $col++) {
                    $cell = $worksheet->getCell($col . $row);
                    $cellValue = $cell->getValue();
                    $rowData[] = $cellValue ?? '';
                }
                
                // Only add row if it has at least student identifier and grade
                if (!empty($rowData[0]) && !empty($rowData[1])) {
                    $rows[] = $rowData;
                }
            }
            
            return $rows;
        } catch (\Exception $e) {
            throw new \Exception('Error reading Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template
     */
    private function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="class_marks_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Student Name/Registration Number',
                'Grade',
                'Numeric Mark (0-100)',
                'Principal Pass (Yes/No)',
                'Remarks'
            ]);

            // Example rows
            fputcsv($file, [
                'John Doe',
                'A',
                '',
                'Yes',
                'Excellent work'
            ]);

            fputcsv($file, [
                'REG123',
                'B',
                '75',
                'Yes',
                ''
            ]);

            fputcsv($file, [
                'Jane Smith',
                'Distinction 1',
                '',
                'Yes',
                'Outstanding'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Excel template
     */
    private function downloadExcelTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header row
            $headers = [
                'Student Name/Registration Number',
                'Grade',
                'Numeric Mark (0-100)',
                'Principal Pass (Yes/No)',
                'Remarks'
            ];
            
            $sheet->fromArray($headers, null, 'A1');
            
            // Add example rows
            $examples = [
                ['John Doe', 'A', '', 'Yes', 'Excellent work'],
                ['REG123', 'B', '75', 'Yes', ''],
                ['Jane Smith', 'Distinction 1', '', 'Yes', 'Outstanding'],
            ];
            
            $sheet->fromArray($examples, null, 'A2');
            
            // Auto-size columns
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Style header row
            $sheet->getStyle('A1:E1')->getFont()->setBold(true);
            $sheet->getStyle('A1:E1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            
            $writer = new Xlsx($spreadsheet);
            
            $filename = 'class_marks_import_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating template: ' . $e->getMessage());
        }
    }

    /**
     * Store single student mark entry.
     */
    private function storeSingleMark(Request $request, $user)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'student_id' => 'required|exists:users,id',
            'academic_level' => 'required|string|in:UACE,UCE',
            'subject_name' => 'required|string|max:255',
            'paper_name' => 'nullable|string|max:255',
            'academic_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'exam_type' => 'required|string|in:Beginning of Term,Mid Term,End of Term,Mock,Other',
            'exam_type_other' => 'required_if:exam_type,Other|nullable|string|max:255',
            'grade' => 'required|string|max:50',
            'numeric_mark' => 'nullable|numeric|min:0|max:100',
            'is_principal_pass' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify teacher teaches this subject
        if (!$user->teachesSubject($request->subject_name)) {
            return redirect()->back()
                ->withErrors(['subject_name' => 'You are not assigned to teach this subject. You can only upload marks for subjects you teach.'])
                ->withInput();
        }

        $class = SchoolClass::where('id', $request->class_id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Verify teacher is assigned to this class
        if (!$user->classes()->where('classes.id', $request->class_id)->exists()) {
            abort(403, 'You are not assigned to this class.');
        }

        $student = User::where('id', $request->student_id)
            ->where('account_type', 'student')
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Convert grade to points
        $points = $this->gradeToPoints($request->grade);

        // Create the mark record
        StudentMark::create([
            'user_id' => $student->id,
            'school_id' => $user->school_id,
            'class_id' => $request->class_id,
            'academic_level' => $request->academic_level,
            'subject_name' => $request->subject_name,
            'paper_name' => $request->paper_name,
            'grade' => $request->grade,
            'numeric_mark' => $request->numeric_mark,
            'points' => $points,
            'is_principal_pass' => $request->boolean('is_principal_pass'),
            'academic_year' => $request->academic_year ?? date('Y'),
            'exam_type' => $request->exam_type,
            'exam_type_other' => $request->exam_type === 'Other' ? $request->exam_type_other : null,
            'remarks' => $request->remarks,
            'uploaded_by' => $user->id,
        ]);

        // Check for course recommendations
        $notificationService = new CourseRecommendationNotificationService();
        $notificationService->checkAndNotifyForStudent($student);

        return redirect()->route('teacher.marks.index')
            ->with('success', 'Mark saved successfully for ' . $student->name);
    }

    /**
     * Get students for a specific class (AJAX endpoint).
     */
    public function getClassStudents($classId)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403);
        }

        // Verify teacher is assigned to this class
        if (!$user->classes()->where('classes.id', $classId)->exists()) {
            abort(403);
        }

        $class = SchoolClass::where('id', $classId)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Get students in this class
        $students = User::where('account_type', 'student')
            ->where('school_id', $user->school_id)
            ->whereHas('student', function($query) use ($class) {
                $query->where(function($q) use ($class) {
                    $q->where('class', 'LIKE', '%' . $class->name . '%')
                      ->orWhereJsonContains('classes', $class->name)
                      ->orWhereJsonContains('classes', $class->id);
                });
            })
            ->with('student')
            ->orderBy('name')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'registration_number' => $student->student->registration_number ?? null,
                ];
            });

        return response()->json($students);
    }

    /**
     * Show the form for editing a specific mark.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        $mark = StudentMark::where('id', $id)
            ->where('school_id', $user->school_id)
            ->where('uploaded_by', $user->id) // Only allow editing own uploads
            ->with(['user', 'user.student', 'class'])
            ->firstOrFail();

        return view('teacher.marks.edit', compact('mark'));
    }

    /**
     * Update the specified mark.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        $mark = StudentMark::where('id', $id)
            ->where('school_id', $user->school_id)
            ->where('uploaded_by', $user->id) // Only allow editing own uploads
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'academic_level' => 'required|string|in:UACE,UCE',
            'subject_name' => 'required|string|max:255',
            'paper_name' => 'nullable|string|max:255',
            'academic_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'exam_type' => 'required|string|in:Beginning of Term,Mid Term,End of Term,Mock,Other',
            'exam_type_other' => 'required_if:exam_type,Other|nullable|string|max:255',
            'grade' => 'required|string|max:50',
            'numeric_mark' => 'nullable|numeric|min:0|max:100',
            'is_principal_pass' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify teacher teaches this subject
        if (!$user->teachesSubject($request->subject_name)) {
            return redirect()->back()
                ->withErrors(['subject_name' => 'You are not assigned to teach this subject. You can only edit marks for subjects you teach.'])
                ->withInput();
        }

        // Convert grade to points
        $points = $this->gradeToPoints($request->grade);

        // Update the mark
        $mark->update([
            'academic_level' => $request->academic_level,
            'subject_name' => $request->subject_name,
            'paper_name' => $request->paper_name,
            'grade' => $request->grade,
            'numeric_mark' => $request->numeric_mark,
            'points' => $points,
            'is_principal_pass' => $request->boolean('is_principal_pass'),
            'academic_year' => $request->academic_year ?? date('Y'),
            'exam_type' => $request->exam_type,
            'exam_type_other' => $request->exam_type === 'Other' ? $request->exam_type_other : null,
            'remarks' => $request->remarks,
        ]);

        // Check for course recommendations after update
        $notificationService = new CourseRecommendationNotificationService();
        $notificationService->checkAndNotifyForStudent($mark->user);

        return redirect()->route('teacher.marks.index')
            ->with('success', 'Mark updated successfully for ' . $mark->user->name);
    }

    /**
     * Delete the specified mark.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher' && $user->account_type !== 'subject_teacher') {
            abort(403, 'Access denied. Only teachers can manage marks.');
        }

        $mark = StudentMark::where('id', $id)
            ->where('school_id', $user->school_id)
            ->where('uploaded_by', $user->id) // Only allow deleting own uploads
            ->firstOrFail();

        $studentName = $mark->user->name;
        $mark->delete();

        return redirect()->route('teacher.marks.index')
            ->with('success', 'Mark deleted successfully for ' . $studentName);
    }
}

