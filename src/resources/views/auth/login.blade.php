@extends('web::layouts.app-mini')

@section('title', 'Sign in')

@section('content')

  {{ trans('web::auth.login_welcome') }}

  <div class="social-auth-links text-center mb-3">
    <a href="{{ route('auth.eve') }}">
      <img src="{{ asset('img/evesso.png') }}">
    </a>
  </div>

@stop
