@extends('layouts.app')
@section('breadcrumb')
    <style>
        .notika-menu-list:before {
            font-family: 'FontAwesome';
            content: "\f03a"; /* Ikon daftar (list) dari FontAwesome */
        }

        .notika-save:before {
            font-family: 'FontAwesome';
            content: "\f0c7"; /* Ikon simpan (save) dari FontAwesome */
        }

        .notika-dot-circle-o:before {
            font-family: 'FontAwesome';
            content: "\f192"; /* Ikon dot-circle-o */
        }
    </style>
    <x-breadcrumb :title="$title"
                  :icon="'notika-menu-list'"
                  :description="'list '.$title.' dan tambah '.$title.''"
                  :buttonTitle="'List '.$title.''">
    </x-breadcrumb>
@endsection

@section('content')
    <div class="inbox-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="inbox-left-sd">
                        <div class="compose-ml">
                            <a class="btn waves-effect save" style="display: none" href="javascript:void(0)">Simpan</a>
                        </div>
                        <div class="inbox-status">
                            <span class="inbox-status-title">List Item</span>
                            <ul class="inbox-st-nav inbox-ft" id="_item_list">

                            </ul>
                        </div>
                        <hr>
                        <div class="inbox-status">
                            <ul class="inbox-st-nav">
                                <li>
                                    <span>Total Bayar: </span><span class="pull-right _total"></span>
                                </li>
                            </ul>
                        </div>
                        <hr>
                        <div class="compose-ml">
                            <a class="btn waves-effect save" style="display: none" href="javascript:void(0)">Simpan</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                    <div class="view-mail-list sm-res-mg-t-30">
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-xs-12">
                                <x-input :name="'search'" :placeholder="'Cari Produk'"></x-input>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12">
                                <x-select :name="'category'" :title="'Kategori'" :options="$categories"></x-select>
                            </div>
                        </div>

                        <div class="row mt-3" id="_products"></div>
                        <div class="vw-ml-action-ls text-right mg-t-20">
                            <div class="btn-group ib-btn-gp active-hook nk-email-inbox">
                                <button class="btn btn-default btn-sm waves-effect"><i
                                        class="notika-icon notika-next"></i> Reply
                                </button>
                                <button class="btn btn-default btn-sm waves-effect"><i
                                        class="notika-icon notika-right-arrow"></i> Forward
                                </button>
                                <button class="btn btn-default btn-sm waves-effect">
                                    <i class="notika-icon notika-save"
                                       aria-hidden="true">
                                    </i> Print
                                </button>
                                <button class="btn btn-default btn-sm waves-effect"><i
                                        class="notika-icon notika-trash"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog" data-backdrop="static">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Informasi Pengguna</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body mt-5">
                    <div id="reader" style="width: 100%;"></div>
                    <form action="" method="post" id="_form" style="display: none">
                        <input id="user_id" type="hidden">
                        <div class="info-box p-5 rounded shadow-sm">
                            <h2 class="m-2 text-primary">Nama: <span id="name" class="font-weight-bold"></span></h2>
                            <h2 class="m-2 text-success">Saldo: <span id="balance" class="font-weight-bold"></span></h2>
                            <h2 class="m-2 text-danger">Total Pembayaran: <span class="_total font-weight-bold"></span></h2>
                        </div>
                    </form>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-primary" id="saveTransaction">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
        getProducts();
        function getProducts(search = '', category = '') {
            let url = `{{ route('products.data') }}?filter[name]=${search}&filter[category.id]=${category}`;
            form(url, 'get', null, function (response) {
                $('#_products').empty();
                response.data.forEach((product) => {
                    let div = $(`<div class="products col-lg-3 col-md-3 col-sm-3 col-xs-12"
                    data-id="${product.id}"
                    data-price="${product.price}"
                    data-name="${product.name}"
                    data-stock="${product.stock}"
                    data-original-stock="${product.stock}"></div>`);

                    let colorSingle = $(`<div class="color-single" style="background-color: #00c292"></div>`);
                    colorSingle.append(`<h2>${product.name}</h2>`);
                    colorSingle.append(`<p>Rp. ${currencyFormat(product.price)}</p>`);
                    colorSingle.append(`<span id="stock_${product.id}">Stok : ${product.stock}</span>`);

                    div.append(colorSingle);
                    $('#_products').append(div);
                });

                // Setelah produk ditampilkan, pastikan stok diperbarui
                showCart();
            });
        }

        $(document).on('click', '.products', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let price = $(this).data('price');
            let stock = parseInt($(this).data('stock'));

            if (stock > 0) {
                addToCart(id, name, price);
                showCart();
                toast('Produk berhasil ditambahkan ke keranjang');
            } else {
                toast('Stok habis!', 'error', 'Gagal!');
            }
        });

        function getCart() {
            return JSON.parse(localStorage.getItem('cart')) || [];
        }

        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            showCart();
        }

        // Tambah produk ke keranjang (create)
        function addToCart(id, name, price) {
            let cart = getCart();
            let product = cart.find(item => item.id === id);

            if (product) {
                product.qty += 1;
            } else {
                cart.push({id: id, qty: 1, name: name, price: price});
            }

            saveCart(cart);
        }

        // Hapus produk dari keranjang (delete)
        function removeFromCart(id) {
            let cart = getCart().filter(item => item.id !== id);
            saveCart(cart);
        }

        // Kurangi jumlah produk (-1)
        function decrementCart(id) {
            let cart = getCart();
            let product = cart.find(item => item.id === id);

            if (product) {
                product.qty -= 1;

                if (product.qty <= 0) {
                    removeFromCart(id);
                } else {
                    saveCart(cart);
                }
            }
        }

        // Perbarui tampilan stok sesuai isi keranjang
        function updateStockDisplay() {
            let cart = getCart();

            $('.products').each(function () {
                let id = $(this).data('id');
                let originalStock = parseInt($(this).data('original-stock')) || 0;
                let cartItem = cart.find(item => item.id === id);
                let newStock = originalStock - (cartItem ? cartItem.qty : 0);

                // Pastikan stok tidak negatif
                newStock = newStock < 0 ? 0 : newStock;

                $(this).data('stock', newStock);
                $(`#stock_${id}`).text(`Stok : ${newStock}`);
            });
        }

        // Menampilkan daftar keranjang
        function showCart() {
            let cart = getCart();
            let total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            $('#_item_list').empty();

            cart.forEach((item) => {
                let li = $(`<li></li>`);
                li.append(`<span>${item.name}</span>`);
                li.append(`<span class="pull-right">Rp. ${currencyFormat(item.price)} x ${item.qty}</span>`);
                li.append(`<span class=""><a style="color: red" href="javascript:void(0)" onclick="decrementCart('${item.id}')">Hapus</a></span>`);
                $('#_item_list').append(li);
            });

            $('._total').text(`Rp. ${currencyFormat(total)}`);

            updateStockDisplay();

            if (cart.length > 0) {
                $('.save').show()
            } else {
                $('.save').hide()
            }
        }

        $(document).on('click', '.save', function () {
            let cart = getCart();
            if (cart.length === 0) {
                toast('Keranjang masih kosong!', 'error', 'Gagal!');
                return;
            }

            $('#myModalone').modal('show');
        });

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            {fps: 10, qrbox: {width: 250, height: 250}},
            /* verbose= */ false);
        // html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        setTimeout(() => {
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }, 3000); // 3000 milliseconds = 3 seconds
        function onScanFailure(error) {
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText) {
                getUserFromQrcode(decodedText)
            }
        }

        function getUserFromQrcode(decodedText) {
            let url = "{{route('merchant.transactions.qr-code',':id')}}".replace(':id', decodedText);
            form(url, 'get', {}, function (response, error) {
                if (response) {
                    $('#_form').show();
                    $('#name').text(response.data.name);
                    $('#balance').text("Rp. " +currencyFormat(response.data.balance));
                    $('#user_id').val(response.data.id);
                    html5QrcodeScanner.clear();
                }
            })
        }

        $('#saveTransaction').click(function () {
            let cart = getCart();
            let user_id = $('#user_id').val();
            let total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            let data = {
                user_id: user_id,
                total: total,
                items: cart
            }

            console.log(data);

            form('{{route('merchant.transactions.store')}}', 'post', data, function (response, error) {
                if (response) {
                    toast(response.message, 'success', 'Berhasil!');
                    $('#myModalone').modal('hide');
                    saveCart([]);
                    showCart();
                }
            })
        });
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.css') }}">
    <style>
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100% !important;
        }

        /* Efek Hover */
        .color-single {
            transition: all 0.3s ease;
            cursor: pointer;
            padding: 9px;
            padding-top: 9px;
            padding-right: 2px;
            padding-bottom: 9px;
            padding-left: 2px;

        }

        .color-single:hover {
            transform: scale(1.05); /* Membesar sedikit saat hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Menambah bayangan */
        }

        /* Efek Aktif Saat Diklik */
        .color-single.active {
            border: 3px solid #000; /* Tambahkan border hitam saat aktif */
            background-color: #d32f2f !important; /* Ubah warna saat aktif */
            color: white !important; /* Ubah warna teks */
        }

        .info-box {
            background: #f9f9f9;
            border-left: 5px solid #007bff;
        }
    </style>
@endpush

