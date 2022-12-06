@extends('layouts.auth.main')
    @section('form')
        <form method="POST" action="{{ route('login') }}" role="form" class="text-start">
            @csrf
            @livewire('login-live') 
        </form>
    @endsection 


