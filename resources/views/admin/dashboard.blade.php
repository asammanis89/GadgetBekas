@extends('layouts.admin')

@section('title', 'Ringkasan')

@section('content')
<div class="page-head">
    <h1>Ringkasan</h1>
    <a class="btn primary" href="{{ route('admin.products.create') }}">Tambah produk</a>
</div>

<div class="kpis">
    <div class="kpi"><b>{{ $counts['available'] }}</b><span>Unit tersedia</span></div>
    <div class="kpi"><b>{{ $counts['reserved'] }}</b><span>Sedang dipesan</span></div>
    <div class="kpi"><b>{{ \App\Models\Product::rupiah($stockValue) }}</b><span>Nilai jual stok (modal {{ \App\Models\Product::rupiah($stockCost) }})</span></div>
    <div class="kpi"><b>{{ $soldMonthCount }}</b><span>Terjual bulan ini: {{ \App\Models\Product::rupiah($soldMonthRevenue) }}, laba kotor {{ \App\Models\Product::rupiah($soldMonthProfit) }}</span></div>
</div>

<div class="panel">
    <div class="head">Produk terbaru <a href="{{ route('admin.products.index') }}">Lihat semua</a></div>
    <div class="table-wrap">
        <table class="list">
            <thead><tr><th>Produk</th><th>Grade</th><th>Harga</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($recent as $p)
                <tr>
                    <td><a href="{{ route('admin.products.edit', $p) }}">{{ $p->title }}</a><div class="muted">{{ $p->spec_line }}</div></td>
                    <td>{{ $p->grade }}</td>
                    <td class="num">{{ $p->price_formatted }}</td>
                    <td><span class="status {{ $p->status }}">{{ \App\Models\Product::STATUSES[$p->status] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
