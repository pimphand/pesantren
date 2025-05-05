@php
    $columns = ['No', 'Nama', 'Email', 'Nomor Telepon', 'Kategori', 'Tanggal dibuat', 'Tindakan'];
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :description="'list Merchant dan tambah Merchant'"
                  :buttonTitle="'Tambah Merchant'">
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
                                <h2>Detail Merchant</h2>
                                <p>Silakan isi detail merchant di bawah ini.</p>
                            </div>
                            <div class="row">
                                <!-- Name -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="name" style="font-weight: normal;">Nama<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="name" id="name" type="text"
                                                class="form-control" placeholder="Nama Toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Phone -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Nomor Telepon</label>
                                        <div class="nk-int-st">
                                            <input name="phone" id="phone" type="text"
                                                   class="form-control" placeholder="Nomor telepon">
                                        </div>
                                    </div>
                                </div>
                                <!-- Kategory -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="category" style="font-weight: normal;">Kategori Merchant<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="category" id="category" type="text"
                                                   class="form-control" placeholder="Kategori Merchant (Contoh: kantin, laundry, dll)">
                                        </div>
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="address" style="font-weight: normal;">Alamat<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="address" id="address" placeholder="Masukkan Alamat Lengkap"
                                                   type="text" class="form-control">
                                            </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="nk-int-st">
                                            <input type="checkbox" name="is_tax" id="is_tax" value="1"
                                                class="i-checks"> <i></i> Tambahkan Pajak
                                            transaksi</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_tax">
                                    <div class="form-group">
                                        <label for="tax_input" style="font-weight: normal;">Pajak<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input type="number" disabled name="tax_input" id="tax_input" class="form-control" placeholder="Masukkan Pajak Transaksi">
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-image"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input type="file" name="photo" id="photo" class="form-control">
                                            <small id="note_photo" style="color: red">*Kosongkan jika tidak ingin mengubah foto</small>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="basic-tb-hd">
                                <h2>User Merchant</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
                                <!-- Username -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="username" style="font-weight: normal;">Username<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="username" id="username" type="text"
                                                   class="form-control" placeholder="Username toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="email" style="font-weight: normal;">Email<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="email" type="text" id="email"
                                                class="form-control" placeholder="Email toko">
                                        </div>
                                    </div>
                                </div>
                                <!-- Password -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_password">
                                    <div class="form-group">
                                        <label for="password" style="font-weight: normal;">Password<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="password" id="password"
                                                   placeholder="Password" type="password"
                                                   class="form-control">
                                            </div>
                                            <small id="note_password" style="color: red"></small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_password_confirmation">
                                    <div class="form-group">
                                        <label for="password_confirmation" style="font-weight: normal;">Konfirmasi Password<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="password_confirmation"
                                                   placeholder="Konfirmasi Password" id="password_confirmation"
                                                   type="password" class="form-control">
                                            <small id="note_password_confirmation" style="color: red"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="nk-int-st">
                                            <input type="checkbox"
                                                name="is_pin" value="1"
                                                class="i-checks" id="is_pin_checkbox"> <i></i> Tambahkan PIN
                                                transaksi</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- PIN -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_pin">
                                    <div class="form-group">
                                        <label for="pin_input" style="font-weight: normal;">PIN<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input
                                                oninput="validateNumericPin(this)"
                                                name="pin"
                                                type="password"
                                                maxlength="6"
                                                placeholder="Masukkan PIN 6 Digit"
                                                class="form-control"
                                                id="pin_input"
                                                disabled
                                            >
                                        </div>
                                        <small id="note_pin" style="color: red;"></small>
                                    </div>
                                </div>

                                <!-- Konfirmasi PIN -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_pin_confirmation">
                                    <div class="form-group">
                                        <label for="pin_confirmation_input" style="font-weight: normal;">Konfirmasi PIN<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input
                                                oninput="validateNumericPin(this)"
                                                name="pin_confirmation"
                                                type="password"
                                                maxlength="6"
                                                placeholder="Masukkan ulang PIN"
                                                class="form-control"
                                                id="pin_confirmation_input"
                                                disabled
                                            >
                                        </div>
                                        <small id="note_pin_confirmation" style="color: red;"></small>
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
                <x-table :title="$title" :id="'table_merchant'" :columns="$columns"></x-table>
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
            let url = `{{ route('merchant_list.data') }}?filter[name]=${search}`;
            form(url, 'get', null, function (response) {
                updateTable(response);
                responseData = response.data;
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        function updateTable(response) {
            let table = $("#table_merchant");
            table.empty();
            response.data.forEach((merchant, index) => {
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };

                const datetime = new Date(merchant.created_at).toLocaleString('id-ID', options);
                
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${merchant.name}</td>`);
                tr.append(`<td>${merchant.email}</td>`);
                tr.append(`<td>${merchant.phone}</td>`);
                tr.append(`<td>${merchant.category}</td>`);
                tr.append(`<td>${datetime}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-success detail" data-id="${merchant.id}"><i class="notika-icon notika-eye"></i></button>`);
                actionTd.append(`<button class="btn btn-danger" onclick="deleteData('/merchant_list/${merchant.id}')"><i class="notika-icon notika-trash"></i></button>`);

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
                                    <input type="text" class="form-control input-sm" placeholder="Cari" id="search">
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
                $('#note_password, #note_pin, #note_pin_confirmation, #note_photo').html('');
                $('#edit, ._add_button').hide();
                $('#is_pin_checkbox').attr('disabled', false);
                $('#is_tax_checkbox').attr('disabled', false);

                $('#is_pin_checkbox').on('change', function () {
                    const isChecked = this.checked;
                    $('#pin_input').prop('disabled', !isChecked);
                    $('#pin_confirmation_input').prop('disabled', !isChecked);
                });
                $('#is_tax').on('change', function () {
                    const isTaxChecked = this.checked;
                    $('#tax_input').prop('disabled', !isTaxChecked);
                });
                //remove _method
                $('#_form input[name="_method"]').remove();
                $('#_form').attr('action', '{{ route('merchant_list.store') }}');
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
            $('#_form').toggle();
            $('#table').toggle();
            $('#name').val(data.name);
            $('#username').val(data.username);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#address').val(data.address);
            $('#category').val(data.category);
            $('#is_pin_checkbox').prop('checked', data.is_pin == 1);
            $('#is_tax').prop('checked', data.is_tax == 1);
            $('#tax_input').val(data.tax);
            $('#id').val(data.id);
            
            if($('#is_tax').is(':checked')) {
                $('#parent_tax').show();
            } else {
                $('#parent_tax').hide();
            }

            // hide fields
            $('._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation').hide();
            $('#note_password, #note_pin, #note_pin_confirmation').html('');

            // readonly fields
            $('#username').attr('readonly', true);
            $('#email').attr('readonly', true);
            $('#phone').attr('readonly', true);
            $('#address').attr('readonly', true);
            $('#is_pin_checkbox').attr('disabled', true);
            $('#is_tax').attr('disabled', true);
            $('#pin_input').attr('readonly', true);
            $('#pin_confirmation_input').attr('readonly', true);
            $('#tax_input').attr('readonly', true);
            $('#name').attr('readonly', true);
            $('#category').attr('readonly', true);
        })
        $(document).on('click', '#edit', function (e) {
            e.preventDefault();
            let id = $('#id').val(); // Retrieve the ID from the hidden input field
            console.log(id);
            let data = responseData.find((item) => item.id == id);
            $('#_form').attr('action', `/merchant_list/${id}`);
            $('#_form').append('<input type="hidden" name="_method" value="PUT">');

            $('#note_password').html('*kosongkan password jika tidak di rubah');
            $('#note_password_confirmation').html('*Pastikan password cocok dan hanya diisi jika ingin mengubah');

            if($('#is_pin_checkbox').is(':checked')) {
                $('#pin_input').prop('disabled', false);
                $('#pin_confirmation_input').prop('disabled', false);
                $('#note_pin').html('*Kosongkan PIN jika tidak ingin mengubahnya');
                $('#note_pin_confirmation').html('*Pastikan PIN cocok dan hanya diisi jika ingin mengubah');
            } else {
                $('#pin_input').prop('disabled', true);
                $('#pin_confirmation_input').prop('disabled', true);
                $('#note_pin').html(''); // Clear the note when not using PIN
                $('#note_pin_confirmation').html(''); // Clear the note when not using PIN
            }

            if($('#is_tax').is(':checked')) {
                $('#tax_input').prop('disabled', false);
            } else {
                $('#tax_input').prop('disabled', true);
            }

            $('#is_pin_checkbox').on('change', function () {
                const isChecked = this.checked;
                $('#pin_input').prop('disabled', !isChecked);
                $('#pin_confirmation_input').prop('disabled', !isChecked);
                if(isChecked) {
                    $('#note_pin').html('*Kosongkan PIN jika tidak ingin mengubahnya');
                    $('#note_pin_confirmation').html('*Pastikan PIN cocok dan hanya diisi jika ingin mengubah');
                } else {
                    $('#note_pin').html(''); // Clear the note when not using PIN
                    $('#note_pin_confirmation').html(''); // Clear the note when not using PIN
                }
            });
            $('#is_tax').on('change', function () {
                const isTaxChecked = this.checked;
                $('#tax_input').prop('disabled', !isTaxChecked);
            });

            // remove readonly
            $('#username').removeAttr('readonly');
            $('#email').removeAttr('readonly');
            $('#phone').removeAttr('readonly');
            $('#address').removeAttr('readonly');
            $('#is_pin_checkbox').removeAttr('disabled');
            $('#is_tax').removeAttr('disabled');
            $('#pin_input').removeAttr('readonly');
            $('#pin_confirmation_input').removeAttr('readonly');
            $('#tax_input').removeAttr('readonly');
            $('#name').removeAttr('readonly');
            $('#category').removeAttr('readonly');

            // show fields
            $('#edit').hide();
            $('#save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_tax').show();
        })
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

