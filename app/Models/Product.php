<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const AVAILABLE = 'available';
    public const RESERVED = 'reserved';
    public const SOLD = 'sold';

    public const STATUSES = [
        self::AVAILABLE => 'Tersedia',
        self::RESERVED => 'Dipesan',
        self::SOLD => 'Terjual',
    ];

    public const GRADES = [
        'A' => 'Grade A: mulus, nyaris tanpa bekas pakai',
        'B' => 'Grade B: lecet halus, tidak mengganggu',
        'C' => 'Grade C: lecet atau penyok terlihat',
    ];

    protected $fillable = [
        'brand_id', 'model', 'slug', 'storage_gb', 'ram_gb', 'color', 'grade', 'battery_health',
        'price_sell', 'price_cost', 'status', 'imei', 'accessories', 'description', 'warranty_days', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'storage_gb' => 'integer',
            'ram_gb' => 'integer',
            'battery_health' => 'integer',
            'price_sell' => 'integer',
            'price_cost' => 'integer',
            'warranty_days' => 'integer',
            'sold_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('position')->orderBy('id');
    }

    /** Produk yang tampil di katalog: tersedia atau sedang dipesan. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->whereIn('status', [self::AVAILABLE, self::RESERVED]);
    }

    public function getTitleAttribute(): string
    {
        return trim(($this->brand?->name ?? '').' '.$this->model);
    }

    /** Ringkasan spesifikasi, mis. "8/256 GB · Hitam". */
    public function getSpecLineAttribute(): string
    {
        $memory = $this->ram_gb ? "{$this->ram_gb}/{$this->storage_gb} GB" : "{$this->storage_gb} GB";

        return $this->color ? "{$memory} · {$this->color}" : $memory;
    }

    public function getPriceFormattedAttribute(): string
    {
        return self::rupiah($this->price_sell);
    }

    public function getIsSoldAttribute(): bool
    {
        return $this->status === self::SOLD;
    }

    public static function rupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
