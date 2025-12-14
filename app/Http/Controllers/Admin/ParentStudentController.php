<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentStudentController extends Controller
{
    /**
     * Display a listing of all parent-student links.
     */
    public function index(Request $request)
    {
        $query = DB::table('parent_student')
            ->join('users as parents', 'parent_student.parent_id', '=', 'parents.id')
            ->join('users as students', 'parent_student.student_id', '=', 'students.id')
            ->select(
                'parent_student.*',
                'parents.name as parent_name',
                'parents.phone_number as parent_phone',
                'parents.email as parent_email',
                'students.name as student_name',
                'students.phone_number as student_phone',
                'students.email as student_email'
            );

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('parents.name', 'like', "%{$search}%")
                  ->orWhere('students.name', 'like', "%{$search}%")
                  ->orWhere('parents.phone_number', 'like', "%{$search}%")
                  ->orWhere('students.phone_number', 'like', "%{$search}%");
            });
        }

        // Get statistics
        $totalLinks = DB::table('parent_student')->count();
        $totalParents = DB::table('parent_student')->distinct('parent_id')->count('parent_id');
        $totalStudents = DB::table('parent_student')->distinct('student_id')->count('student_id');

        $perPage = $request->get('per_page', 50); // Default to 50 for better performance
        $links = $query->orderBy('parent_student.created_at', 'desc')->paginate($perPage);

        return view('admin.parent-student.index', compact('links', 'totalLinks', 'totalParents', 'totalStudents'));
    }

    /**
     * Show the form for creating a new parent-student link.
     */
    public function create()
    {
        return view('admin.parent-student.create');
    }

    /**
     * Search for parents via AJAX.
     */
    public function searchParents(Request $request)
    {
        $search = $request->get('term', '');
        
        $parents = User::where('account_type', 'parent')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone_number', 'email']);

        return response()->json($parents->map(function($parent) {
            return [
                'id' => $parent->id,
                'text' => $parent->name . ' (' . ($parent->phone_number ?: $parent->email) . ')'
            ];
        }));
    }

    /**
     * Search for students via AJAX.
     */
    public function searchStudents(Request $request)
    {
        $search = $request->get('term', '');
        
        $students = User::where('account_type', 'student')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('student')
            ->limit(10)
            ->get();

        return response()->json($students->map(function($student) {
            $class = $student->student ? $student->student->class_name : 'N/A';
            return [
                'id' => $student->id,
                'text' => $student->name . ' (' . $class . ') - ' . ($student->phone_number ?: $student->email)
            ];
        }));
    }

    /**
     * Store a newly created parent-student link.
     */
    public function store(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:users,id',
            'relationship' => 'required|in:parent,guardian,sponsor',
            'is_primary' => 'boolean',
            'receive_notifications' => 'boolean'
        ]);

        // Check if link already exists
        $exists = DB::table('parent_student')
            ->where('parent_id', $request->parent_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['error' => 'This parent is already linked to this student.'])
                ->withInput();
        }

        // Verify account types
        $parent = User::find($request->parent_id);
        $student = User::find($request->student_id);

        if ($parent->account_type !== 'parent') {
            return redirect()->back()
                ->withErrors(['error' => 'Selected user is not a parent account.'])
                ->withInput();
        }

        if ($student->account_type !== 'student') {
            return redirect()->back()
                ->withErrors(['error' => 'Selected user is not a student account.'])
                ->withInput();
        }

        // Create the link
        DB::table('parent_student')->insert([
            'parent_id' => $request->parent_id,
            'student_id' => $request->student_id,
            'relationship' => $request->relationship,
            'is_primary' => $request->has('is_primary') ? 1 : 0,
            'receive_notifications' => $request->has('receive_notifications') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.parent-student.index')
            ->with('success', 'Parent successfully linked to student!');
    }

    /**
     * Remove the specified parent-student link.
     */
    public function destroy($id)
    {
        DB::table('parent_student')->where('id', $id)->delete();

        return redirect()->route('admin.parent-student.index')
            ->with('success', 'Parent-student link removed successfully.');
    }

    /**
     * Update the specified link (for editing relationship type, primary status, etc).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'relationship' => 'required|in:parent,guardian,sponsor',
            'is_primary' => 'boolean',
            'receive_notifications' => 'boolean'
        ]);

        DB::table('parent_student')
            ->where('id', $id)
            ->update([
                'relationship' => $request->relationship,
                'is_primary' => $request->has('is_primary') ? 1 : 0,
                'receive_notifications' => $request->has('receive_notifications') ? 1 : 0,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.parent-student.index')
            ->with('success', 'Link updated successfully.');
    }

    /**
     * Show the bulk import form.
     */
    public function bulkImport()
    {
        return view('admin.parent-student.bulk-import');
    }

    /**
     * Process bulk import from CSV.
     */
    public function processBulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        
        // Remove header row
        $header = array_shift($csvData);
        
        // Validate header
        $expectedHeaders = ['parent_email', 'student_email', 'relationship', 'is_primary', 'receive_notifications'];
        $headerLower = array_map('strtolower', array_map('trim', $header));
        
        if ($headerLower !== $expectedHeaders) {
            return redirect()->back()->with('error', 'Invalid CSV format. Expected headers: ' . implode(', ', $expectedHeaders));
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($csvData as $index => $row) {
                $lineNumber = $index + 2; // +2 because of header and 0-based index
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $parentEmail = trim($row[0]);
                $studentEmail = trim($row[1]);
                $relationship = trim($row[2] ?? 'parent');
                $isPrimary = strtolower(trim($row[3] ?? 'yes')) === 'yes' ? 1 : 0;
                $receiveNotifications = strtolower(trim($row[4] ?? 'yes')) === 'yes' ? 1 : 0;

                // Find parent
                $parent = User::where('email', $parentEmail)
                    ->where('account_type', 'parent')
                    ->first();

                if (!$parent) {
                    $errors[] = "Line {$lineNumber}: Parent with email '{$parentEmail}' not found";
                    $skipped++;
                    continue;
                }

                // Find student
                $student = User::where('email', $studentEmail)
                    ->where('account_type', 'student')
                    ->first();

                if (!$student) {
                    $errors[] = "Line {$lineNumber}: Student with email '{$studentEmail}' not found";
                    $skipped++;
                    continue;
                }

                // Check if link already exists
                $exists = DB::table('parent_student')
                    ->where('parent_id', $parent->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if ($exists) {
                    $errors[] = "Line {$lineNumber}: Link between '{$parentEmail}' and '{$studentEmail}' already exists";
                    $skipped++;
                    continue;
                }

                // Validate relationship
                if (!in_array($relationship, ['parent', 'guardian', 'sponsor'])) {
                    $relationship = 'parent';
                }

                // Create the link
                DB::table('parent_student')->insert([
                    'parent_id' => $parent->id,
                    'student_id' => $student->id,
                    'relationship' => $relationship,
                    'is_primary' => $isPrimary,
                    'receive_notifications' => $receiveNotifications,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported++;
            }

            DB::commit();

            $message = "Successfully imported {$imported} link(s).";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} row(s).";
            }

            // Store errors in session if any
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('admin.parent-student.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="parent-student-import-template.csv"',
        ];

        $columns = ['parent_email', 'student_email', 'relationship', 'is_primary', 'receive_notifications'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, $columns);
            
            // Write sample data
            fputcsv($file, [
                'parent@example.com',
                'student@example.com',
                'parent',
                'yes',
                'yes'
            ]);
            
            fputcsv($file, [
                'guardian@example.com',
                'student2@example.com',
                'guardian',
                'no',
                'yes'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
