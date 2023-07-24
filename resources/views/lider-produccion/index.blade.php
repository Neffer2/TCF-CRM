@extends('layouts.lider-produccion.main')
    @section('nav-hidden')
        g-sidenav-hidden
    @endsection
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
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
                                <select x-on:change="Open" x-model="productor" id="productor" class="form-control" size="10">
                                    @foreach ($productores as $productor)
                                        <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10">
                            @foreach ($productores as $productor)
                                <div id="show{{ $productor->id }}" x-show="false" x-cloak>
                                    @livewire('lider-produccion.asignar-proyecto', ['id_productor' => $productor->id])                             
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
            function asignarProyecto (){ 
                return {
                    productor,
                    prevComponente: null,
                    Open(){
                        this.Close(this.prevComponente);
                        let componente = document.getElementById('show'+productor.value);
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