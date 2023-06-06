@extends('layouts.comercial.main')
  @section('content')     
    @livewire('admin.gestion-comercial.actualizaciones-presto', ['rol' => Auth::user()->rol])
  @endsection
  @section('scripts')  
  @endsection    