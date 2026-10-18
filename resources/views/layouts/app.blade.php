<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('shop.name').' - HP bekas terseleksi')</title>
    <meta name="description" content="@yield('description', 'HP bekas yang sudah dicek lengkap: kondisi jujur, kesehatan baterai tercantum, bergaransi toko.')">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-head">
    <div class="wrap">
        <a class="logo" href="{{ route('catalog.index') }}"><i></i>{{ config('shop.name') }}</a>
        <nav class="nav" aria-label="Menu">
            <a class="keep" href="{{ route('catalog.index') }}">Katalog</a>
            @auth
                <a class="keep" href="{{ route('admin.dashboard') }}">Admin</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
            @endauth
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="site-foot">
    <div class="wrap">
        <p><b>{{ config('shop.name') }}</b></p>
        @if (config('shop.address')) <p>{{ config('shop.address') }}</p> @endif
        @if (config('shop.hours')) <p>{{ config('shop.hours') }}</p> @endif
        <p>Harga dan stok dapat berubah sewaktu-waktu. Unit hanya ada satu, jadi tanyakan ketersediaannya sebelum datang.</p>
    </div>
</footer>
@stack('scripts')
</body>
</html>
