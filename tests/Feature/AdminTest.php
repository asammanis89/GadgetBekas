<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $over = []): array
    {
        return array_merge([
            'brand_id' => Brand::factory()->create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'model' => 'iPhone 12',
            'storage_gb' => 128,
            'ram_gb' => 4,
            'color' => 'Biru',
            'grade' => 'A',
            'battery_health' => 89,
            'price_sell' => 5850000,
            'price_cost' => 5100000,
            'status' => 'available',
            'warranty_days' => 30,
        ], $over);
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/products')->assertRedirect(route('login'));
        $this->post('/admin/products', [])->assertRedirect(route('login'));
    }

    public function test_masuk_dengan_password_benar_dan_salah(): void
    {
        User::factory()->create(['email' => 'toko@contoh.test', 'password' => 'rahasia123']);

        $this->post('/masuk', ['email' => 'toko@contoh.test', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/masuk', ['email' => 'toko@contoh.test', 'password' => 'rahasia123'])
            ->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_keluar_mengakhiri_sesi(): void
    {
        $this->actingAs(User::factory()->create())->post('/keluar')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_tambah_produk_dengan_foto(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->post('/admin/products', $this->validData([
            'photos' => [UploadedFile::fake()->image('depan.jpg', 800, 600), UploadedFile::fake()->image('belakang.png', 800, 600)],
        ]))->assertRedirect(route('admin.products.index'));

        $product = Product::firstOrFail();
        $this->assertSame('apple-iphone-12-128', $product->slug);
        $this->assertCount(2, $product->photos);
        foreach ($product->photos as $photo) {
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    public function test_validasi_menolak_data_buruk(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/admin/products', $this->validData(['model' => '', 'price_sell' => -5, 'grade' => 'Z', 'imei' => '123', 'battery_health' => 150]))
            ->assertSessionHasErrors(['model', 'price_sell', 'grade', 'imei', 'battery_health']);
        $this->assertSame(0, Product::count());
    }

    public function test_slug_tetap_unik_untuk_produk_kembar(): void
    {
        $this->actingAs(User::factory()->create());
        $data = $this->validData();

        $this->post('/admin/products', $data);
        $this->post('/admin/products', $data);

        $this->assertEqualsCanonicalizing(['apple-iphone-12-128', 'apple-iphone-12-128-2'], Product::pluck('slug')->all());
    }

    public function test_tandai_terjual_mengisi_tanggal_dan_menyembunyikan_dari_katalog(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['model' => 'Unik Sekali']);

        $this->post(route('admin.products.status', $product), ['status' => 'sold'])->assertRedirect();

        $this->assertNotNull($product->fresh()->sold_at);
        $this->get('/')->assertDontSee('Unik Sekali');

        $this->post(route('admin.products.status', $product), ['status' => 'available']);
        $this->assertNull($product->fresh()->sold_at);
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $this->post(route('admin.products.status', $product), ['status' => 'hilang'])->assertSessionHasErrors('status');
    }

    public function test_ubah_produk_terjual_tidak_menimpa_tanggal_terjual(): void
    {
        $this->actingAs(User::factory()->create());
        $soldAt = now()->subDays(5)->startOfSecond();
        $product = Product::factory()->create(['status' => 'sold', 'sold_at' => $soldAt]);

        $this->put(route('admin.products.update', $product), $this->validData(['brand_id' => $product->brand_id, 'status' => 'sold', 'price_sell' => 1234000]))
            ->assertRedirect();

        $fresh = $product->fresh();
        $this->assertSame(1234000, $fresh->price_sell);
        $this->assertTrue($fresh->sold_at->equalTo($soldAt));
    }

    public function test_hapus_produk_membersihkan_berkas_foto(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        Storage::disk('public')->put('products/x/a.jpg', 'isi');
        $product->photos()->create(['path' => 'products/x/a.jpg', 'position' => 1]);

        $this->delete(route('admin.products.destroy', $product))->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_photos', 0);
        Storage::disk('public')->assertMissing('products/x/a.jpg');
    }

    public function test_hapus_satu_foto(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        Storage::disk('public')->put('products/x/b.jpg', 'isi');
        $photo = $product->photos()->create(['path' => 'products/x/b.jpg', 'position' => 1]);

        $this->delete(route('admin.photos.destroy', $photo))->assertRedirect();

        $this->assertNull(ProductPhoto::find($photo->id));
        Storage::disk('public')->assertMissing('products/x/b.jpg');
    }

    public function test_dashboard_menghitung_stok_dan_laba_bulan_ini(): void
    {
        $this->actingAs(User::factory()->create());
        Product::factory()->count(2)->create(['price_sell' => 3000000, 'price_cost' => 2500000]);
        Product::factory()->create(['status' => 'sold', 'sold_at' => now(), 'price_sell' => 4000000, 'price_cost' => 3300000]);
        Product::factory()->create(['status' => 'sold', 'sold_at' => now()->subMonths(2), 'price_sell' => 9000000, 'price_cost' => 1]);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Rp 6.000.000')   // nilai jual stok
            ->assertSee('Rp 4.000.000')   // omzet bulan ini
            ->assertSee('Rp 700.000');    // laba kotor bulan ini
    }

    public function test_daftar_produk_bisa_dicari_dan_difilter(): void
    {
        $this->actingAs(User::factory()->create());
        $apple = Brand::factory()->create(['name' => 'Apple', 'slug' => 'apple']);
        Product::factory()->create(['brand_id' => $apple->id, 'model' => 'iPhone 12', 'imei' => '356938035643809']);
        Product::factory()->create(['model' => 'Lainnya', 'status' => 'sold', 'sold_at' => now()]);

        $this->get('/admin/products?q=356938')->assertSee('iPhone 12')->assertDontSee('Lainnya');
        $this->get('/admin/products?status=sold')->assertSee('Lainnya')->assertDontSee('iPhone 12');
    }
}
