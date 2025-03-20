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
                            {{--                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false"--}}
                            {{--                               class="nav-link dropdown-toggle"><span><i--}}
                            {{--                                        class="notika-icon notika-search"></i></span></a>--}}
                            {{--                            <div role="menu" class="dropdown-menu search-dd animated flipInX">--}}
                            {{--                                <div class="search-input">--}}
                            {{--                                    <i class="notika-icon notika-left-arrow"></i>--}}
                            {{--                                    <input type="text"/>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
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
                        <ul class="mobile-menu-nav">
                            <li>
                                <a data-toggle="collapse" data-target="#Charts" href="#">Home</a>
                                <ul class="collapse dropdown-header-top">
                                    <li><a href="index.html">Dashboard One</a></li>
                                    <li><a href="index-2.html">Dashboard Two</a></li>
                                    <li><a href="index-3.html">Dashboard Three</a></li>
                                    <li><a href="index-4.html">Dashboard Four</a></li>
                                    <li><a href="analytics.html">Analytics</a></li>
                                    <li><a href="widgets.html">Widgets</a></li>
                                </ul>
                            </li>
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
        $(document).ready(function () {
            $('#logout').click(function () {
                $.post('{{route('logout')}}', {_token: '{{csrf_token()}}'}, function (data) {
                    window.location.href = '{{route('login')}}';
                });
            })
        });
    </script>
@endpush
