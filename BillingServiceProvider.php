<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Billing\BillingInterface;
use App\Services\Billing\Billing;
use Stripe\StripeClient;

class BillingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BillingInterface::class, Billing::class);

        $this->app->bind(StripeClient::class, function ($app) {
            return new StripeClient(env("STRIPE_SECRET"));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
