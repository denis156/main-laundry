<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\AvatarProviders\UiAvatarsProvider;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'super_admin',
        'phone',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'super_admin' => 'boolean',
        ];
    }

    /**
     * Cek apakah user bisa mengakses panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Dapatkan URL avatar untuk Filament
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Prioritas 1: Avatar yang diupload user
        if (!empty($this->avatar_url)) {
            $avatarPath = $this->avatar_url;

            if (Storage::disk('public')->exists($avatarPath)) {
                return asset('storage/' . $avatarPath);
            }

            // Log file yang hilang untuk debugging
            Log::warning("Avatar file tidak ditemukan: {$avatarPath} untuk user {$this->id}");
        }

        // Prioritas 2: Custom avatar provider (jika tersedia)
        if (class_exists('App\Services\UiAvatarsProvider')) {
            try {
                return (new UiAvatarsProvider())->get($this);
            } catch (Exception $e) {
                Log::warning('UiAvatarsProvider error: ' . $e->getMessage());
            }
        }

        // Prioritas 3: Avatar default dari ui-avatars.com
        return $this->generateDefaultAvatar();
    }

    private function generateDefaultAvatar(): string
    {
        // Cek apakah file fallback avatar ada
        $fallbackPath = 'images/defaults-avatar.png';
        if (file_exists(public_path($fallbackPath))) {
            // Coba ui-avatars.com dulu, jika gagal gunakan fallback lokal
            try {
                $name = urlencode($this->name ?? 'User');
                $background = '000000'; // Warna background hitam
                $color = 'ffffff';      // Warna teks putih
                $size = 128;            // Ukuran avatar 128px

                $uiAvatarUrl = "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}&size={$size}";

                // Test jika URL ui-avatars dapat diakses (sederhana dengan get_headers)
                $headers = @get_headers($uiAvatarUrl);
                if ($headers && strpos($headers[0], '200') !== false) {
                    return $uiAvatarUrl;
                }
            } catch (Exception $e) {
                Log::warning('ui-avatars.com tidak dapat diakses: ' . $e->getMessage());
            }

            // Fallback ke avatar lokal
            return asset($fallbackPath);
        }

        // Jika fallback lokal tidak ada, tetap coba ui-avatars.com
        $name = urlencode($this->name ?? 'User');
        $background = '000000'; // Warna background hitam
        $color = 'ffffff';      // Warna teks putih
        $size = 128;            // Ukuran avatar 128px

        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}&size={$size}";
    }
}
