<?php

declare(strict_types=1);

namespace App\Providers;

use Switch\Container\Container;
use Switch\Container\ServiceProviderInterface;

class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * Register any application services and interface bindings into the container.
     */
    public function register(Container $container): void
    {
        // Example: $container->singleton(PaymentGatewayInterface::class, StripeGateway::class);
    }

    /**
     * Bootstrap any application services, database observers, or package extensions.
     */
    public function boot(Container $container): void
    {
        // Example: Model::observe(UserObserver::class);
    }
}
