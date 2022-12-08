
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
{{-- @dd($category) --}}
    <!-- breadcrumb -->
    {{-- <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{$staff->created_at ? __('admin.partners-update') : __('admin.partners-create')}}</span>
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div> --}}
    <!-- /breadcrumb -->
    <input name="old-images[]" id="old_images" hidden disabled value="{{$portfolio->files}}">
    <!-- row -->
    {!! Form::model($portfolio,['url' => $url, 'method' => $method,'files' => true]) !!}
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
                                                <label class="form-label">@lang('admin.costumer')</label>
                                                <input type="text" name="{{$locale.'[costumer]'}}" class="form-control" placeholder="@lang('admin.costumer')" value="{{$portfolio->translate($locale)->costumer ?? ''}}" >

                                            </div>
                                            @error($locale.'.costumer')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.name')</label>
                                                <input type="text" name="{{$locale.'[name]'}}" class="form-control" placeholder="@lang('admin.name')" value="{{$portfolio->translate($locale)->name ?? ''}}" >

                                            </div>
                                            @error($locale.'.name')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.duration')</label>
                                                <input type="text" name="{{$locale.'[duration]'}}" class="form-control" placeholder="@lang('admin.duration')" value="{{$portfolio->translate($locale)->duration ?? ''}}" >

                                            </div>
                                            @error($locale.'.duration')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror


                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.category')</label>
                                                <select name="category_id" class="select2 browser-default">
                                                    <option value="" disabled selected>Choose your option</option>
                                                    @foreach ($category as $cat)
                                                    {{-- <option value={{$cat->id}}>{{$cat->name}}</option>
                                                    {{old('cat_id') ==  $cat->id   ?   "selected":""}} value="{{$cat->translate($locale)->brand}}"> {{$cat->name}}</option> --}}

                                                    <option{{old('category_id') ==  $cat->id   ?   "selected":""}}  value="{{$cat->id}}" >{{$cat->name}}</option>

                                                    @endforeach
                                                </select>

                                            </div>
                                            {{-- @error($locale.'.category_id')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror --}}

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.about_project')</label>
                                                <input type="text" name="{{$locale.'[about_project]'}}" class="form-control" placeholder="@lang('admin.about_project')" value="{{$portfolio->translate($locale)->about_project ?? ''}}" >

                                            </div>
                                            @error($locale.'.about_project')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.directions')</label>
                                                <input type="text" name="{{$locale.'[directions]'}}" class="form-control" placeholder="@lang('admin.directions')" value="{{$portfolio->translate($locale)->directions ?? ''}}" >

                                            </div>
                                            @error($locale.'.directions')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.design')</label>
                                                <input type="text" name="{{$locale.'[design]'}}" class="form-control" placeholder="@lang('admin.design')" value="{{$portfolio->translate($locale)->design ?? ''}}" >

                                            </div>
                                            @error($locale.'.design')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.animation')</label>
                                                <input type="text" name="{{$locale.'[animation]'}}" class="form-control" placeholder="@lang('admin.animation')" value="{{$portfolio->translate($locale)->animation ?? ''}}" >

                                            </div>
                                            @error($locale.'.animation')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror
                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.music')</label>
                                                <input type="text" name="{{$locale.'[music]'}}" class="form-control" placeholder="@lang('admin.music')" value="{{$portfolio->translate($locale)->music ?? ''}}" >
                                            </div>
                                            @error($locale.'.music')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <label class="form-label">@lang('admin.video_url')</label>
                                                <input type="text" name={{$locale.'[video_url]'}} class="form-control" placeholder="@lang('admin.video_url')" value="{{$portfolio->video_url ?? ''}}">

                                            </div>
                                            @error($locale.'.video_url')
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
                                <input type="checkbox" data-checkboxes="mygroup" name="status" class="custom-control-input" id="checkbox-2" {{$portfolio->status ? 'checked' : ''}}>
                                <label for="checkbox-2" class="custom-control-label mt-1">{{__('admin.status')}}</label>
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            {!! Form::submit($portfolio->created_at ? __('admin.update') : __('admin.create'),['class' => 'btn btn-primary']) !!}
                        </div>
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

@endsection
