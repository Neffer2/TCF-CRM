@extends('layouts.auth.main')
    @section('auth-title')
        Formulario de registro
    @endsection
    @section('form')
        <form method="POST" action="{{ route('register') }}" role="form">
            @csrf   
            @livewire('auth.register-live')
            <p class="text-sm mt-3 mb-0">Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-dark font-weight-bolder">Iniciar sesi&oacute;n</a></p>
        </form>
    @endsection 