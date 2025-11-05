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
            'display' => 'standalone',
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
            'display' => 'standalone',
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
            // Icon maskable untuk Android adaptive icons (paling penting!)
            [
                'src' => '/image/manifest-icons/main-512x512-adaptive.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable'
            ],
            // Icon standard untuk most platforms
            [
                'src' => '/image/manifest-icons/main-512x512-notif.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-384x384.png',
                'sizes' => '384x384',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-256x256.png',
                'sizes' => '256x256',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-152x152.png',
                'sizes' => '152x152',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-128x128.png',
                'sizes' => '128x128',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-96x96.png',
                'sizes' => '96x96',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-72x72.png',
                'sizes' => '72x72',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/image/manifest-icons/main-48x48.png',
                'sizes' => '48x48',
                'type' => 'image/png',
                'purpose' => 'any'
            ]
        ];
    }
}
