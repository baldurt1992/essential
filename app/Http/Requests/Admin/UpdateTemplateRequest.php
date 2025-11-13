<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('templates', 'slug')->ignore($this->route('template'))],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0.5'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'is_popular' => ['sometimes', 'boolean'],
            'is_new' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'preview_image' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'package_file' => ['sometimes', 'nullable', 'file', 'mimes:zip,rar', 'max:51200'], // 50MB en KB
        ];
    }

    public function messages(): array
    {
        return [
            'package_file.max' => 'El archivo es demasiado grande. El tamaño máximo permitido es 50MB.',
            'package_file.mimes' => 'El archivo debe ser un archivo ZIP o RAR.',
            'preview_image.max' => 'La imagen es demasiado grande. El tamaño máximo permitido es 5MB.',
            'preview_image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
        ];
    }
}
