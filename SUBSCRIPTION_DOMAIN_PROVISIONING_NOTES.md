# Subscription Domain Provisioning Notes

Tujuan: saat subscription untuk tenant sudah dibayar dan `payment_status` berubah menjadi `completed`, sistem otomatis menyiapkan domain tenant dan frontend tenant.

## Scope Provisioning
Setelah payment `completed`, sistem harus:
1. Membuat atau meng-update DNS record di Cloudflare untuk domain tenant.
2. Membuat folder tenant di server frontend.
3. Menyalin source frontend tenant dari template aplikasi utama.
4. Mengisi file `.env` tenant dengan konfigurasi tenant yang benar.
5. Membuat konfigurasi Nginx untuk domain tenant.
6. Menjalankan `nginx -t` lalu reload Nginx dengan aman.

## Prinsip Utama
- Provisioning tidak dijalankan saat submit form subscription.
- Provisioning dijalankan hanya setelah payment status benar-benar transisi ke `completed`.
- Proses dijalankan async via queue job, bukan synchronous HTTP request.
- Proses wajib idempotent, aman jika callback payment atau job terpanggil lebih dari sekali.
- Domain sumber provisioning berasal dari `tenants.domain`, karena domain saat ini melekat ke tenant, bukan ke subscription.

## Kondisi Existing Saat Ini
- Form subscription: `resources/views/pages/subscription/form.blade.php`
- Create subscription: `app/Http/Controllers/SubscriptionController.php`
- Payment callback Xendit sudah ada: `app/Http/Controllers/PaymentController.php`
- Model subscription: `app/Models/Subscription.php`
- Model tenant: `app/Models/Tenant.php`
- Domain tenant disimpan di tabel `tenants`

## Target Arsitektur
1. User membuat subscription untuk tenant yang sudah ada atau tenant baru.
2. Tenant sudah memiliki `domain` final, misalnya `tenant-a.rakomsis.com`.
3. Xendit callback masuk dan mengubah status payment subscription ke `completed`.
4. Jika status benar-benar transisi dari non-completed ke `completed`, emit event `PaymentCompleted`.
5. Listener dispatch `ProvisionTenantInfrastructureJob` ke queue.
6. Job provisioning:
   - Lock berdasarkan `tenant_id` atau `subscription_id` untuk mencegah race condition.
   - Validasi subscription berstatus paid.
   - Validasi tenant dan domain valid.
   - Upsert DNS record Cloudflare.
   - Buat folder frontend tenant.
   - Copy template frontend tenant.
   - Tulis file `.env` tenant.
   - Generate konfigurasi Nginx.
   - Jalankan `nginx -t`.
   - Reload Nginx jika test berhasil.
   - Simpan hasil provisioning ke database.

## Status Provisioning yang Disarankan
Karena domain dan hasil provisioning melekat ke tenant, status provisioning lebih aman disimpan di level `tenants` daripada `subscriptions`.

Tambahkan kolom pada tabel `tenants`:
- `provisioning_status` (string, default: `pending`) -> `pending|processing|success|failed`
- `provisioned_at` (timestamp nullable)
- `provisioning_error` (text nullable)
- `cloudflare_record_id` (string nullable)
- `frontend_path` (string nullable)
- `last_provisioned_subscription_id` (foreign id nullable, opsional)

Opsional:
- `provisioning_attempts` (unsigned integer default 0)

Jika tetap butuh audit per subscription, buat tabel log provisioning terpisah, jangan menggandakan status utama di tiap subscription.

## Event, Listener, dan Job
- Event: `PaymentCompleted`
- Listener: `DispatchProvisionTenantInfrastructure`
- Job: `ProvisionTenantInfrastructureJob`

Aturan idempotency:
- Jika `tenant.provisioning_status == success` dan domain/config masih valid, job boleh return tanpa aksi.
- Gunakan lock berbasis `tenant_id`.
- Callback payment harus tahan duplicate callback.
- Payment record sebaiknya dibuat dengan `firstOrCreate` atau dijaga dengan unique guard pada `transaction_id`.
- Event `PaymentCompleted` hanya di-dispatch saat ada transisi status nyata menuju `completed`.

## Service yang Dibutuhkan
- `App\Services\Infrastructure\CloudflareService`
  - `createOrUpdateDnsRecord(domain, target)`
- `App\Services\Infrastructure\ServerProvisioningService`
  - `createTenantDirectory(tenantFolder)`
  - `copyFrontendTemplate(sourcePath, targetPath)`
  - `writeTenantEnv(targetPath, tenantCode)`
  - `writeNginxConfig(domain, rootPath, configFileName)`
  - `testAndReloadNginx()`

## Detail Frontend Tenant Provisioning
Setelah payment `completed` dan DNS record berhasil dibuat, server provisioning harus langsung membuat folder tenant di path:

`/var/www/html/rakomsis_v_4_0/front_end/(nama tenant)`

Isi folder tenant diambil dari template source:

`/var/www/html/rakomsis_v_4_0/front_end/rakomsis_4_0_vue`

Lalu file `.env` di folder tenant target harus diisi atau di-overwrite dengan nilai berikut:

```dotenv
VITE_APP_NAME="Rakomsis"
VITE_APP_VERSION="4.0.0"
VITE_APP_DEMO="Rakomsis"
VITE_APP_FULL_NAME="Rakomsis ver.4.0"
VITE_APP_TENANT_CODE=(nama tenant)

# VITE_APP_DOCS_LINK="https://preview.keenthemes.com/metronic8/vue/docs/"

VITE_APP_API_URL="https://apps-gateway.rakomsis.com"
VITE_APP_FILE_URL="https://apps-file.rakomsis.com"
```

Catatan implementasi:
- `(nama tenant)` sebaiknya disanitasi menjadi nama folder yang aman untuk filesystem.
- Nilai yang sama dipakai untuk `VITE_APP_TENANT_CODE` agar konsisten dengan folder tenant.
- Proses copy harus idempotent. Jika folder sudah ada, lakukan sync/overwrite file yang memang dikelola provisioning, bukan menghapus manual isi tenant tanpa guard.
- Jika frontend tenant harus dilayani dari hasil build, pastikan proses provisioning atau pipeline lanjutan menjalankan build sehingga folder `dist` tersedia untuk Nginx.

## Detail Nginx Tenant Provisioning
File konfigurasi Nginx tenant harus dibuat dengan format nama:

`new_(domain tenant)`

Contoh:

`new_trial-servio.rakomsis.com`

Root Nginx harus diarahkan ke folder tenant yang sudah diprovisioning. Jika struktur deploy tenant menggunakan hasil build frontend tenant, contoh root-nya dapat mengarah ke:

`/var/www/html/rakomsis_v_4_0/front_end/servio/trial_servio/dist`

Contoh konfigurasi Nginx yang harus dihasilkan:

```nginx
server {
    listen 80;
    listen 443 ssl;
    ssl_certificate /etc/ssl/certs/rakomsis.crt;
    ssl_certificate_key /etc/ssl/private/rakomsis.key;

    server_name trial-servio.rakomsis.com;

    root /var/www/html/rakomsis_v_4_0/front_end/servio/trial_servio/dist;
    index  index.php index.html index.htm;

    index index.html index.htm index.php;

    charset utf-8;

    # security
    include             nginxconfig.io/security.conf;

    location / {
        proxy_set_header Host $host;

        try_files $uri $uri/ /index.html?$query_string;
    }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 500;
    }

    # additional config
    include nginxconfig.io/general.conf;
}
```

Catatan implementasi:
- `server_name` wajib memakai domain tenant yang diinput user.
- Nama file config wajib mengikuti format `new_<domain>`.
- `root` wajib menunjuk ke folder tenant yang benar di server.
- Setelah file config dibuat di `sites-available`, provisioning harus langsung membuat symlink ke `sites-enabled` dengan `ln -s`.
- Contoh:
  `ln -s /etc/nginx/sites-available/new_trial-servio.rakomsis.com /etc/nginx/sites-enabled/new_trial-servio.rakomsis.com`
- Jalankan `nginx -t` sebelum reload.
- Jika symlink sudah ada, proses harus tetap idempotent dan tidak gagal hanya karena link sudah terbentuk.

## Environment Variables Server Provisioning
- `CF_API_TOKEN=`
- `CF_ZONE_ID=`
- `CF_PROXIED=true`
- `TENANT_DOMAIN_SUFFIX=rakomsis.com`
- `TENANT_SERVER_API_URL=`
- `TENANT_SERVER_API_TOKEN=`
- `TENANT_FRONTEND_BASE_PATH=/var/www/html/rakomsis_v_4_0/front_end`
- `TENANT_FRONTEND_TEMPLATE_PATH=/var/www/html/rakomsis_v_4_0/front_end/rakomsis_4_0_vue`
- `TENANT_FRONTEND_API_URL=https://apps-gateway.rakomsis.com`
- `TENANT_FRONTEND_FILE_URL=https://apps-file.rakomsis.com`
- `TENANT_NGINX_SITES_AVAILABLE=/etc/nginx/sites-available`
- `TENANT_NGINX_SITES_ENABLED=/etc/nginx/sites-enabled`
- `TENANT_NGINX_SSL_CERT=/etc/ssl/certs/rakomsis.crt`
- `TENANT_NGINX_SSL_KEY=/etc/ssl/private/rakomsis.key`

## Urutan Implementasi yang Disarankan
1. Tambahkan field provisioning ke tabel `tenants`.
2. Rapikan flow payment callback agar hanya memicu event saat transisi ke `completed`.
3. Tambahkan guard untuk duplicate callback payment.
4. Dispatch event `PaymentCompleted`.
5. Buat listener + queue job provisioning.
6. Implement `CloudflareService`.
7. Implement `ServerProvisioningService`.
8. Tambahkan logic copy frontend template dan write `.env` tenant.
9. Tambahkan logic generate file Nginx `new_<domain>` dan mapping ke root tenant.
10. Tambahkan logging terstruktur untuk tiap step provisioning.
11. Tambahkan retry policy job, misalnya 3 kali dengan backoff.
12. Tambahkan tampilan status provisioning di halaman tenant atau subscription detail.

## Acceptance Criteria
- Payment `pending` tidak memicu provisioning.
- Payment yang transisi ke `completed` memicu provisioning via queue.
- DNS record Cloudflare tersedia untuk domain tenant.
- Folder tenant terbentuk di `/var/www/html/rakomsis_v_4_0/front_end/(nama tenant)`.
- Isi folder tenant berasal dari `/var/www/html/rakomsis_v_4_0/front_end/rakomsis_4_0_vue`.
- File `.env` tenant terisi dengan konfigurasi yang sudah ditentukan.
- Konfigurasi Nginx untuk domain tenant terbentuk dengan nama `new_<domain>`.
- Symlink dari `sites-available/new_<domain>` ke `sites-enabled/new_<domain>` terbentuk otomatis.
- `server_name` Nginx menggunakan domain tenant yang benar.
- `root` Nginx mengarah ke folder tenant frontend yang benar.
- `nginx -t` sukses sebelum reload dilakukan.
- Status provisioning tersimpan sebagai `success` atau `failed`, dan error tercatat.
- Re-trigger callback atau event tidak membuat duplikasi DNS, payment record, atau config tenant.

## Failure Handling
Jika salah satu step gagal:
- Set `provisioning_status = failed`
- Simpan detail error di `provisioning_error`
- Jangan lanjut ke step berikutnya
- Sediakan mekanisme retry manual, misalnya action `Retry Provisioning`

Rollback minimal fase berikutnya:
- Jika DNS sudah dibuat tetapi provisioning folder atau Nginx gagal, boleh tambahkan cleanup DNS sebagai compensating action.

## Test Plan
Feature test:
- Payment `completed` memicu dispatch job provisioning.
- Payment non-completed tidak memicu job.
- Duplicate callback payment tidak membuat payment record ganda.
- Subscription paid dengan tenant domain valid memicu provisioning tenant.
- Tenant folder berhasil dibuat dari template frontend.
- File `.env` tenant ditulis dengan nilai yang benar.

Unit test:
- `CloudflareService` mengirim payload request yang benar.
- Job idempotent ketika provisioning sudah `success`.
- `ServerProvisioningService` menghasilkan path folder tenant yang benar.
- `writeTenantEnv()` menulis `VITE_APP_TENANT_CODE`, `VITE_APP_API_URL`, dan `VITE_APP_FILE_URL` sesuai kebutuhan.
- Update status `processing -> success/failed` sesuai hasil job.

## Catatan Keamanan dan Operasional
- Jangan eksekusi shell command root langsung dari web app tanpa guard.
- Lebih aman panggil internal provisioning API/service di server target dengan API token dan IP whitelist.
- Queue worker harus aktif di environment production.
- Simpan kredensial Cloudflare dan server provisioning di secret manager, bukan hardcoded.
- Audit log penting untuk troubleshooting lintas tim app, infra, dan devops.
- Jika frontend tenant akan dilayani oleh Nginx sebagai static app, pastikan ada step build yang jelas setelah `.env` ditulis, atau siapkan pipeline terpisah untuk build output tenant.

## Command Acuan Nanti Saat Eksekusi
- `php artisan make:migration add_provisioning_fields_to_tenants_table --table=tenants --no-interaction`
- `php artisan make:event PaymentCompleted --no-interaction`
- `php artisan make:listener DispatchProvisionTenantInfrastructure --event=PaymentCompleted --queued --no-interaction`
- `php artisan make:job ProvisionTenantInfrastructureJob --no-interaction`
- `php artisan make:test --pest PaymentProvisioningFlowTest --no-interaction`

---
Status: Draft revisi siap dijadikan acuan implementasi.
