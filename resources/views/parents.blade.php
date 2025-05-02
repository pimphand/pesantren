@php
    $columns = ['No', 'Nama', 'Email', 'Nomor Telepon', 'Tanggal dibuat', 'Tindakan'];

    $date = date('Y-m-d');

@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :description="'Daftar Parent dan tambah Parent'"
                  :buttonTitle="'Tambah Parent'">
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
                                <h2>User Parent</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
                                <!-- Name -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-support"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="name" id="name" type="text"
                                                class="form-control" placeholder="Nama Toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Username -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-username"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="username" id="username" type="text"
                                                   class="form-control" placeholder="Username toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-mail"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="email" type="text" id="email"
                                                class="form-control" placeholder="Email toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Phone -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-phone"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="phone" id="phone" type="text"
                                                   class="form-control" placeholder="Nomor telepon">
                                        </div>
                                    </div>
                                </div>
                                <!-- Password -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_password">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-settings"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="password" id="password"
                                                   placeholder="Password" type="password"
                                                   class="form-control">
                                            </div>
                                            <small id="note_password" style="color: red"></small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_password_confirmation">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-settings"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="password_confirmation"
                                                   placeholder="Konfirmasi Password" id="password_confirmation"
                                                   type="password" class="form-control">
                                            <small id="note_password_confirmation" style="color: red"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt-2">
                                    <img src="" id="show_image" alt="" style="max-height: 300px; max-width: 300px">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="save">Simpan</button>
                            <button type="button" class="btn btn-success" id="edit">Edit</button>
                            <button type="button" class="btn btn-danger" id="cancel">Batal</button>
                        </div>
                    </div>
                </form>
                <x-table :title="$title" :id="'table_orang_tua'" :columns="$columns"></x-table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <!-- jQuery UI Datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        getData()
        let responseData = null;

        function getData(search = "", category = "") {
            let url = `{{ route('orang_tua.data') }}?filter[name]=${search}`;
            form(url, 'get', null, function (response) {
                updateTable(response);
                responseData = response.data;
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        function updateTable(response) {
            let table = $("#table_orang_tua");
            table.empty();
            response.data.forEach((parent, index) => {
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };

                const datetime = new Date(parent.created_at).toLocaleString('id-ID', options);
                
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${parent.name}</td>`);
                tr.append(`<td>${parent.email}</td>`);
                tr.append(`<td>${parent.phone}</td>`);
                tr.append(`<td>${datetime}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-success detail" data-id="${parent.id}"><i class="notika-icon notika-eye"></i></button>`);
                actionTd.append(`<button class="btn btn-danger" onclick="deleteData('/orang_tua/${parent.id}')"><i class="notika-icon notika-trash"></i></button>`);

                tr.append(actionTd);
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
                $('#note_password, #note_pin, #note_pin_confirmation').html('');
                $('#edit, ._add_button').hide();

                $("#date_of_birth").datepicker({
                    dateFormat: "yy-mm-dd",
                    maxDate: 0, // mencegah pilih tanggal di masa depan
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100:+0" // misal lahir dalam 100 tahun terakhir
                });

                $('#is_pin_checkbox').on('change', function () {
                    const isChecked = this.checked;
                    $('#pin_input').prop('disabled', !isChecked);
                    $('#pin_confirmation_input').prop('disabled', !isChecked);
                });
                //remove _method
                $('#_form input[name="_method"]').remove();
                $('#_form').attr('action', '{{ route('orang_tua.store') }}');
            });
        });

        const fileName = $('#fileName');

        let idForm = '#_form';
        $('#save').click(function (e) {
            e.preventDefault();
            $('.error').text('').hide();
            let url = $(idForm).attr('action');
            let formData = new FormData($(idForm)[0]);
            if (formData.get('id')) {
                formData.append('_method', 'PUT');
            }
            form(url, 'post', formData, function (response, error) {
                if (error) {

                    swal("Gagal!", error.responseJSON.message, "error");
                    $.each(error.responseJSON.errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]).show();
                    });
                } else {
                    getData();
                    $(idForm).trigger('reset');
                    $('#show_image').attr('src', '');
                    $.each($(idForm).find('input select'), function (index, node) {
                        node.value = '';
                    });
                    $('#_form').toggle();
                    $('#table').toggle();
                    swal("Berhasil!", response.message, "success");

                    $('#edit, ._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation').show();
                }
            });
        });

        function validateNumericPin(input) {
            input.value = input.value.replace(/[^0-9]/g, '').slice(0, 6);
        }

        // Clear errors when typing
        $(idForm).find('input select').on('input change', function () {
            $('#' + this.id + '_error').text('').hide();
        });

        $('#cancel').click(function () {
            $(idForm).trigger('reset');
            $('#show_image').attr('src', '');
            $.each($(idForm).find('input select'), function (index, node) {
                node.value = '';
            });
            $('#edit, ._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation').show();
            $('#_form').toggle();
            $('#table').toggle();
        });

        $(document).on('click', '.detail', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            let data = responseData.find((item) => item.id == id);
            $('#_form').attr('action', `/orang_tua/${id}`);
            $('#_form').append('<input type="hidden" name="_method" value="PUT">');
            $('#_form').toggle();
            $('#table').toggle();
            $('#name').val(data.name)
            $('#username').val(data.username)
            $('#email').val(data.email)
            $('#phone').val(data.phone)
            $('#id').val(data.id);

            // hide fields
            $('._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_photo').hide();
            $('#note_password, #note_pin, #note_pin_confirmation').html('');

            // readonly fields
            $('#username, #email, #phone').attr('readonly', true);
            $('#pin_input, #pin_confirmation_input, #name').attr('readonly', true);
            $('#is_pin_checkbox').attr('disabled', true);
        })

        $(document).on('click', '#edit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            console.log(id);
            let data = responseData.find((item) => item.id == id);
            $('#_form').attr('action', `/orang_tua/${id}`);
            $('#_form').append('<input type="hidden" name="_method" value="PUT">');

            $('#note_password').html('*kosongkan password jika tidak di rubah');
            $('#note_password_confirmation').html('*Pastikan password cocok dan hanya diisi jika ingin mengubah');
            $('#note_pin').html('*Kosongkan PIN jika tidak ingin mengubahnya');
            $('#note_pin_confirmation').html('*Pastikan PIN cocok dan hanya diisi jika ingin mengubah');

            if($('#is_pin_checkbox').is(':checked')) {
                $('#pin_input').prop('disabled', false);
                $('#pin_confirmation_input').prop('disabled', false);
            } else {
                $('#pin_input').prop('disabled', true);
                $('#pin_confirmation_input').prop('disabled', true);
            }

            $('#is_pin_checkbox').on('change', function () {
                const isChecked = this.checked;
                $('#pin_input').prop('disabled', !isChecked);
                $('#pin_confirmation_input').prop('disabled', !isChecked);
            });

            $("#date_of_birth").datepicker({
                dateFormat: "yy-mm-dd",
                maxDate: 0, // mencegah pilih tanggal di masa depan
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0" // misal lahir dalam 100 tahun terakhir
            });

            // remove readonly
            $('#username').removeAttr('readonly');
            $('#email').removeAttr('readonly');
            $('#phone').removeAttr('readonly');
            $('#is_pin_checkbox').removeAttr('disabled');
            $('#pin_input').removeAttr('readonly');
            $('#pin_confirmation_input').removeAttr('readonly');
            $('#name').removeAttr('readonly');

            // show fields
            $('#edit').hide();
            $('#save, #parent_password, #parent_password_confirmation, #parent_pin_confirmation').show();
        })
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.css') }}">
    <style>
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100% !important;
        }

        .custom-file-input {
            border: 1px solid #ccc;
            padding: 5px;
            display: inline-block;
            border-radius: 5px;
            font-family: sans-serif;
            width: 100%;
        }

        .custom-file-input input {
            display: none;
        }

        .file-label {
            display: inline-block;
            background-color: #f1f1f1;
            border: 1px solid #aaa;
            padding: 3px 8px;
            cursor: pointer;
            margin-right: 8px;
        }

        .file-name {
            color: #555;
            font-size: 14px;
        }
    </style>
@endpush

