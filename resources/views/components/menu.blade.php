<div class="main-menu-area mg-tb-40">
    <div class="container">
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro text-center text-align" id="menu">
                    @if(auth()->user()->hasRole('merchant'))
                        <li><a href="{{route('dashboard')}}"><i class="notika-icon notika-house"></i> Dashboard</a></li>
                        <li><a href="{{route('merchant.categories.index')}}"><i class="fa-solid fa-layer-group"></i> Kategori</a>
                        </li>
                        <li><a href="{{route('merchant.products.index')}}"><i class="fa-brands fa-product-hunt"></i> Produk</a>
                        </li>
                        <li><a href="{{route('merchant.transactions.index')}}"><i
                                    class="fa-solid fa-money-bill-transfer"></i> Transaksi</a></li>
                        <li><a href="{{route('merchant.profile.index')}}"><i class="notika-icon notika-support"></i> Profile</a></li>
                    @else
                        <li><a href="{{route('dashboard')}}"><i class="notika-icon notika-house"></i> Dashboard</a></li>
                        <li><a data-toggle="tab" href="#pengguna"><i class="notika-icon notika-support"></i>
                                Pengguna</a></li>
                        <li><a data-toggle="tab" href="#merchant"><i class="notika-icon notika-edit"></i> Merchant</a>
                        </li>
                        <li><a data-toggle="tab" href="#transaksi"><i class="notika-icon notika-bar-chart"></i>Transaksi</a>
                        </li>
                        <li><a data-toggle="tab" href="#settings"><i class="notika-icon notika-windows"></i> Pengaturan</a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content custom-menu-content">
                    <div id="pengguna" class="tab-pane notika-tab-menu-bg animated flipInX">
                        <ul class="notika-main-menu-dropdown">
                            <li><a href="inbox.html">Admin</a></li>
                            <li><a href="view-email.html">Pengawas</a></li>
                            <li><a href="compose-email.html">Santri</a></li>
                            <li><a href="compose-email.html">Orang Tua</a></li>
                        </ul>
                    </div>
                    <div id="merchant" class="tab-pane notika-tab-menu-bg animated flipInX">
                        <ul class="notika-main-menu-dropdown">
                            <li><a href="animations.html">List Merchant</a>
                            </li>
                            <li><a href="google-map.html">Category Merchant</a>
                            </li>
                            <li><a href="data-map.html">Product Merchant</a>
                            </li>
                        </ul>
                    </div>
                    <div id="transaksi" class="tab-pane notika-tab-menu-bg animated flipInX">
                        <ul class="notika-main-menu-dropdown">
                            <li><a href="flot-charts.html">Transaksi</a></li>
                            <li><a href="bar-charts.html">Top Up</a></li>
                        </ul>
                    </div>
                    <div id="settings" class="tab-pane notika-tab-menu-bg animated flipInX">
                        <ul class="notika-main-menu-dropdown">
                            <li><a href="normal-table.html">Data Pondok</a></li>
                            <li><a href="data-table.html">Tugas dan Hak Akses</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            let currentUrl = window.location.href;

            $("#menu li a").each(function () {
                if (this.href === currentUrl) {
                    $(this).parent().addClass("active");
                }
            });
        });
    </script>
@endpush
