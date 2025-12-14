<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone_number',
        'address',
        'logo',
        'website',
        'status',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_package_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    /**
     * Get all users belonging to this school.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the school admin user.
     */
    public function admin(): HasMany
    {
        return $this->hasMany(User::class)->where('account_type', 'school_admin');
    }

    /**
     * Get all students belonging to this school.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get all subjects belonging to this school.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get all classes belonging to this school.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Get all departments belonging to this school.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the subscription package for this school.
     */
    public function subscriptionPackage(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    /**
     * Get all subscriptions for this school.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    /**
     * Get the active subscription for this school.
     */
    public function activeSubscription()
    {
        return $this->hasOne(SchoolSubscription::class)
            ->where('is_active', true)
            ->where('payment_status', 'completed')
            ->where('end_date', '>=', now())
            ->latest();
    }

    /**
     * Check if school subscription is active.
     */
    public function isSubscriptionActive(): bool
    {
        $activeSubscription = $this->activeSubscription;
        return $activeSubscription && $activeSubscription->isActive();
    }

    /**
     * Activate a subscription for the school.
     */
    public function activateSubscription(SchoolSubscription $subscription)
    {
        // Deactivate all other subscriptions
        $this->subscriptions()->update(['is_active' => false]);

        // Activate this subscription
        $subscription->update(['is_active' => true]);

        // Update school subscription dates and activate school
        $this->update([
            'subscription_package_id' => $subscription->subscription_package_id,
            'subscription_start_date' => $subscription->start_date,
            'subscription_end_date' => $subscription->end_date,
            'status' => 'active', // Activate school when subscription is approved
        ]);
    }

    /**
     * Deactivate school subscription.
     */
    public function deactivateSubscription()
    {
        // Deactivate all subscriptions
        $this->subscriptions()->update(['is_active' => false]);

        // Update school status to inactive
        $this->update([
            'subscription_package_id' => null,
            'subscription_start_date' => null,
            'subscription_end_date' => null,
            'status' => 'inactive', // Deactivate school when subscription ends
        ]);
    }

    /**
     * Check if school subscription has expired and deactivate if needed.
     */
    public function checkSubscriptionExpiry()
    {
        $activeSubscription = $this->activeSubscription;

        // If school is active but has no active subscription or expired subscription
        if ($this->status === 'active' && (!$activeSubscription || !$activeSubscription->isActive())) {
            $this->deactivateSubscription();
            return true; // School was deactivated
        }

        return false; // No action needed
    }
}
