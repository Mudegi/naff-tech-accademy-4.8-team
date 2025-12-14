<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Resource;
use App\Models\StudentAssignment;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TeacherGroupController extends Controller
{
    /**
     * Show form to assign students to a group (filtered by teacher's assigned classes)
     */
    public function assignStudentsForm(Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);

        // Get teacher's assigned classes (assuming relation: $user->assignedClasses)
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        // Get students in those classes, exclude already in group
        $students = \App\Models\Student::whereIn('class_id', $classIds)
            ->whereNotIn('id', $group->members->pluck('id')->toArray())
            ->with('schoolClass')
            ->get();

        return view('teacher.groups.assign_students', compact('group', 'students'));
    }

    // ...existing code...
    /**
     * Process assignment of students to group
     */
    public function assignStudentsSubmit(Request $request, Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);

        $data = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        // Only allow students from teacher's assigned classes
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        $validStudentIds = \App\Models\Student::whereIn('class_id', $classIds)
            ->whereIn('id', $data['student_ids'])
            ->pluck('id')->toArray();

        // Attach students to group (assuming many-to-many relation)
        $group->members()->attach($validStudentIds);

        Log::info('Students assigned to group', ['group_id' => $group->id, 'student_ids' => $validStudentIds, 'user_id' => $user->id]);

        return redirect()->route('teacher.groups.index')->with('success', 'Students assigned to group successfully.');
    }
    private function authorizeTeacher()
    {
        $user = Auth::user();
        if (!in_array($user->account_type, ['teacher', 'subject_teacher'])) {
            abort(403, 'Only teachers can manage groups.');
        }
        return $user;
    }

    private function authorizeGroup(Group $group)
    {
        $user = $this->authorizeTeacher();
        if ($group->school_id !== $user->school_id) {
            abort(403, 'You can only manage groups in your school.');
        }
        // Enforce class assignment: teacher can only manage groups for classes they are assigned to
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            abort(403, 'You are not assigned to any class.');
        }

        // If group has a class set, require it to be in teacher's classes
        if ($group->class_id) {
            if (! in_array($group->class_id, $classIds)) {
                abort(403, 'You do not have access to this group (class mismatch).');
            }
        } else {
            // If group has no class, ensure at least one member belongs to teacher classes
            $hasMemberInClasses = $group->members()->whereIn('class_id', $classIds)->exists();
            if (! $hasMemberInClasses) {
                abort(403, 'You do not have access to this group.');
            }
        }
    }

    /**
     * Calculate letter grade based on numeric mark (Uganda education system)
     * Form 1-4: Returns null (percentage marks only)
     * Form 5-6: Returns letter grade (A, B, C, D, E, F)
     */
    private function calculateLetterGrade($numericMark, $classLevel)
    {
        // Extract form number from level string (e.g., "Form 5", "form 5", "5")
        $formNumber = null;
        if (preg_match('/\d+/', strtolower($classLevel), $matches)) {
            $formNumber = intval($matches[0]);
        }

        // Form 1-4: No letter grades, only percentage marks
        if ($formNumber && $formNumber <= 4) {
            return null;
        }

        // Form 5-6: Use letter grades
        if ($numericMark >= 80) return 'A';
        if ($numericMark >= 70) return 'B';
        if ($numericMark >= 60) return 'C';
        if ($numericMark >= 50) return 'D';
        if ($numericMark >= 40) return 'E';
        return 'F';
    }

    public function index()
    {
        $user = $this->authorizeTeacher();

        // Limit groups to classes the teacher is assigned to
        $classIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
        if (empty($classIds)) {
            $groups = collect();
        } else {
            $groups = Group::where('school_id', $user->school_id)
                ->where(function($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds)
                      ->orWhereHas('members', function($mq) use ($classIds) {
                          $mq->whereIn('class_id', $classIds);
                      });
                })
                ->with(['members', 'creator'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('teacher.groups.index', compact('groups'));
    }

    public function create()
    {
        $user = $this->authorizeTeacher();
        $classes = $user->assignedClasses ?? \App\Models\SchoolClass::where('school_id', $user->school_id)->get();
        return view('teacher.groups.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizeTeacher();

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,NULL,id,school_id,' . $user->school_id,
            'description' => 'nullable|string|max:500',
            'max_members' => 'required|integer|min:2|max:50',
            'class_id' => 'nullable|integer|exists:school_classes,id',
        ]);

        // Ensure provided class is within teacher's assigned classes
        if (!empty($data['class_id'])) {
            $allowedClassIds = $user->assignedClasses ? $user->assignedClasses->pluck('id')->toArray() : [];
            if (! in_array($data['class_id'], $allowedClassIds)) {
                abort(403, 'You can only create groups for classes you are assigned to.');
            }
        }

        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'created_by' => $user->id,
            'school_id' => $user->school_id,
            'class_id' => $data['class_id'] ?? null,
            'max_members' => $data['max_members'],
            'status' => 'open',
        ]);

        return redirect()->route('teacher.groups.submissions', $group)->with('success', 'Group created.');
    }

    public function edit(Group $group)
    {
        $this->authorizeGroup($group);
        return view('teacher.groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorizeGroup($group);
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id . ',id,school_id,' . $group->school_id,
            'description' => 'nullable|string|max:500',
            'max_members' => 'required|integer|min:2|max:50',
        ]);

        $group->update($data);
        Log::info('Group updated', ['group_id' => $group->id, 'user_id' => Auth::id()]);

        return redirect()->route('teacher.groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        $this->authorizeGroup($group);
        $groupId = $group->id;
        $group->delete();
        Log::info('Group deleted', ['group_id' => $groupId, 'user_id' => Auth::id()]);
        return redirect()->route('teacher.groups.index')->with('success', 'Group deleted successfully.');
    }

    // Show form to assign a resource to a group
    public function assignForm(Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);
        $resources = Resource::where('teacher_id', $user->id)->where('is_active', true)->orderBy('title')->get();
        return view('teacher.groups.assign', compact('group', 'resources'));
    }

    // Assign an existing resource to a group
    public function assignResource(Request $request, Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);

        $data = $request->validate([
            'resource_id' => 'required|integer|exists:resources,id',
            'due_date' => 'nullable|date|after:today'
        ]);

        $resource = Resource::findOrFail($data['resource_id']);
        if ($resource->teacher_id !== $user->id) {
            abort(403, 'You can only assign your own resources.');
        }

        $members = $group->approvedMembers()->get();

        foreach ($members as $member) {
            StudentAssignment::create([
                'student_id' => $member->id,
                'resource_id' => $resource->id,
                'status' => 'assigned',
                'submitted_at' => null,
            ]);

            \App\Models\Notification::create([
                'user_id' => $member->id,
                'resource_id' => $resource->id,
                'type' => 'assignment_assigned',
                'title' => 'New Group Assignment',
                'message' => 'A new assignment was assigned to your group: ' . $resource->title,
            ]);
        }
        Log::info('Resource assigned to group', ['group_id' => $group->id, 'resource_id' => $resource->id, 'user_id' => $user->id]);

        return redirect()->route('teacher.groups.submissions', $group)->with('success', 'Resource assigned to group members successfully.');
    }

    // Teacher uploads a group submission on behalf of group
    public function submitGroupAssignment(Request $request, Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);

        $data = $request->validate([
            'resource_id' => 'required|integer|exists:resources,id',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip',
        ]);

        $resource = Resource::findOrFail($data['resource_id']);
        if ($resource->teacher_id !== $user->id) {
            abort(403, 'You can only submit assignments for your own resources.');
        }

        $path = $request->file('file')->store('assignments', 'public');
        $type = $request->file('file')->extension();

        $members = $group->approvedMembers()->get();

        foreach ($members as $member) {
            StudentAssignment::create([
                'student_id' => $member->id,
                'resource_id' => $resource->id,
                'assignment_file_path' => $path,
                'assignment_file_type' => $type,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            \App\Models\Notification::create([
                'user_id' => $member->id,
                'resource_id' => $resource->id,
                'type' => 'assignment_submitted',
                'title' => 'Group Submission Uploaded',
                'message' => 'A group submission was uploaded for: ' . $resource->title,
            ]);
        }
        Log::info('Group submission created', ['group_id' => $group->id, 'resource_id' => $resource->id, 'user_id' => $user->id]);

        return redirect()->route('teacher.groups.submissions', $group)->with('success', 'Group submission created for all members successfully.');
    }

    // List student assignments for this group
    public function submissions(Group $group)
    {
        $this->authorizeGroup($group);

        $assignments = StudentAssignment::with('student', 'resource')
            ->whereIn('student_id', $group->approvedMembers()->pluck('id'))
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('teacher.groups.submissions', compact('group', 'assignments'));
    }

    // Grade a group (create StudentMark entries for each member)
    public function gradeGroupSubmission(Request $request, Group $group)
    {
        $user = $this->authorizeTeacher();
        $this->authorizeGroup($group);

        $data = $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|integer|exists:users,id',
            'marks.*.numeric_mark' => 'required|numeric|min:0|max:100',
            'marks.*.remarks' => 'nullable|string|max:500',
            'subject_name' => 'nullable|string|max:255',
            'paper_name' => 'nullable|string|max:255',
        ]);

        // Get class level if group has a class assigned
        $classLevel = $group->class ? $group->class->level : null;

        foreach ($data['marks'] as $m) {
            // Calculate letter grade only for Form 5-6
            $letterGrade = $this->calculateLetterGrade($m['numeric_mark'], $classLevel);

            StudentMark::create([
                'user_id' => $m['student_id'],
                'student_id' => $m['student_id'],
                'group_id' => $group->id,
                'numeric_mark' => $m['numeric_mark'],
                'grade' => $letterGrade,
                'subject_name' => $data['subject_name'] ?? 'Group Assignment',
                'paper_name' => $data['paper_name'] ?? null,
                'remarks' => $m['remarks'] ?? null,
                'school_id' => $group->school_id,
                'uploaded_by' => $user->id,
            ]);
        }
        Log::info('Group graded', ['group_id' => $group->id, 'user_id' => $user->id, 'members_count' => count($data['marks']), 'class_level' => $classLevel]);

        return redirect()->route('teacher.groups.submissions', $group)->with('success', 'Group graded and marks created for members successfully.');
    }
    }


