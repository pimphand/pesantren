<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} | {{$title ?? "Dashboard"}}</title>
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
    <!-- meanmenu CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/meanmenu/meanmenu.min.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/notika-custom-icon.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/wave/waves.min.css">
    <link rel="stylesheet" href="{{asset('assets')}}/css/wave/button.css">
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

    <link rel="stylesheet" href="{{asset('assets')}}/css/dialog/sweetalert2.min.css">
    <link rel="stylesheet" href="{{asset('assets')}}/css/dialog/dialog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('css')
</head>

<body>

{{--Header--}}
<x-header></x-header>
{{--End Header--}}
<!-- Main Menu area start-->
<x-menu></x-menu>
<!-- Main Menu area End-->
<!-- Breadcomb area Start-->
<div class="breadcomb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="breadcomb-list">

                    @yield('breadcrumb')

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcomb area End-->
<!-- Button area Start-->
@yield('content')
<!-- Button area End-->
<!-- Start Footer area-->
<div class="footer-copyright-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="footer-copy-right">
                    <p>Copyright © {{date('Y')}}
                        . All rights reserved. </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Footer area-->
<!-- jquery============================================ -->
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
<script src="{{asset('assets')}}/js/todo/jquery.todo.js"></script>
<script src="{{asset('assets')}}/js/wave/waves.min.js"></script>
<script src="{{asset('assets')}}/js/wave/wave-active.js"></script>
<script src="{{asset('assets')}}/js/plugins.js"></script>

<script src="{{asset('assets')}}/js/main.js"></script>
<script src="{{asset('assets')}}/js/dialog/sweetalert2.min.js"></script>
<script src="{{asset('assets')}}/js/dialog/dialog-active.js"></script>

<script>
    function pagination(response) {
        $("#pagination").empty();
        let ul = $("<ul class='pagination'></ul>");

        response.meta.links.forEach(link => {
            let li = $("<li class='page-item'></li>");
            if (link.active) {
                li.addClass("active");
            }
            if (!link.url) {
                li.addClass("disabled");
            }

            let a = $("<a class='page-link'></a>");
            a.attr("href", link.url ? link.url : "#");
            a.html(link.label);
            a.on("click", function (e) {
                e.preventDefault();
                if (link.url) {
                    fetchData(link.url);
                }
            });

            li.append(a);
            ul.append(li);
        });

        $("#pagination").append(ul);
    }
    function form(url,method,data,callback) {
        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            success: function(response){
                callback(response, null);
            },
            error: function (error) {
                callback(null, error);
            }
        });
    }
    function deleteProduct(url) {
        swal({
            title: "Apakah anda yakin?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus!",
        }).then(function () {
            form(url,'delete',{},function (response) {
                if (response) {
                    getData();
                    swal("Deleted!", "Data berhasil di hapus.", "success");
                } else {
                    swal("Failed!", "Data gagal di hapus.", "error");
                }
            });
        });
    }
    $('.selectpicker').val('');

</script>
@stack('js')
</body>

</html>
