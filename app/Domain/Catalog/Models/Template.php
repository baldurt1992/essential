<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Billing\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'price_cents',
        'currency',
        'stripe_product_id',
        'stripe_price_id',
        'preview_image_path',
        'download_path',
        'tags',
        'metadata',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Template $template): void {
            if (! $template->uuid) {
                $template->uuid = (string) Str::uuid();
            }

            if (! $template->slug) {
                $template->slug = self::generateUniqueSlug($template->title);
            }
        });

        static::updating(function (Template $template): void {
            if ($template->isDirty('title') && ! $template->isDirty('slug')) {
                $template->slug = self::generateUniqueSlug($template->title, $template->getKey());
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function getPriceAttribute(): float
    {
        return $this->price_cents / 100;
    }

    private static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
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
}
