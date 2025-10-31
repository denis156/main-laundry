<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MobileCommands extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mobile:version {version?} {--patch} {--minor} {--major}';

    /**
     * The console command description.
     */
    protected $description = 'Update mobile app version across all files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check which option is used
        if ($this->option('patch')) {
            return $this->updateVersion('patch');
        }

        if ($this->option('minor')) {
            return $this->updateVersion('minor');
        }

        if ($this->option('major')) {
            return $this->updateVersion('major');
        }

        // If version argument provided, set specific version
        $version = $this->argument('version');
        if ($version) {
            return $this->updateVersion('set', $version);
        }

        // If no arguments/options, show help
        return $this->showHelp();
    }

    /**
     * Show help and usage examples
     */
    private function showHelp(): int
    {
        $this->line('');
        $this->info('Mobile App Version Manager');
        $this->line('');
        $this->line('Usage:');
        $this->line('  php artisan mobile:show');
        $this->line('  php artisan mobile:version [version] [--patch] [--minor] [--major]');
        $this->line('');
        $this->line('Commands:');
        $this->line('  mobile:show                    Show current version');
        $this->line('  mobile:version --patch         Auto-increment patch version (x.y.Z)');
        $this->line('  mobile:version --minor         Auto-increment minor version (x.Y.0)');
        $this->line('  mobile:version --major         Auto-increment major version (X.0.0)');
        $this->line('  mobile:version <version>       Set specific version (e.g., 1.0.0)');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan mobile:show              # Show current version');
        $this->line('  php artisan mobile:version --patch   # 1.0.0 -> 1.0.1');
        $this->line('  php artisan mobile:version --minor   # 1.0.1 -> 1.1.0');
        $this->line('  php artisan mobile:version --major   # 1.1.0 -> 2.0.0');
        $this->line('  php artisan mobile:version 1.5.0     # Set to 1.5.0');
        $this->line('');

        return Command::SUCCESS;
    }

    /**
     * Update version (patch/minor/major/set)
     */
    private function updateVersion(string $type, ?string $version = null): int
    {
        if ($type === 'set') {
            $newVersion = $version;

            if (!$this->isValidVersion($newVersion)) {
                $this->error("Format version tidak valid! Gunakan format: x.y.z (contoh: 1.0.0)");
                $this->line("Atau gunakan: php artisan mobile:version --help");
                return Command::FAILURE;
            }
        } else {
            $newVersion = $this->autoIncrementVersion($type);
            if (!$newVersion) {
                return Command::FAILURE;
            }
        }

        $this->info("Updating mobile app version to: {$newVersion}");
        $this->newLine();

        $updatedFiles = [];

        // 1. Update .env
        if ($this->updateEnvFile($newVersion)) {
            $updatedFiles[] = '.env';
            $this->info("Updated: .env");
        }

        // 2. Update .env.example
        if ($this->updateEnvExampleFile($newVersion)) {
            $updatedFiles[] = '.env.example';
            $this->info("Updated: .env.example");
        }

        // 3. Update sw-kurir.js
        if ($this->updateServiceWorker('public/sw-kurir.js', 'kurir', $newVersion)) {
            $updatedFiles[] = 'public/sw-kurir.js';
            $this->info("Updated: public/sw-kurir.js");
        }

        // 4. Update sw-pelanggan.js
        if ($this->updateServiceWorker('public/sw-pelanggan.js', 'pelanggan', $newVersion)) {
            $updatedFiles[] = 'public/sw-pelanggan.js';
            $this->info("Updated: public/sw-pelanggan.js");
        }

        // 5. Update sw.js (fallback)
        if ($this->updateServiceWorker('public/sw.js', 'fallback', $newVersion)) {
            $updatedFiles[] = 'public/sw.js';
            $this->info("Updated: public/sw.js");
        }

        $this->newLine();
        $this->info("Successfully updated " . count($updatedFiles) . " files!");
        $this->newLine();

        // Summary
        $this->table(
            ['File', 'Status'],
            collect($updatedFiles)->map(fn($file) => [$file, 'Updated'])->toArray()
        );

        $this->newLine();
        $this->warn("Don't forget to:");
        $this->line("   1. Run: npm run build");
        $this->line("   2. Deploy changes to production");
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Validate semantic version format (x.y.z)
     */
    private function isValidVersion(string $version): bool
    {
        return preg_match('/^\d+\.\d+\.\d+$/', $version) === 1;
    }

    /**
     * Auto increment version
     */
    private function autoIncrementVersion(string $type): ?string
    {
        $currentVersion = config('app.mobile_version');

        if (!$currentVersion || !$this->isValidVersion($currentVersion)) {
            $this->error("Current version tidak valid di .env: {$currentVersion}");
            $this->line("   Set MOBILE_APP_VERSION di .env terlebih dahulu.");
            return null;
        }

        [$major, $minor, $patch] = explode('.', $currentVersion);

        match ($type) {
            'major' => [$major++, $minor = 0, $patch = 0],
            'minor' => [$minor++, $patch = 0],
            'patch' => $patch++,
        };

        $newVersion = "{$major}.{$minor}.{$patch}";

        $this->line("Auto-increment " . strtoupper($type) . " version:");
        $this->line("   From: {$currentVersion}");
        $this->line("   To:   {$newVersion}");
        $this->newLine();

        return $newVersion;
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(string $newVersion): bool
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error("File .env tidak ditemukan!");
            return false;
        }

        $content = File::get($envPath);
        $updated = preg_replace(
            '/^MOBILE_APP_VERSION=.*/m',
            "MOBILE_APP_VERSION={$newVersion}",
            $content
        );

        if ($updated === $content) {
            $this->warn("MOBILE_APP_VERSION tidak ditemukan di .env");
            return false;
        }

        File::put($envPath, $updated);
        return true;
    }

    /**
     * Update .env.example file
     */
    private function updateEnvExampleFile(string $newVersion): bool
    {
        $envExamplePath = base_path('.env.example');

        if (!File::exists($envExamplePath)) {
            $this->warn("File .env.example tidak ditemukan (skipped)");
            return false;
        }

        $content = File::get($envExamplePath);
        $updated = preg_replace(
            '/^MOBILE_APP_VERSION=.*/m',
            "MOBILE_APP_VERSION={$newVersion}",
            $content
        );

        if ($updated === $content) {
            $this->warn("MOBILE_APP_VERSION tidak ditemukan di .env.example");
            return false;
        }

        File::put($envExamplePath, $updated);
        return true;
    }

    /**
     * Update Service Worker file
     */
    private function updateServiceWorker(string $filePath, string $type, string $newVersion): bool
    {
        $fullPath = base_path($filePath);

        if (!File::exists($fullPath)) {
            $this->error("File {$filePath} tidak ditemukan!");
            return false;
        }

        $content = File::get($fullPath);

        // Update all cache names
        $updated = preg_replace(
            "/const CACHE_NAME = 'main-laundry-{$type}-v[\d.]+';/",
            "const CACHE_NAME = 'main-laundry-{$type}-v{$newVersion}';",
            $content
        );

        $updated = preg_replace(
            "/const STATIC_CACHE = 'main-laundry-{$type}-static-v[\d.]+';/",
            "const STATIC_CACHE = 'main-laundry-{$type}-static-v{$newVersion}';",
            $updated
        );

        $updated = preg_replace(
            "/const DYNAMIC_CACHE = 'main-laundry-{$type}-dynamic-v[\d.]+';/",
            "const DYNAMIC_CACHE = 'main-laundry-{$type}-dynamic-v{$newVersion}';",
            $updated
        );

        if ($updated === $content) {
            $this->warn("Cache names tidak berubah di {$filePath}");
            return false;
        }

        File::put($fullPath, $updated);
        return true;
    }
}
