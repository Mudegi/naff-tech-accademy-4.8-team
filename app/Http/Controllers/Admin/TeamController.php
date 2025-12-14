<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::orderBy('sort_order')->paginate(10);
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.teams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'skills' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('team', $imageName, 'public');
            $validated['image_path'] = $imagePath;
        } else {
            $validated['image_path'] = 'team.jpg'; // Default image
        }

        $validated['is_active'] = $request->has('is_active');

        Team::create($validated);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return view('admin.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'skills' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it's not the default
            if ($team->image_path && $team->image_path !== 'team.jpg' && Storage::disk('public')->exists($team->image_path)) {
                Storage::disk('public')->delete($team->image_path);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('team', $imageName, 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $team->update($validated);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        // Delete image if it's not the default
        if ($team->image_path && $team->image_path !== 'team.jpg' && Storage::disk('public')->exists($team->image_path)) {
            Storage::disk('public')->delete($team->image_path);
        }

        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team member deleted successfully.');
    }

    /**
     * Toggle the active status of a team member
     */
    public function toggleStatus(Team $team)
    {
        $team->update(['is_active' => !$team->is_active]);

        $status = $team->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Team member {$status} successfully.");
    }
}