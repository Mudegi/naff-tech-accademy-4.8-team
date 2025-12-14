<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniversityCutOff;
use App\Models\University;
use App\Services\CourseRecommendationNotificationService;
use App\Services\UniversityCutOffScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UniversityCutOffController extends Controller
{
    /**
     * Check if user is super admin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can manage university cut-offs.');
        }
    }

    /**
     * Display a listing of university cut-offs.
     */
    public function index(Request $request)
    {
        $this->checkSuperAdmin();

        $query = UniversityCutOff::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('university_name', 'like', "%$search%")
                  ->orWhere('course_name', 'like', "%$search%")
                  ->orWhere('faculty', 'like', "%$search%");
            });
        }

        // Filter by university
        if ($request->filled('university')) {
            $query->where('university_name', $request->university);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = $request->get('per_page', 15);
        $cutOffs = $query->orderBy('course_name')->orderBy('university_name')->paginate($perPage);
        $cutOffs->appends($request->query());

        // Get unique universities and years for filters
        $universities = UniversityCutOff::distinct()->pluck('university_name')->sort()->values();
        $academicYears = UniversityCutOff::distinct()->pluck('academic_year')->sort()->values();

        return view('admin.university-cut-offs.index', compact('cutOffs', 'universities', 'academicYears'));
    }

    /**
     * Show the form for creating a new university cut-off.
     */
    public function create()
    {
        $this->checkSuperAdmin();
        return view('admin.university-cut-offs.create');
    }

    /**
     * Store a newly created university cut-off.
     */
    public function store(Request $request)
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'university_name' => 'required|string|max:255',
            'university_code' => 'nullable|string|max:50',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'course_description' => 'nullable|string',
            'faculty' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'minimum_principal_passes' => 'required|integer|min:1|max:5',
            'minimum_aggregate_points' => 'nullable|numeric|min:0|max:100',
            'program_category' => 'required|in:stem,other,both',
            'cut_off_format' => 'required|in:standard,makerere,kyambogo,custom',
            'cut_off_points' => 'nullable|numeric|min:0|max:100',
            'cut_off_points_male' => 'nullable|numeric|min:0|max:100',
            'cut_off_points_female' => 'nullable|numeric|min:0|max:100',
            'cut_off_structure' => 'nullable|json',
            'academic_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'essential_subjects' => 'nullable|string',
            'relevant_subjects' => 'nullable|string',
            'desirable_subjects' => 'nullable|string',
            'additional_requirements' => 'nullable|string',
            'duration_years' => 'nullable|integer|min:1|max:10',
            'degree_type' => 'required|in:bachelor,diploma,certificate,masters,phd',
            'is_active' => 'boolean',
        ]);

        // Handle cut_off_structure JSON
        if (isset($validated['cut_off_structure']) && is_string($validated['cut_off_structure'])) {
            $decoded = json_decode($validated['cut_off_structure'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['cut_off_structure'] = $decoded;
            } else {
                return redirect()->back()
                    ->withErrors(['cut_off_structure' => 'Invalid JSON format. Please check your JSON syntax.'])
                    ->withInput();
            }
        }

        // Validation: Ensure at least one cut-off point is provided based on format
        if ($validated['cut_off_format'] === 'kyambogo') {
            // Kyambogo: Simple single cut-off point (no gender differentiation)
            if (empty($validated['cut_off_points'])) {
                return redirect()->back()
                    ->withErrors(['cut_off_points' => 'Cut-off points are required for Kyambogo format.'])
                    ->withInput();
            }
        } elseif ($validated['cut_off_format'] === 'custom') {
            if (empty($validated['cut_off_structure'])) {
                return redirect()->back()
                    ->withErrors(['cut_off_structure' => 'Custom cut-off structure (JSON) is required for custom format.'])
                    ->withInput();
            }
        } elseif ($validated['cut_off_format'] === 'makerere') {
            if ($validated['program_category'] === 'stem') {
                if (empty($validated['cut_off_points_male']) && empty($validated['cut_off_points_female']) && empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points_male' => 'For Makerere STEM programs, you must provide at least one cut-off point (male, female, or general).'])
                        ->withInput();
                }
            } else {
                if (empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points' => 'Cut-off points are required for Makerere non-STEM programs.'])
                        ->withInput();
                }
            }
        } else {
            // Standard format
            if ($validated['program_category'] === 'stem') {
                if (empty($validated['cut_off_points_male']) && empty($validated['cut_off_points_female']) && empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points_male' => 'For STEM programs, you must provide at least one cut-off point (male, female, or general).'])
                        ->withInput();
                }
            } else {
                if (empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points' => 'Cut-off points are required for non-STEM programs.'])
                        ->withInput();
                }
            }
        }

        // Convert textarea input (newline-separated) to array for subjects
        $validated = $this->processSubjectRequirements($validated);

        $cutOff = UniversityCutOff::create($validated);

        // Notify students about new matching courses
        $notificationService = new CourseRecommendationNotificationService();
        $notificationService->checkAndNotifyForNewCourses($cutOff);

        return redirect()->route('admin.university-cut-offs.index')
            ->with('success', 'University cut-off created successfully.');
    }

    /**
     * Display the specified university cut-off.
     */
    public function show(UniversityCutOff $universityCutOff)
    {
        $this->checkSuperAdmin();
        return view('admin.university-cut-offs.show', compact('universityCutOff'));
    }

    /**
     * Show the form for editing the specified university cut-off.
     */
    public function edit(UniversityCutOff $universityCutOff)
    {
        $this->checkSuperAdmin();
        return view('admin.university-cut-offs.edit', compact('universityCutOff'));
    }

    /**
     * Update the specified university cut-off.
     */
    public function update(Request $request, UniversityCutOff $universityCutOff)
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'university_name' => 'required|string|max:255',
            'university_code' => 'nullable|string|max:50',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'course_description' => 'nullable|string',
            'faculty' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'minimum_principal_passes' => 'required|integer|min:1|max:5',
            'minimum_aggregate_points' => 'nullable|numeric|min:0|max:100',
            'program_category' => 'required|in:stem,other,both',
            'cut_off_format' => 'required|in:standard,makerere,kyambogo,custom',
            'cut_off_points' => 'nullable|numeric|min:0|max:100',
            'cut_off_points_male' => 'nullable|numeric|min:0|max:100',
            'cut_off_points_female' => 'nullable|numeric|min:0|max:100',
            'cut_off_structure' => 'nullable|json',
            'academic_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'essential_subjects' => 'nullable|string',
            'relevant_subjects' => 'nullable|string',
            'desirable_subjects' => 'nullable|string',
            'additional_requirements' => 'nullable|string',
            'duration_years' => 'nullable|integer|min:1|max:10',
            'degree_type' => 'required|in:bachelor,diploma,certificate,masters,phd',
            'is_active' => 'boolean',
        ]);

        // Handle cut_off_structure JSON
        if (isset($validated['cut_off_structure']) && is_string($validated['cut_off_structure'])) {
            $decoded = json_decode($validated['cut_off_structure'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['cut_off_structure'] = $decoded;
            } else {
                return redirect()->back()
                    ->withErrors(['cut_off_structure' => 'Invalid JSON format. Please check your JSON syntax.'])
                    ->withInput();
            }
        }

        // Validation: Ensure at least one cut-off point is provided based on format
        if ($validated['cut_off_format'] === 'kyambogo') {
            // Kyambogo: Simple single cut-off point (no gender differentiation)
            if (empty($validated['cut_off_points'])) {
                return redirect()->back()
                    ->withErrors(['cut_off_points' => 'Cut-off points are required for Kyambogo format.'])
                    ->withInput();
            }
        } elseif ($validated['cut_off_format'] === 'custom') {
            if (empty($validated['cut_off_structure'])) {
                return redirect()->back()
                    ->withErrors(['cut_off_structure' => 'Custom cut-off structure (JSON) is required for custom format.'])
                    ->withInput();
            }
        } elseif ($validated['cut_off_format'] === 'makerere') {
            if ($validated['program_category'] === 'stem') {
                if (empty($validated['cut_off_points_male']) && empty($validated['cut_off_points_female']) && empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points_male' => 'For Makerere STEM programs, you must provide at least one cut-off point (male, female, or general).'])
                        ->withInput();
                }
            } else {
                if (empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points' => 'Cut-off points are required for Makerere non-STEM programs.'])
                        ->withInput();
                }
            }
        } else {
            // Standard format
            if ($validated['program_category'] === 'stem') {
                if (empty($validated['cut_off_points_male']) && empty($validated['cut_off_points_female']) && empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points_male' => 'For STEM programs, you must provide at least one cut-off point (male, female, or general).'])
                        ->withInput();
                }
            } else {
                if (empty($validated['cut_off_points'])) {
                    return redirect()->back()
                        ->withErrors(['cut_off_points' => 'Cut-off points are required for non-STEM programs.'])
                        ->withInput();
                }
            }
        }

        $universityCutOff->update($validated);

        // Notify students if cut-off points changed and they now qualify
        if ($universityCutOff->wasChanged('cut_off_points') || $universityCutOff->wasChanged('cut_off_points_male') || 
            $universityCutOff->wasChanged('cut_off_points_female') || $universityCutOff->wasChanged('cut_off_structure') || 
            $universityCutOff->wasChanged('is_active')) {
            $notificationService = new CourseRecommendationNotificationService();
            $notificationService->checkAndNotifyForNewCourses($universityCutOff);
        }

        return redirect()->route('admin.university-cut-offs.index')
            ->with('success', 'University cut-off updated successfully.');
    }

    /**
     * Remove the specified university cut-off.
     */
    public function destroy(UniversityCutOff $universityCutOff)
    {
        $this->checkSuperAdmin();
        $universityCutOff->delete();

        return redirect()->route('admin.university-cut-offs.index')
            ->with('success', 'University cut-off deleted successfully.');
    }

    /**
     * Show file upload import form
     */
    public function showImportForm()
    {
        $this->checkSuperAdmin();
        return view('admin.university-cut-offs.import-upload');
    }

    /**
     * Download CSV template for importing university programs
     */
    public function downloadTemplate(Request $request)
    {
        $this->checkSuperAdmin();

        $university = $request->get('university', 'Kyambogo University');
        $isMakerere = ($university === 'Makerere University');
        
        $filename = $isMakerere ? 'makerere_programs_template.csv' : 'kyambogo_programs_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = $isMakerere ? [
            'course_name',
            'course_code',
            'cut_off_points',
            'cut_off_points_male',
            'cut_off_points_female'
        ] : [
            'course_name',
            'course_code',
            'cut_off_points',
            'degree_type',
            'minimum_principal_passes',
            'academic_year',
            'duration_years',
            'faculty',
            'department'
        ];

        $callback = function() use ($columns, $isMakerere) {
            $file = fopen('php://output', 'w');
            
            // Write header row
            fputcsv($file, $columns);
            
            if ($isMakerere) {
                // Makerere format examples - matches their official PDF format
                // Format: Programme Name, Programme Code, General Points, Male Points, Female Points
                fputcsv($file, [
                    'Bachelor of Medicine and Surgery',
                    'MED',
                    '39.8',
                    '43.3',
                    '36.4'
                ]);
                
                fputcsv($file, [
                    'Bachelor of Dental Surgery',
                    'DEN',
                    '39.3',
                    '42.8',
                    '35.9'
                ]);
                
                fputcsv($file, [
                    'Bachelor of Science in Civil Engineering',
                    'CIV',
                    '14.2',
                    '15.5',
                    '13.0'
                ]);
            } else {
                // Kyambogo format examples (single cut-off)
                // Order: Program Name, Program Code, Cut Off Points, Degree Type, Min Principal Passes, Academic Year, Duration, Faculty, Department
                fputcsv($file, [
                    'Bachelor of Education',
                    'BED',
                    '20.5',
                    'bachelor',
                    '2',
                    date('Y'),
                    '3',
                    'Faculty of Education',
                    'Department of Educational Foundations'
                ]);
                
                fputcsv($file, [
                    'Bachelor of Science',
                    'BSC',
                    '18.0',
                    'bachelor',
                    '2',
                    date('Y'),
                    '3',
                    'Faculty of Science',
                    ''
                ]);
                
                fputcsv($file, [
                    'Diploma in Education',
                    'DIP/ED',
                    '25.0',
                    'diploma',
                    '1',
                    date('Y'),
                    '2',
                    'Faculty of Education',
                    ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all university cut-offs to Excel
     */
    public function export(Request $request)
    {
        $this->checkSuperAdmin();

        $query = UniversityCutOff::query();

        // Apply same filters as index page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('university_name', 'like', "%$search%")
                  ->orWhere('course_name', 'like', "%$search%")
                  ->orWhere('faculty', 'like', "%$search%");
            });
        }

        if ($request->filled('university')) {
            $query->where('university_name', $request->university);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $cutOffs = $query->orderBy('university_name')->orderBy('course_name')->get();

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('University Cut-Offs');

        // Set headers
        $headers = [
            'University Name',
            'Course Name',
            'Course Code',
            'Faculty',
            'Department',
            'Degree Type',
            'Min Principal Passes',
            'Cut-Off Points',
            'Cut-Off Points (Male)',
            'Cut-Off Points (Female)',
            'Essential Subjects',
            'Academic Year',
            'Duration (Years)',
            'Is Active'
        ];

        // Set header row values using array
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($cutOffs as $cutOff) {
            $essentialSubjects = '';
            if ($cutOff->essential_subjects && is_array($cutOff->essential_subjects)) {
                $essentialSubjects = implode(', ', $cutOff->essential_subjects);
            }

            $rowData = [
                $cutOff->university_name,
                $cutOff->course_name,
                $cutOff->course_code,
                $cutOff->faculty,
                $cutOff->department,
                $cutOff->degree_type,
                $cutOff->minimum_principal_passes,
                $cutOff->cut_off_points,
                $cutOff->cut_off_points_male,
                $cutOff->cut_off_points_female,
                $essentialSubjects,
                $cutOff->academic_year,
                $cutOff->duration_years,
                $cutOff->is_active ? 'Yes' : 'No'
            ];
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to all data cells
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
        ];
        $sheet->getStyle('A1:N' . ($row - 1))->applyFromArray($dataStyle);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'university_cut_offs_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import university programs from CSV/Excel file
     */
    public function import(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'university_name' => 'required|string',
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $universityName = $request->university_name;
            $extension = $file->getClientOriginalExtension();

            $data = [];

            if ($extension === 'csv') {
                // Parse CSV
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle); // Read header row
                
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) === count($header)) {
                        $data[] = array_combine($header, $row);
                    }
                }
                fclose($handle);
            } else {
                // Parse Excel using PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                $header = array_shift($rows); // First row is header
                
                foreach ($rows as $row) {
                    if (count($row) === count($header)) {
                        $data[] = array_combine($header, $row);
                    }
                }
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $isMakerere = ($universityName === 'Makerere University');

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we skipped header
                
                // Normalize column names (handle both variations)
                $normalizedRow = [];
                foreach ($row as $key => $value) {
                    // Convert to lowercase and remove spaces for consistency
                    $normalizedKey = strtolower(str_replace(' ', '_', trim($key)));
                    // Map common variations
                    if ($normalizedKey === 'university_name') $normalizedKey = 'university_name';
                    if ($normalizedKey === 'course_name') $normalizedKey = 'course_name';
                    if ($normalizedKey === 'course_code') $normalizedKey = 'course_code';
                    if ($normalizedKey === 'degree_type') $normalizedKey = 'degree_type';
                    if ($normalizedKey === 'min_principal_passes') $normalizedKey = 'minimum_principal_passes';
                    if ($normalizedKey === 'minimum_principal_passes') $normalizedKey = 'minimum_principal_passes';
                    if ($normalizedKey === 'cut-off_points' || $normalizedKey === 'cut_off_points') $normalizedKey = 'cut_off_points';
                    if ($normalizedKey === 'cut-off_points_(male)' || $normalizedKey === 'cut_off_points_male') $normalizedKey = 'cut_off_points_male';
                    if ($normalizedKey === 'cut-off_points_(female)' || $normalizedKey === 'cut_off_points_female') $normalizedKey = 'cut_off_points_female';
                    if ($normalizedKey === 'essential_subjects') $normalizedKey = 'essential_subjects';
                    if ($normalizedKey === 'academic_year') $normalizedKey = 'academic_year';
                    if ($normalizedKey === 'duration_(years)' || $normalizedKey === 'duration_years') $normalizedKey = 'duration_years';
                    if ($normalizedKey === 'is_active') $normalizedKey = 'is_active';
                    
                    $normalizedRow[$normalizedKey] = $value;
                }
                $row = $normalizedRow;
                
                // Skip empty rows
                if (empty($row['course_name'])) {
                    $skipped++;
                    continue;
                }

                // Validate row data based on university format
                $validationRules = [
                    'course_name' => 'required|string|max:255',
                ];
                
                if ($isMakerere) {
                    // Makerere: Require gender-specific cut-offs only
                    $validationRules['cut_off_points_male'] = 'required|numeric|min:0|max:100';
                    $validationRules['cut_off_points_female'] = 'required|numeric|min:0|max:100';
                } else {
                    // Kyambogo: Full validation
                    $validationRules['degree_type'] = 'required|in:bachelor,diploma,certificate,masters,phd';
                    $validationRules['minimum_principal_passes'] = 'required|integer|min:1|max:5';
                    $validationRules['cut_off_points'] = 'required|numeric|min:0|max:100';
                    $validationRules['academic_year'] = 'required|integer|min:2000|max:' . (date('Y') + 1);
                }
                
                $validator = Validator::make($row, $validationRules);

                if ($validator->fails()) {
                    $errors[] = "Row $rowNumber: " . implode(', ', $validator->errors()->all());
                    $skipped++;
                    continue;
                }

                // Create or update cut-off
                try {
                    if ($isMakerere) {
                        // Makerere: Simple format - auto-fill defaults
                        $cutOffData = [
                            'degree_type' => 'bachelor',
                            'minimum_principal_passes' => 2,
                            'academic_year' => date('Y'),
                            'is_active' => true,
                            'cut_off_points' => !empty($row['cut_off_points']) ? floatval($row['cut_off_points']) : null,
                            'cut_off_points_male' => floatval($row['cut_off_points_male']),
                            'cut_off_points_female' => floatval($row['cut_off_points_female']),
                            'program_category' => 'stem',
                        ];
                        
                        // Add course code if present
                        if (!empty($row['course_code'])) {
                            $cutOffData['course_code'] = $row['course_code'];
                        }
                        
                        // Add essential subjects if present
                        if (!empty($row['essential_subjects']) || isset($row['Essential Subjects'])) {
                            $essentialSubjectsStr = $row['essential_subjects'] ?? $row['Essential Subjects'] ?? '';
                            if (!empty($essentialSubjectsStr)) {
                                $cutOffData['essential_subjects'] = array_map('trim', explode(',', $essentialSubjectsStr));
                            }
                        }
                    } else {
                        // Kyambogo: Full data from CSV
                        $cutOffData = [
                            'degree_type' => $row['degree_type'],
                            'minimum_principal_passes' => $row['minimum_principal_passes'],
                            'academic_year' => $row['academic_year'],
                            'is_active' => true,
                            'cut_off_points' => floatval($row['cut_off_points']),
                            'cut_off_points_male' => null,
                            'cut_off_points_female' => null,
                        ];
                        
                        // Add optional fields if present
                        if (!empty($row['course_code'])) {
                            $cutOffData['course_code'] = $row['course_code'];
                        }
                        if (!empty($row['duration_years'])) {
                            $cutOffData['duration_years'] = intval($row['duration_years']);
                        }
                        if (!empty($row['faculty'])) {
                            $cutOffData['faculty'] = $row['faculty'];
                        }
                        if (!empty($row['department'])) {
                            $cutOffData['department'] = $row['department'];
                        }
                        
                        // Add essential subjects if present
                        if (!empty($row['essential_subjects']) || isset($row['Essential Subjects'])) {
                            $essentialSubjectsStr = $row['essential_subjects'] ?? $row['Essential Subjects'] ?? '';
                            if (!empty($essentialSubjectsStr)) {
                                $cutOffData['essential_subjects'] = array_map('trim', explode(',', $essentialSubjectsStr));
                            }
                        }
                    }
                    
                    $academicYearForMatch = $cutOffData['academic_year'] ?? $row['academic_year'] ?? date('Y');
                    
                    UniversityCutOff::updateOrCreate(
                        [
                            'university_name' => $universityName,
                            'course_name' => $row['course_name'],
                            'academic_year' => $academicYearForMatch,
                        ],
                        $cutOffData
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row $rowNumber: Failed to save - " . $e->getMessage();
                    $skipped++;
                }
            }

            $message = "Import completed! $imported program(s) imported successfully.";
            if ($skipped > 0) {
                $message .= " $skipped row(s) skipped.";
            }

            if (!empty($errors)) {
                $message .= " Errors: " . implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (and " . (count($errors) - 5) . " more errors)";
                }
            }

            // Check for new course recommendations after import
            $notificationService = new CourseRecommendationNotificationService();
            $notificationService->checkAndNotifyForNewCourses();

            return redirect()->route('admin.university-cut-offs.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
