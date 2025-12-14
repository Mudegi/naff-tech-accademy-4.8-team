<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display the chat index page with conversations list
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only allow students to access chat
        if (!$user->canChat()) {
            abort(403, 'Access denied. Only students can access chat.');
        }

        // Get user's active conversations with participants and latest message
        $conversations = $user->activeConversations()
            ->with(['participants', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Add unread count for each conversation
        $conversations->each(function ($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountForUser($user->id);
        });

        return view('student.chat.index', compact('conversations'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$user->canChat()) {
            abort(403, 'Access denied. Only students can access chat.');
        }

        // Check if user is a participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Access denied. You are not a participant in this conversation.');
        }

        if ($this->conversationHasExternalParticipants($conversation, $user)) {
            abort(403, 'You can only chat with students within your school.');
        }

        // Get messages for this conversation
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read for this user
        $conversation->markAsReadForUser($user->id);

        // Get other participants for display
        $otherParticipants = $conversation->activeParticipants()
            ->where('user_id', '!=', $user->id)
            ->get();

        return view('student.chat.show', compact('conversation', 'messages', 'otherParticipants'));
    }

    /**
     * Start a new conversation with another user
     */
    public function startConversation(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->canChat()) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            if (!$user->school_id) {
                return response()->json(['users' => []]);
            }

            if (!$user->school_id) {
                return response()->json(['error' => 'You must belong to a school to start chats.'], 403);
            }

            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $otherUserId = $request->user_id;

            // Check if user is trying to start conversation with themselves
            if ($otherUserId == $user->id) {
                return response()->json(['error' => 'Cannot start conversation with yourself'], 400);
            }

            // Check if other user can chat
            $otherUser = User::findOrFail($otherUserId);
            if (!$otherUser->canChat()) {
                return response()->json(['error' => 'Cannot start conversation with this user'], 400);
            }

            // Ensure both students belong to the same school
            if (!$user->school_id || !$otherUser->school_id || $user->school_id !== $otherUser->school_id) {
                return response()->json(['error' => 'You can only chat with students within your school.'], 403);
            }

            // Check if conversation already exists between these users
            $existingConversation = Conversation::betweenUsers($user->id, $otherUserId)->first();
            
            if ($existingConversation) {
                if ($this->conversationHasExternalParticipants($existingConversation, $user)) {
                    return response()->json(['error' => 'You can only chat with students within your school.'], 403);
                }

                return response()->json([
                    'success' => true,
                    'conversation_id' => $existingConversation->id,
                    'redirect' => route('student.chat.show', $existingConversation)
                ]);
            }

            // Create new private conversation
            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => $user->id,
            ]);

            // Add both users as participants
            $conversation->addParticipant($user->id);
            $conversation->addParticipant($otherUserId);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect' => route('student.chat.show', $conversation)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error starting conversation: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while starting the conversation'], 500);
        }
    }

    /**
     * Create a new group conversation
     */
    public function createGroup(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->canChat()) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            if (!$user->school_id) {
                return response()->json(['error' => 'Group chat is only available for students linked to a school.'], 403);
            }

            \Log::info('Creating group with data:', $request->all());

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'participants' => 'required|array|min:1',
                'participants.*' => 'exists:users,id',
            ]);

            // Check if all participants can chat
            $participants = User::whereIn('id', $request->participants)
                ->where('account_type', 'student')
                ->where('school_id', $user->school_id)
                ->get();

            if ($participants->count() != count($request->participants)) {
                return response()->json(['error' => 'Some selected users cannot participate in chat or are not from your school'], 400);
            }

            // Create group conversation
            $conversation = Conversation::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => 'group',
                'created_by' => $user->id,
            ]);

            // Add creator as participant
            $conversation->addParticipant($user->id);

            // Add other participants
            foreach ($request->participants as $participantId) {
                if ($participantId != $user->id) {
                    $conversation->addParticipant($participantId);
                }
            }

            \Log::info('Group created successfully:', ['conversation_id' => $conversation->id]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect' => route('student.chat.show', $conversation)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating group: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the group'], 500);
        }
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$user->canChat()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if user is a participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if ($this->conversationHasExternalParticipants($conversation, $user)) {
            return response()->json(['error' => 'You can only chat with students within your school.'], 403);
        }

        $request->validate([
            'message' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:pdf,png|max:10240',
            'type' => 'nullable|in:text,image,file',
        ]);

        if (!$request->filled('message') && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Please enter a message or attach a file.'], 422);
        }

        $attachmentPath = null;
        $messageType = 'text';

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $extension = strtolower($file->getClientOriginalExtension());
            $messageType = $extension === 'png' ? 'image' : 'file';
        } elseif ($request->type) {
            $messageType = $request->type;
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'message' => $request->message ?? '',
            'type' => $messageType,
            'attachment_path' => $attachmentPath,
        ]);

        // Update conversation's updated_at timestamp
        $conversation->touch();

        // Load the message with user relationship
        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get messages for a conversation (AJAX)
     */
    public function getMessages(Conversation $conversation, Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canChat()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if user is a participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if ($this->conversationHasExternalParticipants($conversation, $user)) {
            return response()->json(['error' => 'You can only chat with students within your school.'], 403);
        }

        $page = $request->get('page', 1);
        $perPage = 50;

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'messages' => $messages->items(),
            'has_more' => $messages->hasMorePages(),
            'current_page' => $messages->currentPage(),
        ]);
    }

    /**
     * Search for users to start conversations with
     */
    public function searchUsers(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->canChat()) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            if (!$user->school_id) {
                return response()->json(['users' => []]);
            }

            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['users' => []]);
            }

            // Get users that the current user can chat with
            $users = User::where('account_type', 'student')
                ->where('id', '!=', $user->id)
                ->where('is_active', true) // Only active users
                ->where('school_id', $user->school_id)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'email']);

            // Add conversation status for each user
            $users->each(function ($searchedUser) use ($user) {
                $existingConversation = Conversation::betweenUsers($user->id, $searchedUser->id)->first();
                $searchedUser->has_existing_conversation = $existingConversation ? true : false;
                $searchedUser->conversation_id = $existingConversation ? $existingConversation->id : null;
            });

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            \Log::error('Error searching users: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while searching users'], 500);
        }
    }

    /**
     * Leave a conversation
     */
    public function leaveConversation(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$user->canChat()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if user is a participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Remove user from conversation
        $conversation->removeParticipant($user->id);

        return response()->json([
            'success' => true,
            'redirect' => route('student.chat.index')
        ]);
    }

    /**
     * Get unread message count for user
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            
            if (!$user->canChat()) {
                \Log::info('User ' . $user->id . ' cannot chat, returning 0');
                return response()->json(['count' => 0]);
            }

            $totalUnread = 0;
            $conversations = $user->activeConversations()->get();
            
            \Log::info('User ' . $user->id . ' has ' . $conversations->count() . ' active conversations');

            foreach ($conversations as $conversation) {
                $unreadCount = $conversation->getUnreadCountForUser($user->id);
                $totalUnread += $unreadCount;
                \Log::info('Conversation ' . $conversation->id . ' has ' . $unreadCount . ' unread messages for user ' . $user->id);
            }

            \Log::info('Total unread count for user ' . $user->id . ': ' . $totalUnread);
            return response()->json(['count' => $totalUnread]);
        } catch (\Exception $e) {
            \Log::error('Error getting chat unread count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Add members to an existing group conversation
     */
    public function addMembers(Request $request, Conversation $conversation)
    {
        try {
            $user = Auth::user();
            
            if (!$user->canChat()) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // Check if user is a participant in this conversation
            if (!$conversation->hasParticipant($user->id)) {
                return response()->json(['error' => 'Access denied. You are not a participant in this conversation.'], 403);
            }

            if ($this->conversationHasExternalParticipants($conversation, $user)) {
                return response()->json(['error' => 'This conversation includes participants outside your school and cannot be modified.'], 403);
            }

            // Only allow adding members to group conversations
            if ($conversation->type !== 'group') {
                return response()->json(['error' => 'Can only add members to group conversations'], 400);
            }

            $request->validate([
                'participants' => 'required|array|min:1',
                'participants.*' => 'exists:users,id',
            ]);

            \Log::info('Adding members to group:', ['conversation_id' => $conversation->id, 'participants' => $request->participants]);

            // Get existing participant IDs
            $existingParticipantIds = $conversation->activeParticipants()->pluck('user_id')->toArray();
            
            // Filter out users who are already participants
            $newParticipantIds = array_diff($request->participants, $existingParticipantIds);
            
            if (empty($newParticipantIds)) {
                return response()->json(['error' => 'All selected users are already members of this group'], 400);
            }

            // Check if all new participants can chat
            $newParticipants = User::whereIn('id', $newParticipantIds)
                ->where('account_type', 'student')
                ->where('school_id', $user->school_id)
                ->get();

            if ($newParticipants->count() != count($newParticipantIds)) {
                return response()->json(['error' => 'Some selected users cannot participate in chat or are not from your school'], 400);
            }

            // Add new participants
            foreach ($newParticipantIds as $participantId) {
                $conversation->addParticipant($participantId);
            }

            \Log::info('Members added successfully:', ['conversation_id' => $conversation->id, 'new_members' => $newParticipantIds]);

            return response()->json([
                'success' => true,
                'message' => 'Members added successfully',
                'added_count' => count($newParticipantIds),
                'new_members' => $newParticipants->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding members to group: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while adding members'], 500);
        }
    }

    /**
     * Determine if a conversation contains participants outside the user's school
     */
    private function conversationHasExternalParticipants(Conversation $conversation, User $user): bool
    {
        if (!$user->school_id) {
            return false;
        }

        return $conversation->activeParticipants()
            ->where('users.id', '!=', $user->id)
            ->where(function ($query) use ($user) {
                $query->whereNull('users.school_id')
                    ->orWhere('users.school_id', '!=', $user->school_id);
            })
            ->exists();
    }
}