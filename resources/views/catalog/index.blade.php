@extends('layouts.app')

@section('content')
<div class="wrap">
    <section class="hero">
        <h1>HP bekas yang kondisinya ditulis apa adanya</h1>
        <p>Setiap unit dicek layar, kamera, sinyal, dan baterainya sebelum dipajang. Grade dan kesehatan baterai tercantum jelas, dan semua bergaransi toko.</p>
        <div class="trust"><span>Dicek 20 poin</span><span>Garansi toko 7-30 hari</span><span>Bisa COD dan cek langsung</span></div>
    </section>

    <form class="filters" method="get" action="{{ route('catalog.index') }}">
        <div class="wide"><label for="q">Cari</label><input id="q" name="q" value="{{ $filters['q'] }}" placeholder="mis. iPhone 12 atau Samsung"></div>
        <div>
            <label for="merek">Merek</label>
            <select id="merek" name="merek">
                <option value="">Semua</option>
                @foreach ($brands as $b)
                    <option value="{{ $b->slug }}" @selected($filters['merek'] === $b->slug)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="grade">Kondisi</label>
            <select id="grade" name="grade">
                <option value="">Semua</option>
                @foreach (\App\Models\Product::GRADES as $g => $label)
                    <option value="{{ $g }}" @selected($filters['grade'] === $g)>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="maks">Harga maksimal</label>
            <select id="maks" name="maks">
                <option value="">Bebas</option>
                @foreach ([2000000, 3000000, 5000000, 8000000, 12000000] as $limit)
                    <option value="{{ $limit }}" @selected((string) $filters['maks'] === (string) $limit)>{{ \App\Models\Product::rupiah($limit) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="urut">Urutkan</label>
            <select id="urut" name="urut">
                @foreach ($sorts as $value => $label)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn primary" type="submit">Terapkan</button>
    </form>

    @if ($products->isEmpty())
        <div class="empty">
            <strong>Belum ada unit yang cocok</strong>
            Coba longgarkan filter, atau <a href="{{ route('catalog.index') }}">tampilkan semua unit</a>.
        </div>
    @else
        <p class="muted">{{ $products->total() }} unit</p>
        <div class="grid">
            @foreach ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        {{ $products->links() }}
    @endif
</div>
@endsection
