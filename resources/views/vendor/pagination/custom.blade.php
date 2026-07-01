@if ($paginator->hasPages())
    <nav class="d-flex justify-content-end">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Custom Pagination Elements --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                
                $pages = [];
                
                if ($lastPage <= 5) {
                    $pages = range(1, $lastPage);
                } else {
                    if ($currentPage <= 3) {
                        $pages = [1, 2, 3, '...', $lastPage];
                    } elseif ($currentPage >= $lastPage - 2) {
                        $pages = [1, '...', $lastPage - 2, $lastPage - 1, $lastPage];
                    } else {
                        $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
                    }
                }
            @endphp
            
            @foreach ($pages as $page)
                @if ($page === '...')
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                @else
                    @if ($page == $currentPage)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
