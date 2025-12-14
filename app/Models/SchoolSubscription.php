<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\TenantScope;

class SchoolSubscription extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'school_id',
        'subscription_package_id',
        'amount_paid',
        'payment_status',
        'payment_method',
        'transaction_id',
        'payment_reference',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns this subscription.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the subscription package.
     */
    public function subscriptionPackage(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    /**
     * Check if subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active 
            && $this->payment_status === 'completed'
            && $this->end_date->isFuture()
            && $this->start_date->isPast();
    }
}
