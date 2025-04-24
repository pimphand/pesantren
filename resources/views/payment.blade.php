@php
    $columns = ['No', 'Nama Orang Tua', 'Nama Santri', 'Jumlah', 'Status', 'Tindakan'];
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
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-support"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="name"type="text" readonly
                                                class="form-control" placeholder="Nama Orang Tua">
                                        </div>
                                    </div>
                                </div>
                                <!-- Name Student -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-username"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="username" type="text" readonly
                                                   class="form-control"
                                                   placeholder="Nama Santri">
                                        </div>
                                    </div>
                                </div>
                                <!-- Amount -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-mail"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="email" type="text" readonly
                                                class="form-control" placeholder="Jumlah">
                                        </div>
                                    </div>
                                </div>
                                <!-- Bank -->
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <i class="notika-icon notika-phone"></i>
                                        </div>
                                        <div class="nk-int-st">
                                            <input name="phone" type="text" readonly
                                                   class="form-control"
                                                   placeholder="Nama Bank">
                                        </div>
                                    </div>
                                </div>
                                <!-- Receipt -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
                                        <div class="form-ic-cmp">
                                            <img src="" id="show_image" alt="" style="max-height: 300px; max-width: 300px">
                                        </div>
                                    </div>  
                                </div>
                                <!-- Status -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ic-cmp-int">
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
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${payment.parent}</td>`);
                tr.append(`<td>${payment.student}</td>`);
                tr.append(`<td>${payment.amount}</td>`);
                tr.append(`<td>${payment.status}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-success edit" data-id="${payment.id}"><i class="notika-icon notika-eye"></i></button>`);

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
                                    <input type="text" class="form-control input-sm" placeholder="search" id="search">
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
            $('#show_image').attr('src', '');
            $.each($(idForm).find('input select'), function (index, node) {
                node.value = '';
            });
            $('#_form').toggle();
            $('#table').toggle();
        });

        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            console.log(id);
            let data = responseData.find((item) => item.id == id);
            $('#_form').toggle();
            $('#table').toggle();
            $('#_form').attr('action', `/payment/${id}`);
            $('#name').val(data.name)
            $('#id').val(data.id)
            $('#show_image').attr('src', data.photo)
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

