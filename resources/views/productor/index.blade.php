@extends('layouts.productor.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
        <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
    @endsection
    @section('content')
        <div x-data="proyectos">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Proyectos</h3>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="list-group">
                                <select x-on:change="Open" x-model="proyecto" id="proyecto" class="form-control" size="31" style="overflow-x: auto;">
                                    @foreach ($proyectos as $proyecto)
                                        <option value="{{ $proyecto->id }}">{{ $proyecto->cod_cc }} - {{ $proyecto->gestion->nom_proyecto_cot }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10">
                            @foreach ($proyectos as $proyecto)
                                <div id="show{{ $proyecto->id }}" x-show="false" x-cloak>
                                    <div class="card">
                                        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3 class="mb-0">{{ $proyecto->gestion->nom_proyecto_cot }}</h3>
                                                    <p class="text-sm mb-0">Centro de costos: {{ $proyecto->cod_cc }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @livewire('productor.solicitar-recursos', ['id_presupuesto' => $proyecto->id])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script>
            function proyectos (){
                return {
                    proyecto,
                    prevComponente: null,
                    Open(){
                        this.Close(this.prevComponente);
                        let componente = document.getElementById('show'+proyecto.value);
                        this.prevComponente = (componente != this.prevComponente) ? componente : this.prevComponente;
                        componente.style.display = 'block';
                    },
                    Close(compoente){
                        if (compoente){
                            compoente.style.display = 'none';
                        }
                    }
                }
            }
        </script>
    @endsection
