@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <span>&laquo; Sebelumnya</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
    @endif

    <span class="active">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
    @else
        <span>Berikutnya &raquo;</span>
    @endif
@endif
