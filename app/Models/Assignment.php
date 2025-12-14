<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'instructions',
        'assignment_file_path',
        'assignment_file_type',
        'teacher_id',
        'school_id',
        'subject_id',
        'class_id',
        'term_id',
        'topic_id',
        'due_date',
        'total_marks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'date',
    ];

    /**
     * Get the teacher who created this assignment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the school this assignment belongs to
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the subject this assignment is for
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class this assignment is for
     */
    public function classRoom()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the term this assignment belongs to
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the topic this assignment covers
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get all submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get submissions that are graded
     */
    public function gradedSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class)->where('status', 'graded');
    }

    /**
     * Get submissions that are pending review
     */
    public function pendingSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class)->where('status', 'submitted');
    }

    /**
     * Check if assignment is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now();
    }

    /**
     * Get students in the class who haven't submitted
     */
    public function getMissingSubmissions()
    {
        $submittedStudentIds = $this->submissions()->pluck('student_id');
        
        return User::where('account_type', 'student')
            ->where('school_id', $this->school_id)
            ->whereHas('classes', function($query) {
                $query->where('classes.id', $this->class_id);
            })
            ->whereNotIn('id', $submittedStudentIds)
            ->get();
    }
}
