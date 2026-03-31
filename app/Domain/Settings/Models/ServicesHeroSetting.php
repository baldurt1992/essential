<?php

namespace App\Domain\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicesHeroSetting extends Model
{
    protected $table = 'services_hero_settings';

    protected $fillable = [
        'hero_video_path',
        'updated_by',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
