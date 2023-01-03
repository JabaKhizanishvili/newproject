@extends('admin.nowa.views.layouts.app')

@section('styles')

    <!--- Internal Select2 css-->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

    <!---Internal Fileupload css-->
    <link href="{{asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>

    <!---Internal Fancy uploader css-->
    <link href="{{asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />

    <!--Internal Sumoselect css-->
    <link rel="stylesheet" href="{{asset('assets/plugins/sumoselect/sumoselect.css')}}">

    <!--Internal  TelephoneInput css-->
    <link rel="stylesheet" href="{{asset('assets/plugins/telephoneinput/telephoneinput.css')}}">

    <link rel="stylesheet" href="{{asset('uploader/image-uploader.css')}}">

@endsection

@section('content')

    <input name="old-images[]" id="old_images" hidden disabled value="{{$news->files}}">
    <!-- row -->
    {!! Form::model($news,['url' => $url, 'method' => $method,'files' => true]) !!}
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">


                    <div class="mb-4">

                        <div class="panel panel-primary tabs-style-2">
                            <div class=" tab-menu-heading">
                                <div class="tabs-menu1">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs main-nav-line">
                                        @foreach(config('translatable.locales') as $locale)
                                            <?php
                                            $active = '';
                                            if($loop->first) $active = 'active';
                                            ?>

                                            <li><a href="#lang-{{$locale}}" class="nav-link {{$active}}" data-bs-toggle="tab">{{$locale}}</a></li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body tabs-menu-body main-content-body-right border">
                                <div class="tab-content">

                                    @foreach(config('translatable.locales') as $locale)

                                        <?php
                                        $active = '';
                                        if($loop->first) $active = 'active';
                                        ?>
                                        <div class="tab-pane {{$active}}" id="lang-{{$locale}}">

                                            <div class="form-group d-flex w-50">
                                                <label class="form-label">@lang('admin.visible')</label>
                                                <input type="hidden" name="{{$locale.'[visible]'}}" value="{{null}}"/>
                                                <input type="checkbox" class="mx-2" name="{{$locale.'[visible]'}}"  placeholder="@lang('admin.visible')"
                                                {{-- value="{{$news->translate($locale)->visible ?? 1}}" --}}
                                                value="{{true}}"
                                                @if (isset($news->translate($locale)->visible))
                                                    @if ($news->translate($locale)->visible != null)
                                                    checked
                                                    @endif
                                                @endif
                                                >
                                            </div>
                                            @error($locale.'.visible')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.name')</label>
                                                <input type="text" name="{{$locale.'[name]'}}" class="form-control" placeholder="@lang('admin.name')" value="{{$news->translate($locale)->name ?? ''}}">
                                            </div>
                                            @error($locale.'.name')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="main-content-label mg-b-5 text-danger">
                                                @lang('admin.page_seo')
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label($locale.'[meta_title]',__('admin.meta_title'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[meta_title]',$news->translate($locale)->meta_title ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.meta_title')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[meta_description]',__('admin.meta_description'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[meta_description]',$news->translate($locale)->meta_description ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.meta_description')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[meta_keyword]',__('admin.meta_keyword'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[meta_keyword]',$news->translate($locale)->meta_keyword ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.meta_keyword')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[meta_og_title]',__('admin.meta_og_title'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[meta_og_title]',$news->translate($locale)->meta_og_title ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.meta_og_title')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[meta_og_description]',__('admin.meta_og_description'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[meta_og_description]',$news->translate($locale)->meta_og_description ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.meta_og_description')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>




                                        </div>

                                    @endforeach

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            {!! Form::submit($news->created_at ? __('admin.update') : __('admin.create'),['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <select name="parent_id" id="cars" class="form-control">
                        <option value="{{null}}" selected>select parent id</option>
                        @foreach ($menus as $value)
                            <option value={{$value->id}}
                                    @if ($newss != null)
                                        @if ($newss->parent_id == $value->id)
                                        selected
                                        @endif
                                    @endif
                                >{{$value->translations[0]->name}}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2">Widgets</p>
                    <div class="w-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="slider" value="Bike"
                                @if ($news->slider != null)
                                   checked
                                @endif
                                >
                                <label for="vehicle1">სლაიდერი მთავარი</label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div>
                                <input type="checkbox" id="vehiclelikeslider" name="sliderlike" value="Bike"
                                @if ($news->sliderlike != null)
                                   checked
                                @endif
                                >
                                <label for="vehicle1">სლაიდერივით</label>
                            </div>
                        </div>
                        <br>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="partners" value="Bike"
                                    @if ($news->partners != null)
                                    checked
                                    @endif
                                >
                                <label for="vehicle1"> პარტნიორები</label>
                            </div>
                            <input class="form-control w-25" type="number" name="partners_order"
                                @if ($news->partners_order != null)
                                  value='{{$news->partners_order}}'
                                @endif
                            >
                        </div>
                        <br>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="reports" value="Bike"
                                @if ($news->reports != null)
                                  checked
                                @endif
                                >
                                <label for="vehicle1"> გამოხმაურებები</label>
                            </div>
                            <input class="form-control w-25" type="number" name="reports_order"
                                @if ($news->reports_order != null)
                                  value='{{$news->reports_order}}'
                                @endif
                            >
                        </div>
                        <br>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="subscribers" value="Bike"
                                    @if ($news->subscribers != null)
                                      checked
                                    @endif
                                >
                                <label for="vehicle1"> გამომწერის ბლოკი</label>
                            </div>
                            <input class="form-control w-25" name="subscribers_order" type="number"
                                @if ($news->subscribers_order != null)
                                  value='{{$news->subscribers_order}}'
                                @endif
                            >
                        </div>
                        <br>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="blog" value="Bike"
                                    @if ($news->blog != null)
                                      checked
                                    @endif
                                >
                                <label for="vehicle1"> ბლოგის ბლოკი</label>
                            </div>
                            <input class="form-control w-25" name="blog_order" type="number"
                                    @if ($news->blog_order != null)
                                      value='{{$news->blog_order}}'
                                    @endif
                            >
                        </div>
                        <br>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input type="checkbox" id="vehicle1" name="slider1" value="Bike"
                                @if ($news->slider1 != null)
                                   checked
                                @endif
                                >
                                <label for="vehicle1"> სლაიდერი2</label>
                            </div>
                            {{-- <input class="form-control w-25" name="slider_order1" type="number"
                            @php
                            @endphp
                            @if ($news->slider1_order != null)
                             value='{{$news->slider1_order}}'
                            @endif
                            > --}}
                        </div>

                    </div>

                    <p class="mt-4">Layouts</p>
                    <div class="w-50">
                        <select name="layout" id="cars" class="form-control">
                            <option value="volvo" disabled selected>select Layout Type</option>
                            @foreach ($layout as $val)
                            @if ($val->group == 1)
                                <optgroup label={{$val->name}}>
                                        @foreach ($layout as $value)
                                            @if($value->parent_id !=null)
                                                <option value="{{$value->id}}"
                                                        @if ($value->id == $news->layout)
                                                            selected
                                                        @endif
                                                    >{{$value->name}}</option>
                                            @endif
                                        @endforeach
                                </optgroup>
                            @else
                               @if ($val->parent_id == null)
                                 <option value="{{$val->id}}"
                                    @if ($val->id == $news->layout)
                                      selected
                                    @endif
                                    >{{$val->name}}</option>
                               @endif
                            @endif
                            @endforeach

                        </select>
                    </div>
                    {{-- @dd($layout, 'esaa') --}}

                    {{-- <div class="w-50">
                        <select name="layout" id="cars" class="form-control">
                            <option value="volvo" disabled selected>select Layout Type</option>
                            <optgroup label="Category">
                                <option value="vaccancy">vaccancy</option>
                                <option value="blog">blog</option>
                                <option value="main">main</option>
                            </optgroup>
                            <option value="main">post</option>
                            <option value="main">contact</option>
                            <option value="main">application</option>

                        </select>
                    </div> --}}


                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <div>
                            <label for="vehicle1">show in footer</label>
                            <input type="checkbox" id="vehicle1" name="showinfooter" value=true
                                @if ($news->showinfooter != null)
                                  checked
                                @endif
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <div>
                            <label for="vehicle1">active status </label>
                            <input type="checkbox" id="vehicle1" name="status" value=true
                                @if ($news->status != null)
                                  checked
                                @endif
                            >
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <label for="vehicle1">show </label>
                            <input type="checkbox"
                            id="show" name="show" value=true
                                @if ($news->show == 1)
                                  checked
                                @endif
                            >
                        </div>
                    </div>


                   {{-- <input class='form-control' type="date"> --}}
                </div>
            </div>
        </div>

    </div>

    <!-- /row -->
    <!-- row -->
    {{-- <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="input-images"></div>
                    @if ($errors->has('images'))
                        <span class="help-block">
                                            {{ $errors->first('images') }}
                                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}
    {!! Form::close() !!}

@endsection

@section('scripts')

    <!--Internal  Datepicker js -->
    <script src="{{asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>

    <!-- Internal Select2 js-->
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>

    <!--Internal Fancy uploader js-->
    <script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js')}}"></script>

    <!--Internal  Form-elements js-->
    <script src="{{asset('assets/js/advanced-form-elements.js')}}"></script>
    <script src="{{asset('assets/js/select2.js')}}"></script>

    <!--Internal Sumoselect js-->
    <script src="{{asset('assets/plugins/sumoselect/jquery.sumoselect.js')}}"></script>

    <!-- Internal TelephoneInput js-->
    <script src="{{asset('assets/plugins/telephoneinput/telephoneinput.js')}}"></script>
    <script src="{{asset('assets/plugins/telephoneinput/inttelephoneinput.js')}}"></script>

    <script src="{{asset('uploader/image-uploader.js')}}"></script>

    <script>
        let oldImages = $('#old_images').val();
        if (oldImages) {
            oldImages = JSON.parse(oldImages);
        }
        let imagedata = [];
        let getUrl = window.location;
        let baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[0];
        if (oldImages && oldImages.length > 0) {
            oldImages.forEach((el, key) => {
                let directory = '';
                if (el.fileable_type === 'App\\Models\\Project') {
                    directory = 'project';
                }
                imagedata.push({
                    id: el.id,
                    src: `${baseUrl}${el.path}/${el.title}`
                })
            })
            $('.input-images').imageUploader({
                preloaded: imagedata,
                imagesInputName: 'images',
                preloadedInputName: 'old_images'
            });
        } else {
            $('.input-images').imageUploader();
        }
    </script>

@endsection
