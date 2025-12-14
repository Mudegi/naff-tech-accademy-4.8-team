<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FooterSettingsController extends Controller
{
    public function index()
    {
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can access footer settings.');
        }
        
        $footer = FooterContent::first();
        return view('admin.settings.footer', compact('footer'));
    }

    public function update(Request $request)
    {
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can access footer settings.');
        }
        
        try {
            $validated = $request->validate([
                'about_title' => 'required|string|max:255',
                'about_description' => 'required|string',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:255',
                'contact_address' => 'required|string|max:255',
                'facebook_url' => 'nullable|url|max:255',
                'twitter_url' => 'nullable|url|max:255',
                'instagram_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
            ]);

            $footer = FooterContent::first();
            if (!$footer) {
                $footer = new FooterContent();
            }

            $footer->fill($validated);
            $footer->save();

            return redirect()->route('admin.settings.footer')
                ->with('success', 'Footer settings updated successfully.')
                ->with('status', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update footer settings: ' . $e->getMessage())
                ->with('status', 'error');
        }
    }
} 