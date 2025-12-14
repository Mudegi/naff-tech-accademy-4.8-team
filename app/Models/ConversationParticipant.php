<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $table = 'conversation_participants';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'joined_at',
        'last_read_at',
        'is_active',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the participant
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that owns the participant
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark messages as read for this participant
     */
    public function markAsRead(): void
    {
        $this->update([
            'last_read_at' => now(),
        ]);
    }

    /**
     * Get unread message count for this participant
     */
    public function getUnreadCount(): int
    {
        if (!$this->last_read_at) {
            return $this->conversation->messages()
                ->where('user_id', '!=', $this->user_id)
                ->count();
        }

        return $this->conversation->messages()
            ->where('created_at', '>', $this->last_read_at)
            ->where('user_id', '!=', $this->user_id)
            ->count();
    }

    /**
     * Check if participant has unread messages
     */
    public function hasUnreadMessages(): bool
    {
        return $this->getUnreadCount() > 0;
    }

    /**
     * Scope to get active participants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get participants for a specific conversation
     */
    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope to get participants for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}