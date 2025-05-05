@php
    $columns = ['No', 'Nama', 'Email', 'Nomor Telepon', 'Orang Tua', 'Tanggal dibuat', 'Tindakan'];

    $genders = [
        'Laki-Laki',
        'Perempuan',
    ];

    $date = date('Y-m-d');

    $parents = \App\Models\User::withRole('orang_tua')->get();
    
    $levels = \App\Models\Level::get();
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
                            {{-- Detail --}}
                            <div class="basic-tb-hd">
                                <h2>Detail santri</h2>
                                <p>Silakan isi detail santri di bawah ini.</p>
                            </div>
                            <div class="row">
                                <!-- Name -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="name" style="font-weight: normal;">Nama<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="name" id="name" type="text" class="form-control" placeholder="Nama Santri" >
                                        </div>
                                        <code id="name_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Parent -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="parent_id" style="font-weight: normal;">Orang Tua<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <select class="form-control" id="parent_id" name="parent_id" data-live-search="false" >
                                                <option></option>
                                                @foreach($parents as $parent)
                                                    <option value="{{ $parent->id }}" {{ isset($selected) && $selected == $parent->id ? 'selected' : '' }}>{{ $parent->username }}</option>
                                                @endforeach
                                            </select>
                                            <code id="parent_id_error" class="error" style="display: none; background-color: transparent;"></code>
                                        </div>
                                    </div>
                                </div>
                                <!-- Gender -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="gender" style="font-weight: normal;">Jenis Kelamin<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <select class="form-control" id="gender" name="gender" data-live-search="false" >
                                                <option></option>
                                                @foreach($genders as $gender)
                                                    <option value="{{ $gender }}" {{ isset($selected) && $selected == $gender ? 'selected' : '' }}>{{ $gender }}</option>
                                                @endforeach
                                            </select>
                                            <code id="gender_error" class="error" style="display: none; background-color: transparent;"></code>
                                        </div>
                                    </div>
                                </div>
                                <!-- Phone -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="phone" style="font-weight: normal;">Nomor Telepon</label>
                                        <div class="nk-int-st">
                                            <input name="phone" id="phone" type="text" class="form-control" placeholder="Nomor Telepon" >
                                        </div>
                                        <code id="phone_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="address" style="font-weight: normal;">Alamat<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="address" id="address" type="text" class="form-control" placeholder="Masukkan Alamat Lengkap" >
                                        </div>
                                        <code id="address_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Birth Date -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="date_of_birth" style="font-weight: normal;">Tanggal Lahir<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="date_of_birth" id="date_of_birth" type="date" class="form-control" placeholder="Masukkan Tanggal Lahir" >
                                        </div>
                                        <code id="date_of_birth_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>

                                <!-- Birth Place -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="place_of_birth" style="font-weight: normal;">Tempat Lahir<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="place_of_birth" id="place_of_birth" type="text" class="form-control" placeholder="Masukkan Tempat Lahir" >
                                        </div>
                                        <code id="place_of_birth_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Level -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="level" style="font-weight: normal;">Tingkat</label>
                                        <div class="nk-int-st">
                                            <select class="form-control" id="level" name="level" data-live-search="false" >
                                                <option></option>
                                                @foreach($levels as $level)
                                                    <option value="{{ $level->id }}" {{ isset($selected) && $selected == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                            <code id="level_error" class="error" style="display: none; background-color: transparent;"></code>
                                        </div>
                                    </div>
                                </div>
                                <!-- Class -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="class_now" style="font-weight: normal;">Kelas<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="class_now" id="class_now" type="text" class="form-control" placeholder="Kelas Santri (Contoh: 1, 2, dll)" >
                                        </div>
                                        <code id="class_now_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- NSM -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="nsm" style="font-weight: normal;">NSM<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="nsm" id="nsm" type="text" class="form-control" placeholder="Nomor Statistik Madrasah">
                                        </div>
                                        <code id="nsm_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- nisn -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="nisn" style="font-weight: normal;">NISN<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input name="nisn" id="nisn" type="text" class="form-control" placeholder="Nomor Induk Siswa Nasional">
                                        </div>
                                        <code id="nisn_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Pin -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_pin">
                                    <div class="form-group">
                                        <label for="pin" style="font-weight: normal;">PIN<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input
                                                oninput="validateNumericPin(this)"
                                                name="pin"
                                                type="password"
                                                maxlength="6"
                                                placeholder="Masukkan PIN 6 Digit"
                                                class="form-control"
                                                id="pin_input"
                                            >
                                        </div>
                                        <small id="note_pin" style="color: red;"></small>
                                        <code id="pin_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Pin Confirmation -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_pin_confirmation">
                                    <div class="form-group">
                                        <label for="pin" style="font-weight: normal;">Konfirmasi PIN<i class="text-danger">*</i></label>
                                        <div class="nk-int-st">
                                            <input
                                                oninput="validateNumericPin(this)"
                                                name="pin_confirmation"
                                                type="password"
                                                maxlength="6"
                                                placeholder="Masukkan ulang PIN"
                                                class="form-control"
                                                id="pin_confirmation_input"
                                            >
                                        </div>
                                        <small id="note_pin_confirmation" style="color: red;"></small>
                                        <code id="pin_confirmation_error" class="error" style="display: none; background-color: transparent;"></code>
                                    </div>
                                </div>
                                <!-- Photo -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="parent_photo">
                                    <div class="form-group">
                                        <label for="photo" style="font-weight: normal;">Foto</label>
                                        <div class="nk-int-st">
                                            <div class="custom-file-input">
                                                <label class="file-label" for="photo">Pilih Foto</label>
                                                <span class="file-name" id="fileName">Belum ada foto yang dipilih</span>
                                                <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png" class="form-control hidden-file-input" >
                                            </div>
                                        </div>
                                        <code id="photo_error" class="error" style="display: none; background-color: transparent;"></code>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

            $('#gender').select2({
                width: '100%',
                placeholder: 'Pilih Jenis Kelamin',
                allowClear: true,
            });
            $('#level').select2({
                width: '100%',
                placeholder: 'Pilih Tingkat',
                allowClear: true,
            });
            $('#parent_id').select2({
                width: '100%',
                placeholder: 'Pilih Orang Tua',
                allowClear: true,
            });

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
                    $('#gender, #level, #parent_id').val(null).trigger('change');
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
            $('#gender, #level, #parent_id').val(null).trigger('change');
            $('.error').text('').hide();
            const readonlyFields = [
                '#username', '#email', '#phone', '#address',
                '#pin_input', '#pin_confirmation_input',
                '#name', '#place_of_birth', '#class_now',
                '#nsm', '#nisn'
            ];
            readonlyFields.forEach(selector => $(selector).removeAttr('readonly'));

            // Buat array field disabled dan hilangkan attributenya
            const disabledFields = [
                '#is_pin_checkbox', '#photo',
                '#gender', '#parent_id', '#level'
            ];
            disabledFields.forEach(selector => $(selector).removeAttr('disabled'));

            $.each($(idForm).find('input select'), function (index, node) {
                node.value = '';
            });
            $('#edit, ._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation').show();
            $('#_form').toggle();
            $('#table').toggle();
        });

        $(document).on('click', '.detail', function (e) {
            e.preventDefault();
            
            const id = $(this).data('id');
            const data = responseData.find(item => item.id == id);
            if (!data) return;

            $('#_form').toggle();
            $('#table').toggle();

            // Set input values
            $('#name').val(data.name);
            $('#username').val(data.username);
            $('#email').val(data.email);
            $('#address').val(data.address);
            $('#phone').val(data.phone);
            $('#class_now').val(data.class_now);
            $('#date_of_birth').val(data.date_of_birth);
            $('#place_of_birth').val(data.place_of_birth);
            $('#nsm').val(data.admission_number);
            $('#nisn').val(data.national_admission_number);
            $('#show_image').attr('src', data.photo);
            $('#id').val(data.id);

            // Select2 values
            $('#level').val(data.level).trigger('change');
            $('#gender').val(data.gender).trigger('change');
            $('#parent_id').val(data.parent_id).trigger('change');

            // Disable and readonly fields
            $('#username, #email, #phone, #address, #date_of_birth, #nsm, #nisn')
                .prop('readonly', true);
            $('#pin_input, #pin_confirmation_input, #name, #place_of_birth, #class_now')
                .prop('readonly', true);

            $('#gender, #level, #parent_id').prop('disabled', true);
            // Hide certain fields
            $('._add_button, #save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_photo')
                .hide();
            $('#note_password, #note_pin, #note_pin_confirmation').empty();
        });

        $(document).on('click', '#edit', function (e) {
            e.preventDefault();

            const id = $('#id').val(); // Ambil ID dari input hidden
            const data = responseData.find(item => item.id == id);
            if (!data) return;

            console.log(id);

            // Set form action dan metode
            $('#_form').attr('action', `/santri/${id}`);
            if ($('#_form input[name="_method"]').length === 0) {
                $('#_form').append('<input type="hidden" name="_method" value="PUT">');
            }

            // Catatan untuk PIN
            $('#note_pin').html('*Kosongkan PIN jika tidak ingin mengubahnya');
            $('#note_pin_confirmation').html('*Pastikan PIN cocok dan hanya diisi jika ingin mengubah');

            // Inisialisasi datepicker
            $("#date_of_birth").datepicker({
                dateFormat: "yy-mm-dd",
                maxDate: 0,
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0"
            });

            // Buat array field readonly dan hilangkan attributenya
            const readonlyFields = [
                '#username', '#email', '#phone', '#address',
                '#pin_input', '#pin_confirmation_input',
                '#name', '#place_of_birth', '#class_now',
                '#nsm', '#nisn'
            ];
            readonlyFields.forEach(selector => $(selector).removeAttr('readonly'));

            // Buat array field disabled dan hilangkan attributenya
            const disabledFields = [
                '#is_pin_checkbox', '#photo',
                '#gender', '#parent_id', '#level'
            ];
            disabledFields.forEach(selector => $(selector).removeAttr('disabled'));

            // Tampilkan elemen yang sebelumnya disembunyikan
            $('#edit').hide();
            $('#save, #parent_password, #parent_password_confirmation, #parent_pin, #parent_pin_confirmation, #parent_photo, #gender, #level, #parent_id').show();
        });

    </script>
@endpush

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

