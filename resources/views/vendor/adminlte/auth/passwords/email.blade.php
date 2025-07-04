@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $passEmailUrl = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email');

    if (config('adminlte.use_route_url', false)) {
        $passEmailUrl = $passEmailUrl ? route($passEmailUrl) : '';
    } else {
        $passEmailUrl = $passEmailUrl ? url($passEmailUrl) : '';
    }
@endphp

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('auth_body')

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<form action="{{ route('password.email') }}" method="post">
    @csrf

    {{-- Email field --}}
    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Send reset link button --}}
    <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
        <span class="fas fa-share-square"></span>
        {{ __('adminlte::adminlte.send_password_reset_link') }}
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