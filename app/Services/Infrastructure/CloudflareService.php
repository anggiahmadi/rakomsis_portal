<?php

namespace App\Services\Infrastructure;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudflareService
{
    public function createOrUpdateDnsRecord(string $domain, ?string $target = null): string
    {
        $zoneId = (string) config('provisioning.cloudflare.zone_id');
        $apiToken = (string) config('provisioning.cloudflare.api_token');
        $recordType = strtoupper((string) config('provisioning.cloudflare.record_type', 'A'));
        $recordTarget = trim((string) ($target ?? config('provisioning.cloudflare.dns_target')));
        $ttl = (int) config('provisioning.cloudflare.ttl', 1);
        $proxied = (bool) config('provisioning.cloudflare.proxied', true);

        if ($zoneId === '' || $apiToken === '' || $recordTarget === '') {
            throw new RuntimeException('Cloudflare provisioning is not configured completely.');
        }

        $payload = [
            'type' => $recordType,
            'name' => $domain,
            'content' => $recordTarget,
            'ttl' => $ttl,
            'proxied' => $proxied,
        ];

        $http = Http::baseUrl('https://api.cloudflare.com/client/v4')
            ->withToken($apiToken)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);

        $existingResponse = $http->get("zones/{$zoneId}/dns_records", [
            'name' => $domain,
        ]);

        if (! $existingResponse->successful()) {
            throw new RuntimeException('Failed to fetch existing Cloudflare DNS record.');
        }

        $existingPayload = $existingResponse->json();
        $existingRecord = $existingPayload['result'][0] ?? null;

        $response = $existingRecord
            ? $http->patch("zones/{$zoneId}/dns_records/{$existingRecord['id']}", $payload)
            : $http->post("zones/{$zoneId}/dns_records", $payload);

        if (! $response->successful() || ! ($response->json('success') ?? false)) {
            throw new RuntimeException('Failed to create or update Cloudflare DNS record.');
        }

        $recordId = (string) ($response->json('result.id') ?? '');

        if ($recordId === '') {
            throw new RuntimeException('Cloudflare DNS record ID was not returned.');
        }

        return $recordId;
    }
}
