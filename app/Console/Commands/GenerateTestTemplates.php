<?php

namespace App\Console\Commands;

use App\Domain\Catalog\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTestTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:generate-test {count=10 : Número de plantillas a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera plantillas de prueba para testing y navegación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        if ($count < 1) {
            $this->error('El número de plantillas debe ser mayor a 0');
            return Command::FAILURE;
        }

        $this->info("Generando {$count} plantillas de prueba...");

        $templates = [
            'Flyer Summer Party',
            'Poster Electronic Festival',
            'Flyer Luxury Night',
            'Banner Corporate Event',
            'Poster Music Concert',
            'Flyer Night Club',
            'Banner Product Launch',
            'Poster Art Exhibition',
            'Flyer Food Festival',
            'Banner Tech Conference',
            'Poster Fashion Show',
            'Flyer Sports Event',
            'Banner Charity Gala',
            'Poster Film Premiere',
            'Flyer Wedding Reception',
        ];

        $tags = [
            ['flyer', 'summer', 'party'],
            ['poster', 'festival', 'electronic'],
            ['flyer', 'luxury', 'nightlife'],
            ['banner', 'corporate', 'business'],
            ['poster', 'music', 'concert'],
            ['flyer', 'club', 'night'],
            ['banner', 'product', 'launch'],
            ['poster', 'art', 'exhibition'],
            ['flyer', 'food', 'festival'],
            ['banner', 'tech', 'conference'],
            ['poster', 'fashion', 'show'],
            ['flyer', 'sports', 'event'],
            ['banner', 'charity', 'gala'],
            ['poster', 'film', 'premiere'],
            ['flyer', 'wedding', 'reception'],
        ];

        $descriptions = [
            'Plantilla editable para fiestas de verano con tipografía vibrante.',
            'Poster multi-formato para festivales electrónicos.',
            'Diseño elegante para eventos VIP, incluye variantes en PSD y AI.',
            'Banner profesional para eventos corporativos.',
            'Poster moderno para conciertos de música.',
            'Flyer dinámico para clubes nocturnos.',
            'Banner impactante para lanzamientos de productos.',
            'Poster artístico para exposiciones.',
            'Flyer colorido para festivales gastronómicos.',
            'Banner tecnológico para conferencias.',
            'Poster elegante para desfiles de moda.',
            'Flyer deportivo para eventos atléticos.',
            'Banner sofisticado para galas benéficas.',
            'Poster cinematográfico para estrenos.',
            'Flyer romántico para recepciones de boda.',
        ];

        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $index = $i % count($templates);
            $title = $templates[$index] . ' ' . ($i >= count($templates) ? ($i + 1) : '');
            $slug = Str::slug($title) . '-' . Str::random(4);

            // Verificar si ya existe
            if (Template::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            $template = Template::create([
                'uuid' => Str::uuid(),
                'title' => $title,
                'slug' => $slug,
                'description' => $descriptions[$index],
                'price_cents' => fake()->numberBetween(1499, 4999),
                'currency' => config('billing.currency', 'eur'),
                'preview_image_path' => null,
                'download_path' => null,
                'tags' => $tags[$index],
                'metadata' => [
                    'test' => true,
                    'generated_at' => now()->toIso8601String(),
                ],
                'is_active' => true,
                'is_popular' => fake()->boolean(30),
                'is_new' => fake()->boolean(20),
                'sort_order' => ($i + 1) * 10,
                'created_by' => null,
            ]);

            $created++;
            $this->line("✓ Creada: {$template->title} (ID: {$template->id})");
        }

        $this->newLine();
        $this->info("✅ Se generaron {$created} plantillas de prueba exitosamente.");
        $this->line("Total de plantillas activas: " . Template::where('is_active', true)->count());

        return Command::SUCCESS;
    }
}
