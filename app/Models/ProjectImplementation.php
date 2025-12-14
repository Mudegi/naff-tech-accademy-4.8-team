<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImplementation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'gathering_resources',
        'activity_execution',
        'stakeholder_engagement',
        'producing_product_service',
        'documentation_report',
        'dissemination_presentation',
        'documentation_file_path',
        'presentation_file_path',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the project that owns the implementation
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if the implementation is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the implementation is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Get the documentation file URL
     */
    public function getDocumentationUrl(): ?string
    {
        return $this->documentation_file_path ? asset('storage/' . $this->documentation_file_path) : null;
    }

    /**
     * Get the presentation file URL
     */
    public function getPresentationUrl(): ?string
    {
        return $this->presentation_file_path ? asset('storage/' . $this->presentation_file_path) : null;
    }

    /**
     * Get the status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'in_progress' => 'blue',
            'completed' => 'green',
            'submitted' => 'yellow',
            default => 'gray',
        };
    }
}
