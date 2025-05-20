@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('adminlte::adminlte.verify_message'))

@section('auth_body')

@if(session('resent'))
    <div class="alert alert-success" role="alert">
        {{ __('adminlte::adminlte.verify_email_sent') }}
    </div>
@endif

{{ __('adminlte::adminlte.verify_check_your_email') }}
{{ __('adminlte::adminlte.verify_if_not_recieved') }},

<form class="d-inline" method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn btn-danger  mt-4 ml-4 ">
        {{ __('adminlte::adminlte.verify_request_another') }}
    </button>
</form>

@stop

@push('css')
    <style>
        body {
            background: linear-gradient(135deg, rgb(214, 164, 13) 0%, rgb(241, 241, 241) 100%) !important;
            min-height: 100vh;
        }
    </style>
@endpush