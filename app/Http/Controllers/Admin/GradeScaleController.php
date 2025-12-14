<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeScale;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeScaleController extends Controller
{
    /**
     * Display a listing of grade scales.
     */
    public function index()
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        // Get default O-Level scales
        $defaultOLevelScales = GradeScale::active()
            ->forLevel('O-Level')
            ->forSchool(null)
            ->orderByDesc('points')
            ->get();

        // Get default A-Level scales
        $defaultALevelScales = GradeScale::active()
            ->forLevel('A-Level')
            ->forSchool(null)
            ->orderByDesc('points')
            ->get();

        // Get custom scales for this school (if any)
        $customOLevelScales = GradeScale::active()
            ->forLevel('O-Level')
            ->forSchool($schoolId)
            ->orderByDesc('points')
            ->get();

        $customALevelScales = GradeScale::active()
            ->forLevel('A-Level')
            ->forSchool($schoolId)
            ->orderByDesc('points')
            ->get();

        return view('admin.grade-scales.index', compact(
            'defaultOLevelScales',
            'defaultALevelScales',
            'customOLevelScales',
            'customALevelScales'
        ));
    }

    /**
     * Show the form for creating custom grade scales.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get default scales as reference
        $defaultOLevelScales = GradeScale::active()
            ->forLevel('O-Level')
            ->forSchool(null)
            ->orderByDesc('points')
            ->get();

        return view('admin.grade-scales.create', compact('defaultOLevelScales'));
    }

    /**
     * Store custom grade scales for the school.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $validated = $request->validate([
            'academic_level' => 'required|in:O-Level,A-Level',
            'grades' => 'required|array|min:7|max:7',
            'grades.*.grade' => 'required|string|in:A,B,C,D,E,O,F',
            'grades.*.min_percentage' => 'required|numeric|min:0|max:100',
            'grades.*.max_percentage' => 'required|numeric|min:0|max:100',
            'grades.*.points' => 'required|integer|min:0|max:10',
        ]);

        // Validate ranges don't overlap
        foreach ($validated['grades'] as $i => $grade) {
            if ($grade['min_percentage'] > $grade['max_percentage']) {
                return back()->withErrors([
                    "grades.{$i}.min_percentage" => "Minimum percentage cannot be greater than maximum."
                ])->withInput();
            }
        }

        // Delete existing custom scales for this school and level
        GradeScale::where('school_id', $schoolId)
            ->where('academic_level', $validated['academic_level'])
            ->delete();

        // Create new custom scales
        foreach ($validated['grades'] as $gradeData) {
            GradeScale::create([
                'grade' => $gradeData['grade'],
                'min_percentage' => $gradeData['min_percentage'],
                'max_percentage' => $gradeData['max_percentage'],
                'points' => $gradeData['points'],
                'academic_level' => $validated['academic_level'],
                'school_id' => $schoolId,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.grade-scales.index')
            ->with('success', "Custom {$validated['academic_level']} grading scale created successfully!");
    }

    /**
     * Reset to default grade scales (delete custom scales).
     */
    public function destroy(string $level)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        GradeScale::where('school_id', $schoolId)
            ->where('academic_level', $level)
            ->delete();

        return redirect()->route('admin.grade-scales.index')
            ->with('success', "Reset to default {$level} grading scale successfully!");
    }
}
