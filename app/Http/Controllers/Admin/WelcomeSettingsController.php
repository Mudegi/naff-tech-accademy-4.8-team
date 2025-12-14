<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelcomeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WelcomeSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Access denied. Only super administrators can access welcome page settings.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $welcomePage = WelcomeLink::first();
        return view('admin.settings.welcome', compact('welcomePage'));
    }

    public function update(Request $request)
    {
        try {
            // Define image fields for validation
            $imageFields = [
                'hero_image_1', 'hero_image_2', 'hero_image_3', 'hero_image_4', 'hero_image_5',
                'hero_image_6', 'hero_image_7', 'hero_image_8', 'hero_image_9', 'hero_image_10',
                'about_image', 'features_image', 'testimonials_image', 'cta_image',
                'login_image', 'register_image', 'og_image', 'twitter_image',
                'mission_image', 'team_image', 'values_image', 'vision_image'
            ];

            // Build validation rules
            $rules = [
                // Hero Section
                'hero_title' => 'nullable|string|max:255',
                'hero_subtitle' => 'nullable|string|max:255',

                // About Section
                'about_title' => 'nullable|string|max:255',
                'about_description' => 'nullable|string',

                // Features Section
                'features_title' => 'nullable|string|max:255',
                'features_description' => 'nullable|string',

                // Testimonials Section
                'testimonials_title' => 'nullable|string|max:255',
                'testimonials_description' => 'nullable|string',

                // CTA Section
                'cta_title' => 'nullable|string|max:255',
                'cta_description' => 'nullable|string',

                // Meta Tags
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string|max:255',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string',

                // Mission Section
                'mission_title' => 'nullable|string|max:255',
                'mission_description' => 'nullable|string',

                // Team Section
                'team_title' => 'nullable|string|max:255',
                'team_description' => 'nullable|string',

                // Values Section
                'values_title' => 'nullable|string|max:255',
                'values_description' => 'nullable|string',
                
                // Vision Section
                'vision_title' => 'nullable|string|max:255',
                'vision_description' => 'nullable|string',
            ];

            // Add image validation rules
            foreach ($imageFields as $field) {
                $rules[$field] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=1200,min_height=600';
            }

            $validated = $request->validate($rules);

            $welcomePage = WelcomeLink::first();
            if (!$welcomePage) {
                $welcomePage = new WelcomeLink();
            }

            // Handle file uploads
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old image if exists
                    if ($welcomePage->$field) {
                        Storage::delete('public/' . $welcomePage->$field);
                    }

                    // Store new image
                    $path = $request->file($field)->store('welcome-images', 'public');
                    $validated[$field] = $path;
                } else {
                    // Keep existing image if no new file uploaded
                    if ($welcomePage->$field) {
                        $validated[$field] = $welcomePage->$field;
                    }
                }
            }

            $welcomePage->fill($validated);
            $welcomePage->save();

            return redirect()->route('admin.settings.welcome')
                ->with('success', 'Welcome page settings updated successfully.')
                ->with('status', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update welcome page settings: ' . $e->getMessage())
                ->with('status', 'error');
        }
    }
} 