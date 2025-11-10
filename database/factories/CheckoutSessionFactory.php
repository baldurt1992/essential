<?php

namespace Database\Factories;

use App\Domain\Billing\Models\CheckoutSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CheckoutSession>
 */
class CheckoutSessionFactory extends Factory
{
    protected $model = CheckoutSession::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'stripe_session_id' => 'cs_test_' . Str::random(24),
            'mode' => 'subscription',
            'status' => 'open',
            'metadata' => [],
        ];
    }
}
