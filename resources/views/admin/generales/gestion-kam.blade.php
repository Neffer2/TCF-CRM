@extends('layouts.admin.main')
    @section('titulo', 'Gestión por KAM')
    @section('content')
        @livewire('admin.gestion-kam')
        @livewire('admin.detalle-kam')
    @endsection
