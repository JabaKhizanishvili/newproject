<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    {{-- @vite('resources/css/app.css') --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{$meta_title}}</title>
    <meta name="description"
    content="{{ $meta_description }}">
    <meta name="keywords" content="{{ $meta_keyword }}">
    <meta property="og:title" content="{{ $og_title }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta property="og:description" content="{{ $og_description }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> --}}

    @if($image)
        <meta property="og:image" content={{"/".$image->path."/".$image->title}}>
    @endif
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="page">
{{--    <link href="{{ mix('/css/app.css') }}" rel="stylesheet"/>--}}
{{--    @if(app()->getLocale()=="ge")--}}
{{--        <link href="{{ mix('/css/AppGeo.css') }}" rel="stylesheet"/>--}}
{{--    @elseif(app()->getLocale()=="en")--}}
{{--        <link href="{{ mix('/css/AppEng.css') }}" rel="stylesheet"/>--}}
{{--    @endif--}}
    {{--    @dd($page["props"]["page"]["meta_title"])--}}
    @routes
    <!-- JQUERY JS -->
    <script src="{{asset('admin/assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{ mix('/js/app.js') }}" defer></script>
    <script>
        function __(key, sharedData, replace = {}) {
            let data = key.split('.');
            //console.log(sharedData);
            let translation = sharedData[data[1]] || key;

            Object.keys(replace).forEach(function (key) {
                translation = translation.replace(':' + key, replace[key])
            });

            //console.log(translation);
            return translation;

        }
    </script>
</head>
<body>
<!-- Messenger Chat Plugin Code -->
{{--<div id="fb-root"></div>--}}

<!-- Your Chat Plugin code -->
{{--<div id="fb-customer-chat" class="fb-customerchat"></div>--}}

<script>
    /*var chatbox = document.getElementById('fb-customer-chat');
    chatbox.setAttribute("page_id", "100160159275529");
    chatbox.setAttribute("attribution", "biz_inbox");

    window.fbAsyncInit = function () {
        FB.init({
            xfbml: true,
            version: 'v12.0'
        });
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));*/
</script>
@inertia


<!-- Messenger Chat Plugin Code -->
{{-- <div id="fb-root"></div> --}}

<!-- Your Chat Plugin code -->
{{-- <div id="fb-customer-chat" class="fb-customerchat"> --}}
</div>


<!-- Your SDK code -->
{{--  --}}
</body>

</html>
