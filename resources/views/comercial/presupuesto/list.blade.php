@extends('layouts.comercial.main')
  @section('nav-hidden')
      g-sidenav-hidden
  @endsection
  @section('content')     
    @livewire('admin.gestion-comercial.actualizaciones-presto', ['rol' => Auth::user()->rol])
  @endsection
  @section('scripts')  
  @endsection     