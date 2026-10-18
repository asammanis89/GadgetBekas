@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
<div class="page-head">
    <h1>Produk</h1>
    <a class="btn primary" href="{{ route('admin.products.create') }}">Tambah produk</a>
</div>

<form method="get" class="filters" style="grid-template-columns: 2fr 1fr auto; margin-top: 0">
    <div><label for="q">Cari</label><input id="q" name="q" value="{{ $q }}" placeholder="Merek, model, atau IMEI"></div>
    <div>
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Semua</option>
            @foreach (\App\Models\Product::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn" type="submit">Cari</button>
</form>

<div class="panel">
    <div class="table-wrap">
        <table class="list">
            <thead><tr><th></th><th>Produk</th><th>Grade</th><th>Harga jual</th><th>Modal</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>
                        <div class="thumb">
                            @if ($p->photos->isNotEmpty()) <img src="{{ $p->photos->first()->url() }}" alt=""> @else <span class="muted">-</span> @endif
                        </div>
                    </td>
                    <td><a href="{{ route('admin.products.edit', $p) }}"><b>{{ $p->title }}</b></a><div class="muted">{{ $p->spec_line }}</div></td>
                    <td>{{ $p->grade }}</td>
                    <td class="num">{{ $p->price_formatted }}</td>
                    <td class="num muted">{{ \App\Models\Product::rupiah($p->price_cost) }}</td>
                    <td><span class="status {{ $p->status }}">{{ \App\Models\Product::STATUSES[$p->status] }}</span></td>
                    <td style="white-space:nowrap; text-align:right">
                        @if ($p->status !== 'sold')
                            <form class="inline" method="post" action="{{ route('admin.products.status', $p) }}">@csrf <input type="hidden" name="status" value="sold"><button class="btn small" type="submit">Tandai terjual</button></form>
                        @endif
                        @if ($p->status === 'available')
                            <form class="inline" method="post" action="{{ route('admin.products.status', $p) }}">@csrf <input type="hidden" name="status" value="reserved"><button class="btn small" type="submit">Dipesan</button></form>
                        @elseif ($p->status !== 'available')
                            <form class="inline" method="post" action="{{ route('admin.products.status', $p) }}">@csrf <input type="hidden" name="status" value="available"><button class="btn small" type="submit">Jadikan tersedia</button></form>
                        @endif
                        <a class="btn small" href="{{ route('admin.products.edit', $p) }}">Ubah</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty" style="border:0"><strong>Tidak ada produk</strong>Ubah pencarian, atau tambah produk baru.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $products->links() }}
@endsection
