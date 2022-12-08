{{-- @php
    print_r($data)
@endphp --}}

@extends('admin.nowa.views.layouts.app')

@section('styles')


@endsection

@section('content')


   <div class="container-fluid">

    <div class="row d=flex justify-content-center">
        <div class="col-md-8">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Name</th>
                                <td>{{$data->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">
                                   surname
                                </th>
                                <td>{{$data->surname}}</td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    phone
                                </th>
                                <td>{{$data->phone}}</td>
                            </tr>
                            <tr>
                                <th scope="row">
                                   email
                                </th>
                                <td>{{$data->email}}</td>
                            </tr>
                            <tr>
                                <th scope="row">
                                  course
                                </th>
                                <td>{{$data->course}}</td>
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
