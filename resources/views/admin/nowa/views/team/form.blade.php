
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

    <!-- breadcrumb -->
    {{-- <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{$team->created_at ? __('admin.partners-update') : __('admin.partners-create')}}</span>
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div> --}}
    <!-- /breadcrumb -->
    <input name="old-images[]" id="old_images" hidden disabled value="{{$team->files}}">
    <!-- row -->
    {!! Form::model($team,['url' => $url, 'method' => $method,'files' => true]) !!}
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


                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.title')</label>
                                                <input type="text" name="{{$locale.'[title]'}}" class="form-control" placeholder="@lang('admin.title')" value="{{$team->translate($locale)->title ?? ''}}">

                                            </div>
                                            @error($locale.'.title')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.short_description')</label>
                                                <input type="text" name="{{$locale.'[short_description]'}}" class="form-control" placeholder="@lang('admin.short_description')" value="{{$team->translate($locale)->short_description ?? ''}}">

                                            </div>
                                            @error($locale.'.short_description')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            {{-- <div class="form-group">
                                                <label class="form-label">@lang('admin.description')</label>
                                                <input type="text" name="{{$locale.'[description]'}}" class="form-control" placeholder="@lang('admin.description')" value="{{$team->translate($locale)->description ?? ''}}">

                                            </div>
                                            @error($locale.'.description')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror --}}

                                            <div class="form-group">
                                                <label class="form-label" for="description">@lang('admin.description')</label>
                                                <textarea class="form-control" id="description-{{$locale}}"
                                                          name="{{$locale}}[description]'">
                                                {!! $team->translate($locale)->description ?? '' !!}
                                            </textarea>
                                                @error($locale.'.description')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>


                                            {{-- <div class="form-group">
                                                <label class="form-label">@lang('admin.whattolearn')</label>
                                                <input type="text" name="{{$locale.'[whattolearn]'}}" class="form-control" placeholder="@lang('admin.whattolearn')" value="{{$team->translate($locale)->whattolearn ?? ''}}">

                                            </div>
                                            @error($locale.'.whattolearn')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror --}}
                                            <div class="form-group">
                                                <label class="form-label" for="whattolearn">@lang('admin.whattolearn')</label>
                                                <textarea class="form-control" id="whattolearn-{{$locale}}"
                                                          name="{{$locale}}[whattolearn]'">
                                                {!! $team->translate($locale)->whattolearn ?? '' !!}
                                            </textarea>
                                                @error($locale.'.whattolearn')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>

                                            {{-- <div class="form-group">
                                                <label class="form-label">@lang('admin.course_includes')</label>
                                                <input type="text" name="{{$locale.'[course_includes]'}}" class="form-control" placeholder="@lang('admin.course_includes')" value="{{$team->translate($locale)->course_includes ?? ''}}">

                                            </div>
                                            @error($locale.'.course_includes')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror --}}

                                            <div class="form-group">
                                                <label class="form-label" for="course_includes">@lang('admin.course_includes')</label>
                                                <textarea class="form-control" id="course_includes-{{$locale}}"
                                                          name="{{$locale}}[course_includes]'">
                                                {!! $team->translate($locale)->course_includes ?? '' !!}
                                            </textarea>
                                                @error($locale.'.course_includes')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.starts')</label>
                                                <input type="text" name="{{$locale.'[starts]'}}" class="form-control" placeholder="@lang('admin.starts')" value="{{$team->translate($locale)->starts ?? ''}}">

                                            </div>
                                            @error($locale.'.starts')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror


                                        </div>

                                    @endforeach

                                </div>
                            </div>
                        </div>

                    </div>


                    {{-- <div class="form-group mb-0 justify-content-end">
                        <div class="checkbox">
                            <div class="custom-checkbox custom-control">
                                <input type="checkbox" data-checkboxes="mygroup" name="status" class="custom-control-input" id="checkbox-2" {{$team->status ? 'checked' : ''}}>
                                <label for="checkbox-2" class="custom-control-label mt-1">{{__('admin.status')}}</label>
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            {!! Form::submit($team->created_at ? __('admin.update') : __('admin.create'),['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label("video_url",__('admin.btn_video_url'),['class' => 'form-label']) !!}
                        {!! Form::text("video_url",$team->video_url ?? '',['class' => 'form-control']) !!}

                        @error($locale.'.video_url')
                        <small class="text-danger">
                            <div class="error">
                                {{$message}}
                            </div>
                        </small>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label("price",__('admin.btn_price'),['class' => 'form-label']) !!}
                        {!! Form::text("price",$team->price ?? '',['class' => 'form-control']) !!}

                        @error($locale.'.price')
                        <small class="text-danger">
                            <div class="error">
                                {{$message}}
                            </div>
                        </small>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label("special_price",__('admin.btn_special_price'),['class' => 'form-label']) !!}
                        {!! Form::text("special_price",$team->special_price ?? '',['class' => 'form-control']) !!}

                        @error($locale.'.special_price')
                        <small class="text-danger">
                            <div class="error">
                                {{$message}}
                            </div>
                        </small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- /row -->
    <!-- row -->
    <div class="row">
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
    </div>
    <!-- row closed -->

    <!-- /row -->

    <!-- row -->

    <!-- row closed -->
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
 <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
      <script>
        @foreach(config('translatable.locales') as $locale)
        CKEDITOR.replace('description-{{$locale}}', {
            filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form'
        });
        @endforeach
        @foreach(config('translatable.locales') as $locale)
        CKEDITOR.replace('whattolearn-{{$locale}}', {
            filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form'
        });
        @endforeach
        @foreach(config('translatable.locales') as $locale)
        CKEDITOR.replace('course_includes-{{$locale}}', {
            filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form'
        });
        @endforeach
    </script>

@endsection
