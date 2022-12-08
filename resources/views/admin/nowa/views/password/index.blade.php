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
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{ __('admin.password-update') }}</span>
        </div>
        <div class="justify-content-center mt-2">
            @include('admin.nowa.views.layouts.components.breadcrump')
        </div>
    </div> --}}
    <!-- /breadcrumb -->

    <!-- row -->
    <form method="post" action="">

        <div class="row row-sm">
            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                <div class="card  box-shadow-0 ">
                    <div class="card-header">
                        <h4 class="card-title mb-1">{{ __('admin.password-update') }}</h4>
                        {{--<p class="mb-2">It is Very Easy to Customize and it uses in your website apllication.</p>--}}
                    </div>
                    <div class="card-body pt-0">
                        <form method="post" action="{{locale_route('password.update')}}">
                            @csrf

                            <div class="">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('admin.cur_pass')</label>
                                    <input type="password" name="c_pass" class="form-control" id="exampleInputPassword1" placeholder="@lang('admin.cur_pass')">
                                    @error('c_pass')
                                    <small class="errorTxt4">
                                        <div class="text-danger">
                                            {{$message}}
                                        </div>
                                    </small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('admin.new_pass')</label>
                                    <input type="password" name="n_pass" class="form-control" id="exampleInputPassword1" placeholder="@lang('admin.new_pass')">
                                    @error('n_pass')
                                    <small class="errorTxt4">
                                        <div class="text-danger">
                                            {{$message}}
                                        </div>
                                    </small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('admin.repeat_pass')</label>
                                    <input type="password" name="r_pass" class="form-control" id="exampleInputPassword1" placeholder="@lang('admin.repeat_pass')">
                                    @error('r_pass')
                                    <small class="errorTxt4">
                                        <div class="text-danger">
                                            {{$message}}
                                        </div>
                                    </small>
                                    @enderror
                                </div>
                                {{--<div class="checkbox">
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input" id="checkbox-1">
                                        <label for="checkbox-1" class="custom-control-label mt-1">Check me Out</label>
                                    </div>
                                </div>--}}
                            </div>
                            <button type="submit" class="btn btn-primary mt-3 mb-0">@lang('admin.update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- row closed -->
    </form>

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



@endsection

























