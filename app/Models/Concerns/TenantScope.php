<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait TenantScope
{
    /**
     * Boot the tenant scope.
     */
    protected static function bootTenantScope()
    {
        // Apply scope when querying - only for users with school_id
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $schoolId = null;
                
                // For super admin, check if they have a school context set in session
                if (!$user->school_id && $user->account_type === 'admin') {
                    $schoolId = Session::get('admin_school_context');
                } else {
                    // For regular school users, use their school_id
                    $schoolId = $user->school_id;
                }
                
                // Apply scope if we have a school_id
                if ($schoolId) {
                    $builder->where('school_id', $schoolId);
                }
            }
        });

        // Automatically set school_id when creating
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $schoolId = null;
                
                // For super admin, check if they have a school context set in session
                if (!$user->school_id && $user->account_type === 'admin') {
                    $schoolId = Session::get('admin_school_context');
                } else {
                    // For regular school users, use their school_id
                    $schoolId = $user->school_id;
                }
                
                // Only set school_id if we have one and model doesn't already have it
                if ($schoolId && !$model->school_id) {
                    $model->school_id = $schoolId;
                }
            }
        });

        // Prevent updating school_id to a different school
        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                // If user has school_id, prevent changing model's school_id to a different school
                if ($user->school_id && $model->isDirty('school_id')) {
                    $originalSchoolId = $model->getOriginal('school_id');
                    // Only allow if original was null (making it school-specific) or same school
                    if ($originalSchoolId !== null && $originalSchoolId != $user->school_id) {
                        // Revert school_id change if trying to change to different school
                        $model->school_id = $originalSchoolId;
                    }
                }
            }
        });
    }

    /**
     * Get the school that owns this model.
     */
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    /**
     * Scope a query to only include records for a specific school.
     */
    public function scopeForSchool(Builder $query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to include records without school (global records).
     */
    public function scopeWithoutSchool(Builder $query)
    {
        return $query->whereNull('school_id');
    }
}

