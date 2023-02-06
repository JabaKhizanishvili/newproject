<nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style mg-b-0">
        @foreach (request()->breadcrumbs()->segments() as $key => $segment)
            @if($key>0)
                @if(!isset(request()->breadcrumbs()->segments()[$key+1]))

                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);" onclick="return false;">{{is_numeric($segment->name()) ? $segment->name() : __('admin.'.$segment->name()) }}</a>
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{$segment->url()}}">{{is_numeric($segment->name()) ? $segment->name() : __('admin.'.$segment->name()) }}</a>
                    </li>
                @endif
            @endif
        @endforeach
    </ol>


</nav>


