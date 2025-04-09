@php
    $menus = \App\Models\Menu::whereNull('menu_id')
            ->where('status', true)
            ->orderBy('order_menu')
            ->with('permission')
            ->get();
@endphp
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
                        <li>
                            <a href="{{ route('dashboard') }}">
                                <i class="notika-icon notika-house"></i> Dashboard
                            </a>
                        </li>
                
                        @foreach ($menus as $menu)
                            @php
                                $permissionName = $menu->permission->name ?? null;
                            @endphp
                
                            @if ($permissionName && auth()->user()->isAbleTo($permissionName))
                                <li>
                                    <a 
                                        data-toggle="{{ $menu->children->isNotEmpty() ? 'tab' : '' }}"
                                        href="{{ $menu->children->isNotEmpty() ? '#menu-' . $menu->id : $menu->url }}">
                                        <i class="{{ $menu->icon ?? 'fa-solid fa-layer-group' }}"></i> {{ $menu->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
                {{-- Tab Content (Child Menu) --}}
                <div class="tab-content custom-menu-content">
                    @foreach ($menus as $menu)
                        @php
                            $permissionName = $menu->permission->name ?? null;
                        @endphp
            
                        @if ($permissionName && auth()->user()->isAbleTo($permissionName) && $menu->children->isNotEmpty())
                            <div id="menu-{{ $menu->id }}" class="tab-pane notika-tab-menu-bg animated flipInX">
                                <ul class="notika-main-menu-dropdown">
                                    @foreach ($menu->children as $child)
                                        @php
                                            $childPermission = $child->permission->name ?? null;
                                            // dd($childPermission);
                                        @endphp
            
                                        @if ($childPermission && auth()->user()->isAbleTo($childPermission))
                                            <li>
                                                <a href="{{ $child->url }}">{{ $child->name }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
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
