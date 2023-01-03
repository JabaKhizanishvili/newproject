{{-- @php
    print_r($data)
@endphp --}}

@extends('admin.nowa.views.layouts.app')

@section('styles')


@endsection

@section('content')


   <div class="container-fluid">

    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>email</th>
                                <th>position</th>
                                <th>phone</th>
                                <th>mail</th>
                                <th>quantity</th>
                                <th>contact</th>

                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>{{$data->id}}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->mail}}</td>
                                <td>{{$data->position}}</td>
                                <td>{{$data->phone}}</td>
                                <td>{{$data->mail}}</td>
                                <td>{{$data->quantity}}</td>
                                <td>{{$data->contact}}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    </div>



   </div>


@endsection

@section('scripts')



@endsection
