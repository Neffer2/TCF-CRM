@extends('layouts.auth.main')
@section('auth-title')
    Iniciar sesi&oacute;n
@endsection
    @section('form')
        <form method="POST" action="{{ route('login') }}" role="form" class="text-start">
            @csrf
            @livewire('login-live') 
        </form>
    @endsection 


