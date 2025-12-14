<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'type',
        'attachment_path',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['attachment_url'];

    /**
     * Get the conversation that owns the message
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Check if the message has an attachment
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the attachment URL
     */
    public function getAttachmentUrl(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return asset('storage/' . $this->attachment_path);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->getAttachmentUrl();
    }

    /**
     * Get formatted message time
     */
    public function getFormattedTime(): string
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get formatted message date
     */
    public function getFormattedDate(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Check if message was sent today
     */
    public function isToday(): bool
    {
        return $this->created_at->isToday();
    }

    /**
     * Check if message was sent yesterday
     */
    public function isYesterday(): bool
    {
        return $this->created_at->isYesterday();
    }

    /**
     * Get relative time (e.g., "2 hours ago")
     */
    public function getRelativeTime(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get messages for a specific conversation
     */
    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope to get messages from a specific user
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get messages after a specific date
     */
    public function scopeAfter($query, $date)
    {
        return $query->where('created_at', '>', $date);
    }

    /**
     * Scope to get messages before a specific date
     */
    public function scopeBefore($query, $date)
    {
        return $query->where('created_at', '<', $date);
    }
}