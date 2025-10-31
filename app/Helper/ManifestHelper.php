<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * Manifest Helper
 *
 * Helper untuk generate PWA manifest.json secara dynamic
 * Support untuk kurir dan pelanggan dengan konfigurasi berbeda
 *
 * @package App\Helper
 */
class ManifestHelper
{
    /**
     * Generate manifest untuk aplikasi kurir
     *
     * @return array<string, mixed>
     */
    public static function kurirManifest(): array
    {
        return [
            'name' => 'Kurir ' . config('app.name'),
            'short_name' => 'Kurir Main',
            'description' => 'Aplikasi kurir untuk Main Laundry - kelola pengiriman laundry dengan mudah',
            'start_url' => '/kurir/',
            'scope' => '/kurir/',
            'display' => 'fullscreen',
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#3b82f6',
            'categories' => ['business', 'productivity'],
            'icons' => self::getIcons(),
        ];
    }

    /**
     * Generate manifest untuk aplikasi pelanggan
     *
     * @return array<string, mixed>
     */
    public static function pelangganManifest(): array
    {
        return [
            'name' => config('app.name'),
            'short_name' => 'Main Laundry',
            'description' => 'Aplikasi pelanggan Main Laundry - pesan dan lacak laundry Anda dengan mudah',
            'start_url' => '/pelanggan/',
            'scope' => '/pelanggan/',
            'display' => 'fullscreen',
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#3b82f6',
            'categories' => ['lifestyle', 'utilities'],
            'icons' => self::getIcons(),
        ];
    }

    /**
     * Get array icons untuk PWA manifest
     * Icons yang sama digunakan untuk kurir dan pelanggan
     *
     * @return array<int, array<string, string>>
     */
    private static function getIcons(): array
    {
        return [
            [
                'src' => '/image/app.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/app.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable'
            ],
            [
                'src' => '/image/app.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/app.png',
                'sizes' => '180x180',
                'type' => 'image/png',
                'purpose' => 'any'
            ]
        ];
    }
}
