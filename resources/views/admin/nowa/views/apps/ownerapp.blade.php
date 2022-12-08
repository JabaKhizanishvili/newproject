@extends('admin.nowa.views.layouts.app')

@section('styles')


@endsection

@section('content')



    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            {{-- <span class="main-content-title mg-b-0 mg-b-lg-1">@lang('admin.ownerapplication')</span> --}}
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- row opened -->

<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">@lang('admin.ownerapplication')</h4>
                </div>

                {{--<p class="tx-12 tx-gray-500 mb-2">Example of Nowa Simple Table. <a href="">Learn more</a></p>--}}
            </div>
            <div class="card-body">



                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                   <th>id</th>
                                                   <th>name of ship</th>
                                                   <th>details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($data)
                                                     @foreach($data as $item)
                                                     <tr>
                                                        <td>{{$item->id}}</td>
                                                        <td>{{$item->name_of_ship}}</td>
                                                        <td><a class="btn btn-primary" href="{{route('app.details',$item->id)}}">
                                                              details</a></td>
                                                    </tr>

                                            {{-- <tr>
                                          <td scope="row">

                                              </td>


                                                       </tr> --}}
                                                @endforeach
                                             @endif


                                            </tbody>
                                        </table>
                                    </div>
                                </div>




        </div>
    </div>



@endsection

@section('scripts')



@endsection
