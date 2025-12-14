<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TermController extends Controller
{
    /**
     * Display a listing of the terms.
     */
    public function index()
    {
        $terms = SchoolClass::orderBy('grade_level')
            ->orderBy('term')
            ->get();
        
        return view('admin.terms.index', compact('terms'));
    }

    /**
     * Show the form for creating a new term.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.terms.create', compact('subjects'));
    }

    /**
     * Store a newly created term in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|integer|min:1',
            'term' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $term = SchoolClass::create($validated);
        
        // Attach subjects to the term
        $term->subjects()->attach($request->subjects);
        
        return redirect()->route('terms.index')
            ->with('success', 'Term created successfully.');
    }

    /**
     * Display the specified term.
     */
    public function show(SchoolClass $term)
    {
        return view('admin.terms.show', compact('term'));
    }

    /**
     * Show the form for editing the specified term.
     */
    public function edit(SchoolClass $term)
    {
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.terms.edit', compact('term', 'subjects'));
    }

    /**
     * Update the specified term in storage.
     */
    public function update(Request $request, SchoolClass $term)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'required|integer|min:1',
            'term' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $term->update($validated);
        
        // Sync subjects to the term
        $term->subjects()->sync($request->subjects);
        
        return redirect()->route('terms.index')
            ->with('success', 'Term updated successfully.');
    }

    /**
     * Remove the specified term from storage.
     */
    public function destroy(SchoolClass $term)
    {
        $term->delete();
        
        return redirect()->route('terms.index')
            ->with('success', 'Term deleted successfully.');
    }
} 