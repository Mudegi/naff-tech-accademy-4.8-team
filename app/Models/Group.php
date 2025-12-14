<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'school_id',
        'class_id',
        'subject_id',
        'status',
        'max_members',
    ];

    protected $casts = [
        'max_members' => 'integer',
    ];

    /**
     * Get the creator of the group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the school that the group belongs to
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class that the group belongs to
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get all members of the group
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('role', 'status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get approved members of the group
     */
    public function approvedMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'approved');
    }

    /**
     * Get the leader of the group
     */
    public function leader(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'leader');
    }

    /**
     * Get all projects for this group
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the subject this group is associated with
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get all submissions for this group
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(GroupSubmission::class);
    }

    /**
     * Check if the group is full
     */
    public function isFull(): bool
    {
        return $this->approvedMembers()->count() >= $this->max_members;
    }

    /**
     * Check if a user is a member of the group
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->wherePivot('status', 'approved')->exists();
    }

    /**
     * Check if a user is the leader of the group
     */
    public function isLeader(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->wherePivot('role', 'leader')->exists();
    }
}
