<?php

namespace App\Domain\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInformation extends Model
{
    use HasFactory;

    protected $table = 'contact_information';

    protected $fillable = [
        'email',
        'phone',
        'whatsapp',
        'location_line_one',
        'location_line_two',
        'support_hours',
        'instagram_url',
        'facebook_url',
        'behance_url',
        'dribbble_url',
        'website_url',
        'contact_note',
        'metadata',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

