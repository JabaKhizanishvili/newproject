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
                                <th>email</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>

                                <td>{{$data->email}}</td>
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
