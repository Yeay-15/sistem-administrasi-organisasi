<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    /** Barang dianggap "segera kedaluwarsa" bila tanggalnya tinggal sekian hari lagi. */
    public const EXPIRY_WARNING_DAYS = 90;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'location',
        'added_at',
        'expired_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'added_at' => 'date',
            'expired_at' => 'date',
        ];
    }

    /**
     * Jumlah tanpa nol berlebih: 4.00 -> "4", 0.50 -> "0,5", 200.00 -> "200".
     */
    public function getQuantityLabelAttribute(): string
    {
        $formatted = number_format((float) $this->quantity, 2, ',', '.');

        return rtrim(rtrim($formatted, '0'), ',');
    }

    /**
     * Jumlah + satuan untuk ditampilkan/diekspor, mis. "1 gulung", "9 tablet".
     */
    public function getQuantityWithUnitAttribute(): string
    {
        return trim($this->quantity_label . ' ' . ($this->unit ?? ''));
    }

    /**
     * 'expired' = sudah lewat, 'soon' = tinggal <= EXPIRY_WARNING_DAYS hari,
     * 'ok' = masih jauh, null = barang ini tidak punya tanggal kedaluwarsa.
     */
    public function getExpiryStatusAttribute(): ?string
    {
        if (! $this->expired_at) {
            return null;
        }

        if ($this->expired_at->copy()->endOfDay()->isPast()) {
            return 'expired';
        }

        if ($this->expired_at->lte(now()->addDays(self::EXPIRY_WARNING_DAYS))) {
            return 'soon';
        }

        return 'ok';
    }
}
