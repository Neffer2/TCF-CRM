@extends('layouts.auth.main')
    @section('form')
        <form method="POST" action="{{ route('register') }}" role="form">
            @csrf   
            @livewire('register-live')
            <p class="text-sm mt-3 mb-0">Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-dark font-weight-bolder">Iniciar sesi&oacute;n</a></p>
        </form>
    @endsection 