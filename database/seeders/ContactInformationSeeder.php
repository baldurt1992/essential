<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\ContactInformation;
use Illuminate\Database\Seeder;

class ContactInformationSeeder extends Seeder
{
    public function run(): void
    {
        ContactInformation::query()->firstOrCreate([], [
            'email' => 'info@essential-innovation.com',
            'phone' => '+00 000 000 000',
            'whatsapp' => '+00 000 000 000',
            'location_line_one' => 'Bogotá, Colombia',
            'support_hours' => 'Lunes a Viernes - 9:00 a 18:00 (GMT-5)',
            'instagram_url' => 'https://instagram.com/essential_innovation',
            'facebook_url' => 'https://facebook.com/essentialinnovation',
            'website_url' => 'https://essentialinnovation.com',
            'contact_note' => 'Responde en menos de 24 horas hábiles.',
            'metadata' => [
                'social_links' => [
                    ['network' => 'website', 'url' => 'https://essentialinnovation.com'],
                    ['network' => 'instagram', 'url' => 'https://instagram.com/essential_innovation'],
                    ['network' => 'facebook', 'url' => 'https://facebook.com/essentialinnovation'],
                ],
            ],
        ]);
    }
}
