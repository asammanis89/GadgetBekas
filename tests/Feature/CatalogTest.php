<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function phone(string $brand, string $model, array $attrs = []): Product
    {
        $b = Brand::firstOrCreate(['slug' => strtolower($brand)], ['name' => $brand]);

        return Product::factory()->create(array_merge(['brand_id' => $b->id, 'model' => $model, 'slug' => strtolower("$brand-$model")], $attrs));
    }

    public function test_katalog_menampilkan_unit_tersedia_dan_dipesan_tetapi_tidak_yang_terjual(): void
    {
        $this->phone('Apple', 'iPhone 12', ['price_sell' => 5850000]);
        $this->phone('Apple', 'iPhone 13', ['status' => Product::RESERVED]);
        $this->phone('Apple', 'iPhone XR', ['status' => Product::SOLD, 'sold_at' => now()]);

        $this->get('/')
            ->assertOk()
            ->assertSee('iPhone 12')
            ->assertSee('Rp 5.850.000')
            ->assertSee('iPhone 13')
            ->assertSee('Dipesan')
            ->assertDontSee('iPhone XR');
    }

    public function test_filter_merek_harga_dan_pencarian(): void
    {
        $this->phone('Apple', 'iPhone 12', ['price_sell' => 5850000]);
        $this->phone('Samsung', 'Galaxy A54', ['price_sell' => 3950000]);
        $this->phone('Xiaomi', 'Redmi Note 12', ['price_sell' => 1850000]);

        $this->get('/?merek=samsung')->assertSee('Galaxy A54')->assertDontSee('iPhone 12');
        $this->get('/?maks=3000000')->assertSee('Redmi Note 12')->assertDontSee('Galaxy A54');
        $this->get('/?q=iphone')->assertSee('iPhone 12')->assertDontSee('Redmi Note 12');
    }

    public function test_urutan_harga_termurah_dan_termahal(): void
    {
        $this->phone('Apple', 'Murah', ['price_sell' => 1000000]);
        $this->phone('Apple', 'Mahal', ['price_sell' => 9000000]);

        $this->get('/?urut=termurah')->assertSeeInOrder(['Murah', 'Mahal']);
        $this->get('/?urut=termahal')->assertSeeInOrder(['Mahal', 'Murah']);
    }

    public function test_katalog_kosong_menampilkan_pesan(): void
    {
        $this->get('/?q=tidak-ada-yang-cocok')->assertOk()->assertSee('Belum ada unit yang cocok');
    }

    public function test_halaman_produk_memuat_spesifikasi_dan_tautan_whatsapp(): void
    {
        $p = $this->phone('Apple', 'iPhone 12', ['storage_gb' => 128, 'ram_gb' => 4, 'grade' => 'A', 'battery_health' => 89, 'price_sell' => 5850000]);

        $response = $this->get(route('catalog.show', $p));

        $response->assertOk()
            ->assertSee('Apple iPhone 12')
            ->assertSee('Baterai 89%')
            ->assertSee('Rp 5.850.000')
            ->assertSee('https://wa.me/6281200000000?text=', false);
    }

    public function test_produk_terjual_tetap_bisa_dibuka_dengan_pemberitahuan(): void
    {
        $p = $this->phone('Apple', 'iPhone XR', ['status' => Product::SOLD, 'sold_at' => now()]);

        $this->get(route('catalog.show', $p))->assertOk()->assertSee('sudah terjual');
    }

    public function test_halaman_produk_tidak_membocorkan_imei_dan_harga_modal(): void
    {
        $p = $this->phone('Apple', 'iPhone 12', ['imei' => '356938035643809', 'price_cost' => 4321000]);

        $this->get(route('catalog.show', $p))
            ->assertOk()
            ->assertDontSee('356938035643809')
            ->assertDontSee('4.321.000');
    }

    public function test_produk_tidak_ada_memberi_404(): void
    {
        $this->get('/produk/tidak-ada')->assertNotFound();
    }
}
