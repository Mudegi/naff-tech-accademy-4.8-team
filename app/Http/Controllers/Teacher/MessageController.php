<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ParentTeacherMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display teacher's messages (inbox).
     */
    public function index()
    {
        $teacher = Auth::user();
        
        // Get all conversations grouped by parent and student
        $conversations = ParentTeacherMessage::where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhere('sender_id', $teacher->id);
            })
            ->with(['parent', 'student', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) {
                return $message->parent_id . '_' . $message->student_id;
            });
        
        return view('teacher.messages.index', compact('conversations'));
    }
    
    /**
     * View a specific conversation thread.
     */
    public function show($messageId)
    {
        $teacher = Auth::user();
        
        $message = ParentTeacherMessage::where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhere('sender_id', $teacher->id);
            })
            ->findOrFail($messageId);
        
        // Get all messages in this conversation thread
        $thread = ParentTeacherMessage::where('teacher_id', $message->teacher_id)
            ->where('student_id', $message->student_id)
            ->where('parent_id', $message->parent_id)
            ->with(['parent', 'student', 'sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark unread messages as read
        ParentTeacherMessage::where('teacher_id', $teacher->id)
            ->where('parent_id', $message->parent_id)
            ->where('student_id', $message->student_id)
            ->where('sender_id', '!=', $teacher->id)
            ->where('read_by_recipient', false)
            ->update([
                'read_by_recipient' => true,
                'read_at' => now()
            ]);
        
        return view('teacher.messages.show', compact('thread', 'message'));
    }
    
    /**
     * Reply to a parent message.
     */
    public function reply(Request $request, $messageId)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:10',
        ]);
        
        $teacher = Auth::user();
        
        $originalMessage = ParentTeacherMessage::where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhere('sender_id', $teacher->id);
            })
            ->findOrFail($messageId);
        
        ParentTeacherMessage::create([
            'parent_id' => $originalMessage->parent_id,
            'teacher_id' => $teacher->id,
            'student_id' => $originalMessage->student_id,
            'sender_id' => $teacher->id,
            'message' => $validated['message'],
            'read_by_recipient' => false,
        ]);
        
        return redirect()->route('teacher.messages.show', $messageId)
            ->with('success', 'Reply sent successfully!');
    }
    
    /**
     * Get unread message count for badge.
     */
    public function getUnreadCount()
    {
        $teacher = Auth::user();
        
        $unreadCount = ParentTeacherMessage::where('teacher_id', $teacher->id)
            ->where('sender_id', '!=', $teacher->id)
            ->where('read_by_recipient', false)
            ->count();
        
        return response()->json(['unread_count' => $unreadCount]);
    }
}
