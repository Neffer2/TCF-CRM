@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
        {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
    @endsection
    @section('content')
        <div class="col-12"> 
            <div class="col-lg-12 col-12 mx-auto">
                <div class="card card-body mt-4">
                    <h6 class="mb-0">Actualizar perfil</h6>
                    <p class="text-sm mb-0">Mant&eacute;n tus datos siempre actualizados.</p>
                    <hr class="horizontal dark my-3"> 
                    @livewire('update-profile')
                </div>
            </div>
        </div>
    @endsection  