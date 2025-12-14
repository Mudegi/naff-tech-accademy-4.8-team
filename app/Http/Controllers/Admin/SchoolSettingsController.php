<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SchoolSettingsController extends Controller
{
    /**
     * Show the school settings form
     */
    public function index()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only school admin can access settings
        if (!$user->isSchoolAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only school administrators can access settings.');
        }

        return view('admin.school.settings', compact('school'));
    }

    /**
     * Update school settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No school associated with your account.');
        }

        // Only school admin can update settings
        if (!$user->isSchoolAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only school administrators can update settings.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $school->name = $request->name;
            $school->slug = \Illuminate\Support\Str::slug($request->name);
            $school->email = $request->email;
            $school->phone_number = $request->phone_number;
            $school->address = $request->address;
            $school->website = $request->website;

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                    Storage::disk('public')->delete($school->logo);
                }

                $logoPath = $request->file('logo')->store('schools/logos', 'public');
                $school->logo = $logoPath;
            }

            $school->save();

            return redirect()->route('admin.school.settings')
                ->with('success', 'School settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }
}
