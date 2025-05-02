@php
    $columns = ['No', 'Kategori', 'Nama Produk', 'Harga', 'Stok', 'Tindakan'];
    $form = [
        'category_id' => ['type' => 'select', 'title' => "Kategori"],
        'name' => ['type' => 'text', 'title' => "Nama Produk"],
        'price' => ['type' => 'number', 'title' => "Harga"],
        'stock' => ['type' => 'number', 'title' => "Stok"],
        'photo' => ['type' => 'file', 'title' => "Foto"],
        'description' => ['type' => 'textarea', 'title' => "Deskripsi"],
    ];
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title" :description="'Daftar produk dan tambah produk'" :buttonTitle="'Tambah Produk'"></x-breadcrumb>
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
                                <h2>Input Produk</h2>
                                <p>Silakan isi form di bawah ini.</p>
                            </div>
                            <div class="row">
                                @foreach($form as $key => $value)
                                    @if($value['type'] == 'select')
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <x-select :name="$key" :title="'Kategori'" :options="$categories" />
                                        </div>
                                    @elseif($value['type'] == 'textarea')
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <textarea class="form-control" name="{{$key}}" id="{{$key}}" style="height: 100px;"
                                                placeholder="{{$value['title']}}"></textarea>
                                        </div>
                                    @else
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <x-input :type="$value['type']" :name="$key" :placeholder="$value['title']" />
                                        </div>
                                    @endif
                                @endforeach
                                <div class="col-lg-12 col-md-12 col-sm-12 text-center mt-2">
                                    <img src="" id="show_image" alt="" style="max-height: 300px; max-width: 300px">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="save">Simpan</button>
                            <button type="button" class="btn btn-danger" id="cancel">Batal</button>
                        </div>
                    </div>
                </form>
                <x-table :title="$title" :id="'table_product'" :columns="$columns"></x-table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
        let currentSort = {
            column: null,      // nilai: 'name', 'category', 'price', 'stock'
            direction: 'asc'   // atau 'desc'
        };
        let currentPage = 1;  // Tambahkan variabel untuk menyimpan halaman saat ini

        getData()
        let responseData = null;
        function getData(search = "", category = "", page = 1) {
            currentPage = page; // Simpan nomor halaman saat ini
            let url = "{{ route('merchant.products.data') }}?filter[name]=" + search + "&filter[category.id]=" + category + "&page=" + page;
            form(url, 'get', null, function (response) {
                responseData = response.data;
                updateTable(response);
                pagination(response);
            });
        }

        function updateTable(response) {
            let table = $("#table_product");
            table.empty();

            // Sort data if needed
            if (currentSort.column !== null) {
                response.data.sort((a, b) => {
                    let aVal, bVal;

                    // Handle different column types
                    switch (currentSort.column) {
                        case 'no':
                            // Use the index for sorting
                            aVal = response.data.indexOf(a);
                            bVal = response.data.indexOf(b);
                            break;
                        case 'kategori':
                            aVal = a.category;
                            bVal = b.category;
                            break;
                        case 'nama produk':
                            aVal = a.name;
                            bVal = b.name;
                            break;
                        case 'harga':
                            aVal = parseFloat(a.price);
                            bVal = parseFloat(b.price);
                            break;
                        case 'stok':
                            aVal = parseInt(a.stock);
                            bVal = parseInt(b.stock);
                            break;
                        default:
                            return 0;
                    }

                    if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                    if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            // Reset numbering based on current page
            let startNumber = (response.meta.current_page - 1) * response.meta.per_page + 1;
            response.data.forEach((product, index) => {
                let tr = $("<tr></tr>");
                tr.append("<td>" + startNumber + "</td>");
                tr.append("<td>" + product.category + "</td>");
                tr.append("<td>" + product.name + "</td>");
                tr.append("<td>" + product.price + "</td>");
                tr.append("<td>" + product.stock + "</td>");

                let actionTd = $("<td class='text-right'></td>");
                actionTd.append("<button class='btn btn-info edit' data-id='" + product.id + "'><i class='notika-icon notika-edit'></i></button>");
                actionTd.append("<button class='btn btn-danger' onclick=\"deleteData('/merchant/products/" + product.id + "')\"><i class='notika-icon notika-trash'></i></button>");

                tr.append(actionTd);
                table.append(tr);
                startNumber++;
            });

            if (response.meta.total > response.meta.per_page) {
                pagination(response);
            } else {
                let pagination = $('#pagination');
                pagination.empty();
            }
        }

        // Listen for table sort events
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('table');
            table.addEventListener('table-sort', function (e) {
                const { column, direction } = e.detail;
                currentSort.column = column;
                currentSort.direction = direction;
                getData("", "", currentPage); // Gunakan currentPage saat ini
            });
        });

        $('#price').on('keydown', function (e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });

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
                "<div class='col-lg-3 col-md-3 col-sm-3 col-xs-12'>" +
                "<x-select :name=\"'category_search'\" :title=\"'Kategori'\" :options=\"$categories\"/>" +
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

            $search.on("input", debounce(handleSearch, 200)); // kamu bisa naikkan delay jadi 300ms agar lebih smooth
            $category.on("change", handleSearch);

            $('._add_button').on('click', function () {
                $('#_form').toggle();
                $('#table').toggle();
                $('#_form').trigger('reset');
                $('._add_button').hide();
                // Hapus input _method (biasanya ada saat edit PUT/PATCH)
                $('#_form input[name="_method"]').remove();
                $('#_form').attr('action', '{{ route('merchant.products.store') }}');
            });

            $('#photo').on('change', function () {
                const file = this.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#show_image').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Prevent non-numeric input in number fields
            $('input[type="number"]').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Prevent paste of non-numeric values
            $('input[type="number"]').on('paste', function (e) {
                e.preventDefault();
                let pastedData = e.originalEvent.clipboardData.getData('text');
                let numericValue = pastedData.replace(/[^0-9]/g, '');
                this.value = numericValue;
            });

            // Prevent drag and drop of non-numeric values
            $('input[type="number"]').on('drop', function (e) {
                e.preventDefault();
                let droppedData = e.originalEvent.dataTransfer.getData('text');
                let numericValue = droppedData.replace(/[^0-9]/g, '');
                this.value = numericValue;
            });

            // Prevent 'e', '+', '-' characters in number fields
            $('input[type="number"]').on('keydown', function (e) {
                if (['e', 'E', '+', '-'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        });

        const inputFile = $('#photo');
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
                    if (error.status === 419) {
                        // CSRF token mismatch, refresh the page
                        swal({
                            title: "Sesi Berakhir",
                            text: "Silakan refresh halaman untuk melanjutkan",
                            icon: "warning",
                            button: "OK"
                        }).then(() => {
                            location.reload();
                        });
                        return;
                    }

                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    swal({
                        title: "Gagal!",
                        text: errorMessage,
                        icon: "error",
                        button: "OK"
                    });
                    $.each(error.responseJSON.errors || {}, function (key, value) {
                        $('#' + key + '_error').text(value[0]).show();
                    });
                } else {
                    getData();
                    $(idForm).trigger('reset');
                    $('#show_image').attr('src', '');
                    $('#fileName').text('Belum ada foto yang dipilih');
                    $.each($(idForm).find('input select'), function (index, node) {
                        node.value = '';
                    });
                    $('#_form').toggle();
                    $('#table').toggle();
                    swal({
                        title: "Berhasil!",
                        text: response.message || 'Data berhasil disimpan',
                        icon: "success",
                        button: "OK"
                    });
                    $('._add_button').show();
                }
            });
        });

        $('#photo').on('change', function () {
            $('#fileName').text(this.files.length > 0 ? this.files[0].name : 'Belum ada foto yang dipilih');
        });

        // Clear errors when typing
        $(idForm).find('input select').on('input change', function () {
            $('#' + this.id + '_error').text('').hide();
        });

        $('#cancel').click(function () {
            $(idForm).trigger('reset');
            $('.error').text('').hide();
            $('#add').removeClass('hidden')
            $('#show_image').attr('src', '');
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
            $('#_form').attr('action', `/merchant/products/${id}`);
            $('#name').val(data.name)
            $('#price').val(data.price)
            $('#stock').val(data.stock)
            $('#category_id').val(data.category_id)
            $('#description').val(data.description)
            $('#id').val(data.id)
            $('#show_image').attr('src', data.photo)
            //add method
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

        .custom-file-input {
            border: 1px solid #ccc;
            padding: 5px;
            display: inline-block;
            border-radius: 5px;
            font-family: sans-serif;
            width: 100%;
            max-width: 550px;
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