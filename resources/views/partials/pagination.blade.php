@if ($paginator->hasPages())
    <nav class="pager" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="btn small" disabled>Sebelumnya</span>
        @else
            <a class="btn small" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
        @endif
        <span class="muted">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="btn small" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
        @else
            <span class="btn small" disabled>Berikutnya</span>
        @endif
    </nav>
@endif
