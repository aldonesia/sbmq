<!doctype html>
<html lang="en" class="no-focus">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>SBMQ - Track QR Code</title>

        <meta name="description" content="Codebase - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">

        <!-- Open Graph Meta -->
        <meta property="og:title" content="Codebase - Bootstrap 4 Admin Template &amp; UI Framework">
        <meta property="og:site_name" content="Codebase">
        <meta property="og:description" content="Codebase - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta property="og:type" content="website">
        <meta property="og:url" content="">
        <meta property="og:image" content="">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
        <!-- END Icons -->

        <!-- Stylesheets -->

        <!-- Fonts and Codebase framework -->
        <link rel="stylesheet" href="{{ asset('css/googlefont.css') }}">
        <link rel="stylesheet" id="css-main" href="{{ asset('css/codebase.min.css') }}">

        <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="{{ asset('css/themes/flat.min.css') }}"> -->
        <!-- END Stylesheets -->
        <style type="text/css">
            body{
                width:100%;
                text-align:center;
                font-size:14pt
            }
            input {
                font-size:14pt
            }
            input, label {vertical-align:middle}
            .qrcode-text {padding-right:1.7em; margin-right:0}
            .qrcode-text-btn {display:inline-block; background:url(//dab1nmslvvntp.cloudfront.net/wp-content/uploads/2017/07/1499401426qr_icon.svg) 50% 50% no-repeat; height:1em; width:1.7em; margin-left:-1.7em; cursor:pointer}
            .qrcode-text-btn > input[type=file] {position:absolute; overflow:hidden; width:1px; height:1px; opacity:0}
            img{
                border:0;
            }
            #main{
                margin: 15px auto;
                background:white;
                overflow: auto;
                width: 100%;
            }
            #header{
                background:white;
                margin-bottom:15px;
            }
            #mainbody{
                background: white;
                width:100%;
                display:none;
            }
            #footer{
                background:white;
            }
            #v{
                width:320px;
                height:240px;
            }
            #qr-canvas{
                display:none;
            }
            #qrfile{
                width:320px;
                height:240px;
            }
            #mp1{
                text-align:center;
                font-size:35px;
            }
            #imghelp{
                position:relative;
                left:0px;
                top:-160px;
                z-index:100;
                font:18px arial,sans-serif;
                background:#f0f0f0;
                margin-left:35px;
                margin-right:35px;
                padding-top:10px;
                padding-bottom:10px;
                border-radius:20px;
            }
            .selector{
                margin:0;
                padding:0;
                cursor:pointer;
                margin-bottom:-5px;
            }
            #outdiv
            {
                width:320px;
                height:240px;
                border: solid;
                border-width: 3px 3px 3px 3px;
            }
            #result{
                border: solid;
                border-width: 1px 1px 1px 1px;
                padding:20px;
                width:70%;
            }

            ul{
                margin-bottom:0;
                margin-right:40px;
            }
            li{
                display:inline;
                padding-right: 0.5em;
                padding-left: 0.5em;
                font-weight: bold;
                border-right: 1px solid #333333;
            }
            li a{
                text-decoration: none;
                color: black;
            }

            #footer a{
                color: black;
            }
            .tsel{
                padding:0;
            }

        </style>
    </head>
    <body>

        <!-- Page Container -->
        <div id="page-container" class="main-content-boxed">
			@yield('content')
        </div>
        <!-- END Page Container -->

        <!-- Codebase JS Core -->
        <script src="{{ asset('js/codebase.core.min.js') }}"></script>

        <!-- Codebase JS -->
        <script src="{{ asset('js/codebase.app.min.js') }}"></script>

        <!-- Page JS Plugins -->
        <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

        <!-- Page JS Code -->
        <script src="{{ asset('js/pages/op_auth_reminder.min.js') }}"></script>

    </body>
@yield('modal')
@yield('ajax')
</html>
