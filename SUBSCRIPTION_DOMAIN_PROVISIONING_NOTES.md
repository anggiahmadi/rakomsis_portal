# Subscription Domain Provisioning Notes

Tujuan: saat domain di form subscription diisi dan pembayaran sudah `completed`, sistem otomatis:
1. Membuat DNS record di Cloudflare.
2. Membuat folder tenant di server app.
3. Membuat konfigurasi Nginx untuk domain tenant.
4. Reload Nginx dengan aman.

## Prinsip Utama
- Provisioning **tidak** dijalankan saat submit form subscription.
- Provisioning dijalankan **hanya setelah payment status berubah ke `completed`**.
- Proses dijalankan async via queue job (bukan synchronous HTTP request).
- Wajib idempotent (aman kalau event/job ter-trigger lebih dari sekali).

## Kondisi Existing (ringkas)
- Form subscription: `resources/views/pages/subscription/form.blade.php`
- Create subscription: `app/Http/Controllers/SubscriptionController.php`
- Enum status pembayaran: `app/Enums/PaymentStatus.php`
- Payment flow masih kosong: `app/Http/Controllers/PaymentController.php`

## Target Arsitektur
1. Payment masuk atau di-update.
2. Jika status transisi ke `completed`, emit event `PaymentCompleted`.
3. Listener dispatch `ProvisionTenantInfrastructureJob` (queue).
4. Job provisioning melakukan:
   - Validasi subscription paid dan domain valid.
   - Upsert DNS Cloudflare.
   - Create folder tenant di server.
   - Generate Nginx site config.
   - `nginx -t` lalu reload.
   - Simpan hasil provisioning ke DB.

## Data Tambahan yang Perlu Ditambahkan
Tambahkan kolom pada tabel `subscriptions`:
- `provisioning_status` (string, default: `pending`) -> pending|processing|success|failed
- `provisioned_at` (timestamp nullable)
- `provisioning_error` (text nullable)
- `cloudflare_record_id` (string nullable)

Opsional:
- `provisioning_attempts` (unsigned integer default 0)

## Event, Listener, Job yang Dibutuhkan
- Event: `PaymentCompleted`
- Listener: `DispatchProvisionTenantInfrastructure`
- Job: `ProvisionTenantInfrastructureJob`

Aturan idempotency di Job:
- Jika `provisioning_status == success`, return tanpa aksi.
- Lock berdasarkan `subscription_id` (cache lock / DB lock) untuk mencegah race condition.

## Service yang Dibutuhkan
- `App\Services\Infrastructure\CloudflareService`
  - createOrUpdateDnsRecord(domain, target)
- `App\Services\Infrastructure\ServerProvisioningService`
  - createTenantDirectory(domain)
  - writeNginxConfig(domain, rootPath)
  - testAndReloadNginx()

Catatan keamanan:
- Jangan eksekusi shell command root langsung dari web app tanpa guard.
- Lebih aman panggil internal provisioning API/service di server target dengan API token + IP whitelist.

## Environment Variables (rencana)
- `CF_API_TOKEN=`
- `CF_ZONE_ID=`
- `CF_PROXIED=true`
- `TENANT_DOMAIN_SUFFIX=rakomsis.com`
- `TENANT_SERVER_API_URL=`
- `TENANT_SERVER_API_TOKEN=`
- `TENANT_WEB_ROOT_BASE=/var/www/tenants`

## Urutan Implementasi yang Disarankan
1. Buat migration kolom provisioning di `subscriptions`.
2. Implement update payment status ke `completed` di `PaymentController` (atau webhook handler).
3. Saat transisi ke `completed`, dispatch event `PaymentCompleted`.
4. Buat listener + queue job provisioning.
5. Implement CloudflareService + ServerProvisioningService.
6. Tambahkan logging terstruktur untuk tiap step provisioning.
7. Tambahkan retry policy job (misal 3x dengan backoff).
8. Tambahkan tampilan status provisioning di halaman subscription list/detail.

## Acceptance Criteria
- Payment `pending` -> tidak ada provisioning.
- Payment `completed` -> provisioning jalan otomatis via queue.
- DNS record Cloudflare tersedia untuk domain tenant.
- Folder tenant dan file nginx site terbentuk.
- `nginx -t` sukses sebelum reload.
- Status provisioning tersimpan (`success`/`failed`) dan error tercatat.
- Re-trigger event tidak membuat duplikasi config/record.

## Failure Handling
Jika salah satu step gagal:
- Set `provisioning_status = failed`
- Simpan detail error di `provisioning_error`
- Jangan lanjut ke step berikutnya
- Bisa di-retry manual dengan action "Retry Provisioning"

Rollback minimal (opsional fase 2):
- Jika DNS sudah dibuat tapi nginx gagal, hapus DNS record untuk cleanup.

## Test Plan (Pest)
Feature test:
- Payment completed memicu dispatch job provisioning.
- Payment non-completed tidak memicu job.
- Subscription tanpa domain valid -> status failed + error tercatat.

Unit test:
- CloudflareService request payload benar.
- Job idempotent ketika provisioning_status success.
- Update status processing -> success/failed sesuai hasil.

## Command Acuan Nanti Saat Eksekusi
- `php artisan make:migration add_provisioning_fields_to_subscriptions_table --table=subscriptions --no-interaction`
- `php artisan make:event PaymentCompleted --no-interaction`
- `php artisan make:listener DispatchProvisionTenantInfrastructure --event=PaymentCompleted --queued --no-interaction`
- `php artisan make:job ProvisionTenantInfrastructureJob --no-interaction`
- `php artisan make:test --pest PaymentProvisioningFlowTest --no-interaction`

## Catatan Operasional
- Queue worker harus aktif di environment production.
- Simpan kredensial Cloudflare dan server provisioning di secret manager, bukan hardcoded.
- Audit log penting untuk troubleshooting provisioning lintas tim (app, infra, devops).

---
Status: Draft siap eksekusi saat diminta.
