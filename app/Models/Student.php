<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScope;

class Student extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'user_id',
        'account_type',
        'first_name',
        'last_name',
        'middle_name',
        'school_name',
        'academic_levels',
        'registration_number',
        'phone_number',
        'email_verified',
        'phone_verified',
        'classes',
        'class',
        'level',
        'is_referral',
        'referee_name',
        'referee_contact',
        'how_you_know_us',
        'date_of_birth',
        'subscription_package_id',
        'subscription_start_date',
        'subscription_end_date',
        'school_id',
        'level',
        'combination',
    ];

    protected $casts = [
        'academic_levels' => 'array',
        'classes' => 'array',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'is_referral' => 'boolean',
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'date_of_birth' => 'date',
        'level' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the school class that the student belongs to
     * This is an accessor method, not a relationship
     */
    public function getSchoolClassAttribute()
    {
        // Get the first class from the student's user
        if ($this->user) {
            return $this->user->classes()->first();
        }
        return null;
    }
} 