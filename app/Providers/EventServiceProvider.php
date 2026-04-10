<?php

namespace App\Providers;

use App\Events\PaymentCompleted;
use App\Listeners\DispatchProvisionTenantInfrastructure;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaymentCompleted::class => [
            DispatchProvisionTenantInfrastructure::class,
        ],
    ];
}
