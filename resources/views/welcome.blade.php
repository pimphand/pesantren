@extends('layouts.app')

@section('content')
    <div class="buttons-area">
        <div class="container">
            <form id="_form" method="post" action="{{route('api.login')}}">
                <x-input name="email" type="email" placeholder="Enter your email"></x-input>
                <x-input name="password" type="password" placeholder="*************"></x-input>
                <button type="button" class="btn btn-info" id="save">Save</button>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let idForm = '#_form';
        $('#save').click(function () {
            let url = $(idForm).attr('action');
            let error = false;

            $.each($(idForm).find('input'), function (index, node) {
                if (node.value.trim() === '') {
                    $('#' + node.id + '_error').text(node.id + ' is required');
                    error = true;
                }
            });

            if (!error) {
                let formData = new FormData($(idForm)[0]);

                form(url, 'POST', formData, function (response, error) {
                    if (error){
                        $.each(error.responseJSON.errors, function (key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        console.log(response);
                    }
                });
            }
        });

        // Clear errors when typing
        $(idForm).find('input').on('input', function () {
            $('#' + this.id + '_error').text('');
        });

    </script>
@endpush
