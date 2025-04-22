@php
    $columns = ['No', 'Nama', 'username', 'email', 'parent', 'Tindakan'];
    $form = [
        'name' => ['type' => 'text','title' => "Nama Santri"],
        'username' => ['type' => 'text','title' => "Username Santri"],
        'email'=> ['type' => 'email', 'title' => "Email Santri"]
    ];

    $levels = [
        'Madrasah Ibtidaiyah',
        'Madrasah Tsanawiyah',
        'Madrasah Aliyah',
        'Perguruan Tinggi',
    ];

    $genders = [
        'L',
        'P',
    ];

    $date = date('Y-m-d');

    $parents = \App\Models\User::withRole('orang_tua')->get();
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title"
                  :description="'list Santri dan tambah Santri'"
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
                                <h2>Input Santri</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
                                
                                <!-- Name -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-support"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="name" id="name" type="text" 
                                                class="form-control" placeholder="Nama Lengkap">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Username -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-username"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="username" id="username" 
                                                   type="text" class="form-control"
                                                   placeholder="Username">
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-mail"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="email" id="email" type="text" 
                                                class="form-control" placeholder="Email">
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-phone"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="phone" id="phone" type="text"
                                                   class="form-control"
                                                   placeholder="Nomor telepon">
                                        </div>
                                    </div>
                                </div>

                                <!-- Orang Tua -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <select class="form-control parent_id" id="parent_id" name="parent_id" data-live-search="false">
                                        <option value="">Pilih Orang Tua</option>
                                        @foreach($parents as $parent)
                                            <option value="{{$parent->id}}" @if(isset($selected)) {{$selected == $parent->id ? "selected" : ''}}  @endif>{{$parent->username}}</option>
                                        @endforeach
                                    </select>
                                    <code id="{{$name ?? ''}}_error"  class="error" style="display: none; background-color: transparent;"></code>
                                </div>

                                <!-- Kelas -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-support"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="class_now" id="class_now" type="text" 
                                                class="form-control" placeholder="kelas">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Level -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <select class="form-control level" id="level" name="level" data-live-search="false">
                                        <option value="">Pilih Tingkatan</option>
                                        @foreach($levels as $level)
                                            <option
                                                value="{{$level}}" @if(isset($selected)) {{$selected == $level ? "selected" : ''}}  @endif>{{$level}}</option>
                                        @endforeach
                                    </select>
                                    <code id="{{$name ?? ''}}_error"  class="error" style="display: none; background-color: transparent;"></code>
                                </div>

                                <!-- Gender -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <select class="form-control gender" id="gender" name="gender" data-live-search="false">
                                        <option value="">Pilih Kelamin</option>
                                        @foreach($genders as $gender)
                                            <option value="{{$gender}}" @if(isset($selected)) {{$selected == $gender ? "selected" : ''}}  @endif>{{$gender == 'L' ? 'Laki-Laki' : 'Perempuan'}}</option>
                                        @endforeach
                                    </select>
                                    <code id="{{$name ?? ''}}_error"  class="error" style="display: none; background-color: transparent;"></code>
                                </div>
                                
                                <!-- NSM -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-username"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="nsm" id="nsm" type="text" 
                                                class="form-control" placeholder="Nomor Statistik Madrasah">
                                        </div>
                                    </div>
                                </div>
                                <!-- NISN -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-username"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="nisn" id="nisn" type="text"
                                                   class="form-control"
                                                   placeholder="Nomor Induk Siswa Nasional">
                                        </div>
                                    </div>
                                </div>
                                <!-- Tempat Lahir -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-address"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="place_of_birth" id="place_of_birth" type="text" 
                                                class="form-control" placeholder="Tempat Lahir">
                                        </div>
                                    </div>
                                </div>
                                <!-- Tanggal Lahir -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-calendar"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="date_of_birth" id="date_of_birth" type="text"
                                                    class="form-control"
                                                    placeholder="Tanggal Lahir">
                                        </div>
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
                                <!-- Pin -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-key"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input oninput="validatePin(this)" name="pin" id="pin"
                                                   min="100000" max="999999" maxlength="6"
                                                   placeholder="Masukkan PIN 6 Digit"
                                                   type="text" class="form-control">
                                            </div>
                                            <small style="color: red" id="note_pin"></small>
                                    </div>
                                </div>
                                <!-- Password -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-key"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="password" minlength="8"
                                                   placeholder="Password" type="text"
                                                   class="form-control">
                                            </div>
                                            <small style="color: red" id="note_password"></small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-key"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="password_confirmation" id="password_confirmation"
                                                    minlength="8"
                                                   placeholder="Konfirmasi Password"
                                                   type="text" class="form-control">
                                            <small style="color: red"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 text-center mt-2">
                                    <img src="" id="show_image" alt="" style="max-height: 300px; max-width: 300px">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="save">Simpan</button>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${santri.name}</td>`);
                tr.append(`<td>${santri.username}</td>`);
                tr.append(`<td>${santri.email}</td>`);
                tr.append(`<td>${santri.parent}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-info edit" data-id="${santri.id}"><i class="notika-icon notika-edit"></i></button>`);
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
            // ✅ Fungsi debounce untuk membatasi frekuensi eksekusi pencarian
            function debounce(func, delay) {
                let timeout;
                return function () {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            const $search = $("#search");
            const $category = $(".category_search");

            function handleSearch() {
                const searchValue = $search.val();
                const categoryValue = $category.find("option:selected").val();

                if (searchValue.trim() === "" && categoryValue === "") {
                    getData(); // Reset ke semua data
                    return;
                }

                getData(searchValue, categoryValue);
            }

            $search.on("input", debounce(handleSearch, 300)); // kamu bisa naikkan delay jadi 300ms agar lebih smooth
            $category.on("change", handleSearch);

            $('._add_button').on('click', function () {
                $("#date_of_birth").datepicker({
                    dateFormat: "yy-mm-dd",
                    maxDate: 0, // mencegah pilih tanggal di masa depan
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100:+0" // misal lahir dalam 100 tahun terakhir
                });
                $('#_form').toggle();
                $('#table').toggle();
                $('#_form').trigger('reset');
                $('#note_password').html('');
                $('#note_pin').html('');
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

        let idForm = '#_form';
        $('#save').click(function (e) {
            e.preventDefault();
            $('.error').text('').hide();
            let url = $(idForm).attr('action');
            let formData = new FormData($(idForm)[0]);
            console.log(formData.get('id'))
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
                    $('#add').removeClass('hidden')
                    $('#show_image').attr('src', '');
                    $.each($(idForm).find('input select'), function (index, node) {
                        node.value = '';
                    });
                    $('#_form').toggle();
                    $('#table').toggle();
                    swal("Berhasil!", response.message, "success");
                }
            });
        });

        // Clear errors when typing
        $(idForm).find('input select').on('input change', function () {
            $('#' + this.id + '_error').text('').hide();
        });

        $('#cancel').click(function () {
            $(idForm).trigger('reset');
            $('#add').removeClass('hidden')
            $('#show_image').attr('src', '');
            $.each($(idForm).find('input select'), function (index, node) {
                node.value = '';
            });
            $('#_form').toggle();
            $('#table').toggle();
        });

        $(document).on('click', '.edit', function (e) {
            $("#date_of_birth").datepicker({
                dateFormat: "yy-mm-dd",
                maxDate: 0, // mencegah pilih tanggal di masa depan
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0" // misal lahir dalam 100 tahun terakhir
            });
            e.preventDefault();
            let id = $(this).data('id');
            let data = responseData.find((item) => item.id == id);
            $('#add').addClass('hidden')
            $('#note_password').html('*kosongkan pin jika tidak di rubah');
            $('#note_pin').html('*kosongkan pin jika tidak di rubah');
            $('#_form').toggle();
            $('#table').toggle();
            $('#_form').attr('action', `/santri/${id}`);
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
            $('#id').val(data.id)
            $('#_form').append('<input type="hidden" name="_method" value="PUT">');
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

