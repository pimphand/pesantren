@php
    $columns = ['No', 'Nama Orang Tua', 'Nama Santri', 'Jumlah', 'Status', 'Tanggal dibuat', 'Tindakan'];
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :description="'daftar pembayaran'">
    </x-breadcrumb>
@endsection

@section('content')
    <div class="normal-table-area">
        <div class="container">
            <div class="row">
                <form id="_form" class="" style="display: none">
                    @csrf
                    <input hidden="" id="id" name="id">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-element-list">
                            <div class="basic-tb-hd">
                                <h2>Menerima Pembayaran</h2>
                                <p>Silakan cek pembayaran di bawah ini dan ubah statusnya.</p>
                            </div>
                            <div class="row">
                                <!-- Name parent-->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Nama Orang Tua</label>
                                        <div class="nk-int-st">
                                            <input name="parent" id="parent" type="text" readonly
                                                class="form-control" placeholder="Nama Orang Tua">
                                        </div>
                                    </div>
                                </div>
                                <!-- Name Student -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label for="phone" style="font-weight: normal;">Nama Santri</label>
                                    <div class="form-group">
                                        <div class="nk-int-st">
                                            <input name="student" id="student" type="text" readonly
                                                   class="form-control"
                                                   placeholder="Nama Santri">
                                        </div>
                                    </div>
                                </div>
                                <!-- Amount -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Total Pembayaran</label>                                        
                                        <div class="nk-int-st">
                                            <input name="amount" id="amount" type="text" readonly
                                                class="form-control" placeholder="Jumlah">
                                        </div>
                                    </div>
                                </div>
                                <!-- Bank -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Nama Bank</label>
                                        <div class="nk-int-st">
                                            <input name="bank" type="text" readonly
                                                   class="form-control" id="bank"
                                                   placeholder="Nama Bank">
                                        </div>
                                    </div>
                                </div>
                                <!-- Paid At -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Waktu Pembayaran</label>
                                        <div class="nk-int-st">
                                            <input name="paid_at" id="paid_at" type="text" readonly
                                                   class="form-control"
                                                   placeholder="Waktu Pembayaran">
                                        </div>
                                    </div>
                                </div>
                                <!-- Verified At -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Waktu Verifikasi</label>
                                        <div class="nk-int-st">
                                            <input name="verified_at" id="verified_at" type="text" readonly
                                                   class="form-control"
                                                   placeholder="Waktu Verifikasi">
                                        </div>
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn dropdown-toggle" type="button" id="status" data-toggle="dropdown" aria-expanded="false"></button>
                                </div>
                                <!-- Receipt -->
                                <div class="col-lg-12 col-md-12 col-sm-12 text-center mt-2">
                                    <img src="" id="show_image" alt="" style="max-height: 300px; max-width: 300px">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="accept">Terima</button>
                            <button type="button" class="btn btn-warning" id="reject">Tolak</button>
                            <button type="button" class="btn btn-danger" id="cancel">Batal</button>
                        </div>
                    </div>
                </form>
                <x-table :title="$title" :id="'table_payment'" :columns="$columns"></x-table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
        getData()
        let responseData = null;

        function getData(search = "", category = "") {
            let url = `{{ route('payment.data') }}?filter[name]=${search}`;
            form(url, 'get', null, function (response) {
                updateTable(response);
                responseData = response.data;
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        function updateTable(response) {
            let table = $("#table_payment");
            table.empty();
            response.data.forEach((payment, index) => {
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };

                const datetime = new Date(payment.created_at).toLocaleString('id-ID', options);

                // Tentukan class berdasarkan status
                let statusClass = 'btn';
                if (payment.status === 'pending') {
                    statusClass += ' btn-warning';
                } else if (payment.status === 'paid') {
                    statusClass += ' btn-success';
                } else {
                    statusClass += ' btn-danger';
                }

                // Buat baris tabel
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${payment.parent}</td>`);
                tr.append(`<td>${payment.student}</td>`);
                tr.append(`<td>${payment.amount}</td>`);
                tr.append(`<td><button class="${statusClass} dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">${payment.status}</button></td>`);
                tr.append(`<td>${datetime}</td>`);

                // Tombol aksi
                let actionTd = $("<td class='text-right'></td>");
                actionTd.append(`<button class="btn btn-success edit" data-id="${payment.id}"><i class="notika-icon notika-eye"></i></button>`);

                tr.append(actionTd);

                // Tambahkan ke tabel
                table.append(tr);
            });
        }

        $(document).ready(function () {
            $("#search_form").append(`
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="form-example-int form-example-st">
                            <div class="form-group">
                                <div class="nk-int-st">
                                    <input type="text" class="form-control input-sm" placeholder="cari" id="search">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `);
        });

        $(document).ready(function () {
            const $search = $("#search");
            const $category = $(".category_search");

            function handleSearch() {
                const searchValue = $search.val();
                const categoryValue = $category.find("option:selected").val();
                getData(searchValue, categoryValue);
            }

            $search.on("input", handleSearch);
            $category.on("change", handleSearch);

            $('._add_button').on('click', function () {
                $('#_form').toggle();
                $('#table').toggle();
                $('#_form').trigger('reset');
                //remove _method
                $('#_form input[name="_method"]').remove();
                $('#_form').attr('action', '{{ route('payment.store') }}');
            });

            $('#photo').on('change', function () {
                let file = this.files[0];
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#show_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            });
        });

        let idForm = '#_form';
        console.log(idForm);
        
        // Event untuk tombol Accept
        $('#accept').click(function (e) {
            e.preventDefault();
            handleFormSubmit('paid');
        });

        // Event untuk tombol Reject
        $('#reject').click(function (e) {
            e.preventDefault();
            handleFormSubmit('rejected');
        });

        function handleFormSubmit(status) {
            $('.error').text('').hide();
            let url = $(idForm).attr('action');
            let formData = new FormData($(idForm)[0]);

            if (formData.get('id')) {
                formData.append('_method', 'PUT');
            }
            formData.append('status', status);

            form(url, 'post', formData, function (response, error) {
                if (error) {
                    swal("Gagal!", error.responseJSON.message, "error");
                    $.each(error.responseJSON.errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]).show();
                    });
                } else {
                    getData();
                    resetForm();
                    swal("Berhasil!", response.message, "success");
                }
            });
        }

        // Event untuk tombol Cancel
        $('#cancel').click(function () {
            resetForm();
        });

        // Bersihkan error saat input berubah
        $(idForm).find('input, select').on('input change', function () {
            $('#' + this.id + '_error').text('').hide();
        });

        // Event untuk tombol Edit
        $(document).on('click', '.edit', function (e) {
            e.preventDefault();

            let id = $(this).data('id');
            let data = responseData.find(item => item.id == id);

            if (!data) {
                alert('Data tidak ditemukan.');
                return;
            }

            // Format tanggal dan waktu ke Bahasa Indonesia
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };

            const datetime_paid = new Date(data.paid_at).toLocaleString('id-ID', options);
            const datetime_verified = new Date(data.verified_at).toLocaleString('id-ID', options);

            // Tampilkan form, sembunyikan tabel
            $('#_form').show();
            $('#table').hide();

            // Set form action dan isi field
            $('#_form').attr('action', `/payment/${id}`);
            $('#parent').val(data.parent);
            $('#student').val(data.student);
            $('#amount').val(data.amount);
            $('#bank').val(data.bank);
            $('#paid_at').val(datetime_paid);
            $('#verified_at').val(datetime_verified);
            $('#verified_by').val(data.verified_by);
            $('#id').val(data.id);
            $('#show_image').attr('src', data.photo);

            // Update status tampilan
            let statusCapitalized = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            $('#status').text(statusCapitalized).removeClass().addClass('btn');

            if (data.status === 'pending') {
                $('#status').addClass('btn-warning');
                $('#accept, #reject').show();
            } else if (data.status === 'paid') {
                $('#status').addClass('btn-success');
                $('#cancel').html('kembali');
                $('#accept, #reject').hide();
            } else {
                $('#status').addClass('btn-danger');
                $('#cancel').html('kembali'); // Added line to handle 'canceled' status
                $('#accept, #reject').hide();
            }

            // Tambahkan _method=PUT jika belum ada
            if (!$('#_form input[name="_method"]').length) {
                $('#_form').append('<input type="hidden" name="_method" value="PUT">');
            }
        });


        // Fungsi untuk mereset form
        function resetForm() {
            $(idForm).trigger('reset');
            $('#show_image').attr('src', '');
            $(idForm).find('input, select').each(function () {
                this.value = '';
            });
            $('#_form').hide();
            $('#table').show();
        }
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.css') }}">
    <style>
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100% !important;
        }
    </style>
@endpush

