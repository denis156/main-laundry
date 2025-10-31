<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MobileShowCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mobile:show';

    /**
     * The console command description.
     */
    protected $description = 'Show current mobile app version';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $version = config('app.mobile_version');

        if (!$version) {
            $this->error('MOBILE_APP_VERSION not set in .env file');
            return Command::FAILURE;
        }

        $this->info("Current mobile app version: v{$version}");
        return Command::SUCCESS;
    }
}
