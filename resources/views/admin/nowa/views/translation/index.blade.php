@extends('admin.nowa.views.layouts.app')

@section('styles')



@endsection

@section('content')



    <!-- breadcrumb -->
    {{-- <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">@lang('admin.translations')</span>
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div> --}}
    <!-- /breadcrumb -->

    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">@lang('admin.translations')</h4>
                    </div>


                    {{--<p class="tx-12 tx-gray-500 mb-2">Example of Nowa Simple Table. <a href="">Learn more</a></p>--}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                            <table class="table mg-b-0 text-md-nowrap">
                                <thead>
                                <tr>
                                    <th>@lang('admin.id')</th>
                                    <th>@lang('admin.group')</th>
                                    <th>@lang('admin.key')</th>
                                    <th>@lang('admin.text')</th>
                                    <th>@lang('admin.actions')</th>
                                </thead>
                                <tbody>

                                <tr>
                                    <form class="mr-0 p-0">
                                        <th>
                                            <input class="form-control" type="number" name="id" onchange="this.form.submit()"
                                                   value="{{Request::get('id')}}"
                                                   class="validate {{$errors->has('id') ? '' : 'valid'}}">
                                        </th>
                                        <th>
                                            <input class="form-control" type="text" name="group" onchange="this.form.submit()"
                                                   value="{{Request::get('group')}}"
                                                   class="validate {{$errors->has('group') ? '' : 'valid'}}">
                                        </th>
                                        <th>
                                            <input class="form-control" type="text" name="key" onchange="this.form.submit()"
                                                   value="{{Request::get('key')}}"
                                                   class="validate {{$errors->has('key') ? '' : 'valid'}}">
                                        </th>
                                        <th>
                                            <input class="form-control" type="text" name="text" onchange="this.form.submit()"
                                                   value="{{Request::get('text')}}"
                                                   class="validate {{$errors->has('text') ? '' : 'valid'}}">
                                        </th>
                                        <th></th>
                                    </form>
                                </tr>

                                @if($translations)
                                    @foreach($translations as $key_p => $translation)
                                        <?php
                                        $url = locale_route('translation.update', $translation->id, false);
                                        $method = 'PUT';
                                        ?>
                                        <tr>





                                            <td>{{$translation->id}}</td>
                                            <td>{{$translation->group}}</td>
                                            <td>{{$translation->key}}</td>
                                            <td>


                                                <form method="post" action="{{$url}}" class="edit-form-index" id="edit_index_{{$translation->id}}">
                                                    <div class="panel panel-primary tabs-style-2">
                                                        <div class=" tab-menu-heading">
                                                            <div class="tabs-menu1">
                                                                <!-- Tabs -->
                                                                <ul class="nav panel-tabs main-nav-line">
                                                                    @foreach($languages as $key => $language)
                                                                        <?php
                                                                        $active = '';
                                                                        if($loop->first) $active = 'active';
                                                                        ?>
                                                                        <li><a href="#cat-{{$key_p.'-'.$key}}" class="nav-link {{$active}}" data-bs-toggle="tab">{{$language->locale}}</a></li>

                                                                    @endforeach

                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="panel-body tabs-menu-body main-content-body-right border">
                                                            <div class="tab-content">

                                                                @foreach($languages as $key => $language)
                                                                    <?php
                                                                    $active = '';
                                                                    if($loop->first) $active = 'active';
                                                                    ?>

                                                                    <div class="tab-pane {{$active}}" id="cat-{{$key_p.'-'.$key}}">

                                                                        {!! Form::textarea('text['.$key.']',isset($translation->text[$key]) ? $translation->text[$key]:  '',['class' => 'form-control','rows' => '1']) !!}

                                                                    </div>

                                                                @endforeach

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>

                                                <button class="btn btn-primary" data-save_translation="{{$translation->id}}">@lang('admin.save')</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                            </table>

                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
        <div class="col-xl-12">
        {{ $translations->appends(request()->input())->links('admin.vendor.pagination.material') }}
        </div>
    </div>
    <!-- /row -->

@endsection

@section('scripts')

<script>
    $('.edit-form-index').submit(function (e){
        e.preventDefault();
        let $this = $(this);
        let data = $this.serialize();

        data = data + '&_token={{csrf_token()}}'

        console.log(data);

        $.ajax({
            url: $this.attr('action'),
            data: data,
            dataType: 'json',
            type: 'put',
            beforeSend: function (){

            },
            success: function (data){
                notif({
                    type: "success",
                    msg: "<b>Success: </b>Successfully Saved",
                    position: "center",
                    //autohide: false,
                    time: 3000
                });
            },
            error: function (){
                notif({
                    type: "error",
                    msg: "<b>Danger: </b>Error occurred!",
                    position: "center",
                    time: 3000,
                    //autohide: false
                });
            }
        });
    });

    $('[data-save_translation]').click(function (e){
        let id = $(this).data('save_translation');
        //alert(id);
        $('#edit_index_' + id).submit();
    });
</script>

@endsection
