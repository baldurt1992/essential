<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'uuid' => Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title).
                '-'.Str::random(4),
            'description' => fake()->paragraph(),
            'price_cents' => fake()->numberBetween(900, 9900),
            'currency' => 'eur',
            'tags' => fake()->words(3),
            'metadata' => [],
            'is_active' => true,
        ];
    }
}
