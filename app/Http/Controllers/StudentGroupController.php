<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupSubmission;
use App\Models\SchoolClass;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudentGroupController extends Controller
{
    /**
     * Display the groups index
     */
    public function index()
    {
        \Log::info('StudentGroupController index called');
        
        try {
            // Get user's groups and available groups
            $user = Auth::user();
            \Log::info('User retrieved', ['user_id' => $user->id ?? null]);

            // Simple test - return basic data first
            $myGroups = collect(); // Empty collection for now
            $availableGroups = collect(); // Empty collection for now

            // Try to get user's groups
            try {
                if ($user) {
                    \Log::info('Fetching user groups');
                    $myGroups = $user->approvedGroups()->with(['members', 'projects'])->get();
                    \Log::info('User groups fetched', ['count' => $myGroups->count()]);
                }
            } catch (\Exception $e) {
                \Log::error('Error getting user groups: ' . $e->getMessage(), ['exception' => $e]);
            }

            // Try to get available groups
            try {
                if ($user && $user->school_id) {
                    \Log::info('Fetching available groups', ['school_id' => $user->school_id]);
                    $availableGroups = \App\Models\Group::where('school_id', $user->school_id)
                        ->where('status', 'open')
                        ->with(['members', 'creator'])
                        ->get()
                        ->filter(function ($group) {
                            return !$group->isFull();
                        });
                    \Log::info('Available groups fetched', ['count' => $availableGroups->count()]);
                }
            } catch (\Exception $e) {
                \Log::error('Error getting available groups: ' . $e->getMessage(), ['exception' => $e]);
            }

            \Log::info('Returning groups view');
            return view('student.projects.groups.index', compact('myGroups', 'availableGroups'));
        } catch (\Exception $e) {
            \Log::error('Groups index error: ' . $e->getMessage(), ['exception' => (string)$e]);
            // Return view with empty data if everything fails
            return view('student.projects.groups.index', [
                'myGroups' => collect(),
                'availableGroups' => collect()
            ]);
        }
    }

    /**
     * Show the form for creating a new group
     */
    public function create()
    {
        $user = Auth::user();

        // Check if user is already in maximum groups (limit to 3 groups per student)
        $currentGroupCount = $user->approvedGroups()->count();
        if ($currentGroupCount >= 3) {
            return redirect()->route('student.projects.groups.index')
                ->with('warning', 'You can only be a member of up to 3 groups at a time.');
        }

        // Load subjects for the form
        $subjects = \App\Models\Subject::orderBy('name')->get();

        return view('student.projects.groups.create', compact('subjects'));
    }

    /**
     * Store a newly created group
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|integer|exists:subjects,id',
            'max_members' => 'required|integer|min:2|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if user already has a group with this name in same class
        $existingGroup = Group::where('school_id', $user->school_id)
            ->where('class_id', $user->student->class_id ?? null)
            ->where('name', $request->name)
            ->exists();

        if ($existingGroup) {
            return redirect()->back()
                ->withErrors(['name' => 'A group with this name already exists in your class.'])
                ->withInput();
        }

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $user->id,
            'school_id' => $user->school_id,
            'class_id' => $user->student->class_id ?? null,
            'subject_id' => $request->subject_id,
            'max_members' => $request->max_members,
        ]);

        // Add creator as leader
        $group->members()->attach($user->id, [
            'role' => 'leader',
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return redirect()->route('student.projects.groups.show', $group)
            ->with('success', 'Group created successfully! You are now the group leader.');
    }

    /**
     * Display the specified group
     */
    public function show(Group $group)
    {
        $user = Auth::user();

        // Check if user has access to this group
        if (!$group->isMember($user) && $group->school_id !== $user->school_id) {
            abort(403, 'You do not have access to this group.');
        }

        $group->load(['members', 'projects', 'creator', 'subject', 'submissions.uploader']);

        // Load marks the current student received for this group's work
        $groupMarks = StudentMark::with('uploadedBy')
            ->where('group_id', $group->id)
            ->where('student_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('student.projects.groups.show', compact('group', 'groupMarks'));
    }

    /**
     * Join a group
     */
    public function join(Group $group)
    {
        $user = Auth::user();

        // Check if group belongs to user's school and class
        if ($group->school_id !== $user->school_id ||
            $group->class_id !== ($user->student->class_id ?? null)) {
            return redirect()->back()
                ->with('error', 'You can only join groups from your school and class.');
        }

        // Check if user is already a member
        if ($group->isMember($user)) {
            return redirect()->back()
                ->with('warning', 'You are already a member of this group.');
        }

        // Check if group is full
        if ($group->isFull()) {
            return redirect()->back()
                ->with('error', 'This group is already full.');
        }

        // Check if user is already in maximum groups
        $currentGroupCount = $user->approvedGroups()->count();
        if ($currentGroupCount >= 3) {
            return redirect()->back()
                ->with('error', 'You can only be a member of up to 3 groups at a time.');
        }

        // Add user to group
        $group->members()->attach($user->id, [
            'role' => 'member',
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return redirect()->route('student.projects.groups.show', $group)
            ->with('success', 'Successfully joined the group!');
    }

    /**
     * Leave a group
     */
    public function leave(Group $group)
    {
        $user = Auth::user();

        // Check if user is a member of the group
        if (!$group->isMember($user)) {
            return redirect()->back()
                ->with('error', 'You are not a member of this group.');
        }

        // Check if user is the leader
        if ($group->isLeader($user)) {
            // If leader is leaving, check if there are other members
            $otherMembers = $group->approvedMembers()->where('user_id', '!=', $user->id)->count();

            if ($otherMembers > 0) {
                // Promote another member to leader
                $newLeader = $group->approvedMembers()->where('user_id', '!=', $user->id)->first();
                $group->members()->updateExistingPivot($newLeader->id, ['role' => 'leader']);
            } else {
                // If no other members, delete the group
                $group->delete();
                return redirect()->route('student.projects.groups.index')
                    ->with('success', 'You left the group. Since you were the only member, the group has been deleted.');
            }
        }

        // Remove user from group
        $group->members()->detach($user->id);

        return redirect()->route('student.projects.groups.index')
            ->with('success', 'Successfully left the group.');
    }

    /**
     * Remove a member from the group (leader only)
     */
    public function removeMember(Group $group, $memberId)
    {
        $user = Auth::user();

        // Check if user is the leader
        if (!$group->isLeader($user)) {
            abort(403, 'Only group leaders can remove members.');
        }

        // Check if trying to remove themselves
        if ($memberId == $user->id) {
            return redirect()->back()
                ->with('error', 'You cannot remove yourself from the group. Use the leave option instead.');
        }

        // Check if member exists in group
        $memberExists = $group->members()->where('user_id', $memberId)->exists();
        if (!$memberExists) {
            return redirect()->back()
                ->with('error', 'This user is not a member of the group.');
        }

        // Remove member
        $group->members()->detach($memberId);

        return redirect()->route('student.projects.groups.show', $group)
            ->with('success', 'Member removed from the group successfully.');
    }

    /**
     * Upload group submission (PDF or PNG)
     */
    public function uploadSubmission(Request $request, Group $group)
    {
        $user = Auth::user();

        // Check if user is a member of the group
        if (!$group->isMember($user)) {
            abort(403, 'Only group members can upload submissions.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store file
        $file = $request->file('file');
        $path = $file->store('group-submissions', 'public');
        $extension = $file->getClientOriginalExtension();

        // Create submission record
        GroupSubmission::create([
            'group_id' => $group->id,
            'subject_id' => $group->subject_id,
            'class_id' => $group->class_id,
            'uploaded_by' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $extension,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.projects.groups.show', $group)
            ->with('success', 'Group work uploaded successfully!');
    }
}
