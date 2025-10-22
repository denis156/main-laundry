<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CourierMotorcycle extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, HasPushSubscriptions;

    protected $table = 'couriers_motorcycle';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'vehicle_number',
        'assigned_pos_id',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Kurir TIDAK BOLEH mengakses panel admin
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return false;
    }

    /**
     * Relasi many-to-one dengan Pos
     */
    public function assignedPos(): BelongsTo
    {
        return $this->belongsTo(Pos::class, 'assigned_pos_id');
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi one-to-many dengan Payment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Dapatkan URL avatar untuk Filament
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Prioritas 1: Avatar yang diupload kurir
        if (!empty($this->avatar_url)) {
            $avatarPath = $this->avatar_url;

            if (Storage::disk('public')->exists($avatarPath)) {
                return asset('storage/' . $avatarPath);
            }

            // Log file yang hilang untuk debugging
            Log::warning("Avatar file tidak ditemukan: {$avatarPath} untuk kurir motor {$this->id}");
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
                $name = urlencode($this->name ?? 'Kurir');
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
        $name = urlencode($this->name ?? 'Kurir');
        $background = '000000'; // Warna background hitam
        $color = 'ffffff';      // Warna teks putih
        $size = 128;            // Ukuran avatar 128px

        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}&size={$size}";
    }
}
