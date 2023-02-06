
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

    @php
        $blogImage = $news->files->where('icon', '==', null)->where('header_icon', '==', null)->all()
    @endphp

    <input name="old-images[]" id="old_images" hidden disabled value="{{$news->files->where('icon', '==', null)->where('header_icon', '==', null)}}">

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
                                                value="{{true}}"
                                                @if(isset($news->translate($locale)->visible))
                                                @if ($news->translate($locale)->visible != null)
                                                 checked
                                                @endif
                                                @endif
                                                >
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label($locale.'[title]',__('admin.title'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[title]',$news->translate($locale)->title ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.title')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            {{--<div class="form-group">
                                                {!! Form::label($locale.'[title_2]',__('admin.title_2'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[title_2]',$news->translate($locale)->title_2 ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.title_2')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>--}}
                                            {{-- <div class="form-group">
                                                <label class="form-label" for="short_description">@lang('admin.short_description')</label>
                                                <input type='text'
                                                class="form-control" id="short_description-{{$locale}}"
                                                name="{{$locale}}[short_description]'"
                                                value="{!! $news->translate($locale)->short_description ?? '' !!}"
                                            >
                                            </input>
                                                @error($locale.'.short_description')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div> --}}


                                            <div class="form-group">
                                                <label class="form-label" for="description">@lang('admin.description')</label>
                                                <textarea class="form-control" id="description-{{$locale}}"
                                                          name="{{$locale}}[description]'">
                                                {!! $news->translate($locale)->description ?? '' !!}
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
                                                <label class="form-label" for="icons">@lang('admin.icons')</label>
                                                <textarea class="form-control" id="icons-{{$locale}}"
                                                          name="{{$locale}}[icons]'">
                                                {!! $news->translate($locale)->icons ?? '' !!}
                                            </textarea>
                                                @error($locale.'.icons')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div> --}}

                                            <div class="form-group">
                                                {!! Form::label($locale.'[button_text]',__('admin.button_text'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[button_text]',$news->translate($locale)->button_text ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.button_text')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[icon1_text]',__('admin.icon1_text'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[icon1_text]',$news->translate($locale)->icon1_text ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.icon1_text')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[icon2_text]',__('admin.icon2_text'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[icon2_text]',$news->translate($locale)->icon2_text ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.icon2_text')
                                                <small class="text-danger">
                                                    <div class="error">
                                                        {{$message}}
                                                    </div>
                                                </small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label($locale.'[icon3_text]',__('admin.icon3_text'),['class' => 'form-label']) !!}
                                                {!! Form::text($locale.'[icon3_text]',$news->translate($locale)->icon3_text ?? '',['class' => 'form-control']) !!}

                                                @error($locale.'.icon3_text')
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


                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        {{-- {!! Form::label("reddirect_url",__('admin.btn_reddirect_url'),['class' => 'form-label']) !!}
                        {!! Form::text("reddirect_url",$news->reddirect_url ?? '',['class' => 'form-control']) !!}

                        @error($locale.'.reddirect_url')
                        <small class="text-danger">
                            <div class="error">
                                {{$message}}
                            </div>
                        </small>
                        @enderror --}}

                        {{-- non-translatable --}}
                        <select name="category" id="parent_id" class="form-control">
                            <option value="volvo" disabled selected>select category</option>
                            @foreach ($menus as $value)
                                <option value={{$value->id}}
                                    @if ($newss != null)
                                        @if ($newss->category == $value->id)
                                          selected
                                        @endif
                                    @endif

                                    >{{$value->translations[1]->name}}</option>
                            @endforeach
                        </select>
                        {{-- <input type="date" value="2011-09-29"> --}}

                        @php
                            $startBlogday = date('Y-m-d',strtotime('-1 days'));
                            $endBlogday = date('Y-m-d', strtotime('+50 days'));
                        @endphp
                        <div class="form-group">
                            {!! Form::label("publish_start",__('admin.publish_start'),['class' => 'form-label']) !!}
                            {!! Form::date("publish_start", ($news->publish_start ? $news->publish_start : $startBlogday ),['class' => 'form-control']) !!}
                            @error($locale.'.publish_start')
                            <small class="text-danger">
                                <div class="error">
                                    {{$message}}
                                </div>
                            </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label("publish_end",__('admin.publish_end'),['class' => 'form-label']) !!}
                            {!! Form::date("publish_end", ($news->publish_end ? $news->publish_end : $endBlogday),['class' => 'form-control']) !!}
                            @error($locale.'.publish_end')
                            <small class="text-danger">
                                <div class="error">
                                    {{$message}}
                                </div>
                            </small>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        {!! Form::label("youtube_url",__('admin.btn_link'),['class' => 'form-label']) !!}
                        {!! Form::text("youtube_url",$news->youtube_url ?? '',['class' => 'form-control']) !!}

                        @error($locale.'.youtube_url')
                        <small class="text-danger">
                            <div class="error">
                                {{$message}}
                            </div>
                        </small>
                        @enderror
                    </div> --}}
                    <div class="form-group mt-4 border border-success p-5">
                        <p>Vaccancy link if you want vaccany post to reddirect to your specific link</p>
                            <input type="text" class="form-control" name="vaccancylink"
                                   value="{{$news->vaccancylink ? $news->vaccancylink : ''}}">

                        <p class="mt-4">vaccancy links inside SingleVaccancy</p>
                            <input type="text" class="form-control" name="vaccancylink1"
                                   value="{{$news->vaccancylink1 ? $news->vaccancylink1 : ''}}">
                            <input type="text" class="form-control" name="vaccancylink2"
                                   value="{{$news->vaccancylink2 ? $news->vaccancylink2 : ''}}">
                            <input type="text" class="form-control" name="vaccancylink3"
                                   value="{{$news->vaccancylink3 ? $news->vaccancylink3 : ''}}">
                    </div>

                    <div class="form-group mt-4">
                        <p>choose color for application layout</p>
                            <input type="color" class="form-control w-25" name="btncolor"
                                   value="{{$news->btncolor ? $news->btncolor : ''}}">
                    </div>

                    <div class="form-group mt-4">
                        <label class="ckbox">
                            <input type="checkbox" name="status"
                                   value="true" {{$news->status ? 'checked' : ''}}>
                            <span>{{__('admin.status')}}</span>
                        </label>
                    </div>

                    <div class="form-group mt-2">
                        <label class="ckbox">
                            <input type="checkbox" name="visible"
                                   value="true" {{$news->visible ? 'checked' : ''}}>
                            <span>{{__('admin.visible')}}</span>
                        </label>
                    </div>
{{-- @dd($news) --}}
                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            {!! Form::submit($news->created_at ? __('admin.update') : __('admin.create'),['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- /row -->
    <!-- row -->
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <h6 class="card-title mb-1">@lang('admin.images')</h6>
                    </div>
                    <div class="input-images">
                    </div>
                    @if ($errors->has('images'))
                        <span class="help-block">
                                            {{ $errors->first('images') }}
                                        </span>
                    @endif
                </div>
                <div class="card-body">
            </div>
        </div>
    </div>
    <!-- row closed -->

    <!-- /row -->

    <!-- row -->

    <!-- row closed -->
    {!! Form::close() !!}

    @php
    use App\Models\Iconimage;
    $iconHeader = $news->file()->where([
         ['fileable_id',$news->id],
         ['header_icon',1],
     ])->get()->first();

     $blogimage = Iconimage::where([
        ['blog_id',$news->id],
        ['type','header_icon'],
     ])->orderBy('id', 'DESC')->first();
     $icon1 = Iconimage::where([
        ['blog_id',$news->id],
        ['type','icon1'],
     ])->orderBy('id', 'DESC')->first();
     $icon2 = Iconimage::where([
        ['blog_id',$news->id],
        ['type','icon2'],
     ])->orderBy('id', 'DESC')->first();
     $icon3 = Iconimage::where([
        ['blog_id',$news->id],
        ['type','icon3'],
     ])->orderBy('id', 'DESC')->first();

     $filepath = asset('storage/images/blogIcons');
    //  dd(asset('storage/images/blog/blogIcons'));
    // dd($blogimage);
    // dd($filepath.'/'. $blogimage->name);
    @endphp

{{-- @dd($news->id) --}}
{{ Form::open(['route' => 'uploadicon', 'files' => true, 'class']) }}
<input type="hidden" name="blog_id" value="{{$news->id}}">
    {{ Form::label('file','Upload header icon') }}
    {{-- {{ Form::file('header_icon',['placeholder'=>'asd'])}} --}}
    <input type="file" class="dropify" name="header_icon"
    data-default-file="{{$blogimage ? $filepath.'/'.$blogimage->name : ''}}"
    data-height="200" value="{{$blogimage ? $filepath.'/'.$blogimage->name : ''}}"/>

    <input type="hidden" class="header_iconcheck" name='header_iconcheck' value="{{$blogimage ? $filepath.'/'.$blogimage->name : ''}}">

    {{ Form::label('file','Upload icon') }}
    <input type="file" class="dropify" name="icon1"
    data-default-file="{{$icon1 ? $filepath.'/'.$icon1->name : ''}}"
    data-height="200"  />
    <input type="hidden" class="icon1check" name='icon1check' value="{{$icon1 ? $filepath.'/'.$icon1->name : ''}}">
    <input type="file" class="dropify" name="icon2"
    data-default-file="{{$icon2 ? $filepath.'/'.$icon2->name : ''}}"
    data-height="200"  />
    <input type="hidden" class="icon2check" name='icon2check' value="{{$icon2 ? $filepath.'/'.$icon2->name : ''}}">

    <input type="file" class="dropify" name="icon3"
    data-default-file="{{$icon3 ? $filepath.'/'.$icon3->name : ''}}"
    data-height="200"  />
    <input type="hidden" class="icon3check" name='icon3check' value="{{$icon3 ? $filepath.'/'.$icon3->name : ''}}">

    <br />
    {{ Form::submit('Upload',['class' => 'btn btn-primary'])}}
{{Form::close()}}
    {{-- <form action="{{route('uploadicon')}}" method="POST" enctype="multipart/form-data"> --}}
        {{-- <form name="images-upload-form" method="POST" action="{{route('uploadicon')}}" accept-charset="utf-8" enctype="multipart/form-data">
        @csrf
        <p class='card-title mb-1'>admin.header_icon</p>
        <input type="file" class="dropify" name="icon_header"
        data-default-file="{{($iconHeader) ? asset($iconHeader->getFileUrlAttribute()) : ''}}"
        data-height="200"  />
        <button class="btn btn-success" type="submit">upload</button>
    </form> --}}

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
        $( document ).ready(function() {
        });
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
                    src: `${baseUrl}${el.path}/${el.title}`,
                    type: 'icon',
                })
            })
            $('.input-images').imageUploader({
                preloaded: imagedata,
                imagesInputName: 'images',
                preloadedInputName: 'old_images',
            });

        } else {
            $('.input-images').imageUploader();
        }

        var drEvent = $('.dropify').dropify();


        drEvent.on('dropify.afterClear', function(event, element){
            console.log(`.${event.target.name}check`);
            document.querySelector(`.${event.target.name}check`).value = "";
        });
    </script>

{{-- icons --}}

    {{-- <script>
        let oldImages1 = $('#old_images1').val();
        console.log(JSON.parse(oldImages1) , 'esaa');
        if (oldImages1) {
            oldImages1 = JSON.parse(oldImages1);
        }
        let imagedata1 = [];
        if (oldImages1 && oldImages1.length > 0) {
            oldImages1.forEach((el, key) => {
                let directory = '';
                if (el.fileable_type === 'App\\Models\\Project') {
                    directory = 'project';
                }
                imagedata1.push({
                    id: el.id,
                    src: `${baseUrl1}${el.path}/${el.title}`
                })
            })
            $('.input-images1').imageUploader({
                preloaded: imagedata1,
                imagesInputName: 'images1',
                preloadedInputName: 'old_images1'
            });
        } else {
            $('.input-images1').imageUploader();
        }
    </script> --}}

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    {{-- <script>
        @foreach(config('translatable.locales') as $locale)
        CKEDITOR.replace('description-{{$locale}}', {
            filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form'
        });
        @endforeach
    </script> --}}

@endsection
