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
