@php
    $columns = ['No', 'Nama', 'Action'];
    $form = [
        'name' => ['type' => 'text','title' => "Nama Kategori"],
    ];
    $user = auth()->user();
    $parent = $user->parent_id;
@endphp

@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :title="$title" :description="'Profile Merchant'"></x-breadcrumb>
@endsection

@section('content')
    <div class="normal-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="widget-tabs-int">
                        <div class="tab-hd">
                            <h2>Merchant</h2>
                        </div>
                        <div class="widget-tabs-list">
                            <ul class="nav nav-tabs">
                                @if(!$parent)
                                    <li class="active"><a data-toggle="tab" href="#data">Data Merchant</a></li>
                                @endif
                                <li><a data-toggle="tab" href="#profile">Profil</a></li>
                                @if(!$parent)
                                    <li><a data-toggle="tab" href="#employee">Karyawan</a></li>
                                @endif
                            </ul>
                            <div class="tab-content tab-custom-st">
                                @if(!$parent)
                                    <div id="data" class="tab-pane fade in active">
                                        <div class="tab-ctn">
                                            <form id="_form_data">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="fm-checkbox">
                                                                <label>
                                                                    <input type="checkbox" disabled
                                                                           @if($merchant->is_tax) checked=""
                                                                           @endif name="is_tax" id="is_tax" value="1"
                                                                           class="i-checks"> <i></i>Tambahkan Pajak
                                                                    transaksi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-tax"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input disabled name="tax"
                                                                       value="{{$merchant->tax?? ''}}"
                                                                       oninput="validateTax(this)"
                                                                       max="100" min="0"
                                                                       id="tax"
                                                                       type="number"
                                                                       class="form-control" placeholder="Tambahkan pajak">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-support"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input disabled name="name"
                                                                       value="{{$merchant->name?? ''}}"
                                                                       type="text"
                                                                       class="form-control" placeholder="Nama Toko">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-phone"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input disabled name="phone"
                                                                       value="{{$merchant->phone?? ''}}" type="text"
                                                                       class="form-control" placeholder="Nomor Telepon">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-address"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input disabled name="address"
                                                                       value="{{$merchant->address?? ''}}" type="text"
                                                                       class="form-control" placeholder="Alamat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-category"></i>
                                                            </div>
                                                            @php
                                                                $categories = [
                                                                    ['id' => 'kantin', 'name' => 'Kantin'],
                                                                    ['id' => 'loundry', 'name' => 'Loundry'],
                                                                    ['id' => 'retail', 'name' => 'Retail'],
                                                                    ['id' => 'mini-market', 'name' => 'Mini Market'],
                                                                ];
                                                            @endphp
                                                            <div class="nk-int-st">
                                                                <x-select :name="'category'"
                                                                          :selected="$merchant->category ?? ''"
                                                                          :title="'Kategori'"
                                                                          :options="$categories"></x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-home"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                        <textarea disabled type="text" class="form-control"
                                                                  name="description"
                                                                  placeholder="deskripsi">{{$merchant->description?? ''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="fm-checkbox">
                                                                <label>
                                                                    <input type="checkbox" disabled
                                                                           @if($merchant->is_pin) checked=""
                                                                           @endif name="is_pin" value="1"
                                                                           class="i-checks"> <i></i>Tambahkan PIN
                                                                    transaksi</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                                        @if(!$parent)
                                                            <div class="nk-int-st">
                                                                <input disabled name="photo" id="photo"
                                                                       value="{{$merchant->phone?? ''}}" type="file"
                                                                       class="form-control" placeholder="Nomor Telepon">
                                                            </div>
                                                        @endif
                                                    </div>


                                                    <div class="col-lg-12 text-center col-md-12 col-sm-12 col-xs-12">
                                                        <img src="{{$merchant->photo}}" alt="" id="show_photo"
                                                             style="max-width: 300px; max-height: 300px; width: 100%; height: auto;">
                                                    </div>
                                                </div>
                                                @if(!$parent)
                                                    @csrf
                                                    @method('put')
                                                    <button type="button" id="_save_data" class="btn btn-info">Simpan
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                @endif
                                <div id="profile" class="tab-pane fade">
                                    <div class="tab-ctn">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-element-list">
                                                    <div class="basic-tb-hd">
                                                        <h2>Ubah Data</h2>
                                                    </div>

                                                    <form id="_form_profile">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-support"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="name" value="{{$user->name}}"
                                                                               type="text" class="form-control"
                                                                               placeholder="Nama Lengkap">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-username"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="username"
                                                                               value="{{$user->username}}" type="text"
                                                                               class="form-control"
                                                                               placeholder="Username">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-mail"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="email" value="{{$user->email}}"
                                                                               type="text" class="form-control"
                                                                               placeholder="Email">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-phone"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="phone" value="{{$user->phone}}"
                                                                               type="text" class="form-control"
                                                                               placeholder="Nomor telepon">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-key"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input oninput="validatePin(this)" name="pin"
                                                                               min="100000" max="999999" maxlength="6"
                                                                               placeholder="@if($user->pin)  * * * * * * @else Masukkan PIN 6 Digit @endif"
                                                                               type="text" class="form-control">
                                                                        <small style="color: red">*kosongkan pin jika
                                                                            tidak di rubah</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-key"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="password" maxlength="6"
                                                                               placeholder="Password" type="text"
                                                                               class="form-control">
                                                                        <small style="color: red">*kosongkan password
                                                                            jika tidak di rubah</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="form-group ic-cmp-int">
                                                                    <div class="form-ic-cmp">
                                                                        <i class="notika-icon notika-key"></i>
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input name="confirmation_password"
                                                                               maxlength="6"
                                                                               placeholder="Konfirmasi Password"
                                                                               type="text" class="form-control">
                                                                        <small style="color: red"></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @method('put')
                                                    </form>
                                                    <button type="button" id="_save_profile" class="btn btn-info">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$parent)
                                    <div id="employee" class="tab-pane fade">
                                        <div class="tab-ctn">
                                            <p>Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum.
                                                Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus.
                                                Nulla
                                                sit amet est. Praesent ac the massa at ligula laoreet iaculis. Vivamus
                                                aliquet elit ac nisl. Nulla porta dolor. Cras dapibus. Aliquam lorem
                                                ante,
                                                dapibus in, viverra quis, feugiat a, tellus.</p>
                                            <p class="tab-mg-b-0">In hac habitasse platea dictumst. Class aptent taciti
                                                sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos.
                                                Nam
                                                eget dui. In ac felis quis tortor malesuadan of pretium. Phasellus
                                                consectetuer vestibulum elit. Duis lobortis massa imperdiet quam.
                                                Pellentesque commodo eros a enim. Vestibulum ante ipsum primis in
                                                faucibus
                                                orci the luctus et ultrices posuere cubilia Curae; In ac dui quis mi
                                                consectetuer lacinia. Phasellus a est. Pellentesque commodo eros a enim.
                                                Cras ultricies mi eu turpis hendrerit of fringilla. Donec mollis
                                                hendrerit
                                                risus. Vestibulum turpis sem, aliquet eget, lobortis pellentesque,
                                                rutrum
                                                eu, nisl. Praesent egestas neque eu enim. In hac habitasse plat.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        @if(!$parent)
        $('#_form_data').find('input, select, textarea').prop('disabled', false);
        $('#_save_data').click(function () {
            let idForm = '#_form_data';
            let formData = new FormData($(idForm)[0]);
            form('{{ route('merchant.profile.update',$merchant->id) }}', 'post', formData, function (response) {
                if (response) {
                    swal("Berhasil!", response.message, "success");
                } else {
                    toast(response.message, 'error', 'Gagal!');
                }
            });
        });

        $('#photo').on('change', function () {
            let file = this.files[0];
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#show_photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        });

        function validatePin(input) {
            input.value = input.value.replace(/\D/g, '');

            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
        }
        function validateTax(input) {
            input.value = input.value.replace(/\D/g, '');

            if (input.value.length > 3) {
                input.value = input.value.slice(0, 3);
            }
        }
        @endif

        $('#_save_profile').click(function () {
            let idForm = '#_form_profile';
            let formData = new FormData($(idForm)[0]);
            form('{{ route('users.update', $user->id) }}', 'post', formData, function (response, error) {
                if (error) {
                    toast(error.message, 'error', 'Gagal!');
                } else {

                    swal("Berhasil!", response.message, "success");
                }
            });
        });
    </script>
@endpush
