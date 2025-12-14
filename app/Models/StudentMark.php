<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMark extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_id',
        'group_id',
        'academic_level',
        'subject_name',
        'paper_name',
        'grade',
        'numeric_mark',
        'grade_type',
        'points',
        'is_principal_pass',
        'is_essential',
        'is_relevant',
        'is_desirable',
        'academic_year',
        'exam_type',
        'exam_type_other',
        'remarks',
        'school_id',
        'uploaded_by',
        'class_id',
    ];

    protected $casts = [
        'numeric_mark' => 'decimal:2',
        'is_principal_pass' => 'boolean',
        'is_essential' => 'boolean',
        'is_relevant' => 'boolean',
        'is_desirable' => 'boolean',
        'academic_year' => 'integer',
    ];

    /**
     * Get the user that owns this mark.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student profile associated with this mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the group for which this mark was awarded (for group work).
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who uploaded this mark (teacher or student).
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the class associated with this mark.
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Calculate points based on Ugandan grading system.
     * UACE: A=6, B=5, C=4, D=3, E=2, O=1, F=0
     */
    public function calculatePoints()
    {
        if ($this->points !== null) {
            return $this->points;
        }

        $grade = strtoupper(trim($this->grade));
        
        // Letter grades (UACE)
        $letterGrades = [
            'A' => 6,
            'B' => 5,
            'C' => 4,
            'D' => 3,
            'E' => 2,
            'O' => 1,
            'F' => 0,
        ];

        if (isset($letterGrades[$grade])) {
            return $letterGrades[$grade];
        }

        // Distinction/Credit/Pass system
        if (preg_match('/distinction\s*(\d+)/i', $grade, $matches)) {
            $level = (int)$matches[1];
            return 6 - ($level - 1); // Distinction 1 = 6, Distinction 2 = 5, etc.
        }

        if (preg_match('/credit\s*(\d+)/i', $grade, $matches)) {
            $level = (int)$matches[1];
            return 4 - ($level - 3); // Credit 3 = 4, Credit 4 = 3, etc.
        }

        if (preg_match('/pass\s*(\d+)/i', $grade, $matches)) {
            $level = (int)$matches[1];
            return max(1, 2 - ($level - 7)); // Pass 7 = 1, Pass 8 = 0
        }

        // Numeric marks (convert percentage to points)
        if ($this->numeric_mark !== null) {
            $percentage = $this->numeric_mark;
            if ($percentage >= 80) return 6; // A
            if ($percentage >= 70) return 5; // B
            if ($percentage >= 60) return 4; // C
            if ($percentage >= 50) return 3; // D
            if ($percentage >= 40) return 2; // E
            if ($percentage >= 30) return 1; // O
            return 0; // F
        }

        return 0;
    }

    /**
     * Automatically calculate and set points when saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($mark) {
            if ($mark->points === null || $mark->isDirty(['grade', 'numeric_mark'])) {
                $mark->points = $mark->calculatePoints();
            }
        });
    }
}
