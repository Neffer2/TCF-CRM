@extends('layouts.auth.main')
@section('auth-title')
    Iniciar sesi&oacute;n
@endsection
    @section('form')
        <form method="POST" action="{{ route('login') }}" role="form" class="text-start">
            @csrf
            @livewire('auth.login-live') 
        </form>
    @endsection 


