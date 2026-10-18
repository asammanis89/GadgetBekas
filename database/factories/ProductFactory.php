<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $model = fake()->unique()->bothify('Model ?#?-###');

        return [
            'brand_id' => Brand::factory(),
            'model' => $model,
            'slug' => Str::slug($model).'-'.fake()->unique()->numberBetween(1000, 999999),
            'storage_gb' => fake()->randomElement([64, 128, 256, 512]),
            'ram_gb' => fake()->randomElement([4, 6, 8, 12]),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Biru', 'Hijau']),
            'grade' => fake()->randomElement(['A', 'B', 'C']),
            'battery_health' => fake()->numberBetween(75, 100),
            'price_sell' => fake()->numberBetween(8, 150) * 100000,
            'price_cost' => 0,
            'status' => Product::AVAILABLE,
            'warranty_days' => 30,
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'sold_at' => $status === Product::SOLD ? now() : null,
        ]);
    }
}
