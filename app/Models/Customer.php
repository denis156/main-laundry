<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'detail_address',
        'address',
        'member',
    ];

    protected function casts(): array
    {
        return [
            'member' => 'boolean',
        ];
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
}
