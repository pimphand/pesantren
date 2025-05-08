@php
    $columns = ['No', 'Nama', 'Pembuat', 'Tanggal Dibuat', 'Pengubah', 'Tanggal Diperbarui', 'Tindakan'];
    $form = [
        'name' => ['type' => 'text', 'title' => "Nama Kategori", 'label' => "Nama Kategori", 'placeholder' => "Masukkan nama kategori"],
    ];
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title" :description="'Daftar kategori dan tambah kategori'" :buttonTitle="'Tambah Kategori'">
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
                                <h2>Input Kategori</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
                                @foreach($form as $key => $value)
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <x-input :type="$value['type']" :name="$key" :placeholder="$value['placeholder']" :label="$value['label']" />
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-primary" id="save">Simpan</button>
                            <button type="button" class="btn btn-danger" id="cancel">Batal</button>
                        </div>
                    </div>
                </form>
                <x-table :title="$title" :id="'table_category'" :columns="$columns"></x-table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
        getData()
        let responseData = null;

        function getData(search = "", category = "", page = 1) {
            let url = `{{ route('merchant.categories.data') }}?filter[name]=${search}&page=${page}`;
            form(url, 'get', null, function (response) {
                updateTable(response);
                responseData = response.data;
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };

        function updateTable(response) {
            let table = $("#table_category");
            table.empty();save

            response.data.forEach((product, index) => {
                let datetimeCreated = '-';
                let datetimeUpdated = '-';
                if(product.created_by !== null) {
                    datetimeCreated = new Date(product.created_at).toLocaleString('id-ID', options);
                }
                if(product.updated_by !== null) {
                    datetimeUpdated = new Date(product.updated_at).toLocaleString('id-ID', options);
                }
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${product.name}</td>`);
                tr.append(`<td>${product.created_by === null ? '-' : product.created_by}</td>`);
                tr.append(`<td>${datetimeCreated === '-' ? '-' : datetimeCreated}</td>`);
                tr.append(`<td>${product.updated_by === null ? '-' : product.updated_by}</td>`);
                tr.append(`<td>${datetimeUpdated === '-' ? '-' : datetimeUpdated}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-info edit" data-id="${product.id}"><i class="notika-icon notika-edit"></i></button>`);
                actionTd.append(`<button class="btn btn-danger" onclick="deleteData('/merchant/categories/${product.id}')"><i class="notika-icon notika-trash"></i></button>`);

                tr.append(actionTd);
                table.append(tr);
            });
        }

        $(document).ready(function () {
            $("#search_form").append("<div class='row'>" +
                "<div class='col-lg-3 col-md-3 col-sm-3 col-xs-12'>" +
                "<div class='form-example-int form-example-st'>" +
                "<div class='form-group'>" +
                "<div class='nk-int-st'>" +
                "<input type='text' class='form-control input-sm' placeholder='Cari' id='search'>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>");
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

            $search.on("input", debounce(handleSearch, 300));
            $category.on("change", handleSearch);

            $('._add_button').on('click', function () {
                $('#_form').toggle();
                $('#table').toggle();
                $('#_form').trigger('reset');
                $('._add_button').hide();
                // Hapus input _method (biasanya ada saat edit PUT/PATCH)
                $('#_form input[name="_method"]').remove();
                $('#_form').attr('action', '{{ route('merchant.categories.store') }}');
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
                    $('#add').removeClass('hidden')
                    $(idForm).trigger('reset');
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
            $('.error').text('').hide();
            $('#add').removeClass('hidden')
            $.each($(idForm).find('input select'), function (index, node) {
                node.value = '';
            });
            $('#_form').toggle();
            $('#table').toggle();
            $('._add_button').show();
        });

        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            let data = responseData.find((item) => item.id == id);
            $('#add').addClass('hidden')
            $('#_form').toggle();
            $('#table').toggle();
            $('#_form').attr('action', `/merchant/categories/${id}`);
            $('#name').val(data.name)
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