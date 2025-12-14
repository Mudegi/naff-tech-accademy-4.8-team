<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'purpose_objectives',
        'justification',
        'resource_identification',
        'activity_plan_path',
        'status',
        'review_comments',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the project that owns the planning
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if the planning is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if the planning is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get the activity plan file URL
     */
    public function getActivityPlanUrl(): ?string
    {
        return $this->activity_plan_path ? asset('storage/' . $this->activity_plan_path) : null;
    }

    /**
     * Get the status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
