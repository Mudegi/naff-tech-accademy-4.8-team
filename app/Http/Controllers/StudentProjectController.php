<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectPlanning;
use App\Models\ProjectImplementation;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentProjectController extends Controller
{
    /**
     * Display the projects dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's projects (through groups they belong to)
        $projects = $user->projects()->with(['group', 'planning', 'implementation'])->get();

        // Get user's groups
        $groups = $user->approvedGroups()->with('projects')->get();

        return view('student.projects.index', compact('projects', 'groups'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $user = Auth::user();

        // Get user's approved groups that don't have projects yet
        $availableGroups = $user->approvedGroups()
            ->whereDoesntHave('projects')
            ->with('members')
            ->get();

        if ($availableGroups->isEmpty()) {
            return redirect()->route('student.projects.groups.index')
                ->with('warning', 'You need to be a member of a group before creating a project. Create or join a group first.');
        }

        return view('student.projects.create', compact('availableGroups'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $group = Group::findOrFail($request->group_id);

        // Check if user is a member of the group
        if (!$group->isMember($user)) {
            return redirect()->back()
                ->with('error', 'You are not a member of this group.');
        }

        // Check if group already has a project
        if ($group->projects()->exists()) {
            return redirect()->back()
                ->with('error', 'This group already has a project.');
        }

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'group_id' => $request->group_id,
            'created_by' => $user->id,
            'school_id' => $group->school_id,
            'class_id' => $group->class_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        $project->load(['group.members', 'planning', 'implementation']);

        return view('student.projects.show', compact('project'));
    }

    /**
     * Show the form for editing project planning
     */
    public function editPlanning(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        $planning = $project->planning ?? new ProjectPlanning(['project_id' => $project->id]);

        return view('student.projects.edit-planning', compact('project', 'planning'));
    }

    /**
     * Update project planning
     */
    public function updatePlanning(Request $request, Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        $validator = Validator::make($request->all(), [
            'purpose_objectives' => 'required|string',
            'justification' => 'required|string',
            'resource_identification' => 'required|string',
            'activity_plan' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $planningData = [
            'purpose_objectives' => $request->purpose_objectives,
            'justification' => $request->justification,
            'resource_identification' => $request->resource_identification,
        ];

        // Handle file upload
        if ($request->hasFile('activity_plan')) {
            // Delete old file if exists
            if ($project->planning && $project->planning->activity_plan_path) {
                Storage::disk('public')->delete($project->planning->activity_plan_path);
            }

            $file = $request->file('activity_plan');
            $filename = 'activity_plans/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $planningData['activity_plan_path'] = $filename;
        }

        $planning = $project->planning()->updateOrCreate(
            ['project_id' => $project->id],
            $planningData
        );

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project planning updated successfully!');
    }

    /**
     * Submit project planning for review
     */
    public function submitPlanning(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        if (!$project->planning) {
            return redirect()->back()
                ->with('error', 'Please complete the project planning first.');
        }

        $project->planning->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project planning submitted for review!');
    }

    /**
     * Show the form for editing project implementation
     */
    public function editImplementation(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        // Check if planning is approved
        if (!$project->planning || !$project->planning->isApproved()) {
            return redirect()->route('student.projects.show', $project)
                ->with('warning', 'Project planning must be approved before implementation.');
        }

        $implementation = $project->implementation ?? new ProjectImplementation(['project_id' => $project->id]);

        return view('student.projects.edit-implementation', compact('project', 'implementation'));
    }

    /**
     * Update project implementation
     */
    public function updateImplementation(Request $request, Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        $validator = Validator::make($request->all(), [
            'gathering_resources' => 'nullable|string',
            'activity_execution' => 'nullable|string',
            'stakeholder_engagement' => 'nullable|string',
            'producing_product_service' => 'nullable|string',
            'documentation_report' => 'nullable|string',
            'dissemination_presentation' => 'nullable|string',
            'documentation_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480', // 20MB max
            'presentation_file' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', // 20MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $implementationData = [
            'gathering_resources' => $request->gathering_resources,
            'activity_execution' => $request->activity_execution,
            'stakeholder_engagement' => $request->stakeholder_engagement,
            'producing_product_service' => $request->producing_product_service,
            'documentation_report' => $request->documentation_report,
            'dissemination_presentation' => $request->dissemination_presentation,
        ];

        // Handle documentation file upload
        if ($request->hasFile('documentation_file')) {
            if ($project->implementation && $project->implementation->documentation_file_path) {
                Storage::disk('public')->delete($project->implementation->documentation_file_path);
            }

            $file = $request->file('documentation_file');
            $filename = 'documentation/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $implementationData['documentation_file_path'] = $filename;
        }

        // Handle presentation file upload
        if ($request->hasFile('presentation_file')) {
            if ($project->implementation && $project->implementation->presentation_file_path) {
                Storage::disk('public')->delete($project->implementation->presentation_file_path);
            }

            $file = $request->file('presentation_file');
            $filename = 'presentations/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $implementationData['presentation_file_path'] = $filename;
        }

        $implementation = $project->implementation()->updateOrCreate(
            ['project_id' => $project->id],
            $implementationData
        );

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project implementation updated successfully!');
    }

    /**
     * Submit project for completion
     */
    public function submitProject(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if (!$project->group->isMember($user)) {
            abort(403, 'You do not have access to this project.');
        }

        if (!$project->implementation) {
            return redirect()->back()
                ->with('error', 'Please complete the project implementation first.');
        }

        $project->update(['status' => 'completed']);
        $project->implementation->update([
            'status' => 'submitted',
            'completed_at' => now(),
        ]);

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project submitted for completion!');
    }
}
