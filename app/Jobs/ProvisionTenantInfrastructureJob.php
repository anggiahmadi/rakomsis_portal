<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Enums\ProvisioningStatus;
use App\Models\Subscription;
use App\Services\Infrastructure\CloudflareService;
use App\Services\Infrastructure\ServerProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProvisionTenantInfrastructureJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $subscriptionId,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(
        CloudflareService $cloudflareService,
        ServerProvisioningService $serverProvisioningService,
    ): void {
        $subscription = Subscription::with('tenant')->findOrFail($this->subscriptionId);
        $tenant = $subscription->tenant;

        if (! $tenant) {
            throw new \RuntimeException('Subscription does not have an associated tenant.');
        }

        $lock = Cache::lock('tenant-provisioning:' . $tenant->getKey(), 900);

        if (! $lock->get()) {
            $this->release(30);

            return;
        }

        try {
            if (($subscription->payment_status?->value ?? $subscription->payment_status) !== PaymentStatus::Completed->value) {
                throw new \RuntimeException('Provisioning can only run for completed payments.');
            }

            if (blank($tenant->domain)) {
                throw new \RuntimeException('Tenant domain is required for provisioning.');
            }

            $tenantFolder = $serverProvisioningService->resolveTenantFolderName($tenant);
            $frontendPath = $serverProvisioningService->resolveTenantFrontendPath($tenantFolder);
            $configFileName = $serverProvisioningService->resolveNginxConfigFileName($tenant->domain);

            if (($tenant->provisioning_status?->value ?? $tenant->provisioning_status) === ProvisioningStatus::Success->value
                && $tenant->cloudflare_record_id
                && $serverProvisioningService->isProvisioned($tenant->frontend_path ?: $frontendPath, $configFileName)) {
                Log::info('Tenant provisioning skipped because infrastructure is already ready.', [
                    'subscription_id' => $subscription->getKey(),
                    'tenant_id' => $tenant->getKey(),
                    'domain' => $tenant->domain,
                ]);

                return;
            }

            $tenant->forceFill([
                'provisioning_status' => ProvisioningStatus::Processing->value,
                'provisioning_error' => null,
                'provisioning_attempts' => (int) $tenant->provisioning_attempts + 1,
                'last_provisioned_subscription_id' => $subscription->getKey(),
            ])->save();

            Log::info('Tenant provisioning started.', [
                'subscription_id' => $subscription->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain' => $tenant->domain,
            ]);

            $recordId = $cloudflareService->createOrUpdateDnsRecord($tenant->domain);
            Log::info('Cloudflare DNS record provisioned.', [
                'subscription_id' => $subscription->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain' => $tenant->domain,
                'cloudflare_record_id' => $recordId,
            ]);

            $serverProvisioningService->createTenantDirectory($tenantFolder);
            $serverProvisioningService->copyFrontendTemplate(
                (string) config('provisioning.frontend.template_path'),
                $frontendPath
            );
            $serverProvisioningService->writeTenantEnv($frontendPath, $tenantFolder);
            $serverProvisioningService->buildTenantFrontend($frontendPath);

            $distPath = $serverProvisioningService->resolveTenantDistPath($frontendPath);

            $serverProvisioningService->writeNginxConfig($tenant->domain, $distPath, $configFileName);
            $serverProvisioningService->enableNginxSite($configFileName);
            $serverProvisioningService->testAndReloadNginx();

            $tenant->forceFill([
                'provisioning_status' => ProvisioningStatus::Success->value,
                'provisioned_at' => now(),
                'provisioning_error' => null,
                'cloudflare_record_id' => $recordId,
                'frontend_path' => $frontendPath,
                'last_provisioned_subscription_id' => $subscription->getKey(),
            ])->save();

            Log::info('Tenant provisioning finished successfully.', [
                'subscription_id' => $subscription->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain' => $tenant->domain,
                'frontend_path' => $frontendPath,
            ]);
        } catch (Throwable $exception) {
            $tenant->forceFill([
                'provisioning_status' => ProvisioningStatus::Failed->value,
                'provisioning_error' => Str::limit($exception->getMessage(), 65000),
                'last_provisioned_subscription_id' => $subscription->getKey(),
            ])->save();

            Log::error('Tenant provisioning failed.', [
                'subscription_id' => $subscription->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain' => $tenant->domain,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            optional($lock)->release();
        }
    }
}
