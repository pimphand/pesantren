@php
    $merchant = auth()->user()->merchant;
    $columns = ['Date', 'Invoice', 'Customer', 'Total', 'Item', 'Pembayaran', 'Action'];
@endphp
@extends('layouts.app')
@section('breadcrumb')
    <x-breadcrumb :title="$title" :icon="'notika-menu-list'" :description="'list ' . $title . ' dan tambah ' . $title . ''"
        :buttonTitle="'List ' . $title . ''">
    </x-breadcrumb>
@endsection

@section('content')
    <div class="inbox-area">
        <div class="container">
            <div class="row" id="show_transaction">
                <x-table :title="$title" :id="'table_transaction'" :columns="$columns"></x-table>
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
        let currentSort = {
            column: null,
            direction: 'asc'
        };

        getData()
        // List Transaksi
        function getData() {
            form("{{ route('merchant.transactions.data') }}", 'get', null, function (response) {
                $('#table_transaction').html('');
                pagination(response);
                let lastDate = null;
                let totals = {};

                // Sort data if sorting is active
                if (currentSort.column !== null) {
                    response.data.sort((a, b) => {
                        let aVal, bVal;

                        // Handle different column types
                        switch (currentSort.column) {
                            case 'date':
                                aVal = new Date(a.date);
                                bVal = new Date(b.date);
                                break;
                            case 'invoice':
                                aVal = a.invoice_number;
                                bVal = b.invoice_number;
                                break;
                            case 'customer':
                                aVal = a.customer.name;
                                bVal = b.customer.name;
                                break;
                            case 'total':
                                aVal = parseFloat(a.total);
                                bVal = parseFloat(b.total);
                                break;
                            case 'item':
                                aVal = a.items.length;
                                bVal = b.items.length;
                                break;
                            case 'pembayaran':
                                aVal = a.payment.method;
                                bVal = b.payment.method;
                                break;
                            default:
                                aVal = a[currentSort.column];
                                bVal = b[currentSort.column];
                        }

                        if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                        if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                        return 0;
                    });
                }

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

                // Add sorting icons and click handlers to table headers
                $('#table_transaction th').each(function (index) {
                    if (index < 5) { // Only add sorting to first 5 columns
                        let column = $(this).text().toLowerCase();
                        let sortIcon = $('<i class="fa fa-sort" style="margin-left: 5px;"></i>');
                        $(this).append(sortIcon);
                        $(this).css('cursor', 'pointer');

                        $(this).on('click', function () {
                            if (currentSort.column === column) {
                                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                            } else {
                                currentSort.column = column;
                                currentSort.direction = 'asc';
                            }

                            // Update sort icons
                            $('#table_transaction th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                            if (currentSort.column === column) {
                                sortIcon.removeClass('fa-sort').addClass(currentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                            }

                            getData();
                        });
                    }
                });
            });
        }

        // Add event listener for table sorting
        document.querySelector('#table').addEventListener('table-sort', function (e) {
            const { column, direction } = e.detail;
            currentSort.column = column;
            currentSort.direction = direction;
            getData();
        });

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

    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        /* Sorting styles */
        .fa-sort,
        .fa-sort-up,
        .fa-sort-down {
            color: #999;
        }

        .fa-sort-up,
        .fa-sort-down {
            color: #007bff;
        }

        th {
            position: relative;
        }

        th:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush