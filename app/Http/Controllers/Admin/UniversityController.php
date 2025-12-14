<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniversityController extends Controller
{
    /**
     * Check if user is super admin.
     */
    private function checkSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can manage universities.');
        }
    }

    /**
     * Display a listing of universities.
     */
    public function index()
    {
        $this->checkSuperAdmin();
        
        $universities = University::orderBy('name')->paginate(20);
        
        return view('admin.universities.index', compact('universities'));
    }

    /**
     * Show the form for creating a new university.
     */
    public function create()
    {
        $this->checkSuperAdmin();
        
        return view('admin.universities.create');
    }

    /**
     * Store a newly created university.
     */
    public function store(Request $request)
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'code' => 'nullable|string|max:50',
            'url_pattern' => 'nullable|string',
            'base_url' => 'nullable|url',
            'scraper_type' => 'required|in:pdf,html_table,html_custom,auto',
            'cut_off_format' => 'required|in:standard,makerere,kyambogo,custom',
            'scraper_config' => 'nullable|json',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        University::create($validated);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University created successfully.');
    }

    /**
     * Display the specified university.
     */
    public function show(University $university)
    {
        $this->checkSuperAdmin();
        
        return view('admin.universities.show', compact('university'));
    }

    /**
     * Show the form for editing the specified university.
     */
    public function edit(University $university)
    {
        $this->checkSuperAdmin();
        
        return view('admin.universities.edit', compact('university'));
    }

    /**
     * Update the specified university.
     */
    public function update(Request $request, University $university)
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities,name,' . $university->id,
            'code' => 'nullable|string|max:50',
            'url_pattern' => 'nullable|string',
            'base_url' => 'nullable|url',
            'scraper_type' => 'required|in:pdf,html_table,html_custom,auto',
            'cut_off_format' => 'required|in:standard,makerere,kyambogo,custom',
            'scraper_config' => 'nullable|json',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $university->update($validated);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Remove the specified university.
     */
    public function destroy(University $university)
    {
        $this->checkSuperAdmin();
        
        $university->delete();

        return redirect()->route('admin.universities.index')
            ->with('success', 'University deleted successfully.');
    }
}
