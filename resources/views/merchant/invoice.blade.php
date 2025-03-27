<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Cetak Invoice</title>
    <link rel="icon" href="">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('print/css/nprogress.css') }}">
    <link rel="stylesheet" href="{{ asset('print/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('print/css/invoice_pos.css') }}">

</head>
<body onload="print_pos()"> <!-- Tambahkan onload untuk mencetak otomatis -->

@php
    $merchant = auth()->user()->merchant;
    $total = 0;
@endphp

<div id="in_pos">
    <div class="hidden-print">
        <button onclick="print_pos()" id="printInvoice" class="btn btn-primary">Cetak</button>
        <br>
    </div>
    <div id="invoice-POS">
        <div>
            <div class="info mt-10">
                <h2 class="text-center">
                    {{$merchant->name}}
                    <br>
                    <span class="text-center">
                        {{$merchant->address}}
                    </span>
                </h2>
                <table>
                    <th></th>
                    <tbody>
                    <tr>
                        <td>Tanggal : {{ date('d-M-Y', strtotime($order->created_at)) }}</td>
                        <td class="text-right">{{ date('H:i', strtotime($order->created_at)) }} WIB</td>
                    </tr>
                    </tbody>
                </table>
                <span>Penjualan: {{$order->invoice_number}} <br></span>
            </div>
            <table class="detail_invoice">
                <th></th>

                <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td colspan="4">
                            {{$item->product->name}}
                            <table class="tb-detail">
                                <th></th>

                                <tbody>
                                <tr>
                                    <td>{{$item->quantity}}x</td>
                                    <td class="text-center">{{ number_format($item->price) }}</td>
                                    <td class="text-right">{{ number_format($item->quantity * $item->price) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach

                @if($merchant->is_tax)
                    <tr class="mt-10 border-top">
                        <td colspan="3" class="total">Pajak ({{ $merchant->tax }} %)</td>
                        <td class="total text-right">{{ number_format($order->total) }}</td>
                    </tr>
                @endif

                @if($order->discount)
                    <tr class="mt-10">
                        <td colspan="3" class="total">Diskon</td>
                        <td class="total text-right"><span>0</span></td>
                    </tr>
                @endif

                <tr class="mt-10">
                    <td colspan="3" class="total">Total Keseluruhan</td>
                    <td class="total text-right">{{ number_format($order->total) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">Dibayar</td>
                    <td class="total text-right">{{ number_format($order->total) }}</td>
                </tr>
                </tbody>
            </table>

            <table class="change mt-3">
                <thead>
                <tr>
                    <th colspan="1" class="text-left">Dibayar oleh: {{$order->user->name}}</th>
                    <th colspan="2" class="text-right">Jumlah:</th>
                </tr>
                </thead>
                <tbody>
                <tr class="border-bottom">
                    <td colspan="1" class="text-left">{{$order->payment->payment_method}}</td>
                    <td colspan="2" class="text-right">{{ number_format($order->total) }}</td>
                </tr>
                </tbody>
            </table>
            <div id="legalcopy" class="ms-2">
                <p class="legal">Barang Yang Dibeli Tidak Bisa Dikembalikan</p>
            </div>
        </div>
    </div>
</div>

<script>
    function print_pos() {
        const divContents = document.getElementById("invoice-POS").innerHTML;
        const a = window.open("", "", "height=500, width=500");
        a.document.write(`<html lang="">`);
        a.document.write('<head><link rel="stylesheet" href="{{ asset('print/css/pos_print.css') }}"></head><body>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();

        setTimeout(() => {
            a.print();
            a.close(); // Tutup jendela setelah mencetak
        }, 1000);
    }
</script>

</body>
</html>
