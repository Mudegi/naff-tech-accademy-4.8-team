<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\TenantScope;

class SchoolClass extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'grade_level',
        'level',
        'term',
        'start_date',
        'end_date',
        'is_active',
        'school_id',
        'is_system_class',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system_class' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'level' => 'string',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'class_id');
    }

    /**
     * Scope for system-wide classes (available to all schools)
     */
    public function scopeSystemClasses($query)
    {
        return $query->where('is_system_class', true);
    }

    /**
     * Scope for school-specific classes
     */
    public function scopeSchoolClasses($query, $schoolId = null)
    {
        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }
        return $query->whereNotNull('school_id');
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available classes for a school (system classes + school-specific classes)
     */
    public static function getAvailableForSchool($schoolId = null)
    {
        $query = self::active();

        if ($schoolId) {
            $query->where(function($q) use ($schoolId) {
                $q->where('is_system_class', true)
                  ->orWhere('school_id', $schoolId);
            });
        } else {
            $query->where('is_system_class', true);
        }

        return $query->orderBy('grade_level')->get();
    }
} 