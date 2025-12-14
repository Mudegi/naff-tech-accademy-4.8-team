<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentTeacherMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MessageController extends Controller
{
    /**
     * Display parent's messages (inbox).
     */
    public function index()
    {
        $parent = Auth::user();
        
        // Get all conversations grouped by teacher and student
        $conversations = ParentTeacherMessage::where(function($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                      ->orWhere('sender_id', $parent->id);
            })
            ->with(['teacher', 'student', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) {
                return $message->teacher_id . '_' . $message->student_id;
            });
        
        // Get parent's children for the compose dropdown
        $children = $parent->children()->with('student')->get();
        
        return view('parent.messages.index', compact('conversations', 'children'));
    }
    
    /**
     * Show form to create a new message.
     */
    public function create(Request $request)
    {
        $parent = Auth::user();
        
        // Get parent's children
        $children = $parent->children()->with('student')->get();
        
        // If student_id provided in query, pre-select that student
        $selectedStudentId = $request->query('student_id');
        
        // Get teachers for selected student with subject information
        $teachers = [];
        if ($selectedStudentId) {
            // Get class IDs for this student
            $classIds = DB::table('class_user')
                ->where('user_id', $selectedStudentId)
                ->pluck('class_id')
                ->toArray();
            
            if (!empty($classIds)) {
                // Get all teachers assigned to those classes with their subjects
                $teachers = DB::table('class_user')
                    ->join('users', 'class_user.user_id', '=', 'users.id')
                    ->join('classes', 'class_user.class_id', '=', 'classes.id')
                    ->leftJoin('subject_user', 'users.id', '=', 'subject_user.user_id')
                    ->leftJoin('subjects', 'subject_user.subject_id', '=', 'subjects.id')
                    ->whereIn('class_user.class_id', $classIds)
                    ->whereIn('users.account_type', ['teacher', 'subject_teacher'])
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.phone_number',
                        DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", ") as subjects'),
                        DB::raw('GROUP_CONCAT(DISTINCT classes.name ORDER BY classes.name SEPARATOR ", ") as classes')
                    )
                    ->groupBy('users.id', 'users.name', 'users.email', 'users.phone_number')
                    ->get();
            }
        }
        
        return view('parent.messages.create', compact('children', 'teachers', 'selectedStudentId'));
    }
    
    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'teacher_id' => 'required|exists:users,id',
            'message' => 'required|string|min:10',
        ]);
        
        $parent = Auth::user();
        
        // Verify the student belongs to this parent
        if (!$parent->children()->where('student_id', $validated['student_id'])->exists()) {
            abort(403, 'Unauthorized access to this student.');
        }
        
        ParentTeacherMessage::create([
            'parent_id' => $parent->id,
            'teacher_id' => $validated['teacher_id'],
            'student_id' => $validated['student_id'],
            'sender_id' => $parent->id,
            'message' => $validated['message'],
            'read_by_recipient' => false,
        ]);
        
        return redirect()->route('parent.messages.index')
            ->with('success', 'Message sent successfully!');
    }
    
    /**
     * View a specific conversation thread.
     */
    public function show($messageId)
    {
        $parent = Auth::user();
        
        $message = ParentTeacherMessage::where(function($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                      ->orWhere('sender_id', $parent->id);
            })
            ->findOrFail($messageId);
        
        // Get all messages in this conversation thread
        $thread = ParentTeacherMessage::where('teacher_id', $message->teacher_id)
            ->where('student_id', $message->student_id)
            ->where(function($query) use ($parent) {
                $query->where('parent_id', $parent->id);
            })
            ->with(['teacher', 'student', 'sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark unread messages as read
        ParentTeacherMessage::where('teacher_id', $message->teacher_id)
            ->where('student_id', $message->student_id)
            ->where('parent_id', $parent->id)
            ->where('sender_id', '!=', $parent->id)
            ->where('read_by_recipient', false)
            ->update([
                'read_by_recipient' => true,
                'read_at' => now()
            ]);
        
        return view('parent.messages.show', compact('thread', 'message'));
    }
    
    /**
     * Reply to a message.
     */
    public function reply(Request $request, $messageId)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:10',
        ]);
        
        $parent = Auth::user();
        
        $originalMessage = ParentTeacherMessage::where(function($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                      ->orWhere('sender_id', $parent->id);
            })
            ->findOrFail($messageId);
        
        ParentTeacherMessage::create([
            'parent_id' => $parent->id,
            'teacher_id' => $originalMessage->teacher_id,
            'student_id' => $originalMessage->student_id,
            'sender_id' => $parent->id,
            'message' => $validated['message'],
            'read_by_recipient' => false,
        ]);
        
        return redirect()->route('parent.messages.show', $messageId)
            ->with('success', 'Reply sent successfully!');
    }
    
    /**
     * Get unread message count for badge.
     */
    public function getUnreadCount()
    {
        $parent = Auth::user();
        
        $unreadCount = ParentTeacherMessage::where('parent_id', $parent->id)
            ->where('sender_id', '!=', $parent->id)
            ->where('read_by_recipient', false)
            ->count();
        
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    /**
     * Show parent profile page.
     */
    public function showProfile()
    {
        $parent = Auth::user()->load('school');
        return view('parent.profile', compact('parent'));
    }
    
    /**
     * Update parent profile.
     */
    public function updateProfile(Request $request)
    {
        $parent = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $parent->id,
            'phone_number' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        // Update basic info
        $parent->name = $validated['name'];
        $parent->email = $validated['email'];
        $parent->phone_number = $validated['phone_number'] ?? null;
        
        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $parent->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $parent->password = Hash::make($request->new_password);
        }
        
        $parent->save();
        
        return redirect()->route('parent.profile')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Get teachers for a specific student (AJAX endpoint).
     */
    public function getTeachersForStudent($studentId)
    {
        $parent = Auth::user();
        
        // Verify the student belongs to this parent
        $parentChild = $parent->children()->where('student_id', $studentId)->first();
        if (!$parentChild) {
            return response()->json(['error' => 'Unauthorized', 'teachers' => []], 403);
        }
        
        // Get the actual student user record
        $student = User::where('id', $studentId)
            ->where('account_type', 'student')
            ->with('classes')
            ->first();
            
        if (!$student) {
            return response()->json(['error' => 'Student not found', 'teachers' => []], 404);
        }
        
        // Get class IDs for this student
        $classIds = DB::table('class_user')
            ->where('user_id', $studentId)
            ->pluck('class_id')
            ->toArray();
        
        if (empty($classIds)) {
            return response()->json(['teachers' => [], 'message' => 'Student not assigned to any classes']);
        }
        
        // Get all teachers assigned to those classes with their subjects
        $teachers = DB::table('class_user')
            ->join('users', 'class_user.user_id', '=', 'users.id')
            ->join('classes', 'class_user.class_id', '=', 'classes.id')
            ->leftJoin('subject_user', 'users.id', '=', 'subject_user.user_id')
            ->leftJoin('subjects', 'subject_user.subject_id', '=', 'subjects.id')
            ->whereIn('class_user.class_id', $classIds)
            ->whereIn('users.account_type', ['teacher', 'subject_teacher'])
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone_number',
                DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", ") as subjects'),
                DB::raw('GROUP_CONCAT(DISTINCT classes.name ORDER BY classes.name SEPARATOR ", ") as classes')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone_number')
            ->get();
        
        return response()->json([
            'teachers' => $teachers,
            'student_id' => $studentId,
            'class_count' => count($classIds)
        ]);
    }
}
