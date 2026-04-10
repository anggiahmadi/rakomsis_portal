<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Jobs\ProvisionTenantInfrastructureJob;

class DispatchProvisionTenantInfrastructure
{
    public function handle(PaymentCompleted $event): void
    {
        ProvisionTenantInfrastructureJob::dispatch($event->subscription->getKey());
    }
}
