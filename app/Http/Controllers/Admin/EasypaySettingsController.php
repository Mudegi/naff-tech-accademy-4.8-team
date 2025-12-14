<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EasypaySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Access denied. Only super administrators can access payment settings.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $settings = EasypayConfiguration::first();
        return view('admin.settings.easypay', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'required|string',
                'secret' => 'required|string',
                'website_url' => 'required|url',
                'ipn_url' => 'required|url',
                'is_active' => 'boolean'
            ]);

            // Convert checkbox value to boolean
            $validated['is_active'] = $request->has('is_active');

            $settings = EasypayConfiguration::first();
            
            if ($settings) {
                $settings->update($validated);
                $message = 'Easypay settings updated successfully.';
            } else {
                EasypayConfiguration::create($validated);
                $message = 'Easypay settings created successfully.';
            }

            DB::commit();
            return redirect()->route('admin.settings.easypay')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->route('admin.settings.easypay')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Easypay settings update failed: ' . $e->getMessage());
            return redirect()->route('admin.settings.easypay')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }
} 