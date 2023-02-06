@extends('admin.nowa.views.layouts.app')

@section('styles')



@endsection

@section('content')

<div class="container-fluid bg-white ">
    {{-- <h5 class='mb-2 pt-2'>admin.irons</h5> --}}
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between">
            <h4 class="card-title mg-b-0">@lang('admin.irons')</h4>
        </div>
        {{-- <a href="{{locale_route('product.create')}}" class="btn ripple btn-primary" type="button">@lang('admin.createbutt')</a> --}}
        <button type="submit" id='costumbtn' class="btn ripple btn-primary">admin.create</button>

        {{--<p class="tx-12 tx-gray-500 mb-2">Example of Nowa Simple Table. <a href="">Learn more</a></p>--}}
    </div>
    <form style='display: none' id='popup' class='w-25' action={{route("admin.irons.create")}} method="post">
        @csrf
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Choose Product</label>
          <select class="form-control" name="product" id='size' required>
            <option disabled selected>choose product</option>
            @foreach ($zoma as $cat)
            <option value={{$cat->id}}>{{$cat->translate($locale)->name}}</option>

            @endforeach
        </select>

        <label for="exampleInputEmail1" class="form-label">Enter Size</label>
          <input name="size" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
        </div>
        <button type="submit" class="btn ripple btn-primary">create</button>
      </form>
 </div>

 <table class="table mg-b-0 text-md-nowrap mt-3 mb-2">
    <thead>
        <tr>
            {{-- <th>ID</th> --}}
            <th>id</th>
            <th>name</th>
            <th>size</th>
            <th></th>
            <th></th>
            {{-- <th>delete</th> --}}
            {{-- <th>Salary</th> --}}
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <form method='get' action={{route('admin.irons.search')}}>
                {{-- @csrf --}}
                <td>
                    <input class="form-control" name='search' placeholder="search">
                </td>
            </form>
            <form method='get' action={{route('admin.irons.search')}}>
                {{-- @csrf --}}
                <td>
                    <input class="form-control" name='size' placeholder="search">
                </td>
            </form>
              <td></td>
        </tr>

        @if ($search != [])
        @foreach ($search as $v)
              <tr>
                  <td>{{$v->id}}</td>
                  <td>{{$v->iron->name}}</td>
                  <form action={{route('admin.irons.update')}} method="post">
                      @csrf
                  <td>
                      <input class="form-control" type="text" value="{{$v->size}}" name="size">
                      <input class="form-control" type="hidden" name='id' value="{{$v->iron_id}}">
                  </td>
                  <td>
                      <a href="{{locale_route('admin.irons.del',$v->id)}}"
                          onclick="return confirm('Are you sure?')" class="pl-3">
                           <i class="fa fa-edit">წაშლა</i>
                       </a>
                  </td>
                  <td>
                      <button class='btn' href="{{locale_route('admin.irons.update',$v->id)}}"
                      class="pl-3">
                       <i class="fa fa-edit">შეცვლა</i>
                      </button>
                  </td>
              </form>
              </tr>
              @endforeach
        @else
        @foreach ($sizes as $v)

        {{-- @dd($v->iron) --}}
              <tr>
                  <td>{{$v->id}}</td>
                  <td>{{$v->iron->name}}</td>
                  <form action={{route('admin.irons.update')}} method="post">
                      @csrf
                  <td>
                      <input class="form-control" type="text" value="{{$v->size}}" name="size">
                      <input class="form-control" type="hidden" name='id' value="{{$v->iron_id}}">
                  </td>
                  <td>
                      <a href="{{locale_route('admin.irons.del',$v->id)}}"
                          onclick="return confirm('Are you sure?')" class="pl-3">
                           <i class="fa fa-edit">წაშლა</i>
                       </a>
                  </td>
                  <td>
                      <button class='btn' href="{{locale_route('admin.irons.update',$v->id)}}"
                      class="pl-3">
                       <i class="fa fa-edit">შეცვლა</i>
                      </button>
                  </td>
              </form>
              </tr>
              @endforeach
        @endif




    </tbody>
</table>

@if ($search != [])
<div class="contaner">

    {{ $search->appends(request()->input())->links('admin.vendor.pagination.material') }}
</div>
@else
<div class="contaner">

    {{ $sizes->appends(request()->input())->links('admin.vendor.pagination.material') }}
</div>
@endif
{{-- <div class="contaner">
    {{ $sizes->appends(request()->input())->links('admin.vendor.pagination.material') }}
</div> --}}

@endsection

@section('scripts')
<script>
    let btn = document.getElementById('costumbtn'), box = document.getElementById('popup'),clicked=false;
    btn.addEventListener('click', ()=>{
        clicked = !clicked;
        if(clicked){
            box.style.display = 'block';
            btn.style.display = 'none';
        }
    })
</script>



@endsection
