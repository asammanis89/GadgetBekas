<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('shop.name') }}</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="admin-bar">
    <div class="wrap">
        <a class="logo" style="color:var(--ink)" href="{{ route('admin.dashboard') }}"><i></i>{{ config('shop.name') }}</a>
        <a class="tab" href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>Ringkasan</a>
        <a class="tab" href="{{ route('admin.products.index') }}" @if (request()->routeIs('admin.products.*')) aria-current="page" @endif>Produk</a>
        <div class="push">
            <a class="btn small" href="{{ route('catalog.index') }}" target="_blank" rel="noopener">Lihat toko</a>
            <form method="post" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="btn small" type="submit">Keluar</button>
            </form>
        </div>
    </div>
</div>
<main class="wrap">
    @if (session('status'))
        <div class="flash" role="status" style="margin-top:18px">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
