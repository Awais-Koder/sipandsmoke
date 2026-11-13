@if ($paginator->hasPages())
  @php
    $last = $paginator->lastPage();
    $current = $paginator->currentPage();
    $start = max(1, $current - 2);
    $end = min($last, $current + 2);
  @endphp
  <nav class="admin-pagination" aria-label="Pagination">
    @if ($paginator->onFirstPage())
      <span class="is-disabled" aria-hidden="true">←</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" wire:click.prevent="previousPage('page')" rel="prev" aria-label="Previous page">←</a>
    @endif

    @if ($start > 1)
      <a href="{{ $paginator->url(1) }}" wire:click.prevent="gotoPage(1, 'page')">1</a>
      @if ($start > 2)
        <span class="is-disabled">…</span>
      @endif
    @endif

    @for ($page = $start; $page <= $end; $page++)
      @if ($page === $current)
        <span class="is-current" aria-current="page">{{ $page }}</span>
      @else
        <a href="{{ $paginator->url($page) }}" wire:click.prevent="gotoPage({{ $page }}, 'page')">{{ $page }}</a>
      @endif
    @endfor

    @if ($end < $last)
      @if ($end < $last - 1)
        <span class="is-disabled">…</span>
      @endif
      <a href="{{ $paginator->url($last) }}" wire:click.prevent="gotoPage({{ $last }}, 'page')">{{ $last }}</a>
    @endif

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" wire:click.prevent="nextPage('page')" rel="next" aria-label="Next page">→</a>
    @else
      <span class="is-disabled" aria-hidden="true">→</span>
    @endif
  </nav>
@endif
