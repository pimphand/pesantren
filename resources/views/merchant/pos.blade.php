@php
    $merchant = auth()->user()->merchant;
@endphp
@extends('layouts.app')
@section('breadcrumb')
    <x-breadcrumb :title="$title" :icon="'notika-menu-list'" :description="'list ' . $title . ' dan tambah ' . $title . ''">
    </x-breadcrumb>
@endsection

@section('content')
    <div class="inbox-area">
        <div class="container">
            <div class="row" id="form_transaction">
                @if($merchant->is_pin)
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <h5>memerlukan PIN untuk transaksi</h5>
                    </div>
                @endif
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="inbox-left-sd">
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
                            <a class="btn waves-effect" id="showDraft" style="display: none" href="javascript:void(0)">List
                                Draft</a>
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
                                    <i class="notika-icon notika-print" aria-hidden="true">
                                    </i> Print Order Terakhir
                                </button>
                                <button class="btn btn-default btn-sm waves-effect" id="save-to-draft"><i
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
                            <h2 class="m-2 text-danger">Total Pembayaran: <span class="_total font-weight-bold"></span>
                            </h2>
                        </div>
                        @if($merchant->is_pin)
                            <div class="form-group">
                                <label for="pin">PIN:</label>
                                <input type="password" class="form-control" id="pin" oninput="validatePin(this)" name="pin"
                                    min="100000" max="999999" maxlength="6" placeholder="Masukkan PIN 6 Digit" required>
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
    <div class="modal fade" id="draftModal" data-backdrop="static">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Daftar Draft Keranjang</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="draftContent">
                        <div class="accordion" id="draftAccordion"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Draft Detail Modal -->
    <div class="modal fade" id="draftDetailModal" data-backdrop="static">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Draft</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Draft tersimpan: <span id="draftTime"></span></p>
                    <div id="draftItems" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteDraft">Hapus Draft</button>
                    <button type="button" class="btn btn-primary" id="useDraft">Gunakan Draft</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('js/transaction.js') }}"></script>
    <script>
        getProducts();

        function getProducts(search = '', category = '') {
            let url = "{{ route('merchant.products.data') }}?filter[name]=" + search + "&filter[category.id]=" + category;
            form(url, 'get', null, function (response) {
                $('#_products').empty();
                response.data.forEach((product) => {
                    let div = $("<div class='products col-lg-3 col-md-3 col-sm-3 col-xs-12 mb-2' " +
                        "data-id='" + product.id + "' " +
                        "data-price='" + product.price + "' " +
                        "data-name='" + product.name + "' " +
                        "data-stock='" + product.stock + "' " +
                        "data-original-stock='" + product.stock + "'></div>");

                    let colorSingle = $("<div class='color-single' style='background-color: #00c292'></div>");
                    colorSingle.append("<h2>" + product.name + "</h2>");
                    colorSingle.append("<strong style='font-size: 15px;color: white'>Rp. " + currencyFormat(product.price) + "</strong>");
                    colorSingle.append("<strong style='font-size: 15px;color: white' id='stock_" + product.id + "'>Stok : " + product.stock + "</strong>");

                    div.append(colorSingle);
                    $('#_products').append(div);
                });

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
                cart.push({ id: id, qty: 1, name: name, price: price });
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
                tax = total * ({{(int) $merchant->tax}} / 100);
            @endif
            $('#_item_list').empty();

            cart.forEach((item) => {
                let li = $("<li class='cart-item'></li>");

                let itemHeader = $("<div class='item-header'>" +
                    "<span class='item-name'>" + item.name + "</span>" +
                    "<span class='item-price'>Rp. " + currencyFormat(item.price) + "</span>" +
                    "</div>");
                li.append(itemHeader);

                let quantityControls = $("<div class='quantity-row'>" +
                    "<button class='qty-btn minus' data-id='" + item.id + "'>-</button>" +
                    "<input type='number' class='qty-input' data-id='" + item.id + "' value='" + item.qty + "' min='1'>" +
                    "<button class='qty-btn plus' data-id='" + item.id + "'>+</button>" +
                    "<a href='javascript:void(0)' class='delete-btn' onclick=\"removeFromCart('" + item.id + "')\"><i class='notika-icon notika-trash' style='color:red'></i></a>" +
                    "</div>");
                li.append(quantityControls);

                $('#_item_list').append(li);
            });

            $('._total').text("Rp. " + currencyFormat(total + tax));
            $('._tax').text("Rp. " + currencyFormat(tax));
            updateStockDisplay();

            if (cart.length > 0) {
                $('.save').show()
            } else {
                $('.save').hide()
            }
        }

        // Update event handlers for new buttons
        $(document).on('click', '.qty-btn.minus', function () {
            const id = $(this).data('id');
            let cart = getCart();
            const item = cart.find(item => item.id === id);

            if (item && item.qty > 1) {
                item.qty -= 1;
                saveCart(cart);
                showCart();
            }
        });

        $(document).on('click', '.qty-btn.plus', function () {
            const id = $(this).data('id');
            let cart = getCart();
            const item = cart.find(item => item.id === id);

            if (item) {
                item.qty += 1;
                saveCart(cart);
                showCart();
            }
        });

        $(document).on('change', '.qty-input', function () {
            const id = $(this).data('id');
            const newQty = parseInt($(this).val());

            if (newQty < 1) {
                $(this).val(1);
                return;
            }

            let cart = getCart();
            const item = cart.find(item => item.id === id);

            if (item) {
                item.qty = newQty;
                saveCart(cart);
                showCart();
            }
        });

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
            { fps: 60, qrbox: { width: 250, height: 250 } },
            false
        );


        function scannerTranslator() {
            const traducciones = [
                // Html5QrcodeStrings
                { original: "QR code parse error, error =", traduccion: "Kesalahan membaca kode QR, kesalahan =" },
                { original: "Error getting userMedia, error =", traduccion: "Gagal mendapatkan userMedia, kesalahan =" },
                { original: "The device doesn't support navigator.mediaDevices , only supported cameraIdOrConfig in this case is deviceId parameter (string).", traduccion: "Perangkat tidak mendukung navigator.mediaDevices, hanya parameter deviceId (string) yang didukung dalam kasus ini." },
                { original: "Camera streaming not supported by the browser.", traduccion: "Streaming kamera tidak didukung oleh browser." },
                { original: "Unable to query supported devices, unknown error.", traduccion: "Tidak dapat mendeteksi perangkat yang didukung, kesalahan tidak diketahui." },
                { original: "Camera access is only supported in secure context like https or localhost.", traduccion: "Akses kamera hanya didukung dalam konteks yang aman seperti https atau localhost." },
                { original: "Scanner paused", traduccion: "Pemindai dijeda" },

                // Html5QrcodeScannerStrings
                { original: "Scanning", traduccion: "Memindai" },
                { original: "Idle", traduccion: "Diam" },
                { original: "Error", traduccion: "Kesalahan" },
                { original: "Permission", traduccion: "Izin" },
                { original: "No Cameras", traduccion: "Tidak ada kamera" },
                { original: "Last Match:", traduccion: "Kecocokan terakhir:" },
                { original: "Code Scanner", traduccion: "Pemindai Kode" },
                { original: "Request Camera Permissions", traduccion: "Meminta Izin Kamera" },
                { original: "Requesting camera permissions...", traduccion: "Meminta izin kamera..." },
                { original: "No camera found", traduccion: "Kamera tidak ditemukan" },
                { original: "Stop Scanning", traduccion: "Hentikan Pemindaian" },
                { original: "Start Scanning", traduccion: "Mulai Pemindaian" },
                { original: "Switch On Torch", traduccion: "Nyalakan Senter" },
                { original: "Switch Off Torch", traduccion: "Matikan Senter" },
                { original: "Failed to turn on torch", traduccion: "Gagal menyalakan senter" },
                { original: "Failed to turn off torch", traduccion: "Gagal mematikan senter" },
                { original: "Launching Camera...", traduccion: "Menyalakan Kamera..." },
                { original: "Scan an Image File", traduccion: "Pindai Berkas Gambar" },
                { original: "Scan using camera directly", traduccion: "Pindai langsung dengan kamera" },
                { original: "Select Camera", traduccion: "Pilih Kamera" },
                { original: "Choose Image", traduccion: "Pilih Gambar" },
                { original: "Choose Another", traduccion: "Pilih Gambar Lain" },
                { original: "No image choosen", traduccion: "Tidak ada gambar yang dipilih" },
                { original: "Anonymous Camera", traduccion: "Kamera Anonim" },
                { original: "Or drop an image to scan", traduccion: "Atau seret gambar untuk dipindai" },
                { original: "Or drop an image to scan (other files not supported)", traduccion: "Atau seret gambar untuk dipindai (berkas lain tidak didukung)" },
                { original: "zoom", traduccion: "zoom" },
                { original: "Loading image...", traduccion: "Memuat gambar..." },
                { original: "Camera based scan", traduccion: "Pemindaian menggunakan kamera" },
                { original: "Fule based scan", traduccion: "Pemindaian menggunakan berkas" },

                // LibraryInfoStrings
                { original: "Powered by ", traduccion: "Didukung oleh " },
                { original: "Report issues", traduccion: "Laporkan masalah" },

                // Others
                { original: "NotAllowedError: Permission denied", traduccion: "NotAllowedError: Izin ditolak" }
            ];

            function traducirTexto(texto) {
                const traduccion = traducciones.find(t => t.original === texto);
                return traduccion ? traduccion.traduccion : texto;
            }

            function traducirNodosDeTexto(nodo) {
                if (nodo.nodeType === Node.TEXT_NODE) {
                    nodo.textContent = traducirTexto(nodo.textContent.trim());
                } else {
                    for (let i = 0; i < nodo.childNodes.length; i++) {
                        traducirNodosDeTexto(nodo.childNodes[i]);
                    }
                }
            }

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((nodo) => {
                            traducirNodosDeTexto(nodo);
                        });
                    }
                });
            });

            const config = { childList: true, subtree: true };
            observer.observe(document.body, config);

            traducirNodosDeTexto(document.body);
        }

        document.addEventListener('DOMContentLoaded', function () {
            scannerTranslator(document.querySelector('#qr-reader'));
        });

        setTimeout(() => {
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);

            // Fungsi untuk mengubah teks tombol
            function updateButtonText() {
                const button = document.querySelector('#html5-qrcode-button-file-selection');
                if (button) {
                    button.textContent = 'Pilih Gambar - Tidak ada gambar yang dipilih';
                }
            }

            // Tambahkan event listener untuk memantau perubahan pada span
            const scanTypeSpan = document.querySelector('#html5-qrcode-anchor-scan-type-change');
            if (scanTypeSpan) {
                scanTypeSpan.addEventListener('click', function () {
                    // Tunggu sebentar untuk memastikan perubahan sudah terjadi
                    setTimeout(updateButtonText, 100);
                });
            }

            // Tambahkan event listener untuk input file
            const fileInput = document.querySelector('#html5-qrcode-private-filescan-input');
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const button = document.querySelector('#html5-qrcode-button-file-selection');
                    if (button) {
                        button.textContent = 'Pilih Gambar Lain';
                    }
                });
            }

            // Coba ubah teks awal
            updateButtonText();
        }, 3000); // Tunggu 3 detik sebelum mulai


        function onScanFailure(error) {
            $('#reader__header_message').text("Kode QR tidak valid");
            toast("Kode QR tidak valid", 'error', 'Gagal!');
        }

        function onScanSuccess(decodedText) {
            if (decodedText) {
                getUserFromQrcode(decodedText);
            }
        }

        function getUserFromQrcode(decodedText) {
            let url = "{{route('merchant.transactions.qr-code', ':id')}}".replace(':id', decodedText);
            form(url, 'get', {}, function (response, error) {
                if (response) {
                    $('#_form').show();
                    $('#name').text(response.data.name);
                    $('#balance').text("Rp. " + currencyFormat(response.data.balance));
                    $('#user_id').val(response.data.id);
                    html5QrcodeScanner.clear();
                } else {
                    $('#_form').hide();
                    toast(error.responseJSON.message, 'error', 'Gagal!');
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            })
        }

        $('#saveTransaction').click(async function () {
            let cart = getCart();
            let user_id = $('#user_id').val();
            let total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            let now = Date.now();

            try {
                // Ambil data transaksi terakhir dari localStorage
                const recentTransactions = JSON.parse(localStorage.getItem('recentTransactions') || '[]');

                // Cek apakah ada transaksi dalam 3 detik terakhir
                const lastTransaction = recentTransactions[recentTransactions.length - 1];
                if (lastTransaction && (now - lastTransaction.timestamp < 3000)) {
                    toast('Mohon tunggu beberapa saat sebelum melakukan transaksi berikutnya', 'error', 'Gagal!');
                    return;
                }

                // Generate idempotency key setelah lolos validasi
                const idempotencyKey = 'trans-' + now + '-' + Math.random().toString(36).substr(2, 9);

                let formData = new FormData();
                formData.append('user_id', user_id);
                formData.append('pin', $('#pin').val());
                formData.append('total', total);

                cart.forEach((item, index) => {
                    formData.append(`items[${index}][product]`, item.id);
                    formData.append(`items[${index}][qty]`, item.qty);
                });

                $('#pin_error').text('').hide();

                const response = await fetch('{{ route("merchant.transactions.store") }}', {
                    method: 'POST',
                    headers: {
                        'Idempotency-Key': idempotencyKey,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    // Simpan idempotency key dan timestamp
                    recentTransactions.push({ key: idempotencyKey, timestamp: now });
                    if (recentTransactions.length > 10) {
                        recentTransactions.shift(); // Simpan hanya 10 transaksi terakhir
                    }
                    localStorage.setItem('recentTransactions', JSON.stringify(recentTransactions));

                    getToday();
                    toast(result.message, 'success', 'Berhasil!');
                    saveCart([]);
                    showCart();
                    getProducts();
                    $('#myModalone').modal('hide');
                    $('#removeCustomer').click();
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                    localStorage.setItem('last_transaction', result.data);
                } else {
                    if (result.message === 'Transaction has already been processed') {
                        getToday();
                        toast('Transaksi berhasil diproses', 'success', 'Berhasil!');
                        $('#myModalone').modal('hide');
                        saveCart([]);
                        showCart();
                        getProducts();
                        $('#_form').hide();
                        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                        localStorage.setItem('last_transaction', result.data);
                        $('#removeCustomer').click();
                    } else {
                        $('#pin_error').text(result.message).show();
                        toast(result.message, 'error', 'Gagal!');
                    }
                }
            } catch (error) {
                console.error(error);
                toast('Terjadi kesalahan saat memproses transaksi', 'error', 'Gagal!');
            }
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
                $('#_show_order_today').html(`<span><strong>Pendapatan: Rp. ${currencyFormat(Number(response.total_amount))}</strong></span><span><strong>Dari :  ${response.total_order} Order</strong></span>`);
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

            let url = `{{route('merchant.transactions.printInvoice', ':id')}}`.replace(':id', id ?? lastTransaction);
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
                pagination(response);
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
                    tr.append(`<td>${item.customer.name}</td>`);
                    tr.append(`<td>Rp. ${currencyFormat(item.total)}</td>`);
                    tr.append(`<td class="${item.id}"></td>`);
                    tr.append(`<td>${item.payment.method}</td>`);
                    tr.append(`<td class="text-right"><a href="javascript:void(0)" onclick="printLastTransaction('${item.id}')" class="btn btn-primary btn-sm">Print</a></td>`);

                    let quantity = 0;
                    let table = $('#table_transaction');
                    table.append(tr);

                    // Tambahkan baris tersembunyi untuk menampilkan detail item dalam tabel
                    let itemRow = $('<tr class="item-row" id="items-' + item.id + '" style="display: none;"></tr>');
                    let itemDetails = '<td colspan="7">' +
                        '<strong>Detail Items: ' + item.invoice_number + '</strong>' +
                        '<table class="table table-bordered mt-2">' +
                        '<thead>' +
                        '<tr>' +
                        '<th>Nama Item</th>' +
                        '<th>Harga</th>' +
                        '<th>Jumlah</th>' +
                        '<th>Total</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>';
                    item.items.forEach((itm) => {
                        quantity += itm.quantity;
                        itemDetails += '<tr>' +
                            '<td>' + itm.name + '</td>' +
                            '<td>Rp. ' + currencyFormat(itm.price) + '</td>' +
                            '<td>' + itm.quantity + '</td>' +
                            '<td>Rp. ' + currencyFormat(itm.price * itm.quantity) + '</td>' +
                            '</tr>';
                        if (item.tax) {
                            itemDetails += '<tr>' +
                                '<td>Pajak </td>' +
                                '<td>Rp. ' + currencyFormat(item.tax) + '</td>' +
                                '<td>1</td>' +
                                '<td>Rp. ' + currencyFormat(item.tax) + '</td>' +
                                '</tr>';
                        }
                    });
                    itemDetails += '</tbody>' +
                        '</table>' +
                        '</td>';
                    itemRow.append(itemDetails);
                    table.append(itemRow);
                    $('.' + item.id).html(`<button class="btn btn-success notika-btn-success waves-effect item-btn" data-id="${item.id}">${quantity} Item</button>`);
                    // Cek apakah ini transaksi terakhir untuk tanggal tersebut
                    let nextItem = array[index + 1];
                    if (!nextItem || nextItem.date !== item.date) {
                        let totalRow = $(`<tr style="font-weight: bold; background-color: #f8f9fa;"></tr>`);
                        totalRow.append(`<td colspan="3" class="text-right">Total:</td>`);
                        totalRow.append(`<td>Rp. ${currencyFormat(currentTotal)}</td>`);
                        totalRow.append(`<td colspan="3"></td>`);
                        table.append(totalRow);
                    }
                });

                // Tambahkan event listener untuk tombol item
                $('.item-btn').on('click', function () {
                    let itemId = $(this).data('id');

                    // Tutup semua detail item sebelum membuka yang baru
                    $('.item-row').hide();

                    // Toggle hanya untuk item yang diklik
                    $(`#items-${itemId}`).toggle();
                });

            });
        }

        // search
        $(document).ready(function () {
            $("#search_form").append("<div class='row'>" +
                "<div class='col-lg-3 col-md-3 col-sm-3 col-xs-12'>" +
                "<div class='form-example-int form-example-st'>" +
                "<div class='form-group'>" +
                "<div class='nk-int-st'>" +
                "<input type='text' class='form-control input-sm' placeholder='search' id='search'>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>");
        });
        //End List Transaksi

        // Function to check and update draft button visibility
        function updateDraftButtonVisibility() {
            const drafts = JSON.parse(localStorage.getItem('drafts')) || [];
            const now = new Date().getTime();

            // Filter out expired drafts
            const validDrafts = drafts.filter(draft => {
                const hoursDiff = (now - draft.timestamp) / (1000 * 60 * 60);
                return hoursDiff <= 20;
            });

            // Update localStorage with only valid drafts
            localStorage.setItem('drafts', JSON.stringify(validDrafts));

            // Show/hide draft button based on valid drafts count
            if (validDrafts.length > 0) {
                $('#showDraft').show();
            } else {
                $('#showDraft').hide();
            }
        }

        // Call on page load
        $(document).ready(function () {
            updateDraftButtonVisibility();
        });

        // Update draft button visibility after saving to draft
        $('#save-to-draft').click(function () {
            let cart = getCart();
            if (cart.length === 0) {
                toast('Keranjang masih kosong!', 'error', 'Gagal!');
                return;
            }

            // Get existing drafts or initialize empty array
            let drafts = JSON.parse(localStorage.getItem('drafts')) || [];

            // Create new draft
            const newDraft = {
                id: Date.now(), // Use timestamp as unique ID
                cart: cart,
                timestamp: new Date().getTime(),
                name: `Draft`
            };

            // Add new draft to array
            drafts.push(newDraft);

            // Save back to localStorage
            localStorage.setItem('drafts', JSON.stringify(drafts));

            // Clear current cart
            saveCart([]);
            showCart();

            toast('Keranjang berhasil dipindahkan ke draft!', 'success', 'Berhasil!');
            updateDraftButtonVisibility();
        });

        // Show draft modal with list of drafts
        $('#showDraft').click(function () {
            const drafts = JSON.parse(localStorage.getItem('drafts')) || [];
            if (drafts.length === 0) {
                toast('Tidak ada draft tersimpan!', 'warning', 'Peringatan!');
                $('#showDraft').hide();
                return;
            }

            // Clear and update draft list
            $('#draftAccordion').empty();

            // Filter out expired drafts
            const now = new Date().getTime();
            const validDrafts = drafts.filter(draft => {
                const hoursDiff = (now - draft.timestamp) / (1000 * 60 * 60);
                return hoursDiff <= 20;
            });

            // Update localStorage with only valid drafts
            localStorage.setItem('drafts', JSON.stringify(validDrafts));

            if (validDrafts.length === 0) {
                toast('Semua draft telah kadaluarsa!', 'warning', 'Peringatan!');
                return;
            }

            // Display each draft
            validDrafts.forEach((draft, index) => {
                const draftDate = new Date(draft.timestamp);
                const timeString = draftDate.toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Calculate totals
                const totalItems = draft.cart.reduce((sum, item) => sum + item.qty, 0);
                const totalPrice = draft.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

                const draftCard = $(`
                    <div class= "draft-card">
                                 <div class="draft-header" data-toggle="collapse" data-target="#collapse${draft.id}">
                                     <div class="draft-title">${draft.name}</div>
                                     <div class="draft-time">${timeString}</div>
                                     <div class="draft-summary">
                                         <span>${totalItems} Item</span>
                                         <span>Total: Rp. ${currencyFormat(totalPrice)}</span>
                                     </div>
                                 </div>
                                 <div id="collapse${draft.id}" class="collapse">
                                     <div class="draft-body">
                                         <div class="draft-items">
                                             ${draft.cart.map(item => `
                                                 <div class="draft-item">
                                                     <span class="draft-item-name">${item.name}</span>
                                                     <span class="draft-item-qty">${item.qty}x</span>
                                                     <span class="draft-item-price">Rp. ${currencyFormat(item.price * item.qty)}</span>
                                                 </div>
                                             `).join('')}
                                         </div>
                                         <div class="draft-total">
                                             Total: Rp. ${currencyFormat(totalPrice)}
                                         </div>
                                         <div class="draft-actions">
                                             <button class="btn-draft delete delete-draft" data-id="${draft.id}">
                                                 <i class="notika-icon notika-trash"></i> Hapus
                                             </button>
                                             <button class="btn-draft use use-draft" data-id="${draft.id}">
                                                 <i class="notika-icon notika-checked"></i> Gunakan
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>`);
                $('#draftAccordion').append(draftCard);
            });

            $('#draftModal').modal('show');
        });

        // Handle use draft button
        $(document).on('click', '.use-draft', function () {
            const draftId = $(this).data('id');
            const drafts = JSON.parse(localStorage.getItem('drafts')) || [];
            const draft = drafts.find(d => d.id === draftId);

            if (draft) {
                saveCart(draft.cart);
                showCart();

                // Remove the used draft
                const updatedDrafts = drafts.filter(d => d.id !== draftId);
                localStorage.setItem('drafts', JSON.stringify(updatedDrafts));

                toast('Draft berhasil digunakan!', 'success', 'Berhasil!');
                $('#draftModal').modal('hide');
                updateDraftButtonVisibility();
            }
        });

        // Handle delete draft button
        $(document).on('click', '.delete-draft', function () {
            const draftId = $(this).data('id');
            const drafts = JSON.parse(localStorage.getItem('drafts')) || [];

            // Remove the draft from storage
            const updatedDrafts = drafts.filter(d => d.id !== draftId);
            localStorage.setItem('drafts', JSON.stringify(updatedDrafts));

            // Remove the draft card from view
            $(this).closest('.draft-card').fadeOut(300, function () {
                $(this).remove();

                // If no drafts left, close the modal and hide the button
                if (updatedDrafts.length === 0) {
                    $('#draftModal').modal('hide');
                    $('#showDraft').hide();
                }
            });

            toast('Draft berhasil dihapus!', 'success', 'Berhasil!');
        });
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.css') }}">
    <style>
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100% !important;
        }

        /* Product Card Styles */
        .products {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .color-single {
            transition: all 0.3s ease;
            cursor: pointer;
            padding: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .color-single h2 {
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.4;
            height: 44px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .color-single p {
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
        }

        .color-single span {
            font-size: 13px;
            color: #666;
        }

        .color-single:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Efek Aktif Saat Diklik */
        .color-single.active {
            border: 3px solid #000;
            background-color: #d32f2f !important;
            color: white !important;
        }

        .info-box {
            background: #f9f9f9;
            border-left: 5px solid #007bff;
        }

        .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .item-name {
            font-size: 14px;
        }

        .item-price {
            font-size: 14px;
            color: #666;
        }

        .quantity-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            padding: 0;
        }

        .qty-input {
            width: 40px;
            height: 28px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
        }

        .qty-input::-webkit-inner-spin-button,
        .qty-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .qty-input {
            -moz-appearance: textfield;
        }

        .delete-btn {
            color: #dc3545;
            font-size: 20px;
            text-decoration: none;
            line-height: 1;
            margin-left: auto;
        }

        #_item_list {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Draft Modal Styles */
        .draft-card {
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .draft-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
        }

        .draft-header:hover {
            background: #e9ecef;
        }

        .draft-body {
            padding: 15px;
        }

        .draft-title {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            color: #333;
        }

        .draft-time {
            font-size: 12px;
            color: #666;
        }

        .draft-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }

        .draft-items {
            margin-top: 10px;
        }

        .draft-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .draft-item:last-child {
            border-bottom: none;
        }

        .draft-item-name {
            flex: 1;
        }

        .draft-item-qty {
            width: 60px;
            text-align: center;
            color: #666;
        }

        .draft-item-price {
            width: 120px;
            text-align: right;
            color: #666;
        }

        .draft-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-draft {
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-draft.delete {
            background: #dc3545;
            color: white;
            border: none;
        }

        .btn-draft.use {
            background: #28a745;
            color: white;
            border: none;
        }

        .draft-total {
            font-size: 16px;
            font-weight: 500;
            text-align: right;
            margin-top: 10px;
            color: #333;
        }
    </style>
@endpush