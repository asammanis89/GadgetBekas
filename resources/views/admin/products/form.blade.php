@extends('layouts.admin')

@php($editing = $product->exists)
@section('title', $editing ? 'Ubah '.$product->title : 'Tambah produk')

@section('content')
<div class="page-head">
    <h1>{{ $editing ? 'Ubah '.$product->title : 'Tambah produk' }}</h1>
    <a href="{{ route('admin.products.index') }}">&larr; Kembali ke daftar</a>
</div>

@if ($errors->any())
    <div class="errors" role="alert">
        Periksa kembali isian berikut:
        <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
    </div>
@endif

<form class="panel form" method="post" enctype="multipart/form-data"
      action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="field w3">
        <label for="brand_id">Merek</label>
        <select id="brand_id" name="brand_id" required>
            <option value="">Pilih merek</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected((string) old('brand_id', $product->brand_id) === (string) $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field w3">
        <label for="model">Model</label>
        <input id="model" name="model" value="{{ old('model', $product->model) }}" required maxlength="80" placeholder="mis. iPhone 12">
    </div>

    <div class="field"><label for="ram_gb">RAM (GB)</label><input id="ram_gb" type="number" name="ram_gb" value="{{ old('ram_gb', $product->ram_gb) }}" min="1"></div>
    <div class="field"><label for="storage_gb">Penyimpanan (GB)</label><input id="storage_gb" type="number" name="storage_gb" value="{{ old('storage_gb', $product->storage_gb) }}" min="1" required></div>
    <div class="field"><label for="color">Warna</label><input id="color" name="color" value="{{ old('color', $product->color) }}" maxlength="40"></div>

    <div class="field">
        <label for="grade">Kondisi</label>
        <select id="grade" name="grade" required>
            @foreach (\App\Models\Product::GRADES as $g => $label)
                <option value="{{ $g }}" @selected(old('grade', $product->grade) === $g)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label for="battery_health">Kesehatan baterai (%)</label><input id="battery_health" type="number" name="battery_health" value="{{ old('battery_health', $product->battery_health) }}" min="1" max="100"></div>
    <div class="field"><label for="warranty_days">Garansi toko (hari)</label><input id="warranty_days" type="number" name="warranty_days" value="{{ old('warranty_days', $product->warranty_days) }}" min="0" max="365"></div>

    <div class="field"><label for="price_sell">Harga jual (Rp)</label><input id="price_sell" type="number" name="price_sell" value="{{ old('price_sell', $product->price_sell) }}" min="0" step="1000" required></div>
    <div class="field"><label for="price_cost">Harga modal (Rp)</label><input id="price_cost" type="number" name="price_cost" value="{{ old('price_cost', $product->price_cost) }}" min="0" step="1000"><small>Hanya terlihat admin.</small></div>
    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (\App\Models\Product::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $product->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="field w3"><label for="imei">IMEI</label><input id="imei" name="imei" value="{{ old('imei', $product->imei) }}" inputmode="numeric" maxlength="15" placeholder="15 digit"><small>Hanya terlihat admin.</small></div>
    <div class="field w3"><label for="accessories">Kelengkapan</label><input id="accessories" name="accessories" value="{{ old('accessories', $product->accessories) }}" maxlength="300" placeholder="mis. Unit, charger, dus"></div>

    <div class="field full"><label for="description">Deskripsi</label><textarea id="description" name="description" maxlength="2000">{{ old('description', $product->description) }}</textarea></div>

    @if ($editing && $product->photos->isNotEmpty())
        <div class="field full">
            <label>Foto saat ini</label>
            <div class="photos">
                @foreach ($product->photos as $photo)
                    <figure>
                        <img src="{{ $photo->url() }}" alt="Foto {{ $loop->iteration }}">
                        <button class="btn small danger" style="margin-top:6px;width:100%" type="submit" form="del-photo-{{ $photo->id }}">Hapus</button>
                    </figure>
                @endforeach
            </div>
        </div>
    @endif

    <div class="field full">
        <label for="photos">Tambah foto</label>
        <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
        <small>Maksimal 8 foto, masing-masing 4 MB. Foto pertama menjadi sampul.</small>
    </div>

    <div class="form-actions">
        <a class="btn" href="{{ route('admin.products.index') }}">Batal</a>
        <button class="btn primary" type="submit">{{ $editing ? 'Simpan perubahan' : 'Tambah produk' }}</button>
    </div>
</form>

{{-- Formulir hapus foto berada di luar formulir utama karena formulir tidak boleh bersarang. --}}
@if ($editing)
    @foreach ($product->photos as $photo)
        <form id="del-photo-{{ $photo->id }}" method="post" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini?')">@csrf @method('DELETE')</form>
    @endforeach
    <form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus {{ addslashes($product->title) }} beserta fotonya?')" style="margin:18px 0 40px">
        @csrf @method('DELETE')
        <button class="btn danger" type="submit">Hapus produk ini</button>
    </form>
@endif
@endsection
