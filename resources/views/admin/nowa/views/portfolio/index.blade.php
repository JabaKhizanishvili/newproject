@extends('admin.nowa.views.layouts.app')

@section('styles')



@endsection

@section('content')

    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">@lang('admin.partners')</h4>
                    </div>
                    <a href="{{locale_route('portfolio.create')}}" class="btn ripple btn-primary" type="button">@lang('admin.createbutt')</a>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form class="mr-0 p-0">
                            <table class="table mg-b-0 text-md-nowrap">
                                <thead>
                                <tr>
                                    <th>@lang('admin.id')</th>
                                    {{-- <th>@lang('admin.status')</th> --}}
                                    <th>@lang('admin.title')</th>
                                    <th>@lang('admin.actions')</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <th>
                                        <input class="form-control" type="number" name="id" onchange="this.form.submit()"
                                               value="{{Request::get('id')}}"
                                               class="validate {{$errors->has('id') ? '' : 'valid'}}">
                                    </th>

                                    <th>
                                        <input class="form-control" type="text" name="title" onchange="this.form.submit()"
                                               value="{{Request::get('title')}}"
                                               class="validate {{$errors->has('title') ? '' : 'valid'}}">
                                    </th>
                                    <th></th>
                                </tr>


                                @if($data)
                                    @foreach($data as $item)
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            {{-- <td>
                                                @if($item->status)
                                                    <span class="green-text">@lang('admin.active')</span>
                                                @else
                                                    <span class="red-text">@lang('admin.not_active')</span>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <div class="panel panel-primary tabs-style-2">
                                                    <div class=" tab-menu-heading">
                                                        <div class="tabs-menu1">
                                                            <!-- Tabs -->
                                                            <ul class="nav panel-tabs main-nav-line">
                                                                @foreach(config('translatable.locales') as $locale)
                                                                    <li><a href="#cat-{{$locale}}-{{$item->id}}" class="nav-link {{$loop->first?"active":""}}" data-bs-toggle="tab">{{$locale}}</a></li>
                                                                @endforeach

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body tabs-menu-body main-content-body-right border">
                                                        <div class="tab-content">

                                                            @foreach(config('translatable.locales') as $locale)
                                                                <div class="tab-pane {{$loop->first?"active":""}}" id="cat-{{$locale}}-{{$item->id}}">
                                                                    {{$item->translate($locale)->title ?? ''}}
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>

                                            </td>

                                            <td>

                                                <a href="{{locale_route('portfolio.edit',$item->id)}}"
                                                   class="pl-3">
                                                    <i class="fa fa-edit">შეცვლა</i>
                                                </a>

                                                <a href="{{locale_route('portfolio.destroy',$item->id)}}"
                                                   onclick="return confirm('Are you sure?')" class="pl-3">
                                                    <i class="fa fa-edit">წაშლა</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

        {{ $data->appends(request()->input())->links('admin.vendor.pagination.material') }}
    </div>
    <!-- /row -->

@endsection

@section('scripts')



@endsection
