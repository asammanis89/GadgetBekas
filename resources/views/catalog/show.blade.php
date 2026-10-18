@extends('layouts.app')

@section('title', $product->title.' '.$product->spec_line.' - '.config('shop.name'))
@section('description', 'Grade '.$product->grade.($product->battery_health ? ', baterai '.$product->battery_health.'%' : '').', '.$product->price_formatted.'. '.config('shop.name'))

@section('content')
@php
    $message = "Halo ".config('shop.name').", saya tertarik dengan {$product->title} ({$product->spec_line}) seharga {$product->price_formatted}. Apakah masih tersedia?\n".route('catalog.show', $product);
    $wa = 'https://wa.me/'.config('shop.whatsapp').'?text='.rawurlencode($message);
@endphp
<div class="wrap">
    <p style="margin:18px 0 0"><a href="{{ route('catalog.index') }}">&larr; Semua unit</a></p>
    <div class="detail">
        <div class="gallery">
            <div class="main">
                @if ($product->photos->isNotEmpty())
                    <img id="main-photo" src="{{ $product->photos->first()->url() }}" alt="{{ $product->title }}">
                @else
                    @include('partials.phone-art', ['product' => $product])
                @endif
            </div>
            @if ($product->photos->count() > 1)
                <div class="thumbs">
                    @foreach ($product->photos as $photo)
                        <a href="{{ $photo->url() }}" data-photo="{{ $photo->url() }}"><img src="{{ $photo->url() }}" alt="Foto {{ $loop->iteration }} {{ $product->title }}" loading="lazy"></a>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="tags" style="margin-bottom:8px">
                <span class="tag {{ strtolower($product->grade) }}">Grade {{ $product->grade }}</span>
                @if ($product->battery_health) <span class="tag">Baterai {{ $product->battery_health }}%</span> @endif
                @if ($product->warranty_days) <span class="tag">Garansi {{ $product->warranty_days }} hari</span> @endif
            </div>
            <h1>{{ $product->title }}</h1>
            <div class="muted">{{ $product->spec_line }}</div>
            <span class="price num">{{ $product->price_formatted }}</span>

            @if ($product->is_sold)
                <div class="notice">Unit ini sudah terjual. Lihat unit serupa di bawah, atau tanyakan stok berikutnya.</div>
            @elseif ($product->status === 'reserved')
                <div class="notice">Unit ini sedang dipesan pembeli lain. Anda masih bisa menanyakan bila pesanannya batal.</div>
            @endif

            <div class="cta">
                <a class="btn wa" href="{{ $wa }}" target="_blank" rel="noopener">Tanya lewat WhatsApp</a>
            </div>

            <table class="specs">
                <tr><th>Kondisi</th><td>{{ \App\Models\Product::GRADES[$product->grade] ?? $product->grade }}</td></tr>
                @if ($product->battery_health)<tr><th>Kesehatan baterai</th><td>{{ $product->battery_health }}%</td></tr>@endif
                <tr><th>Memori</th><td>{{ $product->ram_gb ? $product->ram_gb.' GB RAM / ' : '' }}{{ $product->storage_gb }} GB penyimpanan</td></tr>
                @if ($product->color)<tr><th>Warna</th><td>{{ $product->color }}</td></tr>@endif
                @if ($product->accessories)<tr><th>Kelengkapan</th><td>{{ $product->accessories }}</td></tr>@endif
                <tr><th>Garansi toko</th><td>{{ $product->warranty_days ? $product->warranty_days.' hari' : 'Tanpa garansi' }}</td></tr>
            </table>

            @if ($product->description)<p>{{ $product->description }}</p>@endif
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="section">Unit {{ $product->brand->name }} lainnya</h2>
        <div class="grid">
            @foreach ($related as $item)
                @include('partials.product-card', ['product' => $item])
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Klik gambar kecil untuk menampilkannya di gambar utama.
    document.querySelectorAll('[data-photo]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var main = document.getElementById('main-photo');
            if (main) { main.src = a.dataset.photo; }
        });
    });
</script>
@endpush
