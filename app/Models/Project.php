<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'group_id',
        'created_by',
        'school_id',
        'class_id',
        'status',
        'start_date',
        'end_date',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get the group that owns the project
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the creator of the project
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the school that the project belongs to
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class that the project belongs to
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the project planning
     */
    public function planning(): HasOne
    {
        return $this->hasOne(ProjectPlanning::class);
    }

    /**
     * Get the project implementation
     */
    public function implementation(): HasOne
    {
        return $this->hasOne(ProjectImplementation::class);
    }

    /**
     * Check if the project is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the project is in planning phase
     */
    public function isInPlanning(): bool
    {
        return $this->status === 'planning';
    }

    /**
     * Check if the project is in implementation phase
     */
    public function isInImplementation(): bool
    {
        return $this->status === 'implementation';
    }

    /**
     * Get the project status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'planning' => 'blue',
            'implementation' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
