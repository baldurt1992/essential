<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlanCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'success_url' => ['nullable', 'string', 'url', 'max:500'],
            'cancel_url' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }
}
