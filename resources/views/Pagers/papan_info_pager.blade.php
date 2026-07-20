@php
    $pager->setSurroundCount(1);
    $links = $pager->links();
@endphp

@if(count($links) > 1)
    <nav class="sipencak-pager d-flex justify-content-end" aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            @if($pager->hasPrevious())
                <li class="page-item">
                    <a class="page-link" href="{!! $pager->getFirst() !!}" aria-label="First">«</a>
                </li>
            @endif

            @foreach($links as $link)
                <li class="page-item {!! $link['active'] ? 'active' : '' !!}">
                    <a class="page-link" href="{!! $link['uri'] !!}">
                        {!! $link['title'] !!}
                    </a>
                </li>
            @endforeach

            @if($pager->hasNext())
                <li class="page-item">
                    <a class="page-link" href="{!! $pager->getLast() !!}" aria-label="Last">»</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
