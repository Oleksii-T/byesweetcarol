<style>
    ._pagination {
        display: flex;
        /* align-items: center; */
        justify-content: center;
        gap: 20px;
    }
    ._pagination .pagination-number {
        padding: 12px 17px;
        font-size: 15px;
        color: #e9e9ff;
        background-color: rgb(23,21,35);
        transition: all .3s;
        border-radius: 14px;
        border: 2px solid rgba(197,193,242,.1);
    }
    ._pagination .active .pagination-number {
        background-color: rgb(119,44,251);
    }
    ._pagination .pagination-side {
        color: #e9e9ff;
    }
</style>
@if ($paginator->hasPages())
    <nav>
        <ul class="_pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="pagination-side" aria-hidden="true">&lsaquo; PREV</span>
                </li>
            @else
                <li>
                    <a class="pagination-side" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo; PREV</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true" class="pagination-number">
                        <span>{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page">
                                <span class="pagination-number">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a class="pagination-number" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="pagination-side" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">NEXT &rsaquo;</a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="pagination-side" aria-hidden="true">NEXT &rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
