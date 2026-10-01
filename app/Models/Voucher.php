<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    // used_count dan is_active DIHAPUS dari fillable agar tidak bisa dimanipulasi via mass assignment
    protected $fillable = [
        'code', 'type', 'value', 'scope',
        'usage_limit', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    // Cek apakah voucher berlaku untuk buku tertentu
    public function appliesTo(Book $book): bool
    {
        // Pastikan voucher masih valid sebelum cek scope
        if (!$this->isValid()) {
            return false;
        }

        if ($this->scope === 'all') {
            return true;
        }
        return $this->books->contains($book->id);
    }

    // Cek apakah voucher masih valid dipakai
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    // Hitung harga setelah diskon — AMAN dari harga negatif
    public function calculateDiscount(float $price): float
    {
        if ($this->type === 'percentage') {
            // Batasi value antara 0-100 agar tidak menghasilkan harga negatif
            $percentage = max(0, min(100, $this->value));
            return max(0, $price - ($price * $percentage / 100));
        }

        if ($this->type === 'fixed') {
            return max(0, $price - $this->value);
        }

        // Tipe tidak dikenal — kembalikan harga asli tanpa diskon
        return $price;
    }
}