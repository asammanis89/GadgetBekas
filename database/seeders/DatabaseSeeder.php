<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('shop.admin.email')],
            ['name' => config('shop.admin.name'), 'password' => config('shop.admin.password')],
        );

        $brands = [];
        foreach (['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'Infinix'] as $name) {
            $brands[$name] = Brand::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        // Stok contoh: seluruhnya fiktif. [merek, model, RAM, penyimpanan, warna, grade, baterai %, harga jual, modal, status, garansi hari, kelengkapan]
        $stock = [
            ['Apple', 'iPhone 11', 4, 128, 'Hitam', 'A', 87, 4350000, 3700000, 'available', 30, 'Unit, kabel Lightning'],
            ['Apple', 'iPhone 12', 4, 128, 'Biru', 'A', 89, 5850000, 5100000, 'available', 30, 'Unit, kabel, adaptor'],
            ['Apple', 'iPhone 13', 4, 128, 'Midnight', 'B', 85, 7250000, 6400000, 'reserved', 30, 'Unit, kabel'],
            ['Apple', 'iPhone XR', 3, 64, 'Putih', 'B', 81, 2950000, 2400000, 'available', 14, 'Unit saja'],
            ['Apple', 'iPhone 14 Pro', 6, 256, 'Deep Purple', 'A', 92, 12900000, 11500000, 'available', 30, 'Fullset, dus asli'],
            ['Samsung', 'Galaxy A54 5G', 8, 256, 'Hitam', 'A', 94, 3950000, 3300000, 'available', 30, 'Unit, charger'],
            ['Samsung', 'Galaxy S21', 8, 128, 'Phantom Gray', 'B', 88, 4750000, 4000000, 'available', 30, 'Unit, kabel'],
            ['Samsung', 'Galaxy A34 5G', 6, 128, 'Lime', 'A', 96, 2950000, 2450000, 'available', 30, 'Fullset'],
            ['Samsung', 'Galaxy S22 Ultra', 12, 256, 'Burgundy', 'B', 86, 8950000, 7900000, 'sold', 30, 'Unit, S Pen, kabel'],
            ['Xiaomi', 'Redmi Note 12', 6, 128, 'Biru Es', 'A', 95, 1850000, 1500000, 'available', 14, 'Fullset'],
            ['Xiaomi', 'Poco X5 Pro', 8, 256, 'Hitam', 'B', 90, 2450000, 2000000, 'available', 14, 'Unit, charger'],
            ['Xiaomi', 'Redmi 10C', 4, 64, 'Abu-abu', 'C', 82, 950000, 700000, 'available', 7, 'Unit saja'],
            ['Oppo', 'Reno 8T', 8, 128, 'Emas', 'A', 97, 2850000, 2350000, 'available', 30, 'Fullset'],
            ['Oppo', 'A57', 4, 64, 'Hitam', 'B', 88, 1150000, 850000, 'available', 7, 'Unit, kabel'],
            ['Vivo', 'V27', 8, 256, 'Hijau', 'A', 93, 3150000, 2650000, 'available', 30, 'Unit, charger, dus'],
            ['Vivo', 'Y22', 4, 64, 'Biru', 'C', 79, 1050000, 780000, 'sold', 7, 'Unit saja'],
            ['Realme', 'C55', 6, 128, 'Hitam', 'A', 96, 1650000, 1300000, 'available', 14, 'Fullset'],
            ['Infinix', 'Note 30', 8, 256, 'Hitam', 'B', 89, 1750000, 1400000, 'available', 14, 'Unit, charger'],
        ];

        foreach ($stock as $i => [$brand, $model, $ram, $storage, $color, $grade, $battery, $sell, $cost, $status, $warranty, $accessories]) {
            Product::updateOrCreate(
                ['slug' => Str::slug("$brand $model $storage")],
                [
                    'brand_id' => $brands[$brand]->id,
                    'model' => $model,
                    'ram_gb' => $ram,
                    'storage_gb' => $storage,
                    'color' => $color,
                    'grade' => $grade,
                    'battery_health' => $battery,
                    'price_sell' => $sell,
                    'price_cost' => $cost,
                    'status' => $status,
                    'warranty_days' => $warranty,
                    'accessories' => $accessories,
                    'description' => "Unit trade-in yang sudah dicek lengkap: layar, kamera, speaker, sinyal, dan baterai. Kondisi grade $grade.",
                    'sold_at' => $status === 'sold' ? now()->subDays(2 + $i) : null,
                ],
            );
        }
    }
}
