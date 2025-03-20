@php
    $columns = ['No', 'Kategori', 'Nama Produk', 'Harga', 'Stok', 'Action'];
    $form = [
        'category_id' => ['type' => 'select','title' => "Kategori"],
        'name' => ['type' => 'text','title' => "Nama Produk"],
        'price' => ['type' => 'number','title' => "Harga"],
        'stock' => ['type' => 'number','title' => "Stok"],
         'photo' => ['type' => 'file','title' => "Foto"],
        'description' => ['type' => 'textarea','title' => "Deskripsi"],
    ];
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title" :description="'list produk dan tambah produk'"
                  :buttonTitle="'Tambah Produk'"></x-breadcrumb>
@endsection

@section('content')
    <div class="normal-table-area">
        <div class="container">
            <div class="row">
                <form id="_form" class="" style="display: none">
                    @csrf
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
                                            <x-select :name="$key" :title="'Kategori'" :options="$categories"/>
                                        </div>
                                    @elseif($value['type'] == 'textarea')
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <textarea class="form-control" name="{{$key}}" id="{{$key}}" style="height: 100px;"
                                                      placeholder="{{$value['title']}}"></textarea>
                                        </div>
                                    @else
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <x-input :type="$value['type']" :name="$key" :placeholder="$value['title']"/>
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
        getData()

        function getData(search = "", category = "") {
            let url = `{{ route('products.data') }}?filter[name]=${search}&filter[category.id]=${category}`;
            form(url,'get',null,function (response) {
                updateTable(response);
                if (response.meta.total > response.meta.per_page) {
                    pagination(response);
                }
            });
        }

        function updateTable(response) {
            let table = $("#table_product");
            table.empty();
            response.data.forEach((product, index) => {
                let tr = $("<tr></tr>");
                tr.append(`<td>${response.meta.from++}</td>`);
                tr.append(`<td>${product.name}</td>`);
                tr.append(`<td>${product.category}</td>`);
                tr.append(`<td>${product.price}</td>`);
                tr.append(`<td>${product.stock}</td>`);

                let actionTd = $("<td class='text-right'></td>");

                actionTd.append(`<button class="btn btn-info edit" data-id="${product.id}"><i class="notika-icon notika-edit"></i></button>`);
                actionTd.append(`<button class="btn btn-danger" onclick="deleteProduct('/products/${product.id}')"><i class="notika-icon notika-trash"></i></button>`);

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
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <x-select :name="'category_search'" :title="'Kategori'" :options="$categories"/>
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
                $('#_form').attr('action', '{{ route('products.store') }}');
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

            form(url, 'POST', formData, function (response, error) {
                if (error){
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

