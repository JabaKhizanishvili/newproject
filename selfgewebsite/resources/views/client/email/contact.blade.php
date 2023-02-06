<!DOCTYPE html>
<html>
<head>
    <title>glass service</title>
    <!--<link type="image/x-icon" rel="icon" href="images/icon.ico">-->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <script>
        document.createElement("nav");
        document.createElement("section")
    </script>
    <style>
        *{ margin:0; padding:0}
        html, body{width:100%; min-height:100%;}
        a{ text-decoration:none;}
        li{ list-style-type:none;}
        img{border:0;}
        /*main css*/
        #wrapper{
            width:92%;
            max-width:600px;
            padding:29px 4% 10px 4%;
            background:#2e393e no-repeat right bottom;
            background-size:auto 100%;
            margin:50px auto;
        }
        #maincContent{
            -webkit-box-shadow: #771732;
            -moz-box-shadow: #771732;
            box-shadow: #771732;
            background-color:#fff;
            margin-bottom:13px;
            position:relative;
            clear:both;
            overflow:hidden;
            border-bottom-right-radius:40px;
        }
        #head{
            width:92%;
            max-width:550px;
            margin:25px auto;
        }
        #head h1{
            width:94px;
            height:47px;
            margin:25px 5.45%;
        }
        #content{
            width:92%;
            max-width:550px;
            margin:25px auto;
        }
        #content h2{
            color: #2e393e;
            font-size:15px;
            margin-bottom:20px;
            font-family: 'Conv_BPG DejaVu Sans ExtraLight 2012';
            margin:0px 5.45% 25px 5.45%;
        }
        #content h5{
            color: #818181;
            font-size:12px;
            margin-bottom:20px;
            font-family: 'Conv_BPG DejaVu Sans ExtraLight 2012';
            margin:0px 5.45% 25px 5.45%;
        }
    </style>
</head>
<body>
<section id="wrapper">
    <section id="maincContent">
        <section id="content">
            <h2> სრული სახელი: {{$data['name']}}</h2>
            <h2> ელ-ფოსტა: {{$data['mail']}}</h2>
            <h2> მობილური: {{$data['phone']}}</h2>
            <h2> შეტყობინება</h2>
            <h5>{{$data['message']}}</h5>
        </section>
    </section>
</section>
</body>
</html>
