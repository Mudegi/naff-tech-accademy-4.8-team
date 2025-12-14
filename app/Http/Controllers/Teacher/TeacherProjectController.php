<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Project;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeacherProjectController extends Controller
{
    /**
     * Display the projects dashboard for teacher
     */
    public function index()
    {
        $user = Auth::user();
        Log::info('Teacher projects index accessed', ['user_id' => $user->id ?? null, 'school_id' => $user->school_id ?? null]);

        if (empty($user->school_id)) {
            $projects = collect();
        } else {
            // Limit projects to groups/classes the teacher is assigned to
            $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
            if (empty($classIds)) {
                $projects = collect();
            } else {
                $projects = Project::whereHas('group', function ($query) use ($user, $classIds) {
                    $query->where('school_id', $user->school_id)
                          ->where(function($q) use ($classIds) {
                              $q->whereIn('class_id', $classIds)
                                ->orWhereHas('members', function($mq) use ($classIds) {
                                    $mq->whereIn('class_id', $classIds);
                                });
                          });
                })
                ->with(['group.members', 'planning', 'implementation'])
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return view('teacher.projects.index', compact('projects'));
    }

    /**
     * Show a specific project details
     */
    public function show(Project $project)
    {
        $user = Auth::user();

        if (! $project->group || ($project->group->school_id !== ($user->school_id ?? null))) {
            abort(403, 'You do not have access to this project.');
        }

        // Ensure teacher is assigned to the class for this group's students
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403, 'You are not assigned to any class.');
        }
        $group = $project->group;
        if ($group->class_id) {
            if (! in_array($group->class_id, $classIds)) {
                abort(403, 'You do not have access to this project (class mismatch).');
            }
        } else {
            $hasMemberInClasses = $group->members()->whereIn('class_id', $classIds)->exists();
            if (! $hasMemberInClasses) {
                abort(403, 'You do not have access to this project.');
            }
        }

        $project->load(['group.members', 'planning', 'implementation']);

        $existingMarks = StudentMark::where('school_id', $project->school_id)
            ->where('subject_name', 'Group Project: ' . $project->title)
            ->get();

        return view('teacher.projects.show', compact('project', 'existingMarks'));
    }

    /**
     * Show grading form for a project with individual member grading
     */
    public function gradeForm(Project $project)
    {
        $user = Auth::user();
        if (! $project->group || ($project->group->school_id !== ($user->school_id ?? null))) {
            abort(403);
        }

        // Enforce teacher class assignment
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403);
        }
        $group = $project->group;
        if ($group->class_id) {
            if (! in_array($group->class_id, $classIds)) {
                abort(403);
            }
        } else {
            $hasMemberInClasses = $group->members()->whereIn('class_id', $classIds)->exists();
            if (! $hasMemberInClasses) {
                abort(403);
            }
        }

        $project->load(['group.approvedMembers', 'implementation']);
        
        // Get existing marks for each member
        $members = $project->group->approvedMembers ?? collect();
        $existingMarks = StudentMark::where('school_id', $project->school_id)
            ->where('subject_name', 'Group Project: ' . $project->title)
            ->get()
            ->groupBy('user_id');

        return view('teacher.projects.grade', compact('project', 'members', 'existingMarks'));
    }

    /**
     * Submit grades for a project (creates/updates StudentMark entries for each member)
     */
    public function gradeSubmit(Request $request, Project $project)
    {
        $user = Auth::user();
        if (! $project->group || ($project->group->school_id !== ($user->school_id ?? null))) {
            abort(403);
        }

        // Enforce teacher class assignment
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403);
        }
        $group = $project->group;
        if ($group->class_id) {
            if (! in_array($group->class_id, $classIds)) {
                abort(403);
            }
        } else {
            $hasMemberInClasses = $group->members()->whereIn('class_id', $classIds)->exists();
            if (! $hasMemberInClasses) {
                abort(403);
            }
        }

        // Validate input with individual marks for each member
        $data = $request->validate([
            'marks' => 'required|array',
            'marks.*.numeric_mark' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string|max:2000',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $members = $project->group->approvedMembers ?? collect();
        
        foreach ($members as $member) {
            $markData = $data['marks'][$member->id] ?? null;
            
            if ($markData && isset($markData['numeric_mark'])) {
                // Delete existing marks for this member/project combo
                StudentMark::where('user_id', $member->id)
                    ->where('subject_name', 'Group Project: ' . $project->title)
                    ->delete();

                // Create new mark record
                StudentMark::create([
                    'user_id' => $member->id,
                    'student_id' => $member->id,
                    'academic_level' => $member->student->level ?? null,
                    'subject_name' => 'Group Project: ' . $project->title,
                    'paper_name' => 'Project Implementation',
                    'numeric_mark' => $markData['numeric_mark'],
                    'grade_type' => 'numeric',
                    'remarks' => isset($markData['member_remarks']) ? $markData['member_remarks'] : null,
                    'school_id' => $project->school_id,
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        // Update project implementation with grading status and feedback
        if ($project->implementation) {
            $project->implementation->update([
                'status' => 'graded',
                'feedback' => $data['feedback'] ?? null,
                'graded_at' => now(),
                'graded_by' => $user->id,
            ]);
        }

        Log::info('Project graded by teacher', [
            'project_id' => $project->id,
            'teacher_id' => $user->id,
            'group_id' => $project->group_id,
            'members_count' => $members->count(),
        ]);

        return redirect()->route('teacher.projects.show', $project)
            ->with('success', 'Project graded successfully! Marks assigned to all group members.');
    }

    /**
     * Show grading feedback for a graded project
     */
    public function viewGradingFeedback(Project $project)
    {
        $user = Auth::user();
        if (! $project->group || ($project->group->school_id !== ($user->school_id ?? null))) {
            abort(403);
        }

        // Enforce teacher class assignment
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403);
        }
        $group = $project->group;
        if ($group->class_id) {
            if (! in_array($group->class_id, $classIds)) {
                abort(403);
            }
        } else {
            $hasMemberInClasses = $group->members()->whereIn('class_id', $classIds)->exists();
            if (! $hasMemberInClasses) {
                abort(403);
            }
        }

        $project->load(['group.approvedMembers', 'implementation']);
        
        // Get marks for all members
        $marks = StudentMark::where('school_id', $project->school_id)
            ->where('subject_name', 'Group Project: ' . $project->title)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('teacher.projects.feedback', compact('project', 'marks'));
    }

    /**
     * Display the groups dashboard for teacher
     */
    public function indexGroups()
    {
        // Redirect legacy project-group listing to the canonical teacher groups index
        return redirect()->route('teacher.groups.index');
    }

    /**
     * Display group submissions for teacher's subjects and classes
     */
    public function groupSubmissions(Request $request)
    {
        $user = Auth::user();
        
        // Get teacher's assigned classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        
        // Get teacher's assigned subjects (from resources they created)
        $subjectIds = \App\Models\Resource::where('teacher_id', $user->id)
            ->distinct()
            ->pluck('subject_id')
            ->toArray();
        
        // Build query
        $query = \App\Models\GroupSubmission::with(['group', 'uploader', 'subject', 'schoolClass'])
            ->whereIn('class_id', $classIds);
        
        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $submissions = $query->orderByDesc('submitted_at')->paginate(12);
        
        // Get subjects and classes for filters
        $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
        $classes = \App\Models\SchoolClass::whereIn('id', $classIds)->orderBy('name')->get();
        
        return view('teacher.projects.group-submissions', compact('submissions', 'subjects', 'classes'));
    }

    /**
     * Show a specific group details
     */
    public function showGroup(Group $group)
    {
        $user = Auth::user();
        // Validate access and redirect to canonical group submissions view
        if ($group->school_id !== ($user->school_id ?? null)) {
            abort(403, 'You do not have access to this group.');
        }

        return redirect()->route('teacher.groups.submissions', $group);
    }
}

