<?php

namespace App\Domain\Billing\Models;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_price_id',
        'status',
        'quantity',
        'cancel_at_period_end',
        'trial_ends_at',
        'starts_at',
        'current_period_start',
        'current_period_end',
        'cancel_at',
        'canceled_at',
        'ends_at',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cancel_at_period_end' => 'boolean',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancel_at' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',
        'status' => SubscriptionStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscription $subscription): void {
            if (! $subscription->uuid) {
                $subscription->uuid = (string) Str::uuid();
            }
        });
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::Active);
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function willCancel(): bool
    {
        return $this->cancel_at_period_end === true;
    }
}
