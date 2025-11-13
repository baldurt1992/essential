<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:plans,slug'],
            'description' => ['nullable', 'string'],
            'billing_interval' => ['required', 'string', Rule::in(['day', 'week', 'month', 'year'])],
            'billing_interval_count' => ['required', 'integer', 'min:1', 'max:24'],
            'price' => ['required', 'numeric', 'min:0.5'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:150'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'download_limit' => ['nullable', 'integer', 'min:1'],
            'unlimited_downloads' => ['boolean'],
        ];
    }
}
