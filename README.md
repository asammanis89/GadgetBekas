# GadgetBekas

Toko HP bekas dengan katalog publik dan halaman admin untuk mengelola produk, dibuat dengan **Laravel 12**. Pembeli menelusuri unit, melihat grade dan kesehatan baterai, lalu bertanya lewat WhatsApp. Admin mengelola stok, foto, harga modal, dan status tiap unit.

> **Status: kode lengkap, tetapi belum pernah dijalankan.** Proyek ini ditulis di lingkungan yang tidak bisa mengakses Packagist, sehingga `composer install` tidak bisa dijalankan dan Laravel tidak pernah terpasang di sana. Yang sudah dilakukan hanya pemeriksaan sintaks PHP (`php -l`) pada seluruh berkas PHP. Tes, tampilan, dan migrasi belum dijalankan, dan **belum ada tangkapan layar**. Jalankan langkah di bawah dan `php artisan test` untuk memastikan; bila ada yang gagal, itu bug nyata yang perlu diperbaiki, bukan salah pemakaian.

## Fitur

**Katalog publik**
- Daftar unit dengan pencarian, filter merek, kondisi (grade A/B/C), harga maksimal, dan pengurutan (terbaru, termurah, termahal, baterai terbaik), dengan halaman bernomor.
- Halaman produk: galeri foto, grade, kesehatan baterai, memori, kelengkapan, garansi toko, dan tombol **Tanya lewat WhatsApp** yang sudah berisi nama unit, harga, dan tautannya.
- Unit *dipesan* tetap tampil dengan label, unit *terjual* hilang dari katalog tetapi tautannya masih terbuka dengan pemberitahuan.
- IMEI dan harga modal tidak pernah tampil di sisi publik.
- Unit tanpa foto memakai siluet HP berwarna sesuai merek.

**Admin** (`/admin`, wajib masuk)
- Ringkasan: jumlah unit tersedia dan dipesan, nilai jual stok beserta modalnya, serta omzet dan laba kotor bulan ini.
- Produk: tambah, ubah, hapus; unggah sampai 8 foto (JPG, PNG, WebP, maks. 4 MB), hapus foto satuan, cari dengan merek, model, atau IMEI, filter status.
- Tombol cepat **Tandai terjual**, **Dipesan**, **Jadikan tersedia**. Tanggal terjual terisi otomatis dan tidak tertimpa saat unit yang sudah terjual diubah.
- Validasi dengan pesan bahasa Indonesia, slug unik otomatis.

## Menjalankan

Butuh PHP 8.2+ (ekstensi `mbstring`, `pdo_sqlite`, `gd`, `fileinfo`) dan Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed        # skema + 18 unit contoh fiktif + akun admin
php artisan storage:link          # agar foto yang diunggah bisa diakses
php artisan serve                 # http://localhost:8000
```

Masuk di `/masuk` dengan `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dari `.env` (bawaan: `admin@gadgetbekas.test` / `ganti-password-ini`). **Ganti password sebelum dipakai sungguhan**, dan jalankan `db:seed` sebelum `config:cache` (atau ulangi setelahnya).

Ubah nomor WhatsApp, nama, alamat, dan jam buka toko lewat `SHOP_*` di `.env`. Nomor diisi tanpa tanda `+`, diawali kode negara (mis. `62812...`). Untuk MySQL atau PostgreSQL, ganti `DB_CONNECTION` dan isi `DB_HOST`, `DB_DATABASE`, dan seterusnya.

## Pengujian

```bash
php artisan test
```

Ada 21 tes fitur (`tests/Feature`) dengan SQLite di memori, ditulis tetapi **belum dijalankan** (lihat status di atas): filter, urutan, dan pencarian katalog; unit terjual tersembunyi; IMEI dan harga modal tidak bocor; tautan WhatsApp; tamu diarahkan ke halaman masuk; login benar dan salah; tambah produk dengan foto; validasi; slug unik; status terjual mengisi dan menghapus tanggal; tanggal terjual tidak tertimpa; hapus produk dan foto membersihkan berkas; dan angka ringkasan dashboard. CI GitHub Actions menjalankannya pada tiap push.

## Struktur

```
app/Http/Controllers/    CatalogController, AuthController, Admin/{Dashboard,Product,Photo}Controller
app/Http/Requests/       ProductRequest (aturan dan pesan validasi)
app/Models/              Product, Brand, ProductPhoto, User
database/                migrasi, factory, seeder (stok contoh fiktif)
resources/views/         layouts, katalog, admin, partial
public/css/app.css       gaya tunggal tanpa langkah build (tanpa Vite/npm)
tests/Feature/           CatalogTest, AdminTest
```

Konfigurasi memakai bawaan framework Laravel 12; hanya `config/shop.php` yang ditambahkan.

## Keamanan

- Rute admin dilindungi middleware `auth`; formulir memakai token CSRF; percobaan login dibatasi 10 kali per menit.
- Upload dibatasi pada tipe gambar dan ukuran; berkas disimpan di disk `public` dengan nama acak.
- Pencarian memakai parameter terikat dan karakter wildcard `LIKE` di-escape.
- Hanya ada satu peran (admin). Belum ada manajemen pengguna di antarmuka; tambah akun lewat tinker atau seeder.

## Batasan dan rencana

- Belum ada tangkapan layar dan belum ada bukti bahwa semuanya berjalan; lihat status di atas.
- Belum ada keranjang atau pembayaran: transaksi lewat WhatsApp dan tatap muka.
- Foto tidak diperkecil otomatis; unggah foto yang sudah wajar ukurannya.
- Belum ada pengurutan ulang foto dan belum ada pengelolaan merek dari antarmuka (merek diisi lewat seeder).

## Lisensi

MIT. Lihat [LICENSE](LICENSE).
