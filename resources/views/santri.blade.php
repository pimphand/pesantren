@php
    $columns = ['No', 'Nama', 'Email', 'Nomor Telepon', 'Orang Tua', 'Tanggal dibuat', 'Tindakan'];

    
    $levels = [
        'Madrasah Ibtidaiyah',
        'Madrasah Tsanawiyah',
        'Madrasah Aliyah',
        'Perguruan Tinggi',
    ];

    $genders = [
        'Laki-Laki',
        'Perempuan',
    ];

    $date = date('Y-m-d');

    $parents = \App\Models\User::withRole('orang_tua')->get();
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :description="'Daftar santri dan tambah santri'"
                  :buttonTitle="'Tambah Santri'">
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
                                <h2>User Santri</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
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
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-key"></i>
                                        </div>
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
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-key"></i>
                                        </div>
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
                            </div>

                            {{-- Detail --}}
                            <div class="basic-tb-hd">
                                <h2>Detail santri</h2>
                                <p>Silakan isi detail santri di bawah ini.</p>
                            </div>
                            <div class="row">
                                <!-- Level -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-list2"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <select class="form-control level" id="level" name="level" data-live-search="false">
                                                <option value="">Pilih Tingkatan</option>
                                                @foreach($levels as $level)
                                                    <option value="{{ $level }}" {{ isset($selected) && $selected == $level ? 'selected' : '' }}>{{ $level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <code id="level_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Orang Tua -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-hands-holding-child"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <select class="form-control parent_id" id="parent_id" name="parent_id" data-live-search="false">
                                                <option value="">Pilih Orang Tua</option>
                                                @foreach($parents as $parent)
                                                    <option value="{{ $parent->id }}" {{ isset($selected) && $selected == $parent->id ? 'selected' : '' }}>{{ $parent->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <code id="parent_id_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
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
                                <!-- Class -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-app"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="class_now" id="class_now" type="text"
                                                   class="form-control" placeholder="Kelas Santri (Contoh: 1, 2, dll)">
                                        </div>
                                    </div>
                                </div>
                                <!-- Gender -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-man-woman"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <select class="form-control gender" id="gender" name="gender" data-live-search="false">
                                                <option value="">Pilih Kelamin</option>
                                                @foreach($genders as $gender)
                                                    <option value="{{$gender}}" @if(isset($selected)) {{$selected == $gender ? "selected" : ''}}  @endif>{{$gender}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <code id="gender_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-address"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="address" id="address" placeholder="Masukkan Alamat Lengkap"
                                                   type="text" class="form-control">
                                            </div>
                                    </div>
                                </div>
                                {{-- Birth --}}
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-calendar"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="date_of_birth" id="date_of_birth" placeholder="Masukkan Tanggal Lahir"
                                                   type="text" class="form-control">
                                            </div>
                                    </div>
                                </div>
                                {{-- Birth Place --}}
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-map-pin"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="place_of_birth" id="place_of_birth" placeholder="Masukkan Tempat Lahir"
                                                   type="text" class="form-control">
                                            </div>
                                    </div>
                                </div>
                                {{-- Photo --}}
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_photo">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-picture"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <div class="custom-file-input">
                                                <label class="file-label" for="photo">Pilih foto</label>
                                                <span class="file-name" id="fileName">Belum ada foto yang dipilih</span>
                                                <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png" class="form-control hidden-file-input" placeholder="Pilih foto">
                                            </div>
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
                <x-table :title="$title" :id="'table_santri'" :columns="$columns"></x-table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>

    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <!-- jQuery UI Datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        getData()
        let responseData = null;

        function getData(search = "", category = "") {
            let url = `{{ route('santri.data') }}?filter[name]=${search}`;
            form(url, 'get', null, function (response) {
                updateTable(response);
                responseData = response.data;
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        function updateTable(response) {
            let table = $("#table_santri");
            table.empty();
            response.data.forEach((santri, index) => {
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };

                const datetime = new Date(santri.created_at).toLocaleString('id-ID', options);
                
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${santri.name}</td>`);
                tr.append(`<td>${santri.email}</td>`);
                tr.append(`<td>${santri.phone}</td>`);
                tr.append(`<td>${santri.parent}</td>`);
                tr.append(`<td>${datetime}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-success detail" data-id="${santri.id}"><i class="notika-icon notika-eye"></i></button>`);
                actionTd.append(`<button class="btn btn-danger" onclick="deleteData('/santri/${santri.id}')"><i class="notika-icon notika-trash"></i></button>`);

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
                $('#note_password, #note_pin, #note_pin_confirmation, #note_photo').html('');
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
                $('#_form').attr('action', '{{ route('santri.store') }}');
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

        const inputFile = $('#photo');
        const fileName = $('#fileName');

        $('#photo').on('change', function () {
            $('#fileName').text(this.files.length > 0 ? this.files[0].name : 'Belum ada foto yang dipilih');
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
            $('#name').val(data.name)
            $('#username').val(data.username)
            $('#email').val(data.email)
            $('#address').val(data.address)
            $('#phone').val(data.phone)
            $('#class_now').val(data.class_now)
            $('#level').val(data.level)
            $('#date_of_birth').val(data.date_of_birth)
            $('#place_of_birth').val(data.place_of_birth)
            $('#gender').val(data.gender)
            $('#parent_id').val(data.parent_id)
            $('#nsm').val(data.admission_number)
            $('#nisn').val(data.national_admission_number)
            $('#show_image').attr('src', data.photo)
            $('#id').val(data.id);
            
            if($('#is_tax').is(':checked')) {
                $('#parent_tax').show();
            } else {
                $('#parent_tax').hide();
            }

            // hide fields
            $('._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_photo').hide();
            $('#note_password, #note_pin, #note_pin_confirmation').html('');

            // readonly fields
            $('#username, #email, #phone, #address, #date_of_birth, #gender, #nsm, #nisn').attr('readonly', true);
            $('#pin_input, #pin_confirmation_input, #tax_input, #name, #place_of_birth, #class_now').attr('readonly', true);
            $('#is_pin_checkbox, #is_tax, #photo, #level, #parent_id, #gender').attr('disabled', true);
        })
        $(document).on('click', '#edit', function (e) {
            e.preventDefault();
            let id = $('#id').val(); // Retrieve the ID from the hidden input field
            console.log(id);
            let data = responseData.find((item) => item.id == id);
            $('#_form').attr('action', `/santri/${id}`);
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

            if($('#is_tax').is(':checked')) {
                $('#tax_input').prop('disabled', false);
            } else {
                $('#tax_input').prop('disabled', true);
            }

            $('#is_pin_checkbox').on('change', function () {
                const isChecked = this.checked;
                $('#pin_input').prop('disabled', !isChecked);
                $('#pin_confirmation_input').prop('disabled', !isChecked);
            });
            $('#is_tax').on('change', function () {
                const isTaxChecked = this.checked;
                $('#tax_input').prop('disabled', !isTaxChecked);
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
            $('#address').removeAttr('readonly');
            $('#is_pin_checkbox').removeAttr('disabled');
            $('#is_tax').removeAttr('disabled');
            $('#pin_input').removeAttr('readonly');
            $('#pin_confirmation_input').removeAttr('readonly');
            $('#tax_input').removeAttr('readonly');
            $('#name').removeAttr('readonly');
            $('#category').removeAttr('readonly');

            // remove disabled
            $('#gender').removeAttr('disabled');
            $('#parent_id').removeAttr('disabled');
            $('#level').removeAttr('disabled');
            $('#photo').removeAttr('disabled');

            // show fields
            $('#edit').hide();
            $('#save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_tax, #parent_photo').show();
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

