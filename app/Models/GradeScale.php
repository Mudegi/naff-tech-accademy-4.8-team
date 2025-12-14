<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScale extends Model
{
    protected $fillable = [
        'grade',
        'min_percentage',
        'max_percentage',
        'points',
        'academic_level',
        'school_id',
        'is_active',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns this custom grade scale.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope to get grade scales for a specific academic level.
     */
    public function scopeForLevel($query, string $level)
    {
        return $query->where('academic_level', $level);
    }

    /**
     * Scope to get grade scales for a specific school (or default if school_id is null).
     */
    public function scopeForSchool($query, ?int $schoolId = null)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope to get active grade scales only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get points for a given percentage and academic level.
     * Tries school-specific scale first, falls back to default scale.
     *
     * @param float $percentage The percentage score (0-100)
     * @param string $academicLevel 'O-Level' or 'A-Level'
     * @param int|null $schoolId School ID for custom scale lookup
     * @return array ['grade' => 'A', 'points' => 6]
     */
    public static function getGradeAndPoints(float $percentage, string $academicLevel, ?int $schoolId = null): array
    {
        // Try school-specific scale first if school ID is provided
        if ($schoolId) {
            $scale = static::active()
                ->forLevel($academicLevel)
                ->forSchool($schoolId)
                ->where('min_percentage', '<=', $percentage)
                ->where('max_percentage', '>=', $percentage)
                ->first();

            if ($scale) {
                return [
                    'grade' => $scale->grade,
                    'points' => $scale->points,
                ];
            }
        }

        // Fall back to default system-wide scale (school_id = null)
        $scale = static::active()
            ->forLevel($academicLevel)
            ->forSchool(null)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        if ($scale) {
            return [
                'grade' => $scale->grade,
                'points' => $scale->points,
            ];
        }

        // If no scale found, return F grade with 0 points
        return [
            'grade' => 'F',
            'points' => 0,
        ];
    }

    /**
     * Get just the points for a given percentage.
     *
     * @param float $percentage The percentage score (0-100)
     * @param string $academicLevel 'O-Level' or 'A-Level'
     * @param int|null $schoolId School ID for custom scale lookup
     * @return int Points awarded
     */
    public static function getPointsForPercentage(float $percentage, string $academicLevel, ?int $schoolId = null): int
    {
        $result = static::getGradeAndPoints($percentage, $academicLevel, $schoolId);
        return $result['points'];
    }

    /**
     * Get just the grade for a given percentage.
     *
     * @param float $percentage The percentage score (0-100)
     * @param string $academicLevel 'O-Level' or 'A-Level'
     * @param int|null $schoolId School ID for custom scale lookup
     * @return string Grade letter (A, B, C, D, E, O, F)
     */
    public static function getGradeForPercentage(float $percentage, string $academicLevel, ?int $schoolId = null): string
    {
        $result = static::getGradeAndPoints($percentage, $academicLevel, $schoolId);
        return $result['grade'];
    }

    /**
     * Get all grade scales for a specific level and school.
     * Returns school-specific scales if available, otherwise returns default scales.
     *
     * @param string $academicLevel 'O-Level' or 'A-Level'
     * @param int|null $schoolId School ID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getScalesForSchool(string $academicLevel, ?int $schoolId = null)
    {
        // Try to get school-specific scales first
        if ($schoolId) {
            $scales = static::active()
                ->forLevel($academicLevel)
                ->forSchool($schoolId)
                ->orderByDesc('points')
                ->get();

            if ($scales->isNotEmpty()) {
                return $scales;
            }
        }

        // Fall back to default scales
        return static::active()
            ->forLevel($academicLevel)
            ->forSchool(null)
            ->orderByDesc('points')
            ->get();
    }
}
