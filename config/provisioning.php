<?php

return [
    'cloudflare' => [
        'api_token' => env('CF_API_TOKEN'),
        'zone_id' => env('CF_ZONE_ID'),
        'record_type' => env('TENANT_DNS_RECORD_TYPE', 'A'),
        'dns_target' => env('TENANT_DNS_TARGET'),
        'ttl' => (int) env('TENANT_DNS_TTL', 1),
        'proxied' => env('CF_PROXIED', true),
    ],

    'frontend' => [
        'base_path' => env('TENANT_FRONTEND_BASE_PATH', '/var/www/html/rakomsis_v_4_0/front_end'),
        'template_path' => env('TENANT_FRONTEND_TEMPLATE_PATH', '/var/www/html/rakomsis_v_4_0/front_end/rakomsis_4_0_vue'),
        'api_url' => env('TENANT_FRONTEND_API_URL', 'https://apps-gateway.rakomsis.com'),
        'file_url' => env('TENANT_FRONTEND_FILE_URL', 'https://apps-file.rakomsis.com'),
        'run_build' => env('TENANT_FRONTEND_RUN_BUILD', true),
        'install_command' => env('TENANT_FRONTEND_INSTALL_COMMAND', 'npm install --no-fund --no-audit'),
        'build_command' => env('TENANT_FRONTEND_BUILD_COMMAND', 'npm run build'),
    ],

    'nginx' => [
        'sites_available' => env('TENANT_NGINX_SITES_AVAILABLE', '/etc/nginx/sites-available'),
        'sites_enabled' => env('TENANT_NGINX_SITES_ENABLED', '/etc/nginx/sites-enabled'),
        'ssl_certificate' => env('TENANT_NGINX_SSL_CERT', '/etc/ssl/certs/rakomsis.crt'),
        'ssl_certificate_key' => env('TENANT_NGINX_SSL_KEY', '/etc/ssl/private/rakomsis.key'),
        'php_fpm_socket' => env('TENANT_NGINX_PHP_FPM_SOCKET', '/var/run/php/php8.3-fpm.sock'),
        'security_include' => env('TENANT_NGINX_SECURITY_INCLUDE', 'nginxconfig.io/security.conf'),
        'general_include' => env('TENANT_NGINX_GENERAL_INCLUDE', 'nginxconfig.io/general.conf'),
        'test_command' => env('TENANT_NGINX_TEST_COMMAND', 'nginx -t'),
        'reload_command' => env('TENANT_NGINX_RELOAD_COMMAND', 'systemctl reload nginx'),
    ],
];
