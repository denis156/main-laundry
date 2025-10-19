<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateReverbKeys extends Command
{
    protected $signature = 'reverb:keys';
    protected $description = 'Generate secure Reverb configuration keys';

    public function handle()
    {
        $appId = random_int(100000000, 999999999);
        $appKey = Str::random(40);
        $appSecret = Str::random(40);

        $this->info('Generated Reverb Keys:');
        $this->line("REVERB_APP_ID={$appId}");
        $this->line("REVERB_APP_KEY={$appKey}");
        $this->line("REVERB_APP_SECRET={$appSecret}");

        // Optional: langsung update .env
        if ($this->confirm('Update .env file automatically?')) {
            $this->updateEnvFile($appId, $appKey, $appSecret);
            $this->info('.env file updated successfully!');
        }
    }

    private function updateEnvFile($appId, $appKey, $appSecret)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $replacements = [
            'REVERB_APP_ID=' => "REVERB_APP_ID={$appId}",
            'REVERB_APP_KEY=' => "REVERB_APP_KEY={$appKey}",
            'REVERB_APP_SECRET=' => "REVERB_APP_SECRET={$appSecret}",
        ];

        foreach ($replacements as $key => $value) {
            if (strpos($envContent, $key) !== false) {
                $envContent = preg_replace("/^{$key}.*/m", $value, $envContent);
            } else {
                $envContent .= "\n{$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
