<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} | {{$title ?? "Dashboard"}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets')}}/img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.theme.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.transitions.css')}}">
    <!-- meanmenu CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/meanmenu/meanmenu.min.css')}}">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/animate.css')}}">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/normalize.css')}}">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/scrollbar/jquery.mCustomScrollbar.min.css')}}">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/notika-custom-icon.css')}}">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/wave/waves.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/wave/button.css')}}">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/main.css')}}">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/style.css')}}">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">
    <!-- modernizr JS
		============================================ -->
    <script src="{{asset('assets/js/vendor/modernizr-2.8.3.min.js')}}"></script>

    <link rel="stylesheet" href="{{asset('assets/css/dialog/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/dialog/dialog.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Struktur halaman agar footer tetap di bawah */
        html,
        body {
            height: 100%;
            margin: 0;
        }

        .normal-table-area {
            min-height: 100%;
            padding-bottom: 60px;
            /* Height of footer */
        }

        .footer-copyright-area {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #2d3436;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .footer-copyright-area p {
            margin: 0;
        }

        .notika-menu-list:before {
            font-family: 'FontAwesome', serif;
            content: "\f03a";
            /* Ikon daftar (list) dari FontAwesome */
        }

        .notika-save:before {
            font-family: 'FontAwesome', serif;
            content: "\f0c7";
            /* Ikon simpan (save) dari FontAwesome */
        }

        .notika-dot-circle-o:before {
            font-family: 'FontAwesome', serif;
            content: "\f192";
            /* Ikon dot-circle-o */
        }

        .notika-address:before {
            font-family: 'FontAwesome', serif;
            content: "\f3c5";
            /* Ikon dot-circle-o */
        }

        .notika-username:before {
            font-family: 'FontAwesome', serif;
            content: "\f2bb";
            /* Ikon dot-circle-o */
        }
        .notika-credit-card:before {
            font-family: 'FontAwesome', serif;
            content: "\f09d";
            /* Ikon credit-card dari FontAwesome */
        }

        .notika-image:before {
            font-family: 'FontAwesome', serif;
            content: "\f03e";
            /* Ikon gambar (image) dari FontAwesome */
        }

        .notika-key:before {
            font-family: 'FontAwesome', serif;
            content: "\f084";
            /* Ikon dot-circle-o */
        }

        .notika-tax:before {
            font-family: 'FontAwesome', serif;
            content: "\f0d6";
            /* Ikon dot-circle-o */
        }


        .notika-category:before {
            font-family: 'FontAwesome', serif;
            content: "\f5fd";
            /* Ikon dot-circle-o */
        }

        .notika-close:before {
            font-family: 'FontAwesome', serif;
            content: "\f00d";
        }

        .notika-plus:before {
            font-family: 'FontAwesome', serif;
            content: "\f067";
        }

        .notika-minus:before {
            font-family: 'FontAwesome', serif;
            content: "\f068";
        }

        .justify-between {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
    @stack('css')
</head>

<body>

    <div class="normal-table-area">
        {{-- Header --}}
        <x-header></x-header>
        {{-- End Header --}}
        
        <!-- Main Menu area start -->
        <x-menu></x-menu>
        <!-- Main Menu area End -->
        
        <!-- Breadcrumb area Start -->
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
        <!-- Breadcrumb area End -->
        
        <!-- Content area Start -->
        <div class="content-area">
            @yield('content')
        </div>
        <!-- Content area End -->
        <!-- Footer area Start -->
        <div class="footer-copyright-area fixed bottom-0 left-0 w-full bg-gray-800 text-white py-3">
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
        <!-- Footer area End -->
    </div>

    <!-- End Footer area-->
    <!-- jquery============================================ -->
    <script src="{{asset('assets/js/vendor/jquery-1.12.4.min.js')}}"></script>
    <!-- bootstrap JS
    ============================================ -->
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <!-- wow JS
    ============================================ -->
    <script src="{{asset('assets/js/wow.min.js')}}"></script>
    <!-- price-slider JS
    ============================================ -->
    <script src="{{asset('assets/js/jquery-price-slider.js')}}"></script>
    <!-- owl.carousel JS
    ============================================ -->
    <script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>
    <!-- scrollUp JS
    ============================================ -->
    <script src="{{asset('assets/js/jquery.scrollUp.min.js')}}"></script>
    <!-- meanmenu JS
    ============================================ -->
    <script src="{{asset('assets/js/meanmenu/jquery.meanmenu.js')}}"></script>
    <!-- counterup JS
    ============================================ -->
    <script src="{{asset('assets/js/counterup/jquery.counterup.min.js')}}"></script>
    <script src="{{asset('assets/js/counterup/waypoints.min.js')}}"></script>
    <script src="{{asset('assets/js/counterup/counterup-active.js')}}"></script>
    <!-- mCustomScrollbar JS
    ============================================ -->
    <script src="{{asset('assets/js/scrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <!-- sparkline JS
    ============================================ -->
    <script src="{{asset('assets/js/sparkline/jquery.sparkline.min.js')}}"></script>
    <script src="{{asset('assets/js/sparkline/sparkline-active.js')}}"></script>
    <!-- flot JS
    ============================================ -->
    <script src="{{asset('assets/js/flot/jquery.flot.js')}}"></script>
    <script src="{{asset('assets/js/flot/jquery.flot.resize.js')}}"></script>
    <script src="{{asset('assets/js/flot/flot-active.js')}}"></script>
    <!-- knob JS
    ============================================ -->
    <script src="{{asset('assets/js/knob/jquery.knob.js')}}"></script>
    <script src="{{asset('assets/js/knob/jquery.appear.js')}}"></script>
    <script src="{{asset('assets/js/knob/knob-active.js')}}"></script>
    <!--  Chat JS
    ============================================ -->
    <script src="{{asset('assets/js/todo/jquery.todo.js')}}"></script>
    <script src="{{asset('assets/js/wave/waves.min.js')}}"></script>
    <script src="{{asset('assets/js/wave/wave-active.js')}}"></script>
    <script src="{{asset('assets/js/plugins.js')}}"></script>

    <script src="{{asset('assets/js/main.js')}}"></script>
    <script src="{{asset('assets/js/dialog/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/dialog/dialog-active.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script>
        function pagination(response) {
            let pagination = $('#pagination');
            pagination.empty();
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
                        // Extract page number from URL
                        const url = new URL(link.url);
                        const page = url.searchParams.get('page') || 1;
                        const search = url.searchParams.get('filter[name]') || '';
                        const category = url.searchParams.get('filter[category.id]') || '';
                        getData(search, category, page);
                    }
                });

                li.append(a);
                ul.append(li);
            });

            pagination.append(ul);
        }

        function form(url, method, data, callback) {
            $.ajax({
                url: url,
                type: method,
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (response) {
                    callback(response, null);
                },
                error: function (error) {
                    callback(null, error);
                }
            });
        }

        function deleteData(url) {
            swal({
                title: "Apakah anda yakin?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then(function () {
                form(url, 'delete', {}, function (response, error) {
                    if (response) {
                        getData();
                        swal("Hapus!", "Data berhasil di hapus.", "success");
                    } else {
                        swal("Gagal!", error.responseJSON.message, "error");
                    }
                });
            });
        }

        $('.selectpicker').val('');

        function currencyFormat(number) {
            return number % 1 === 0
                ? number.toLocaleString()
                : number.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        function toast(message, icon = 'success', heading = 'Berhasil') {
            $.toast({
                text: message, // Text that is to be shown in the toast
                heading: heading, // Optional heading to be shown on the toast
                icon: icon, // Type of toast icon
                showHideTransition: 'fade', // fade, slide or plain
                allowToastClose: true, // Boolean value true or false
                hideAfter: 3000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                stack: 2, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                position: 'top-right', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                textAlign: 'left',  // Text alignment i.e. left, right or center
                loader: true,  // Whether to show loader or not. True by default
                loaderBg: '#9EC600',  // Background color of the toast loader
                beforeShow: function () {
                }, // will be triggered before the toast is shown
                afterShown: function () {
                }, // will be triggered after the toat has been shown
                beforeHide: function () {
                }, // will be triggered before the toast gets hidden
                afterHidden: function () {
                }  // will be triggered after the toast has been hidden
            });
        }
    </script>
    @stack('js')
</body>

</html>