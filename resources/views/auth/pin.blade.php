<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} | Login</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets')}}/img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/bootstrap.min.css">
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/owl.carousel.css">
    <link rel="stylesheet" href="{{asset('assets')}}/css/owl.theme.css">
    <link rel="stylesheet" href="{{asset('assets')}}/css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/wave/waves.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/notika-custom-icon.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/main.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="{{asset('assets')}}/js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        .text-start {
            text-align: left !important;
        }
    </style>
</head>

<body>
<div class="login-content">
    <!-- Login -->
    <div class="nk-block toggled" id="l-login">
        <form class="nk-form text-start">
            <x-input name="email" type="email" placeholder="Masukan PIN"></x-input>

            <button type="button" class="btn btn-login btn-success btn-float" id="login"><i
                    class="notika-icon notika-right-arrow right-arrow-ant"></i></button>
        </form>

    </div>
</div>
<!-- Login Register area End-->
<!-- jquery
    ============================================ -->
<script src="{{asset('assets')}}/js/vendor/jquery-1.12.4.min.js"></script>
<!-- bootstrap JS
    ============================================ -->
<script src="{{asset('assets')}}/js/bootstrap.min.js"></script>
<!-- wow JS
    ============================================ -->
<script src="{{asset('assets')}}/js/wow.min.js"></script>
<!-- price-slider JS
    ============================================ -->
<script src="{{asset('assets')}}/js/jquery-price-slider.js"></script>
<!-- owl.carousel JS
    ============================================ -->
<script src="{{asset('assets')}}/js/owl.carousel.min.js"></script>
<!-- scrollUp JS
    ============================================ -->
<script src="{{asset('assets')}}/js/jquery.scrollUp.min.js"></script>
<!-- meanmenu JS
    ============================================ -->
<script src="{{asset('assets')}}/js/meanmenu/jquery.meanmenu.js"></script>
<!-- counterup JS
    ============================================ -->
<script src="{{asset('assets')}}/js/counterup/jquery.counterup.min.js"></script>
<script src="{{asset('assets')}}/js/counterup/waypoints.min.js"></script>
<script src="{{asset('assets')}}/js/counterup/counterup-active.js"></script>
<!-- mCustomScrollbar JS
    ============================================ -->
<script src="{{asset('assets')}}/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<!-- sparkline JS
    ============================================ -->
<script src="{{asset('assets')}}/js/sparkline/jquery.sparkline.min.js"></script>
<script src="{{asset('assets')}}/js/sparkline/sparkline-active.js"></script>
<!-- flot JS
    ============================================ -->
<script src="{{asset('assets')}}/js/flot/jquery.flot.js"></script>
<script src="{{asset('assets')}}/js/flot/jquery.flot.resize.js"></script>
<script src="{{asset('assets')}}/js/flot/flot-active.js"></script>
<!-- knob JS
    ============================================ -->
<script src="{{asset('assets')}}/js/knob/jquery.knob.js"></script>
<script src="{{asset('assets')}}/js/knob/jquery.appear.js"></script>
<script src="{{asset('assets')}}/js/knob/knob-active.js"></script>
<!--  Chat JS
    ============================================ -->
<script src="{{asset('assets')}}/js/chat/jquery.chat.js"></script>
<!--  wave JS
    ============================================ -->
<script src="{{asset('assets')}}/js/wave/waves.min.js"></script>
<script src="{{asset('assets')}}/js/wave/wave-active.js"></script>
<!-- icheck JS
    ============================================ -->
<script src="{{asset('assets')}}/js/icheck/icheck.min.js"></script>
<script src="{{asset('assets')}}/js/icheck/icheck-active.js"></script>
<script src="{{asset('assets')}}/js/todo/jquery.todo.js"></script>
<!-- Login JS
    ============================================ -->
<script src="{{asset('assets')}}/js/login/login-action.js"></script>
<!-- plugins JS
    ============================================ -->
<script src="{{asset('assets')}}/js/plugins.js"></script>
<!-- main JS
    ============================================ -->
<script src="{{asset('assets')}}/js/main.js"></script>

<script>
    $('#login').click(function () {
        $('.error').text('').hide();
        let url = '{{ route('login') }}';
        let formData = new FormData($('form')[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (response) {
                window.location.href = '{{ route('dashboard') }}';
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]).show();
                    });
                }
            }
        });

    });

</script>
</body>

</html>
