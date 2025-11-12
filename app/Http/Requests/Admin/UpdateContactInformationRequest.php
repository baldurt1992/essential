<?php

namespace App\Http\Requests\Admin;

use App\Domain\Settings\Services\ContactInformationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'location_line_one' => ['nullable', 'string', 'max:255'],
            'location_line_two' => ['nullable', 'string', 'max:255'],
            'support_hours' => ['nullable', 'string', 'max:255'],
            'instagram_url' => [
                'nullable',
                'url',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && !ContactInformationService::isValidSocialUrl('instagram', $value)) {
                        $fail('La URL de Instagram no es válida. Debe ser un enlace de instagram.com o instagr.am');
                    }
                },
            ],
            'facebook_url' => [
                'nullable',
                'url',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && !ContactInformationService::isValidSocialUrl('facebook', $value)) {
                        $fail('La URL de Facebook no es válida. Debe ser un enlace de facebook.com, fb.com o fb.me');
                    }
                },
            ],
            'behance_url' => ['nullable', 'url', 'max:255'],
            'dribbble_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'contact_note' => ['nullable', 'string', 'max:2000'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.network' => ['required', 'string', Rule::in(ContactInformationService::AVAILABLE_SOCIAL_NETWORKS)],
            'social_links.*.url' => [
                'required',
                'url',
                'max:255',
                function ($attribute, $value, $fail) {
                    $index = (int) preg_replace('/[^0-9]/', '', explode('.', $attribute)[1] ?? '0');
                    $network = $this->input("social_links.{$index}.network");
                    
                    if ($network && !ContactInformationService::isValidSocialUrl($network, $value)) {
                        $fail("La URL de {$network} no es válida. Debe corresponder al dominio correcto de la red social.");
                    }
                },
            ],
        ];
    }
}
