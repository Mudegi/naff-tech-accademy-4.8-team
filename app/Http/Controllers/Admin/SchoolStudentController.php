<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class SchoolStudentController extends Controller
{
    /**
     * Check if user is authorized to manage students
     */
    private function checkAuthorization()
    {
        $user = Auth::user();
        
        if (!$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can manage students.');
        }
    }

    /**
     * Available academic levels for students.
     */
    private function getStudentLevels(): array
    {
        return [
            'O Level' => 'O Level (UCE)',
            'A Level' => 'A Level (UACE)',
        ];
    }

    /**
     * Normalize level input from import/template.
     */
    private function normalizeLevel(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim($value));

        $oLevelMatches = ['o level', 'olevel', 'o-level', 'uce', 'ordinary level', 'ordinary'];
        $aLevelMatches = ['a level', 'alevel', 'a-level', 'uace', 'advanced level', 'advanced'];

        if (in_array($normalized, $oLevelMatches, true)) {
            return 'O Level';
        }

        if (in_array($normalized, $aLevelMatches, true)) {
            return 'A Level';
        }

        return null;
    }

    /**
     * Display a listing of school students
     */
    public function index(Request $request)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Get all classes for the school (system classes OR school-specific classes)
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id') // System classes
                      ->orWhere('school_id', $school->id); // School-specific classes
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = User::where('school_id', $school->id)
            ->where('account_type', 'student')
            ->with(['student', 'student.class']);

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone_number', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('student', function($sq) use ($searchTerm) {
                      $sq->where('registration_number', 'like', '%' . $searchTerm . '%')
                         ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                         ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply class filter
        if ($request->filled('class_id')) {
            $query->whereHas('student', function($studentQuery) use ($request) {
                $studentQuery->where('class_id', $request->class_id);
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->whereHas('student', function($studentQuery) use ($request) {
                $studentQuery->where('level', $request->level);
            });
        }

        // Get levels for dropdown
        $levels = $this->getStudentLevels();

        // If no filters, group by class, otherwise show flat list
        if (!$request->hasAny(['search', 'class_id', 'status', 'level'])) {
            // Group students by class
            $allStudents = $query->get();
            $studentsByClass = $allStudents->groupBy(function($user) {
                return $user->student->class_id ?? 'unassigned';
            });
            // Also pass an empty paginator for $students to avoid undefined variable in view
            $students = collect([]);
            return view('admin.school.students.index', compact('studentsByClass', 'students', 'classes', 'levels'));
        } else {
            // Show filtered flat list
            $students = $query->latest()->paginate(15);
            $students->appends($request->query());
            // Also pass an empty $studentsByClass for view compatibility
            $studentsByClass = collect([]);
            return view('admin.school.students.index', compact('students', 'studentsByClass', 'classes', 'levels'));
        }
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Load system classes (available to all schools) OR school-specific classes
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id') // System classes
                      ->orWhere('school_id', $school->id); // School-specific classes
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $levels = $this->getStudentLevels();

        return view('admin.school.students.create', compact('classes', 'levels'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Check if combination is required (A Level or class S.5/S.6)
        $requiresCombination = $request->level === 'A Level' || 
                               (preg_match('/S\.?[56]/i', $request->class ?? ''));

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:8',
            'registration_number' => 'required|string|max:255|unique:students,registration_number',
            'class' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'level' => 'required|in:O Level,A Level',
            'combination' => $requiresCombination ? 'required|string|max:255' : 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user account
        $userAccount = User::create([
            'name' => trim($request->first_name . ' ' . ($request->middle_name ?? '') . ' ' . $request->last_name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'account_type' => 'student',
            'school_id' => $school->id,
            'is_active' => $request->has('is_active'),
        ]);

        // Find class_id from class name (check both system and school-specific classes)
        $classId = null;
        if ($request->class) {
            $classObj = SchoolClass::withoutGlobalScope('school')
                ->where(function($query) use ($school) {
                    $query->whereNull('school_id') // System classes
                          ->orWhere('school_id', $school->id); // School-specific classes
                })
                ->where('name', $request->class)
                ->first();
            $classId = $classObj ? $classObj->id : null;
        }

        // Create student profile
        $student = Student::create([
            'user_id' => $userAccount->id,
            'school_id' => $school->id,
            'account_type' => 'student',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'registration_number' => $request->registration_number,
            'class' => $request->class,
            'class_id' => $classId,
            'date_of_birth' => $request->date_of_birth,
            'school_name' => $school->name,
            'is_active' => $request->has('is_active'),
            'level' => $request->level,
            'combination' => $request->combination,
        ]);

        // Store credentials in session for display
        $request->session()->flash('new_student_credentials', [
            'name' => $userAccount->name,
            'email' => $userAccount->email,
            'phone_number' => $userAccount->phone_number,
            'password' => $request->password,
            'registration_number' => $student->registration_number,
        ]);

        return redirect()->route('admin.school.students.index')
            ->with('success', 'Student created successfully! Please note the login credentials below.')
            ->with('show_credentials', true);
    }

    /**
     * Show the form for editing a student
     */
    public function edit($id)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $studentUser = User::where('school_id', $school->id)
            ->where('account_type', 'student')
            ->with('student')
            ->findOrFail($id);

        // Load system classes (available to all schools) OR school-specific classes
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id') // System classes
                      ->orWhere('school_id', $school->id); // School-specific classes
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $levels = $this->getStudentLevels();

        return view('admin.school.students.edit', compact('studentUser', 'classes', 'levels'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, $id)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $studentUser = User::where('school_id', $school->id)
            ->where('account_type', 'student')
            ->with('student')
            ->findOrFail($id);

        // Check if combination is required (A Level or class S.5/S.6)
        $requiresCombination = $request->level === 'A Level' || 
                               (preg_match('/S\.?[56]/i', $request->class ?? ''));

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $studentUser->id,
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $studentUser->id,
            'password' => 'nullable|string|min:8',
            'registration_number' => 'nullable|string|max:255|unique:students,registration_number,' . ($studentUser->student->id ?? 0),
            'class' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'level' => 'required|in:O Level,A Level',
            'combination' => $requiresCombination ? 'required|string|max:255' : 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user account
        $studentUser->name = trim($request->first_name . ' ' . ($request->middle_name ?? '') . ' ' . $request->last_name);
        $studentUser->email = $request->email;
        $studentUser->phone_number = $request->phone_number;
        $studentUser->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $studentUser->password = Hash::make($request->password);
            if ($request->has('show_password')) {
                $request->session()->flash('updated_student_credentials', [
                    'name' => $studentUser->name,
                    'email' => $studentUser->email,
                    'password' => $request->password,
                ]);
            }
        }

        $studentUser->save();

        // Update student profile
        if ($studentUser->student) {
            // Find class_id from class name (check both system and school-specific classes)
            $classId = null;
            if ($request->class) {
                $classObj = SchoolClass::withoutGlobalScope('school')
                    ->where(function($query) use ($school) {
                        $query->whereNull('school_id') // System classes
                              ->orWhere('school_id', $school->id); // School-specific classes
                    })
                    ->where('name', $request->class)
                    ->first();
                $classId = $classObj ? $classObj->id : null;
            }

            $studentUser->student->first_name = $request->first_name;
            $studentUser->student->last_name = $request->last_name;
            $studentUser->student->middle_name = $request->middle_name;
            $studentUser->student->registration_number = $request->registration_number;
            $studentUser->student->class = $request->class;
            $studentUser->student->class_id = $classId;
            $studentUser->student->date_of_birth = $request->date_of_birth;
            $studentUser->student->is_active = $request->has('is_active');
            $studentUser->student->level = $request->level;
            $studentUser->student->combination = $request->combination;
            $studentUser->student->save();
        }

        $message = 'Student updated successfully!';
        if ($request->filled('password') && $request->has('show_password')) {
            $message .= ' New password displayed below.';
        }

        return redirect()->route('admin.school.students.index')
            ->with('success', $message)
            ->with('show_updated_credentials', $request->filled('password') && $request->has('show_password'));
    }

    /**
     * Show the import form
     */
    public function showImport()
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Get all active classes for dropdown (system classes OR school-specific classes)
        $classes = SchoolClass::withoutGlobalScope('school')
            ->where(function($query) use ($school) {
                $query->whereNull('school_id') // System classes
                      ->orWhere('school_id', $school->id); // School-specific classes
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.school.students.import', compact('classes'));
    }

    /**
     * Handle bulk student import from CSV
     */
    public function import(Request $request)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'generate_passwords' => 'boolean',
            'class_id' => 'nullable|exists:school_classes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('csv_file');
        $generatePasswords = $request->has('generate_passwords');
        $assignToClassId = $request->input('class_id'); // Get class_id if provided
        $fileExtension = strtolower($file->getClientOriginalExtension());
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'credentials' => [],
        ];

        try {
            $rows = [];
            
            // Check if file is Excel or CSV
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // Process Excel file
                $rows = $this->readExcelFile($file);
            } else {
                // Process CSV file
                $rows = $this->readCsvFile($file);
            }
            
            $rowNumber = 1;
            
            foreach ($rows as $row) {
                $rowNumber++;
                
                // Map columns (adjust based on your format)
                // Expected format: first_name, last_name, middle_name, email, phone_number, registration_number, class, date_of_birth, level, combination
                $data = [
                    'first_name' => trim($row[0] ?? ''),
                    'last_name' => trim($row[1] ?? ''),
                    'middle_name' => trim($row[2] ?? ''),
                    'email' => trim($row[3] ?? ''),
                    'phone_number' => trim($row[4] ?? ''),
                    'registration_number' => trim($row[5] ?? ''),
                    'class' => trim($row[6] ?? ''),
                    'date_of_birth' => trim($row[7] ?? ''),
                    'level' => trim($row[8] ?? ''),
                    'combination' => trim($row[9] ?? ''),
                ];

                // Validate required fields
                if (empty($data['first_name']) || empty($data['last_name']) || empty($data['phone_number']) || empty($data['level'])) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Missing required fields (first_name, last_name, phone_number, or level)";
                    continue;
                }

                $normalizedLevel = $this->normalizeLevel($data['level']);
                if (!$normalizedLevel) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Invalid level '{$data['level']}'. Use 'O Level' or 'A Level'.";
                    continue;
                }
                $data['level'] = $normalizedLevel;

                // Check if combination is required (A Level or class S.5/S.6)
                $requiresCombination = $data['level'] === 'A Level' || 
                                       (preg_match('/S\.?[56]/i', $data['class'] ?? ''));
                
                if ($requiresCombination && empty($data['combination'])) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Combination is required for A Level students or students in S.5/S.6.";
                    continue;
                }

                // Generate password if needed
                $password = $generatePasswords ? \Illuminate\Support\Str::random(12) : \Illuminate\Support\Str::random(8);

                // Check for duplicates
                $existingUser = User::where('phone_number', $data['phone_number'])
                    ->orWhere(function($q) use ($data) {
                        if (!empty($data['email'])) {
                            $q->where('email', $data['email']);
                        }
                    })
                    ->first();

                if ($existingUser) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Student with phone {$data['phone_number']} or email {$data['email']} already exists";
                    continue;
                }

                // Check for duplicate registration number
                if (!empty($data['registration_number'])) {
                    $existingStudent = Student::where('registration_number', $data['registration_number'])->first();
                    if ($existingStudent) {
                        $results['failed']++;
                        $results['errors'][] = "Row $rowNumber: Registration number {$data['registration_number']} already exists";
                        continue;
                    }
                }

                try {
                    // Create user account
                    $userAccount = User::create([
                        'name' => trim($data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name']),
                        'email' => !empty($data['email']) ? $data['email'] : null,
                        'phone_number' => $data['phone_number'],
                        'password' => Hash::make($password),
                        'account_type' => 'student',
                        'school_id' => $school->id,
                        'is_active' => true,
                    ]);

                    // Determine class_id: use form selection if provided, otherwise use CSV class data
                    $finalClassId = $assignToClassId; // Use form-selected class if provided
                    $finalClassName = null;
                    
                    if ($assignToClassId) {
                        // Get class name from class_id
                        $classObj = SchoolClass::find($assignToClassId);
                        $finalClassName = $classObj ? $classObj->name : null;
                    } elseif (!empty($data['class'])) {
                        // Try to find class by name from CSV (check both system and school-specific classes)
                        $classObj = SchoolClass::withoutGlobalScope('school')
                            ->where(function($query) use ($school) {
                                $query->whereNull('school_id') // System classes
                                      ->orWhere('school_id', $school->id); // School-specific classes
                            })
                            ->where('name', $data['class'])
                            ->first();
                        if ($classObj) {
                            $finalClassId = $classObj->id;
                            $finalClassName = $classObj->name;
                        } else {
                            // Just use the text class name if no matching class found
                            $finalClassName = $data['class'];
                        }
                    }

                    // Create student profile
                    $student = Student::create([
                        'user_id' => $userAccount->id,
                        'school_id' => $school->id,
                        'account_type' => 'student',
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'middle_name' => !empty($data['middle_name']) ? $data['middle_name'] : null,
                        'registration_number' => !empty($data['registration_number']) ? $data['registration_number'] : null,
                        'class' => $finalClassName,
                        'class_id' => $finalClassId, // Assign class_id
                        'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                        'school_name' => $school->name,
                        'is_active' => true,
                        'level' => $data['level'],
                        'combination' => !empty($data['combination']) ? $data['combination'] : null,
                    ]);

                    $results['success']++;
                    $results['credentials'][] = [
                        'name' => $userAccount->name,
                        'email' => $userAccount->email,
                        'phone_number' => $userAccount->phone_number,
                        'password' => $password,
                        'registration_number' => $student->registration_number,
                    ];
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
                }
            }

            // Store results in session
            $request->session()->flash('import_results', $results);

            $message = "Import completed! {$results['success']} students imported successfully.";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} students failed to import.";
            }

            return redirect()->route('admin.school.students.import')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Read CSV file and return rows
     */
    private function readCsvFile($file)
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        
        fclose($handle);
        return $rows;
    }

    /**
     * Read Excel file and return rows
     */
    private function readExcelFile($file)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            
            // Get the highest row number
            $highestRow = $worksheet->getHighestRow();
            
            // Start from row 2 to skip header
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                // Get data from columns A to H (8 columns)
                for ($col = 'A'; $col <= 'H'; $col++) {
                    $cell = $worksheet->getCell($col . $row);
                    $cellValue = $cell->getValue();
                    
                    // Handle date cells (column H is date of birth)
                    if ($col == 'H' && !empty($cellValue)) {
                        try {
                            if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                                $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                                $cellValue = $dateValue->format('Y-m-d');
                            } elseif (is_numeric($cellValue)) {
                                // Try to convert numeric date
                                $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                                $cellValue = $dateValue->format('Y-m-d');
                            }
                        } catch (\Exception $e) {
                            // If date conversion fails, use the value as is
                        }
                    }
                    
                    $rowData[] = $cellValue ?? '';
                }
                // Only add row if it has at least first name, last name, or phone
                if (!empty($rowData[0]) || !empty($rowData[1]) || !empty($rowData[4])) {
                    $rows[] = $rowData;
                }
            }
            
            return $rows;
        } catch (\Exception $e) {
            throw new \Exception('Error reading Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Download template (CSV or Excel based on request)
     */
    public function downloadTemplate(Request $request)
    {
        $this->checkAuthorization();
        
        $format = $request->get('format', 'csv'); // csv or excel
        
        if ($format === 'excel') {
            return $this->downloadExcelTemplate();
        } else {
            return $this->downloadCsvTemplate();
        }
    }

    /**
     * Download CSV template
     */
    private function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'First Name',
                'Last Name',
                'Middle Name',
                'Email',
                'Phone Number',
                'Registration Number',
                'Class',
                'Date of Birth (YYYY-MM-DD)',
                'Level (O Level or A Level)',
                'Combination (Required for A Level or S.5/S.6, e.g., PCM/ICT)'
            ]);

            // Example rows
            fputcsv($file, [
                'John',
                'Doe',
                'James',
                'john.doe@example.com',
                '0770123456',
                'STD2024001',
                'S.3',
                '2006-05-15',
                'O Level',
                ''
            ]);

            fputcsv($file, [
                'Jane',
                'Smith',
                '',
                'jane.smith@example.com',
                '0770123457',
                'STD2024002',
                'S.5',
                '2005-08-20',
                'A Level',
                'PCM/ICT'
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
                'First Name',
                'Last Name',
                'Middle Name',
                'Email',
                'Phone Number',
                'Registration Number',
                'Class',
                'Date of Birth (YYYY-MM-DD)',
                'Level (O Level or A Level)',
                'Combination (Required for A Level or S.5/S.6, e.g., PCM/ICT)'
            ];
            
            $sheet->fromArray($headers, null, 'A1');
            
            // Add example rows
            $examples = [
                ['John', 'Doe', 'James', 'john.doe@example.com', '0770123456', 'STD2024001', 'S.3', '2006-05-15', 'O Level', ''],
                ['Jane', 'Smith', '', 'jane.smith@example.com', '0770123457', 'STD2024002', 'S.5', '2005-08-20', 'A Level', 'PCM/ICT']
            ];
            
            $sheet->fromArray($examples, null, 'A2');
            
            // Auto-size columns
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Style header row
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:J1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            
            $writer = new Xlsx($spreadsheet);
            
            $filename = 'student_import_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            // Fallback to CSV if Excel generation fails
            return $this->downloadCsvTemplate();
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy($id)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        $studentUser = User::where('school_id', $school->id)
            ->where('account_type', 'student')
            ->findOrFail($id);

        // Delete student profile (if exists)
        if ($studentUser->student) {
            $studentUser->student->delete();
        }

        // Delete user account
        $studentUser->delete();

        return redirect()->route('admin.school.students.index')
            ->with('success', 'Student deleted successfully!');
    }
}

