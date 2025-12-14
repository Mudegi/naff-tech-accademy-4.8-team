<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\TenantScope;

class Department extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'head_of_department_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns this department.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the head of department for this department.
     */
    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }

    /**
     * Get all teachers in this department.
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(User::class, 'department_id')
            ->where('account_type', 'subject_teacher');
    }

    /**
     * Get all staff members in this department (HOD + Teachers).
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'department_id')
            ->whereIn('account_type', ['head_of_department', 'subject_teacher']);
    }
}
