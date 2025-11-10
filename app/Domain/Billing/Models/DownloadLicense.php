<?php

namespace App\Domain\Billing\Models;

use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DownloadLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'source_type',
        'source_id',
        'user_id',
        'template_id',
        'purchase_code',
        'download_count',
        'download_limit',
        'expires_at',
        'last_downloaded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (DownloadLicense $license): void {
            if (! $license->uuid) {
                $license->uuid = (string) Str::uuid();
            }

            if (! $license->purchase_code) {
                $license->purchase_code = strtoupper(Str::random(5) . '-' . Str::random(5));
            }
        });
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function canDownload(): bool
    {
        if ($this->expires_at instanceof Carbon && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->download_limit !== null && $this->download_count >= $this->download_limit) {
            return false;
        }

        return true;
    }

    public function registerDownload(): void
    {
        $this->download_count++;
        $this->last_downloaded_at = now();
        $this->save();
    }
}
