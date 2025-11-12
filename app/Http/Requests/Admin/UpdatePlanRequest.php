<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id ?? null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('plans', 'slug')->ignore($planId)],
            'description' => ['sometimes', 'nullable', 'string'],
            'billing_interval' => ['sometimes', 'required', 'string', Rule::in(['day', 'week', 'month', 'year'])],
            'billing_interval_count' => ['sometimes', 'required', 'integer', 'min:1', 'max:24'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0.5'],
            'features' => ['sometimes', 'nullable', 'array'],
            'features.*' => ['string', 'max:150'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
