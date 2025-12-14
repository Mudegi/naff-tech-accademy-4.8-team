<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the conversation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants in the conversation
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get active participants only
     */
    public function activeParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('is_active', true);
    }

    /**
     * Get all messages in the conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message in the conversation
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    /**
     * Check if a user is a participant in this conversation
     */
    public function hasParticipant($userId): bool
    {
        return $this->activeParticipants()->where('user_id', $userId)->exists();
    }

    /**
     * Add a participant to the conversation
     */
    public function addParticipant($userId): void
    {
        $this->participants()->syncWithoutDetaching([
            $userId => [
                'joined_at' => now(),
                'last_read_at' => null, // Initially null means all messages are unread
                'is_active' => true,
            ]
        ]);
    }

    /**
     * Remove a participant from the conversation
     */
    public function removeParticipant($userId): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'is_active' => false,
        ]);
    }

    /**
     * Get unread message count for a specific user
     */
    public function getUnreadCountForUser($userId): int
    {
        try {
            $participant = $this->participants()
                ->where('user_id', $userId)
                ->first();
            
            if (!$participant) {
                \Log::warning('User ' . $userId . ' not found in conversation ' . $this->id);
                return 0;
            }
            
            $lastReadAt = $participant->pivot->last_read_at;
            $totalMessages = $this->messages()->count();
            
            \Log::info('Conversation ' . $this->id . ' - User ' . $userId . ' - Last read: ' . ($lastReadAt ? $lastReadAt : 'never') . ' - Total messages: ' . $totalMessages);

            if (!$lastReadAt) {
                // If never read, all messages are unread
                return $totalMessages;
            }

            $unreadCount = $this->messages()
                ->where('created_at', '>', $lastReadAt)
                ->where('user_id', '!=', $userId)
                ->count();
                
            \Log::info('Conversation ' . $this->id . ' - User ' . $userId . ' - Unread count: ' . $unreadCount);
            
            return $unreadCount;
        } catch (\Exception $e) {
            \Log::error('Error getting unread count for user ' . $userId . ' in conversation ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark messages as read for a specific user
     */
    public function markAsReadForUser($userId): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Get conversation title for display
     */
    public function getDisplayTitle($userId = null): string
    {
        if ($this->type === 'group' && $this->title) {
            return $this->title;
        }

        if ($this->type === 'private') {
            $otherParticipant = $this->activeParticipants()
                ->where('user_id', '!=', $userId)
                ->first();
            
            return $otherParticipant ? $otherParticipant->name : 'Unknown User';
        }

        return $this->title ?: 'Group Chat';
    }

    /**
     * Scope to get conversations for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('conversation_participants.is_active', true);
        });
    }

    /**
     * Scope to get private conversations between two users
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where('type', 'private')
            ->whereHas('participants', function ($q) use ($userId1) {
                $q->where('user_id', $userId1)->where('conversation_participants.is_active', true);
            })
            ->whereHas('participants', function ($q) use ($userId2) {
                $q->where('user_id', $userId2)->where('conversation_participants.is_active', true);
            });
    }
}