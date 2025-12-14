<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCutOff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'university_name',
        'university_code',
        'course_name',
        'course_code',
        'course_description',
        'faculty',
        'department',
        'minimum_principal_passes',
        'minimum_aggregate_points',
        'cut_off_points',
        'cut_off_points_male',
        'cut_off_points_female',
        'program_category',
        'cut_off_format',
        'cut_off_structure',
        'academic_year',
        'academic_level',
        'essential_subjects',
        'relevant_subjects',
        'desirable_subjects',
        'additional_requirements',
        'duration_years',
        'degree_type',
        'is_active',
    ];

    protected $casts = [
        'minimum_aggregate_points' => 'decimal:2',
        'cut_off_points' => 'decimal:2',
        'cut_off_points_male' => 'decimal:2',
        'cut_off_points_female' => 'decimal:2',
        'academic_year' => 'integer',
        'essential_subjects' => 'array',
        'relevant_subjects' => 'array',
        'desirable_subjects' => 'array',
        'cut_off_structure' => 'array',
        'duration_years' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get active cut-offs only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to filter by minimum points (considers gender for STEM programs).
     */
    public function scopeQualifying($query, $studentPoints, $gender = null)
    {
        return $query->where(function($q) use ($studentPoints, $gender) {
            // For STEM programs with gender-specific cut-offs
            if ($gender === 'male' || $gender === 'M') {
                $q->where(function($subQ) use ($studentPoints) {
                    $subQ->where('program_category', 'stem')
                         ->where(function($stemQ) use ($studentPoints) {
                             $stemQ->where('cut_off_points_male', '<=', $studentPoints)
                                   ->orWhereNull('cut_off_points_male');
                         });
                })
                // Or for other programs
                ->orWhere(function($otherQ) use ($studentPoints) {
                    $otherQ->where('program_category', '!=', 'stem')
                           ->where('cut_off_points', '<=', $studentPoints);
                });
            } elseif ($gender === 'female' || $gender === 'F') {
                $q->where(function($subQ) use ($studentPoints) {
                    $subQ->where('program_category', 'stem')
                         ->where(function($stemQ) use ($studentPoints) {
                             $stemQ->where('cut_off_points_female', '<=', $studentPoints)
                                   ->orWhereNull('cut_off_points_female');
                         });
                })
                // Or for other programs
                ->orWhere(function($otherQ) use ($studentPoints) {
                    $otherQ->where('program_category', '!=', 'stem')
                           ->where('cut_off_points', '<=', $studentPoints);
                });
            } else {
                // No gender specified - check all possible cut-offs
                $q->where(function($allQ) use ($studentPoints) {
                    $allQ->where('cut_off_points', '<=', $studentPoints)
                         ->orWhere('cut_off_points_male', '<=', $studentPoints)
                         ->orWhere('cut_off_points_female', '<=', $studentPoints);
                });
            }
        });
    }

    /**
     * Get the effective cut-off point for a student based on their gender and university format.
     */
    public function getEffectiveCutOff($gender = null)
    {
        // Handle different university formats
        switch ($this->cut_off_format) {
            case 'makerere':
                // Makerere format: STEM programs have gender-specific, Other programs have single cut-off
                if ($this->program_category === 'stem') {
                    if ($gender === 'male' || $gender === 'M') {
                        return $this->cut_off_points_male ?? $this->cut_off_points;
                    } elseif ($gender === 'female' || $gender === 'F') {
                        return $this->cut_off_points_female ?? $this->cut_off_points;
                    }
                    // If gender not specified, return the lower of the two (more inclusive)
                    if ($this->cut_off_points_male !== null && $this->cut_off_points_female !== null) {
                        return min($this->cut_off_points_male, $this->cut_off_points_female);
                    }
                    return $this->cut_off_points_male ?? $this->cut_off_points_female ?? $this->cut_off_points;
                }
                // For non-STEM programs, use the general cut-off
                return $this->cut_off_points;
                
            case 'kyambogo':
                // Kyambogo format: Simple single cut-off point for all programs (no gender differentiation)
                // Location variations (Day/Evening/Bushenyi/Soroti) are part of the program name
                // All programs use a single cut-off point regardless of gender
                return $this->cut_off_points;
                
            case 'custom':
                // Custom format: Use cut_off_structure JSON
                if ($this->cut_off_structure && is_array($this->cut_off_structure)) {
                    // Try to find gender-specific or general cut-off
                    if ($gender === 'male' || $gender === 'M') {
                        return $this->cut_off_structure['male'] ?? $this->cut_off_structure['M'] ?? $this->cut_off_structure['cut_off'] ?? $this->cut_off_points;
                    } elseif ($gender === 'female' || $gender === 'F') {
                        return $this->cut_off_structure['female'] ?? $this->cut_off_structure['F'] ?? $this->cut_off_structure['cut_off'] ?? $this->cut_off_points;
                    }
                    return $this->cut_off_structure['cut_off'] ?? $this->cut_off_structure['all'] ?? $this->cut_off_points;
                }
                return $this->cut_off_points;
                
            case 'standard':
            default:
                // Standard format: Use program_category logic
                if ($this->program_category === 'stem') {
                    if ($gender === 'male' || $gender === 'M') {
                        return $this->cut_off_points_male ?? $this->cut_off_points;
                    } elseif ($gender === 'female' || $gender === 'F') {
                        return $this->cut_off_points_female ?? $this->cut_off_points;
                    }
                    if ($this->cut_off_points_male !== null && $this->cut_off_points_female !== null) {
                        return min($this->cut_off_points_male, $this->cut_off_points_female);
                    }
                    return $this->cut_off_points_male ?? $this->cut_off_points_female ?? $this->cut_off_points;
                }
                return $this->cut_off_points;
        }
    }

    /**
     * Check if a student qualifies based on their aggregate points and gender.
     */
    public function studentQualifies($studentAggregatePoints, $studentPrincipalPasses = 0, $gender = null)
    {
        // Check minimum principal passes
        if ($studentPrincipalPasses < $this->minimum_principal_passes) {
            return false;
        }

        // Check minimum aggregate points if specified
        if ($this->minimum_aggregate_points !== null && $studentAggregatePoints < $this->minimum_aggregate_points) {
            return false;
        }

        // Get the effective cut-off point based on program category and gender
        $effectiveCutOff = $this->getEffectiveCutOff($gender);
        
        if ($effectiveCutOff === null) {
            return false; // No cut-off defined
        }

        // Check cut-off points
        if ($studentAggregatePoints < $effectiveCutOff) {
            return false;
        }

        return true;
    }
}
