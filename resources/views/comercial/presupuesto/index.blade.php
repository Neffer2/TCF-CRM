@extends('layouts.comercial.presupuesto')  
@section('nav-hidden')
    g-sidenav-hidden
@endsection
@section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div> --}}
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