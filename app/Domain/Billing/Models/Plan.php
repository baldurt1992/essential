<?php

namespace App\Domain\Billing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'billing_interval',
        'billing_interval_count',
        'price_cents',
        'currency',
        'stripe_product_id',
        'stripe_price_id',
        'is_active',
        'features',
        'metadata',
        'sort_order',
        'download_limit',
        'unlimited_downloads',
        'created_by',
    ];

    protected $casts = [
        'billing_interval_count' => 'integer',
        'price_cents' => 'integer',
        'is_active' => 'boolean',
        'features' => 'array',
        'metadata' => 'array',
        'download_limit' => 'integer',
        'unlimited_downloads' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Plan $plan): void {
            if (! $plan->uuid) {
                $plan->uuid = (string) Str::uuid();
            }

            if (! $plan->slug) {
                $plan->slug = self::generateUniqueSlug($plan->name);
            }
        });

        static::updating(function (Plan $plan): void {
            if ($plan->isDirty('name') && ! $plan->isDirty('slug')) {
                $plan->slug = self::generateUniqueSlug($plan->name, $plan->getKey());
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    private static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while (self::query()
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getPriceAttribute(): float
    {
        return $this->price_cents / 100;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();

        // Trim whitespace and ensure proper UUID format
        $value = trim((string) $value);

        $plan = $this->where($field, $value)->first();

        if (!$plan) {
            \Illuminate\Support\Facades\Log::warning('Plan not found for route binding', [
                'uuid' => $value,
                'field' => $field,
                'request_url' => request()->fullUrl(),
            ]);

            abort(404, "No query results for model [{$this->getMorphClass()}] {$value}");
        }

        return $plan;
    }
}
