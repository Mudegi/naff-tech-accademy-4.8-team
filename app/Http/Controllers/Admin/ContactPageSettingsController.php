<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactPageSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Access denied. Only super administrators can access contact page settings.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $contactPage = ContactPage::first();
        return view('admin.settings.contact', compact('contactPage'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                // Meta Tags
                'meta_title' => 'required|string|max:255',
                'meta_description' => 'required|string',
                'meta_keywords' => 'required|string|max:255',
                'meta_author' => 'required|string|max:255',
                'meta_robots' => 'required|string|max:255',
                'meta_language' => 'required|string|max:255',
                'meta_revisit_after' => 'required|string|max:255',
                
                // Open Graph / Facebook
                'og_title' => 'required|string|max:255',
                'og_description' => 'required|string',
                'og_image' => 'nullable|string|max:255',
                
                // Twitter
                'twitter_title' => 'required|string|max:255',
                'twitter_description' => 'required|string',
                'twitter_image' => 'nullable|string|max:255',
                
                // Schema.org
                'schema_name' => 'required|string|max:255',
                'schema_description' => 'required|string',
                'schema_street_address' => 'required|string|max:255',
                'schema_address_locality' => 'required|string|max:255',
                'schema_address_region' => 'required|string|max:255',
                'schema_postal_code' => 'required|string|max:255',
                'schema_address_country' => 'required|string|max:255',
                'schema_telephone' => 'required|string|max:255',
                'schema_email' => 'required|email|max:255',
                'schema_opening_hours' => 'required|string|max:255',
                
                // Contact Information
                'contact_title' => 'required|string|max:255',
                'contact_description' => 'required|string',
                'contact_phone' => 'required|string|max:255',
                'contact_phone_hours' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_address' => 'required|string',
                
                // Map Section
                'map_title' => 'required|string|max:255',
                'map_description' => 'required|string',
                'map_embed_url' => 'required|string',
                'map_opening_hours_monday_friday' => 'required|string|max:255',
                'map_opening_hours_saturday' => 'required|string|max:255',
                'map_opening_hours_sunday' => 'required|string|max:255',
            ]);

            $contactPage = ContactPage::first();
            if (!$contactPage) {
                $contactPage = new ContactPage();
            }

            $contactPage->fill($validated);
            $contactPage->save();

            return redirect()->route('admin.settings.contact')
                ->with('success', 'Contact page settings updated successfully.')
                ->with('status', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update contact page settings: ' . $e->getMessage())
                ->with('status', 'error');
        }
    }
} 