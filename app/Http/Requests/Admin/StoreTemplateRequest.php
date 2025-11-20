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
            'preview_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'], // 10MB en KB
            'package_file' => ['required', 'file', 'mimes:zip,rar', 'max:153600'], // 150MB en KB
        ];
    }

    public function messages(): array
    {
        return [
            'package_file.max' => 'El archivo es demasiado grande. El tamaño máximo permitido es 150MB.',
            'package_file.mimes' => 'El archivo debe ser un archivo ZIP o RAR.',
            'preview_image.max' => 'La imagen es demasiado grande. El tamaño máximo permitido es 10MB.',
            'preview_image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
        ];
    }
}
