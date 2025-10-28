<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'avatar_url',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'detail_address',
        'address',
        'member',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'member' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Customer TIDAK BOLEH mengakses panel admin
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return false;
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Generate initials dari nama customer
     * - Satu kata: ambil 2 huruf pertama (contoh: "Budi" -> "BU")
     * - Multiple kata: ambil huruf pertama dari kata pertama & terakhir (contoh: "Budi Santoso" -> "BS")
     */
    public function getInitials(): string
    {
        $name = trim($this->name ?? 'N/A');
        $words = preg_split('/\s+/', $name);
        $initials = '';

        if (count($words) === 1) {
            // Kalau cuma satu kata → ambil dua huruf pertama
            $initials = strtoupper(substr($words[0], 0, 2));
        } else {
            // Kalau lebih dari satu kata → ambil huruf pertama dari kata pertama & terakhir
            $first = strtoupper(substr($words[0], 0, 1));
            $last = strtoupper(substr(end($words), 0, 1));
            $initials = $first . $last;
        }

        return $initials;
    }

    /**
     * Dapatkan URL avatar untuk Filament
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Prioritas 1: Avatar yang diupload customer
        if (!empty($this->avatar_url)) {
            $avatarPath = $this->avatar_url;

            if (Storage::disk('public')->exists($avatarPath)) {
                return asset('storage/' . $avatarPath);
            }

            // Log file yang hilang untuk debugging
            Log::warning("Avatar file tidak ditemukan: {$avatarPath} untuk customer {$this->id}");
        }

        // Prioritas 2: Avatar default dari ui-avatars.com
        return $this->generateDefaultAvatar();
    }

    private function generateDefaultAvatar(): string
    {
        // Cek apakah file fallback avatar ada
        $fallbackPath = 'images/defaults-avatar.png';
        if (file_exists(public_path($fallbackPath))) {
            // Coba ui-avatars.com dulu, jika gagal gunakan fallback lokal
            try {
                $name = urlencode($this->name ?? 'Customer');
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
        $name = urlencode($this->name ?? 'Customer');
        $background = '000000'; // Warna background hitam
        $color = 'ffffff';      // Warna teks putih
        $size = 128;            // Ukuran avatar 128px

        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}&size={$size}";
    }
}
