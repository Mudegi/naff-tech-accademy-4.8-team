<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        try {
            $packages = SubscriptionPackage::orderByDesc('created_at')->paginate(10);
            return view('admin.subscription-packages.index', compact('packages'));
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function create()
    {
        return view('admin.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'maximum_active_sessions' => 'required|integer|min:1',
            'access_to_notices' => 'boolean',
            'access_to_videos' => 'boolean',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'is_active' => 'boolean',
            'subscription_type' => 'required|in:term,subject,topic',
            'downloadable_content' => 'boolean',
            'practice_questions_bank' => 'boolean',
            'performance_analytics' => 'boolean',
            'parent_progress_reports' => 'boolean',
            'one_on_one_tutoring_sessions' => 'boolean',
            'shared_resources' => 'boolean',
            'priority_support' => 'boolean',
            'email_support' => 'boolean'
        ]);

        $validated['features'] = json_encode($validated['features']);
        $validated['is_active'] = $request->has('is_active');
        $validated['access_to_notices'] = $request->has('access_to_notices');
        $validated['access_to_videos'] = $request->has('access_to_videos');
        $validated['downloadable_content'] = $request->has('downloadable_content');
        $validated['practice_questions_bank'] = $request->has('practice_questions_bank');
        $validated['performance_analytics'] = $request->has('performance_analytics');
        $validated['parent_progress_reports'] = $request->has('parent_progress_reports');
        $validated['one_on_one_tutoring_sessions'] = $request->has('one_on_one_tutoring_sessions');
        $validated['shared_resources'] = $request->has('shared_resources');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['email_support'] = $request->has('email_support');
        $validated['created_by'] = auth()->id();

        SubscriptionPackage::create($validated);

        return redirect()->route('admin.subscription-packages.index')
            ->with('success', 'Subscription package created successfully.');
    }

    public function show(SubscriptionPackage $subscriptionPackage)
    {
        return view('admin.subscription-packages.show', compact('subscriptionPackage'));
    }

    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->features = json_decode($subscriptionPackage->features, true) ?: [];
        return view('admin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'maximum_active_sessions' => 'required|integer|min:1',
            'access_to_notices' => 'boolean',
            'access_to_videos' => 'boolean',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'is_active' => 'boolean',
            'subscription_type' => 'required|in:term,subject,topic',
            'downloadable_content' => 'boolean',
            'practice_questions_bank' => 'boolean',
            'performance_analytics' => 'boolean',
            'parent_progress_reports' => 'boolean',
            'one_on_one_tutoring_sessions' => 'boolean',
            'shared_resources' => 'boolean',
            'priority_support' => 'boolean',
            'email_support' => 'boolean'
        ]);

        $validated['features'] = json_encode($validated['features']);
        $validated['is_active'] = $request->has('is_active');
        $validated['access_to_notices'] = $request->has('access_to_notices');
        $validated['access_to_videos'] = $request->has('access_to_videos');
        $validated['downloadable_content'] = $request->has('downloadable_content');
        $validated['practice_questions_bank'] = $request->has('practice_questions_bank');
        $validated['performance_analytics'] = $request->has('performance_analytics');
        $validated['parent_progress_reports'] = $request->has('parent_progress_reports');
        $validated['one_on_one_tutoring_sessions'] = $request->has('one_on_one_tutoring_sessions');
        $validated['shared_resources'] = $request->has('shared_resources');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['email_support'] = $request->has('email_support');

        $subscriptionPackage->update($validated);

        return redirect()->route('admin.subscription-packages.index')
            ->with('success', 'Subscription package updated successfully.');
    }

    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->delete();
        return redirect()->route('admin.subscription-packages.index')
            ->with('success', 'Subscription package deleted successfully.');
    }
}