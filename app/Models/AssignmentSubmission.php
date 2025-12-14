<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_file_path',
        'submission_file_type',
        'student_comment',
        'status',
        'grade',
        'teacher_feedback',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the assignment this submission belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student who submitted this assignment
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Check if submission is graded
     */
    public function isGraded()
    {
        return $this->status === 'graded' && $this->grade !== null;
    }

    /**
     * Check if submission is late
     */
    public function isLate()
    {
        if (!$this->assignment->due_date || !$this->submitted_at) {
            return false;
        }
        
        return $this->submitted_at->gt($this->assignment->due_date);
    }

    /**
     * Get grade percentage
     */
    public function getGradePercentage()
    {
        if (!$this->grade || !$this->assignment->total_marks) {
            return null;
        }
        
        return round(($this->grade / $this->assignment->total_marks) * 100, 2);
    }
}
