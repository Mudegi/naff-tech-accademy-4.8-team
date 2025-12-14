<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterContent;
use Illuminate\Http\Request;

class FooterContentController extends Controller
{
    public function index()
    {
        $footerContent = FooterContent::first();
        return view('admin.footer-content.index', compact('footerContent'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'about_title' => 'required|string|max:255',
            'about_description' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'contact_address' => 'required|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        $footerContent = FooterContent::first();
        if (!$footerContent) {
            $footerContent = new FooterContent();
        }

        $footerContent->fill($request->all());
        $footerContent->save();

        return redirect()->route('admin.footer-content.index')
            ->with('success', 'Footer content updated successfully.');
    }
} 