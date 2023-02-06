<?php
$ids = $vacancy->skills->pluck("id")->toArray();
?>
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
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{$vacancy->created_at ? __('admin.vacancy-update') : __('admin.vacancy-create')}}</span>
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div>
    <!-- /breadcrumb -->
    <input name="old-images[]" id="old_images" hidden disabled value="{{$vacancy->files}}">
    <!-- row -->
    {!! Form::model($vacancy,['url' => $url, 'method' => $method,'files' => true]) !!}
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <h6 class="card-title mb-1">@lang('admin.skills')</h6>
                    </div>
                    <div class="mb-4">
                        @foreach($skills as $skill)
                            <div class="form-group">
                                <label class="ckbox">
                                    <input type="checkbox" name="skills[]" data-checkboxes="mygroup" class="custom-control-input"  id="{{$skill->id}}" value="{{$skill->id}}" {{in_array($skill->id,$ids) ? 'checked' : ''}}>
                                    <span style="margin-left: 5px">{{$skill->title}}</span>

                                </label>
                            </div>
                        @endforeach
                    </div>

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
                                                <input type="text" name="{{$locale.'[title]'}}" class="form-control" placeholder="@lang('admin.title')" value="{{$vacancy->translate($locale)->title ?? ''}}">
                                            </div>
                                            @error($locale.'.title')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <input type="text" name="{{$locale.'[sub_title]'}}" class="form-control" placeholder="@lang('admin.sub_title')" value="{{$vacancy->translate($locale)->sub_title ?? ''}}">
                                            </div>
                                            @error($locale.'.sub_title')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <textarea type="text" name="{{$locale.'[description]'}}" class="form-control" placeholder="@lang('admin.description')" >{{$vacancy->translate($locale)->description ?? ''}}</textarea>
                                            </div>
                                            @error($locale.'.description')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <input type="text" name="{{$locale.'[remuneration]'}}" class="form-control" placeholder="@lang('admin.remuneration')" value="{{$vacancy->translate($locale)->remuneration ?? ''}}">
                                            </div>
                                            @error($locale.'.remuneration')
                                            <small class="text-danger">
                                                <div class="error">
                                                    {{$message}}
                                                </div>
                                            </small>
                                            @enderror

                                            <div class="form-group">
                                                <input type="text" name="{{$locale.'[time]'}}" class="form-control" placeholder="@lang('admin.time')" value="{{$vacancy->translate($locale)->time ?? ''}}">
                                            </div>
                                            @error($locale.'.time')
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

                    <?php
                    $rates = [
                        1 => __('client.full_rate'),
                        2 => __('client.half_rate')
                    ]
                    ?>

                    {{--<div class="form-group">
                        <label class="form-label">@lang('admin.rate')</label>
                        <select name="rate" class="form-control">
                            @foreach($rates as $key => $rate)
                            <option value="{{$key}}" {{$vacancy->rate == $key ? 'selected':''}}>{{$rate}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('rate')
                    <small class="text-danger">
                        <div class="error">
                            {{$message}}
                        </div>
                    </small>
                    @enderror--}}

                    <div class="form-group">
                        <label class="form-label">@lang('admin.slug')</label>
                        <input type="text" name="slug" class="form-control" placeholder="@lang('admin.slug')" value="{{$vacancy->slug ?? ''}}">
                    </div>
                    @error('slug')
                    <small class="text-danger">
                        <div class="error">
                            {{$message}}
                        </div>
                    </small>
                    @enderror
                    <div class="form-group mb-0 justify-content-end">
                        <div class="checkbox">
                            <div class="custom-checkbox custom-control">
                                <input type="checkbox" data-checkboxes="mygroup" name="status" class="custom-control-input" id="checkbox-2" {{$vacancy->status ? 'checked' : ''}}>
                                <label for="checkbox-2" class="custom-control-label mt-1">{{__('admin.status')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            {!! Form::submit($vacancy->created_at ? __('admin.update') : __('admin.create'),['class' => 'btn btn-primary']) !!}
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

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <h6 class="card-title mb-1">@lang('admin.vacancy_files')</h6>
                    </div>
                    <input type="file" name="files[]" multiple>

                    @foreach($vacancy->docs as $doc)
                        <div>
                            {{$doc->title}} <span><a href="{{locale_route('vacancy.delete-doc',$doc->id)}}">delete</a> </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
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
