{{-- Gambar pengganti bila produk belum punya foto: siluet HP berwarna sesuai merek. --}}
@php($hue = crc32($product->brand->name ?? 'x') % 360)
<svg viewBox="0 0 120 160" style="width: 38%; max-height: 82%;" role="img" aria-label="{{ $product->title }}">
    <rect x="22" y="6" width="76" height="148" rx="13" fill="hsl({{ $hue }}, 22%, 22%)"/>
    <rect x="27" y="13" width="66" height="134" rx="8" fill="hsl({{ $hue }}, 60%, 72%)"/>
    <rect x="48" y="16" width="24" height="5" rx="2.5" fill="hsl({{ $hue }}, 22%, 22%)"/>
    <text x="60" y="86" text-anchor="middle" font-family="sans-serif" font-size="13" font-weight="800" fill="hsl({{ $hue }}, 40%, 18%)">{{ \Illuminate\Support\Str::limit($product->brand->name ?? '', 9, '') }}</text>
</svg>
