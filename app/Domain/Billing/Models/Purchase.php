<?php

namespace App\Domain\Billing\Models;

use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'guest_email',
        'template_id',
        'checkout_session_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'purchase_code',
        'amount_cents',
        'currency',
        'status',
        'metadata',
        'purchased_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'purchased_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase): void {
            if (! $purchase->uuid) {
                $purchase->uuid = (string) Str::uuid();
            }

            if (! $purchase->purchase_code) {
                $purchase->purchase_code = self::generateUniqueCode();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function checkoutSession(): BelongsTo
    {
        return $this->belongsTo(CheckoutSession::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (self::where('purchase_code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('uuid', $value)->firstOrFail();
    }
}
