@if ($paginator->hasPages() && $paginator->count() > 1)
    <nav class="sipencak-pager d-flex justify-content-end">
        <ul class="pagination pagination-sm mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link border-0 bg-transparent text-muted px-3" aria-hidden="true">
                        <i class="ri-arrow-left-s-line"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 bg-transparent text-dark px-3 fw-bold" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border-0 bg-transparent text-muted">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link border-0 bg-primary text-white fw-bold px-3 shadow-sm mx-1 rounded-pill">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 bg-transparent text-dark px-3 fw-medium hover-bg-light" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 bg-transparent text-dark px-3 fw-bold" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link border-0 bg-transparent text-muted px-3" aria-hidden="true">
                        <i class="ri-arrow-right-s-line"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
