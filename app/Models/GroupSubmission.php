<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'subject_id',
        'class_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_type',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the group that owns this submission
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the student who uploaded this submission
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the subject for this submission
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class for this submission
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
