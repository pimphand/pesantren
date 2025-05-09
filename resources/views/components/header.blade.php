<!-- Start Header Top Area -->
<div class="header-top-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="logo-area">
                    <a href="#"><img src="{{asset('assets')}}/img/logo/logo.png" alt=""/></a>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <div class="header-top-menu">
                    <ul class="nav navbar-nav notika-top-nav">
                        <li class="nav-item dropdown">

                            <a href="#" class="nav-link" id="logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Header Top Area -->
<!-- Mobile Menu start -->
<div class="mobile-menu-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="mobile-menu">
                    <nav id="dropdown">
                        <ul class="mobile-menu-nav" id="menu_mobile">

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Mobile Menu end -->
@push('js')
    <script>
        //logout
        $(document).ready(function () {
            $('#logout').click(function () {
                $.post('{{route('logout')}}', {_token: '{{csrf_token()}}'}, function (data) {
                    window.location.href = '{{route('login')}}';
                });
            })
        });

        //if mobile view copy menu to mobile menu
        if ($(window).width() < 768) {
            $('#menu li').each(function () {
                $('#menu_mobile').append($(this).clone());
            });
        }
    </script>
@endpush
