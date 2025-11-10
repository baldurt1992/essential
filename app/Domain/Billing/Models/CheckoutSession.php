<?php

namespace App\Domain\Billing\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class CheckoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'stripe_session_id',
        'mode',
        'status',
        'reference_type',
        'reference_id',
        'metadata',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CheckoutSession $session): void {
            if (! $session->uuid) {
                $session->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }
}
