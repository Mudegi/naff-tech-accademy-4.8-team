<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentMark;
use App\Models\Student;
use App\Services\CourseRecommendationNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class StudentMarkController extends Controller
{
    /**
     * Display a listing of the student's marks.
     */
    public function index()
    {
        $user = Auth::user();
        $marks = StudentMark::where('user_id', $user->id)
            ->orderBy('academic_level')
            ->orderBy('subject_name')
            ->get()
            ->groupBy('academic_level');

        // Calculate aggregate points
        $aggregatePoints = $this->calculateAggregatePoints($user->id);
        $principalPasses = StudentMark::where('user_id', $user->id)
            ->where('is_principal_pass', true)
            ->where('points', '>=', 2) // E or better
            ->count();

        return view('student.marks.index', compact('marks', 'aggregatePoints', 'principalPasses'));
    }

    /**
     * Show the form for creating a new mark entry.
     */
    public function create()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Check if student is A Level
        $isALevel = false;
        $combination = null;
        $subjects = [];

        if ($student) {
            // Check if level is A Level or class is S.5/S.6
            $isALevel = $student->level === 'A Level' || preg_match('/S\.?[56]/i', $student->class ?? '');
            
            if ($isALevel) {
                $combination = $student->combination;
                
                if ($combination) {
                    $subjects = $this->parseCombinationToSubjects($combination);
                } else {
                    return redirect()->route('student.marks.index')
                        ->with('error', 'Your subject combination is not set. Please contact your school administrator to set your combination.');
                }
            } else {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Only A Level students (Form 5 and 6) can add marks. Please contact your school administrator if you believe this is an error.');
            }
        } else {
            return redirect()->route('student.marks.index')
                ->with('error', 'Student profile not found. Please contact support.');
        }

        return view('student.marks.create', compact('subjects', 'combination'));
    }

    /**
     * Store a newly created mark entry.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Verify student is A Level
        if (!$student) {
            return redirect()->route('student.marks.index')
                ->with('error', 'Student profile not found. Please contact support.');
        }

        $isALevel = $student->level === 'A Level' || preg_match('/S\.?[56]/i', $student->class ?? '');
        
        if (!$isALevel) {
            return redirect()->route('student.marks.index')
                ->with('error', 'Only A Level students (Form 5 and 6) can add marks.');
        }

        if (empty($student->combination)) {
            return redirect()->route('student.marks.index')
                ->with('error', 'Your subject combination is not set. Please contact your school administrator.');
        }

        // Validate subject is from student's combination
        $subjects = $this->parseCombinationToSubjects($student->combination);
        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($subjects) {
                if (!in_array($value, $subjects)) {
                    $fail('The selected subject must be from your combination: ' . implode(', ', $subjects));
                }
            }],
            'paper_name' => 'nullable|string|max:255',
            'grade' => 'required|string|max:50',
            'numeric_mark' => 'nullable|numeric|min:0|max:100',
            'grade_type' => 'required|in:letter,distinction_credit_pass,numeric',
            'is_principal_pass' => 'boolean',
            'is_essential' => 'boolean',
            'is_relevant' => 'boolean',
            'is_desirable' => 'boolean',
            'academic_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'remarks' => 'nullable|string',
        ]);

        // Auto-set academic level to UACE for A Level students
        $validated['academic_level'] = 'UACE';
        $validated['user_id'] = $user->id;
        $validated['student_id'] = $student->id;
        $validated['school_id'] = $user->school_id;
        $validated['uploaded_by'] = $user->id; // Student uploaded their own mark

        $mark = StudentMark::create($validated);

        // Check for new course recommendations after adding marks
        $notificationService = new CourseRecommendationNotificationService();
        $notificationService->notifyOnMarksUpdate($user);

        return redirect()->route('student.marks.index')
            ->with('success', 'Mark added successfully.');
    }

    /**
     * Show the form for editing the specified mark.
     */
    public function edit(StudentMark $mark)
    {
        // Ensure the mark belongs to the authenticated user
        if ($mark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Check if student is A Level
        $isALevel = false;
        $combination = null;
        $subjects = [];

        if ($student) {
            $isALevel = $student->level === 'A Level' || preg_match('/S\.?[56]/i', $student->class ?? '');
            
            if ($isALevel && $student->combination) {
                $combination = $student->combination;
                $subjects = $this->parseCombinationToSubjects($combination);
            }
        }

        return view('student.marks.edit', compact('mark', 'subjects', 'combination'));
    }

    /**
     * Update the specified mark.
     */
    public function update(Request $request, StudentMark $mark)
    {
        // Ensure the mark belongs to the authenticated user
        if ($mark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Verify student is A Level
        if (!$student) {
            return redirect()->route('student.marks.index')
                ->with('error', 'Student profile not found. Please contact support.');
        }

        $isALevel = $student->level === 'A Level' || preg_match('/S\.?[56]/i', $student->class ?? '');
        
        if (!$isALevel) {
            return redirect()->route('student.marks.index')
                ->with('error', 'Only A Level students (Form 5 and 6) can update marks.');
        }

        // Validate subject is from student's combination
        $subjects = [];
        if ($student->combination) {
            $subjects = $this->parseCombinationToSubjects($student->combination);
        }

        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($subjects) {
                if (!empty($subjects) && !in_array($value, $subjects)) {
                    $fail('The selected subject must be from your combination: ' . implode(', ', $subjects));
                }
            }],
            'paper_name' => 'nullable|string|max:255',
            'grade' => 'required|string|max:50',
            'numeric_mark' => 'nullable|numeric|min:0|max:100',
            'grade_type' => 'required|in:letter,distinction_credit_pass,numeric',
            'is_principal_pass' => 'boolean',
            'is_essential' => 'boolean',
            'is_relevant' => 'boolean',
            'is_desirable' => 'boolean',
            'academic_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'remarks' => 'nullable|string',
        ]);

        // Auto-set academic level to UACE for A Level students
        $validated['academic_level'] = 'UACE';
        // Preserve uploaded_by - don't change it on update
        $validated['uploaded_by'] = $mark->uploaded_by ?? Auth::id();
        
        $mark->update($validated);

        // Check for new course recommendations after updating marks
        $notificationService = new CourseRecommendationNotificationService();
        $notificationService->notifyOnMarksUpdate(Auth::user());

        return redirect()->route('student.marks.index')
            ->with('success', 'Mark updated successfully.');
    }

    /**
     * Remove the specified mark.
     */
    public function destroy(StudentMark $mark)
    {
        // Ensure the mark belongs to the authenticated user
        if ($mark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $mark->delete();

        return redirect()->route('student.marks.index')
            ->with('success', 'Mark deleted successfully.');
    }

    /**
     * Show the import form
     */
    public function showImport()
    {
        return view('student.marks.import');
    }

    /**
     * Handle bulk marks import from Excel/CSV
     */
    public function import(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $validator = Validator::make($request->all(), [
            'marks_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

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
            
            foreach ($rows as $row) {
                $rowNumber++;
                
                // Expected format: academic_level, subject_name, paper_name, grade, numeric_mark, grade_type, is_principal_pass, is_essential, is_relevant, is_desirable, academic_year
                $data = [
                    'academic_level' => trim($row[0] ?? ''),
                    'subject_name' => trim($row[1] ?? ''),
                    'paper_name' => trim($row[2] ?? ''),
                    'grade' => trim($row[3] ?? ''),
                    'numeric_mark' => !empty($row[4]) ? (float)$row[4] : null,
                    'grade_type' => trim($row[5] ?? 'letter'),
                    'is_principal_pass' => !empty($row[6]) && strtolower(trim($row[6])) === 'yes',
                    'is_essential' => !empty($row[7]) && strtolower(trim($row[7])) === 'yes',
                    'is_relevant' => !empty($row[8]) && strtolower(trim($row[8])) === 'yes',
                    'is_desirable' => !empty($row[9]) && strtolower(trim($row[9])) === 'yes',
                    'academic_year' => !empty($row[10]) ? (int)$row[10] : date('Y'),
                ];

                // Validate required fields
                if (empty($data['academic_level']) || empty($data['subject_name']) || empty($data['grade'])) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Missing required fields (academic_level, subject_name, or grade)";
                    continue;
                }

                // Validate academic level
                if (!in_array(strtoupper($data['academic_level']), ['UACE', 'UCE'])) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Invalid academic level. Must be UACE or UCE";
                    continue;
                }

                // Validate grade type
                if (!in_array($data['grade_type'], ['letter', 'distinction_credit_pass', 'numeric'])) {
                    $data['grade_type'] = 'letter';
                }

                try {
                    $mark = StudentMark::create([
                        'user_id' => $user->id,
                        'student_id' => $student?->id,
                        'school_id' => $user->school_id,
                        'uploaded_by' => $user->id, // Student uploaded their own mark
                        'academic_level' => strtoupper($data['academic_level']),
                        'subject_name' => $data['subject_name'],
                        'paper_name' => $data['paper_name'] ?: null,
                        'grade' => $data['grade'],
                        'numeric_mark' => $data['numeric_mark'],
                        'grade_type' => $data['grade_type'],
                        'is_principal_pass' => $data['is_principal_pass'],
                        'is_essential' => $data['is_essential'],
                        'is_relevant' => $data['is_relevant'],
                        'is_desirable' => $data['is_desirable'],
                        'academic_year' => $data['academic_year'],
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
                }
            }

            $message = "Import completed! {$results['success']} marks imported successfully.";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} marks failed to import.";
            }

            // Check for new course recommendations after bulk import
            $notificationService = new CourseRecommendationNotificationService();
            $notificationService->notifyOnMarksUpdate($user);

            return redirect()->route('student.marks.index')
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
                // Get data from columns A to K (11 columns)
                for ($col = 'A'; $col <= 'K'; $col++) {
                    $cell = $worksheet->getCell($col . $row);
                    $cellValue = $cell->getValue();
                    $rowData[] = $cellValue ?? '';
                }
                
                // Only add row if it has at least academic_level, subject_name, or grade
                if (!empty($rowData[0]) || !empty($rowData[1]) || !empty($rowData[3])) {
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
            'Content-Disposition' => 'attachment; filename="marks_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Academic Level (UACE/UCE)',
                'Subject Name',
                'Paper Name',
                'Grade',
                'Numeric Mark (0-100)',
                'Grade Type (letter/distinction_credit_pass/numeric)',
                'Principal Pass (Yes/No)',
                'Essential (Yes/No)',
                'Relevant (Yes/No)',
                'Desirable (Yes/No)',
                'Academic Year'
            ]);

            // Example rows
            fputcsv($file, [
                'UACE',
                'Mathematics',
                'Paper 1',
                'A',
                '',
                'letter',
                'Yes',
                'Yes',
                '',
                '',
                '2024'
            ]);

            fputcsv($file, [
                'UACE',
                'Physics',
                '',
                'B',
                '',
                'letter',
                'Yes',
                'Yes',
                '',
                '',
                '2024'
            ]);

            fputcsv($file, [
                'UACE',
                'Chemistry',
                '',
                'Distinction 1',
                '',
                'distinction_credit_pass',
                'Yes',
                'Yes',
                '',
                '',
                '2024'
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
                'Academic Level (UACE/UCE)',
                'Subject Name',
                'Paper Name',
                'Grade',
                'Numeric Mark (0-100)',
                'Grade Type (letter/distinction_credit_pass/numeric)',
                'Principal Pass (Yes/No)',
                'Essential (Yes/No)',
                'Relevant (Yes/No)',
                'Desirable (Yes/No)',
                'Academic Year'
            ];
            
            $sheet->fromArray($headers, null, 'A1');
            
            // Add example rows
            $examples = [
                ['UACE', 'Mathematics', 'Paper 1', 'A', '', 'letter', 'Yes', 'Yes', '', '', '2024'],
                ['UACE', 'Physics', '', 'B', '', 'letter', 'Yes', 'Yes', '', '', '2024'],
                ['UACE', 'Chemistry', '', 'Distinction 1', '', 'distinction_credit_pass', 'Yes', 'Yes', '', '', '2024'],
            ];
            
            $sheet->fromArray($examples, null, 'A2');
            
            // Auto-size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Style header row
            $sheet->getStyle('A1:K1')->getFont()->setBold(true);
            $sheet->getStyle('A1:K1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            
            $writer = new Xlsx($spreadsheet);
            
            $filename = 'marks_import_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating template: ' . $e->getMessage());
        }
    }

    /**
     * Calculate aggregate points for a student.
     * Uses the best 3 principal passes for UACE.
     */
    private function calculateAggregatePoints($userId)
    {
        $principalPasses = StudentMark::where('user_id', $userId)
            ->where('academic_level', 'UACE')
            ->where('is_principal_pass', true)
            ->orderByDesc('points')
            ->take(3)
            ->get();

        if ($principalPasses->count() < 2) {
            return 0; // Need at least 2 principal passes
        }

        return $principalPasses->sum('points');
    }

    /**
     * Parse combination code to subject names.
     * Example: "PCM/ICT" -> ["Physics", "Chemistry", "Mathematics", "Information and Communication Technology"]
     */
    private function parseCombinationToSubjects($combination)
    {
        // Subject code mapping
        $subjectMap = [
            'P' => 'Physics',
            'C' => 'Chemistry',
            'M' => 'Mathematics',
            'B' => 'Biology',
            'E' => 'Economics',
            'A' => 'Agriculture',
            'H' => 'History',
            'G' => 'Geography',
            'L' => 'Literature in English',
            'ICT' => 'Information and Communication Technology',
            'I' => 'Information and Communication Technology', // Alternative
            'GSC' => 'General Studies',
            'GS' => 'General Studies',
            'ENT' => 'Entrepreneurship',
            'FST' => 'Food Science and Technology',
            'ART' => 'Art',
            'CRE' => 'Christian Religious Education',
            'IRE' => 'Islamic Religious Education',
        ];

        $subjects = [];
        
        // Split by / to separate main subjects from subsidiary
        $parts = explode('/', $combination);
        $mainSubjects = trim($parts[0] ?? '');
        $subsidiary = trim($parts[1] ?? '');

        // Parse main subjects (usually 3 letters like PCM, BCM, etc.)
        if (!empty($mainSubjects)) {
            $mainSubjects = strtoupper($mainSubjects);
            // Split into individual letters
            $letters = str_split($mainSubjects);
            foreach ($letters as $letter) {
                if (isset($subjectMap[$letter])) {
                    $subjects[] = $subjectMap[$letter];
                }
            }
        }

        // Add subsidiary subject
        if (!empty($subsidiary)) {
            $subsidiary = strtoupper($subsidiary);
            if (isset($subjectMap[$subsidiary])) {
                $subjects[] = $subjectMap[$subsidiary];
            } else {
                // If not found in map, try to match common patterns
                $subsidiaryLower = strtolower($subsidiary);
                if (strpos($subsidiaryLower, 'ict') !== false) {
                    $subjects[] = 'Information and Communication Technology';
                } elseif (strpos($subsidiaryLower, 'general') !== false || strpos($subsidiaryLower, 'gsc') !== false) {
                    $subjects[] = 'General Studies';
                } else {
                    // If not found, use the original value (might be a full name)
                    $subjects[] = ucwords(strtolower($subsidiary));
                }
            }
        }

        // Remove duplicates and return
        return array_unique($subjects);
    }
}
