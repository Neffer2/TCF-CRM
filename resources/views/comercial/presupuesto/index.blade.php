@extends('layouts.comercial.presupuesto')  
@section('nav-hidden')
    g-sidenav-hidden
@endsection
@section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            {{-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div> --}}
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection
@section('content')      
    @livewire("com.presupuesto.presupuesto", ['id_gestion' => $id_gestion])
@endsection 

@section('scripts')  
    <script>
        
    </script>
@endsection    