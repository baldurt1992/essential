<?php

namespace App\Providers;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Infrastructure\Billing\Stripe\StripeBillingGateway;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function (): StripeClient {
            return new StripeClient(config('services.stripe.secret'));
        });

        $this->app->bind(BillingGateway::class, function ($app): BillingGateway {
            return new StripeBillingGateway($app->make(StripeClient::class));
        });
    }
}
