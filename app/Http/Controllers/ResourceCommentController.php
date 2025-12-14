<?php

namespace App\Http\Controllers;

use App\Models\ResourceComment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceCommentController extends Controller
{
    // Fetch all comments (and replies) for a resource
    public function index($resourceId)
    {
        $comments = ResourceComment::with([
                'user',
                'likes',
                'dislikes',
                'replies',
            ])
            ->withCount(['likes', 'dislikes'])
            ->where('resource_id', $resourceId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get();
        // Recursively append likes_count and dislikes_count to all replies
        $appendCounts = function ($comments) use (&$appendCounts) {
            foreach ($comments as $comment) {
                $comment->append('likes_count', 'dislikes_count');
                if ($comment->relationLoaded('replies')) {
                    $appendCounts($comment->replies);
                }
            }
        };
        $appendCounts($comments);
        return response()->json($comments);
    }

    // Store a new comment or reply
    public function store(Request $request, $resourceId)
    {
        $request->validate([
            'comment' => 'required|string',
            // 'parent_id' => 'nullable|exists:resource_comments,id',
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = [
            'resource_id' => $resourceId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id ?? null,
        ];
        \Log::info('Creating ResourceComment', $data);
        $comment = ResourceComment::create($data);

        $comment->load('user');
        
        // Create notification for teacher if student commented on their video
        $this->createNotificationForTeacher($comment, $resourceId);
        
        // Create notification for student if teacher replied to their comment
        $this->createNotificationForStudent($comment, $resourceId);
        
        return response()->json($comment, 201);
    }

    // Update a comment (only by owner)
    public function update(Request $request, $resourceId, $commentId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        $comment = ResourceComment::where('resource_id', $resourceId)->where('id', $commentId)->firstOrFail();
        if (auth()->id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $comment->comment = $request->comment;
        $comment->save();
        return response()->json($comment);
    }

    // Delete a comment (only by owner)
    public function destroy($resourceId, $commentId)
    {
        $comment = ResourceComment::where('resource_id', $resourceId)->where('id', $commentId)->firstOrFail();
        
        if (auth()->id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete all replies to this comment first
        ResourceComment::where('parent_id', $commentId)->delete();
        
        // Delete the comment itself
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    }

    // Like or dislike a comment
    public function like(Request $request, $commentId)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $like = \App\Models\ResourceCommentLike::updateOrCreate(
            [
                'user_id' => $user->id,
                'resource_comment_id' => $commentId,
            ],
            [
                'type' => $request->type,
            ]
        );
        return response()->json($like);
    }

    /**
     * Create notification for teacher when student comments on their video
     */
    private function createNotificationForTeacher($comment, $resourceId)
    {
        // Only create notification for student comments (not replies)
        if ($comment->parent_id !== null) {
            return;
        }

        // Get the resource and its teacher
        $resource = \App\Models\Resource::with('teacher')->find($resourceId);
        
        if (!$resource || !$resource->teacher) {
            return;
        }

        // Only create notification if the commenter is a student
        if ($comment->user->account_type !== 'student') {
            return;
        }

        // Don't create notification if the teacher commented on their own video
        if ($comment->user_id === $resource->teacher_id) {
            return;
        }

        // Check if notification already exists for this comment
        $existingNotification = Notification::where('user_id', $resource->teacher_id)
            ->where('resource_id', $resourceId)
            ->where('comment_id', $comment->id)
            ->exists();

        if ($existingNotification) {
            return;
        }

        // Create the notification
        Notification::create([
            'user_id' => $resource->teacher_id,
            'resource_id' => $resourceId,
            'comment_id' => $comment->id,
            'type' => 'student_comment',
            'title' => 'New Student Comment',
            'message' => $comment->user->name . ' commented on your video: "' . $resource->title . '"',
        ]);
    }

    /**
     * Create notification for student when teacher replies to their comment
     */
    private function createNotificationForStudent($comment, $resourceId)
    {
        // Only create notification for replies (not top-level comments)
        if ($comment->parent_id === null) {
            return;
        }

        // Get the parent comment and its author
        $parentComment = ResourceComment::with('user')->find($comment->parent_id);
        
        if (!$parentComment || !$parentComment->user) {
            return;
        }

        // Only create notification if the replier is a teacher
        if ($comment->user->account_type !== 'teacher') {
            return;
        }

        // Only create notification if the parent comment was made by a student
        if ($parentComment->user->account_type !== 'student') {
            return;
        }

        // Don't create notification if the teacher replied to their own comment
        if ($comment->user_id === $parentComment->user_id) {
            return;
        }

        // Get the resource
        $resource = \App\Models\Resource::find($resourceId);
        if (!$resource) {
            return;
        }

        // Check if notification already exists for this reply
        $existingNotification = Notification::where('user_id', $parentComment->user_id)
            ->where('resource_id', $resourceId)
            ->where('comment_id', $comment->id)
            ->exists();

        if ($existingNotification) {
            return;
        }

        // Create the notification
        Notification::create([
            'user_id' => $parentComment->user_id,
            'resource_id' => $resourceId,
            'comment_id' => $comment->id,
            'type' => 'teacher_reply',
            'title' => 'Teacher Replied to Your Comment',
            'message' => $comment->user->name . ' replied to your comment on: "' . $resource->title . '"',
        ]);
    }
} 