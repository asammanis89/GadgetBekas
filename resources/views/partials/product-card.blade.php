<a class="card {{ $product->is_sold ? 'is-sold' : '' }}" href="{{ route('catalog.show', $product) }}">
    <div class="art">
        @if ($product->photos->isNotEmpty())
            <img src="{{ $product->photos->first()->url() }}" alt="{{ $product->title }}" loading="lazy">
        @else
            @include('partials.phone-art', ['product' => $product])
        @endif
    </div>
    @if ($product->status === 'reserved') <span class="ribbon reserved">Dipesan</span> @endif
    @if ($product->is_sold) <span class="ribbon">Terjual</span> @endif
    <div class="body">
        <h3>{{ $product->title }}</h3>
        <div class="spec">{{ $product->spec_line }}</div>
        <div class="tags">
            <span class="tag {{ strtolower($product->grade) }}">Grade {{ $product->grade }}</span>
            @if ($product->battery_health) <span class="tag">Baterai {{ $product->battery_health }}%</span> @endif
        </div>
        <span class="price num">{{ $product->price_formatted }}</span>
    </div>
</a>
