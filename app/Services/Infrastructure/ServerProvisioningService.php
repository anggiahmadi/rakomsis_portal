<?php

namespace App\Services\Infrastructure;

use App\Models\Tenant;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class ServerProvisioningService
{
    public function __construct(
        protected Filesystem $files,
    ) {
    }

    public function resolveTenantFolderName(Tenant $tenant): string
    {
        $domainPrefix = Str::before(Str::lower((string) $tenant->domain), '.');
        $candidate = $domainPrefix !== ''
            ? $domainPrefix
            : Str::lower((string) ($tenant->code ?: $tenant->name ?: 'tenant-' . $tenant->getKey()));

        $normalized = preg_replace('/[^a-z0-9]+/', '_', $candidate) ?? '';
        $normalized = trim($normalized, '_');

        return $normalized !== '' ? $normalized : 'tenant_' . $tenant->getKey();
    }

    public function resolveTenantFrontendPath(string $tenantFolder): string
    {
        return rtrim((string) config('provisioning.frontend.base_path'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $tenantFolder;
    }

    public function resolveTenantDistPath(string $frontendPath): string
    {
        return rtrim($frontendPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'dist';
    }

    public function resolveNginxConfigFileName(string $domain): string
    {
        return 'new_' . $domain;
    }

    public function createTenantDirectory(string $tenantFolder): string
    {
        $targetPath = $this->resolveTenantFrontendPath($tenantFolder);

        $this->files->ensureDirectoryExists($targetPath);

        return $targetPath;
    }

    public function copyFrontendTemplate(string $sourcePath, string $targetPath): void
    {
        if (! $this->files->isDirectory($sourcePath)) {
            throw new RuntimeException('Frontend template path does not exist.');
        }

        $this->files->ensureDirectoryExists($targetPath);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathName();

            if ($this->shouldSkipTemplatePath($relativePath)) {
                continue;
            }

            $destination = $targetPath . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                $this->files->ensureDirectoryExists($destination);
                continue;
            }

            $this->files->ensureDirectoryExists(dirname($destination));
            $this->files->copy($item->getPathname(), $destination);
        }
    }

    public function writeTenantEnv(string $targetPath, string $tenantCode): void
    {
        $envContents = implode("\n", [
            'VITE_APP_NAME="Rakomsis"',
            'VITE_APP_VERSION="4.0.0"',
            'VITE_APP_DEMO="Rakomsis"',
            'VITE_APP_FULL_NAME="Rakomsis ver.4.0"',
            'VITE_APP_TENANT_CODE="' . $tenantCode . '"',
            '',
            '# VITE_APP_DOCS_LINK="https://preview.keenthemes.com/metronic8/vue/docs/"',
            '',
            'VITE_APP_API_URL="' . (string) config('provisioning.frontend.api_url') . '"',
            'VITE_APP_FILE_URL="' . (string) config('provisioning.frontend.file_url') . '"',
            '',
        ]);

        $this->files->put($targetPath . DIRECTORY_SEPARATOR . '.env', $envContents);
    }

    public function buildTenantFrontend(string $targetPath): void
    {
        if (! (bool) config('provisioning.frontend.run_build', true)) {
            return;
        }

        $installCommand = trim((string) config('provisioning.frontend.install_command', 'npm install --no-fund --no-audit'));
        $buildCommand = trim((string) config('provisioning.frontend.build_command', 'npm run build'));

        if ($installCommand !== '') {
            $this->runShellCommand($installCommand, $targetPath, 3600);
        }

        if ($buildCommand !== '') {
            $this->runShellCommand($buildCommand, $targetPath, 3600);
        }

        if (! $this->files->isDirectory($this->resolveTenantDistPath($targetPath))) {
            throw new RuntimeException('Frontend build finished without a dist directory.');
        }
    }

    public function writeNginxConfig(string $domain, string $rootPath, string $configFileName): string
    {
        $sitesAvailablePath = rtrim((string) config('provisioning.nginx.sites_available'), DIRECTORY_SEPARATOR);
        $configPath = $sitesAvailablePath . DIRECTORY_SEPARATOR . $configFileName;

        $this->files->ensureDirectoryExists($sitesAvailablePath);

        $configContents = <<<NGINX
server {
    listen 80;
    listen 443 ssl;
    ssl_certificate {$this->escapeNginxValue((string) config('provisioning.nginx.ssl_certificate'))};
    ssl_certificate_key {$this->escapeNginxValue((string) config('provisioning.nginx.ssl_certificate_key'))};

    server_name {$domain};

    root {$this->escapeNginxValue($rootPath)};
    index index.php index.html index.htm;

    charset utf-8;

    # security
    include {$this->escapeNginxValue((string) config('provisioning.nginx.security_include'))};

    location / {
        proxy_set_header Host \$host;

        try_files \$uri \$uri/ /index.html?\$query_string;
    }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:{$this->escapeNginxValue((string) config('provisioning.nginx.php_fpm_socket'))};
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 500;
    }

    # additional config
    include {$this->escapeNginxValue((string) config('provisioning.nginx.general_include'))};
}
NGINX;

        $this->files->put($configPath, $configContents . "\n");

        return $configPath;
    }

    public function enableNginxSite(string $configFileName): void
    {
        $sitesAvailablePath = rtrim((string) config('provisioning.nginx.sites_available'), DIRECTORY_SEPARATOR);
        $sitesEnabledPath = rtrim((string) config('provisioning.nginx.sites_enabled'), DIRECTORY_SEPARATOR);

        $sourcePath = $sitesAvailablePath . DIRECTORY_SEPARATOR . $configFileName;
        $targetPath = $sitesEnabledPath . DIRECTORY_SEPARATOR . $configFileName;

        $this->files->ensureDirectoryExists($sitesEnabledPath);

        $this->runShellCommand(
            'ln -sfn ' . escapeshellarg($sourcePath) . ' ' . escapeshellarg($targetPath),
            null,
            30
        );
    }

    public function testAndReloadNginx(): void
    {
        $this->runShellCommand((string) config('provisioning.nginx.test_command', 'nginx -t'), null, 120);
        $this->runShellCommand((string) config('provisioning.nginx.reload_command', 'systemctl reload nginx'), null, 120);
    }

    public function isProvisioned(?string $frontendPath, string $configFileName): bool
    {
        if (! $frontendPath) {
            return false;
        }

        $distPath = $this->resolveTenantDistPath($frontendPath);
        $configPath = rtrim((string) config('provisioning.nginx.sites_available'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $configFileName;
        $enabledPath = rtrim((string) config('provisioning.nginx.sites_enabled'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $configFileName;

        return $this->files->isDirectory($distPath)
            && $this->files->exists($configPath)
            && $this->files->exists($enabledPath);
    }

    protected function shouldSkipTemplatePath(string $relativePath): bool
    {
        $segments = preg_split('#[\\/]+#', $relativePath) ?: [];
        $skipEntries = [
            '.env',
            '.git',
            '.idea',
            '.vscode',
            'node_modules',
            'dist',
        ];

        foreach ($segments as $segment) {
            if (in_array($segment, $skipEntries, true)) {
                return true;
            }
        }

        return false;
    }

    protected function runShellCommand(string $command, ?string $workingDirectory = null, int $timeout = 120): void
    {
        $pendingProcess = Process::timeout($timeout);

        if ($workingDirectory) {
            $pendingProcess = $pendingProcess->path($workingDirectory);
        }

        $result = $pendingProcess->run(['sh', '-lc', $command]);

        if (! $result->successful()) {
            throw new RuntimeException(trim($result->errorOutput()) ?: ('Command failed: ' . $command));
        }
    }

    protected function escapeNginxValue(string $value): string
    {
        return str_replace(' ', '\ ', $value);
    }
}
