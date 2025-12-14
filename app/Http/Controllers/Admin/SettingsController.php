<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

    public function flutterwave()
    {
        $settings = DB::table('flutterwave_settings')->first();
        return view('admin.settings.flutterwave', compact('settings'));
    }

    public function updateFlutterwave(Request $request)
    {
        $request->validate([
            'public_key' => 'required|string',
            'secret_key' => 'required|string',
            'encryption_key' => 'required|string',
            'test_mode' => 'boolean',
            'webhook_secret' => 'nullable|string',
            'currency_code' => 'required|string|size:3',
        ]);

        DB::table('flutterwave_settings')->updateOrInsert(
            ['id' => 1],
            [
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
                'encryption_key' => $request->encryption_key,
                'test_mode' => $request->boolean('test_mode'),
                'webhook_secret' => $request->webhook_secret,
                'currency_code' => $request->currency_code,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.settings.flutterwave')
            ->with('success', 'Flutterwave settings updated successfully.');
    }

    public function sms()
    {
        $settings = DB::table('sms_settings')->first();
        return view('admin.settings.sms', compact('settings'));
    }

    public function updateSms(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'sender_id' => 'required|string',
            'api_url' => 'required|url',
        ]);

        DB::table('sms_settings')->updateOrInsert(
            ['id' => 1],
            [
                'username' => $request->username,
                'password' => $request->password,
                'sender_id' => $request->sender_id,
                'api_url' => $request->api_url,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.settings.sms')
            ->with('success', 'SMS settings updated successfully.');
    }

    public function company()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can access system settings.');
        }

        $settings = DB::table('company_settings')->first();
        return view('admin.settings.company', compact('settings'));
    }

    public function updateCompany(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can access system settings.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_website' => 'nullable|url|max:255',
            'company_description' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'mtn_registered_name' => 'nullable|string|max:255',
            'mtn_mobile_number' => 'nullable|string|max:20',
            'airtel_registered_name' => 'nullable|string|max:255',
            'airtel_mobile_number' => 'nullable|string|max:20',
        ]);

        // Exclude non-database fields
        $data = $request->except(['_token', '_method', 'company_logo']);

        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            $oldSettings = DB::table('company_settings')->first();
            if ($oldSettings && $oldSettings->company_logo) {
                Storage::delete('public/' . $oldSettings->company_logo);
            }

            // Store new logo
            $path = $request->file('company_logo')->store('company', 'public');
            $data['company_logo'] = $path;
        }

        DB::table('company_settings')->updateOrInsert(
            ['id' => 1],
            $data
        );

        return redirect()->route('admin.settings.company')
            ->with('success', 'Company settings updated successfully.');
    }
} 