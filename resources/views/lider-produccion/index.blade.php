@extends('layouts.lider-produccion.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
        <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
    @endsection
    @section('content')        
        <div x-data="asignarProyecto">
            <div class="card"> 
                <div class="card-body">
                    <h3 class="mb-4">Asignar proyecto</h3>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="list-group">
                                <select x-on:change="Livewire.emit('productorElegido', $event.target.value)" x-model="productor" id="productor" class="form-control" size="20">
                                    @foreach ($productores as $productor)
                                        <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10">
                            {{-- Un solo componente: muestra al productor elegido en la lista --}}
                            @livewire('lider-produccion.asignar-proyecto')
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="mt-3">
            @livewire('lider-produccion.buscar-proyecto')
        </div>
    @endsection 
    @section('scripts')
        <script>
            function asignarProyecto (){ 
                return { productor: '' }
            }
            window.addEventListener('crm-aviso', function (e) {
                if (window.Swal) { Swal.fire('Hecho', e.detail.texto, 'success'); }
            });
        </script>
    @endsection