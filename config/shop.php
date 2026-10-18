<?php

return [
    'name' => env('SHOP_NAME', 'GadgetBekas'),
    // Nomor WhatsApp tanpa tanda + atau spasi, diawali kode negara (62 untuk Indonesia).
    'whatsapp' => preg_replace('/\D+/', '', (string) env('SHOP_WHATSAPP', '6281200000000')),
    'address' => env('SHOP_ADDRESS', ''),
    'hours' => env('SHOP_HOURS', ''),
    // Dipakai oleh `php artisan db:seed` untuk membuat akun admin pertama.
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin Toko'),
        'email' => env('ADMIN_EMAIL', 'admin@gadgetbekas.test'),
        'password' => env('ADMIN_PASSWORD', 'ganti-password-ini'),
    ],
];
