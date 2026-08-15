<?php

namespace App\Providers;

use App\Services\Messaging\AfricasTalkingGateway;
use App\Services\Messaging\MessagingGatewayInterface;
use App\Services\Messaging\SimulatedMessagingGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the messaging gateway (simulated for dev, Africa's Talking for prod)
        // used by the SMS & WhatsApp notification channels.
        $this->app->bind(MessagingGatewayInterface::class, function ($app) {
            return config('messaging.driver') === 'africas_talking'
                ? new AfricasTalkingGateway()
                : new SimulatedMessagingGateway();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
