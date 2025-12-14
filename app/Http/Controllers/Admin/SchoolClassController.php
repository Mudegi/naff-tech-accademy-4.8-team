<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of classes for the school
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies or School Admin
        if (!$user->isDirectorOfStudies() && !$user->isSchoolAdmin()) {
            abort(403, 'Access denied. Only Directors of Studies and School Admins can manage classes.');
        }

        // Show system classes (bypass tenant scope for system-wide classes)
        $query = SchoolClass::withoutGlobalScope('school')->where('is_system_class', true);

        $searchTerm = trim($request->search ?? '');
        $status = $request->status;

        if ($searchTerm !== '' && $status !== '' && $status !== null) {
            $query->where(function($q) use ($searchTerm, $status) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->where('is_active', (int)$status);
            });
        } elseif ($searchTerm !== '') {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        } elseif ($status !== '' && $status !== null) {
            $query->where('is_active', (int)$status);
        }

        $perPage = $request->get('per_page', 10);
        $classes = $query->with('subjects')->latest()->paginate($perPage);
        $classes->appends($request->query());

        return view('admin.school.classes.index', compact('classes', 'school'));
    }



    /**
     * Show the form for editing a class
     */
    public function edit($id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies or School Admin
        if (!$user->isDirectorOfStudies() && !$user->isSchoolAdmin()) {
            abort(403, 'Access denied. Only Directors of Studies and School Admins can manage classes.');
        }

        $class = SchoolClass::withoutGlobalScope('school')->where('is_system_class', true)->findOrFail($id);
        
        // Get subjects and terms for this school
        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $terms = Term::orderBy('name')->get();

        return view('admin.school.classes.edit', compact('class', 'subjects', 'terms', 'school'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'No school associated with your account.');
        }

        // Check if user is Director of Studies or School Admin
        if (!$user->isDirectorOfStudies() && !$user->isSchoolAdmin()) {
            abort(403, 'Access denied. Only Directors of Studies and School Admins can manage classes.');
        }

        $class = SchoolClass::withoutGlobalScope('school')->where('is_system_class', true)->findOrFail($id);

        // Schools can only assign subjects to system classes, not modify basic class info
        $validated = $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        // Verify all subjects belong to the school
        $subjectIds = $request->subjects;
        $subjectsCount = Subject::where('school_id', $school->id)
            ->whereIn('id', $subjectIds)
            ->count();

        if ($subjectsCount !== count($subjectIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Some selected subjects do not belong to your school.');
        }

        // Update class subjects (sync to replace existing relationships)
        $class->subjects()->sync($request->subjects);

        return redirect()->route('admin.school.classes.index')
            ->with('success', 'Class subjects updated successfully.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|integer|min:1',
            'term_id' => 'required|exists:terms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'is_active' => 'boolean'
        ]);

        // Verify all subjects belong to the school
        $subjectIds = $request->subjects;
        $subjectsCount = Subject::where('school_id', $school->id)
            ->whereIn('id', $subjectIds)
            ->count();

        if ($subjectsCount !== count($subjectIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Some selected subjects do not belong to your school.');
        }

        // Validate slug uniqueness per school
        $slugValidator = \Illuminate\Support\Facades\Validator::make(
            ['slug' => $slug],
            ['slug' => ['required', 'string', 'max:255', $slugRule]]
        );
        
        if ($slugValidator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'A class with this name already exists in your school.'])
                ->with('error', 'A class with this name already exists in your school.');
        }

        $validated['slug'] = $slug;
        $validated['term'] = Term::findOrFail($validated['term_id'])->name;
        unset($validated['term_id']);
        
        $class->update($validated);
        
        // Sync subjects to the class
        $class->subjects()->sync($request->subjects);
        
        return redirect()->route('admin.school.classes.index')
            ->with('success', 'Class updated successfully.');
    }

}

