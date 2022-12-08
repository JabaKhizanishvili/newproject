@if($paginator->hasPages())
    <div class="pagination-container" style="text-align-last: end">
        <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><a onclick="return false;" class="page-link" href="javascript:void(0);"><i class="icon ion-ios-arrow-back"></i></a></li>
            @else

                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}"><i class="icon ion-ios-arrow-back"></i></a></li>
            @endif
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())

                                <li class="page-item active"><a onclick="return false;" class="page-link" href="javascript:void(0);">{{$page}}</a></li>


                        @else

                                <li class="page-item"><a class="page-link" href="{{$url}}">{{$page}}</a></li>

                        @endif
                    @endforeach
                @endif
            @endforeach
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())

                    <li class="page-item"><a class="page-link" href="{{$paginator->nextPageUrl() }}"><i class="icon ion-ios-arrow-forward"></i></a></li>
            @else

                    <li class="page-item"><a onclick="return false;" class="page-link" href="javascript:void(0);"><i class="icon ion-ios-arrow-forward"></i></a></li>

            @endif
        </ul>
    </div>
@else
    <div class="pagination-container" style="text-align-last: end">



        <ul class="pagination mb-0">
            <li class="page-item"><a onclick="return false;" class="page-link" href="javascript:void(0);"><i class="icon ion-ios-arrow-back"></i></a></li>
            <li class="page-item active"><a onclick="return false;" class="page-link" href="javascript:void(0);">1</a></li>
            <li class="page-item"><a onclick="return false;" class="page-link" href="javascript:void(0);"><i class="icon ion-ios-arrow-forward"></i></a></li>
        </ul>
    </div>
@endif
