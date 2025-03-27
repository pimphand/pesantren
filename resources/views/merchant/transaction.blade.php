@php
$merchant = auth()->user()->merchant;
$columns = ['Date','Invoice','Total','Pembayaran','Action'];
@endphp
@extends('layouts.app')
@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :icon="'notika-menu-list'"
                  :description="'list '.$title.' dan tambah '.$title.''"
                  :buttonTitle="'List '.$title.''">
    </x-breadcrumb>
@endsection

@section('content')
    <div class="inbox-area">
        <div class="container">
            <div class="row" id="show_transaction" style="display: none">
                <x-table :title="$title" :id="'table_transaction'" :columns="$columns"></x-table>
            </div>
            <div class="row" id="form_transaction">
                @if($merchant->is_pin)
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <h5>memerlukan PIN untuk transaksi</h5>
                    </div>
                @endif
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
                                @if($merchant->is_tax)
                                    <li>
                                        <span>Pajak ({{$merchant->tax}}%) </span><span class="pull-right _tax"></span>
                                    </li>
                                @endif
                                <li>
                                    <span>Total Bayar </span><span class="pull-right _total"></span>
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
                        <div class="row mb-5 p-4">
                            <div class="d-flex justify-between w-100 mb-2" id="_show_order_today"></div>
                        </div>

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
                                <button class="btn btn-default btn-sm waves-effect" onclick="printLastTransaction()">
                                    <i class="notika-icon notika-print"
                                       aria-hidden="true">
                                    </i> Print Order Terakhir
                                </button>
                                <button class="btn btn-default btn-sm waves-effect"><i
                                        class="notika-icon notika-save"></i> Simpan Ke Draft
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="myModalone" data-backdrop="static">
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
                        @if($merchant->is_pin)
                            <div class="form-group">
                                <label for="pin">PIN:</label>
                                <input type="password" class="form-control" id="pin" oninput="validatePin(this)" name="pin" min="100000" max="999999" maxlength="6" placeholder="Masukkan PIN 6 Digit" required>
                                <small class="form-text text-muted">Hanya angka 6 digit yang diperbolehkan.</small> <br>
                                <code id="pin_error" class="error" style="display: none"></code>
                            </div>
                        @endif
                        <button type="button" class="btn btn-danger" id="removeCustomer">Hapus Customer</button>
                    </form>

                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-primary" id="saveTransaction">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div id="printData" style="display: none"></div>
@endsection

@push('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        getProducts();
        function getProducts(search = '', category = '') {
            let url = `{{ route('merchant.products.data') }}?filter[name]=${search}&filter[category.id]=${category}`;
            form(url, 'get', null, function (response) {
                $('#_products').empty();
                response.data.forEach((product) => {
                    let div = $(`<div class="products col-lg-3 col-md-3 col-sm-3 col-xs-12 mb-2"
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

        function removeFromCart(id) {
            let cart = getCart().filter(item => item.id !== id);
            saveCart(cart);
        }

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
            let tax = 0;
            @if($merchant->is_tax)
                 tax = total * ({{(int)$merchant->tax}} / 100);
            @endif
            $('#_item_list').empty();

            cart.forEach((item) => {
                let li = $(`<li class="mb-2"></li>`);
                li.append(`<span>${item.name}</span>`);
                li.append(`<span class="pull-right">Rp. ${currencyFormat(item.price)} x ${item.qty}</span>`);
                li.append(`<span class=""><a style="color: red" href="javascript:void(0)" onclick="decrementCart('${item.id}')">Hapus</a></span>`);
                $('#_item_list').append(li);
            });

            $('._total').text(`Rp. ${currencyFormat(total + tax)}`);
            $('._tax').text(`Rp. ${currencyFormat(tax)}`);
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
                }else {
                    $('#_form').hide();
                    toast(error.responseJSON.message, 'error', 'Gagal!');
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            })
        }
        $('#saveTransaction').click(function () {
            let cart = getCart();
            let user_id = $('#user_id').val();
            let total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);

            let formData = new FormData();
            formData.append('user_id', user_id);
            formData.append('pin', $('#pin').val());
            formData.append('total', total);
            $.each(cart, function (index, item) {
                formData.append(`items[${index}][product]`, item.id);
                formData.append(`items[${index}][qty]`, item.qty);
            });

            $('#pin_error').text('').hide();
            form('{{route('merchant.transactions.store')}}', 'post', formData, function (response, error) {
                if (response) {
                    getToday()
                    toast(response.message, 'success', 'Berhasil!');
                    $('#myModalone').modal('hide');
                    saveCart([]);
                    showCart();
                    getProducts();
                    $('#_form').hide();
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                    //save to local storage
                    localStorage.setItem('last_transaction', response.data);
                }else{
                    $('#pin_error').text(error.responseJSON.message).show();
                    toast(error.responseJSON.message, 'error', 'Gagal!');
                }
            })
        });

        $('#removeCustomer').click(function () {
            $('#_form').hide();
            $('#name').text('');
            $('#balance').text('');
            $('#user_id').val('');
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });

        $('#search').on('input', function () {
            getProducts($(this).val(), $('#category').val());
        });

        $('#category').on('change', function () {
            getProducts($('#search').val(), $(this).val());
        });
        getToday()
        function getToday() {
            let urlCreate = `{{route('merchant.transactions.data')}}?create=1`;
            $('_show_order_today').html('');
            form(urlCreate, 'get', null, function (response) {
                $('#_show_order_today').html(`
                    <span><strong>Pendapatan: Rp. ${currencyFormat(Number(response.total_amount))}</strong></span>
                    <span><strong>Dari :  ${response.total_order} Order</strong></span>
                `);
            });
        }

        function validatePin(input) {
            input.value = input.value.replace(/\D/g, '');

            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
        }

        function printLastTransaction(id = null) {
            let lastTransaction = localStorage.getItem('last_transaction');

            let url = `{{route('merchant.transactions.printInvoice',':id')}}`.replace(':id', id ?? lastTransaction);
            console.log("Generated URL:", url); // Debugging
            const print = '#printData'
            form(url, 'get', null, function (response) {
                $(print).html(response);
                $(document).find('#printInvoice').click();
                $(print).html('');
            });
        }

        // List Transaksi
        function getData(url) {
            form(url, 'get', null, function (response) {
                $('#table_transaction').html('');
                pagination(response)
                let lastDate = null;
                let totals = {};

                response.data.forEach((item) => {
                    if (!totals[item.date]) {
                        totals[item.date] = 0;
                    }
                    totals[item.date] += item.total;
                });

                let currentTotal = 0;

                response.data.forEach((item, index, array) => {
                    let tr = $(`<tr></tr>`);

                    if (lastDate !== item.date) {
                        tr.append(`<td>${item.date}</td>`);
                        lastDate = item.date;
                        currentTotal = totals[item.date];
                    } else {
                        tr.append(`<td></td>`);
                    }

                    tr.append(`<td>${item.invoice_number}</td>`);
                    tr.append(`<td>Rp. ${currencyFormat(item.total)}</td>`);
                    tr.append(`<td>${item.payment.method}</td>`);
                    tr.append(`<td class="text-right">
                        <a href="javascript:void(0)" onclick="printLastTransaction('${item.id}')" class="btn btn-primary btn-sm">Print</a></td>`);
                    let table = $('#table_transaction');
                    table.append(tr);

                    // Cek apakah ini transaksi terakhir untuk tanggal tersebut
                    let nextItem = array[index + 1];
                    if (!nextItem || nextItem.date !== item.date) {
                        let totalRow = $(`<tr style="font-weight: bold; background-color: #f8f9fa;"></tr>`);
                        totalRow.append(`<td colspan="2" class="text-right">Total:</td>`);
                        totalRow.append(`<td>Rp. ${currencyFormat(currentTotal)}</td>`);
                        totalRow.append(`<td colspan="2"></td>`);
                        table.append(totalRow);
                    }
                });
            });
        }

        $('._add_button').click(function (e) {
            $('#show_transaction').toggle();
            $('#form_transaction').toggle();
            let urlTransaksi = `{{ route('merchant.transactions.data') }}`;
            getData(urlTransaksi)
        })
        // function exportTableToExcel(filename = 'data_transaksi.xlsx') {
        //     let table = document.querySelector('.table-striped'); // Ambil tabel
        //     let wb = XLSX.utils.book_new(); // Buat workbook baru
        //     let ws = XLSX.utils.table_to_sheet(table); // Konversi tabel ke sheet
        //
        //     // Hapus kolom "Action" berdasarkan header
        //     let range = XLSX.utils.decode_range(ws['!ref']); // Ambil rentang sel
        //     let actionColIndex = null;
        //
        //     // Temukan kolom "Action"
        //     for (let col = range.s.c; col <= range.e.c; col++) {
        //         let cellAddress = XLSX.utils.encode_col(col) + "1"; // Baris pertama (header)
        //         if (ws[cellAddress] && ws[cellAddress].v.toLowerCase() === 'action') {
        //             actionColIndex = col;
        //             break;
        //         }
        //     }
        //
        //     // Jika ditemukan, hapus semua data di kolom "Action"
        //     if (actionColIndex !== null) {
        //         for (let row = range.s.r; row <= range.e.r; row++) {
        //             let cellAddress = XLSX.utils.encode_col(actionColIndex) + XLSX.utils.encode_row(row);
        //             delete ws[cellAddress]; // Hapus sel
        //         }
        //
        //         // Perbaiki rentang agar kolom tidak muncul di Excel
        //         if (actionColIndex === range.e.c) {
        //             range.e.c--; // Kurangi jumlah kolom
        //         } else {
        //             for (let col = actionColIndex; col < range.e.c; col++) {
        //                 for (let row = range.s.r; row <= range.e.r; row++) {
        //                     let fromCell = XLSX.utils.encode_col(col + 1) + XLSX.utils.encode_row(row);
        //                     let toCell = XLSX.utils.encode_col(col) + XLSX.utils.encode_row(row);
        //                     if (ws[fromCell]) {
        //                         ws[toCell] = ws[fromCell]; // Geser isi kolom ke kiri
        //                         delete ws[fromCell]; // Hapus kolom lama
        //                     }
        //                 }
        //             }
        //             range.e.c--; // Kurangi jumlah kolom
        //         }
        //         ws['!ref'] = XLSX.utils.encode_range(range); // Perbarui rentang sel
        //     }
        //
        //     XLSX.utils.book_append_sheet(wb, ws, 'Sheet 1'); // Tambahkan sheet ke workbook
        //     XLSX.writeFile(wb, filename); // Simpan sebagai file Excel
        // }
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

