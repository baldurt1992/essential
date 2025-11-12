<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    private array $services = [
        [
            'title' => 'Flyers personalizados',
            'slug' => 'flyers-personalizados',
            'description' => 'Diseños exclusivos para tus eventos con entrega en formatos editables.',
            'image_path' => 'images/services/flyers.png',
            'link_url' => '/servicios/flyers',
            'is_popular' => true,
            'sort_order' => 10,
        ],
        [
            'title' => 'Animación Motion',
            'slug' => 'animacion-motion',
            'description' => 'Secuencias animadas para redes sociales y anuncios digitales.',
            'image_path' => 'images/services/motion.png',
            'link_url' => '/servicios/animacion',
            'is_popular' => true,
            'sort_order' => 20,
        ],
        [
            'title' => 'Gestión de redes',
            'slug' => 'gestion-redes',
            'description' => 'Planificación y ejecución de contenido de alto impacto.',
            'image_path' => 'images/services/social.png',
            'link_url' => '/servicios/rrss',
            'is_popular' => false,
            'sort_order' => 30,
        ],
    ];

    public function run(): void
    {
        foreach ($this->services as $definition) {
            Service::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                array_merge($definition, [
                    'uuid' => Service::where('slug', $definition['slug'])->value('uuid') ?? (string) Str::uuid(),
                    'is_active' => true,
                ])
            );
        }
    }
}
