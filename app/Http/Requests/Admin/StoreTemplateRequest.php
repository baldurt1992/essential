<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:templates,slug'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.5'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'is_popular' => ['boolean'],
            'is_new' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'preview_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'package_file' => ['required', 'file', 'mimes:zip,rar', 'max:51200'],
        ];
    }
}
