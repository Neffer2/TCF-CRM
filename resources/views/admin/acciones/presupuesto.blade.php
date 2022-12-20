@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection 
  
@section('content')
    <div x-data="menu" class="row">
        <div class="col-lg-3 col-md-12">
            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill flex-column p-1" role="tablist">
                    <li x-on:click="Toggle('lista')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#profile-tabs-vertical" role="tab" aria-controls="preview" aria-selected="true">
                            Presupuestos 
                        </a>
                    </li>
                    <li x-on:click="Toggle('asignar')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#dashboard-tabs-vertical" role="tab" aria-controls="code" aria-selected="false">
                            Asignar
                        </a> 
                    </li>
                    <li x-on:click="Toggle('conf')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#payments-tabs-vertical" role="tab" aria-controls="code" aria-selected="false">
                            Configurar
                        </a>
                    </li> 
                </ul>
            </div>

            <hr>
            <div class="card-header" x-show="conf" x-transition x-cloak>
                @livewire('admin.presupuestos.nuevo-año')  
                <hr>
            </div>
        </div>

        {{-- Presupuestos list --}}
        <div class="col-md-9 col-sm-12" x-show="lista" x-transition x-cloak>
            <div class="card card-frame">
                <div class="card-header">
                    <div>
                        <h5 class="mb-0">Lista de presupuestos</h5> 
                    </div>
                    <hr>
                    <select class="form-control">
                        <option value="#">Comericial 1</option>
                        <option value="#">Comericial 2</option>
                        <option value="#">Comericial 3</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Año</label>
                                <select name="" id="" class="form-control">
                                    <option value="">2001</option>
                                    <option value="">2002</option>
                                </select>
                            </div>
                            <div class="author align-items-center">
                                <img src="./assets/img/kit/pro/team-2.jpg" alt="..." class="avatar shadow">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mes</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presupuesto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">Enero</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">5'000.000</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">Febrero</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">5'000.000</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">Marzo</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">5'000.000</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">Abril</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">5'000.000</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        {{-- Asignar --}}
        <div class="col-md-9 col-sm-12" x-show="asignar" x-transition x-cloak>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-frame">
                        <div class="card-header">
                            <div>
                                <h5 class="mb-0">Asignar</h5> 
                                <p class="text-sm mb-0">Asigna un presupuesto a cada uno de los miembros de tu equipo.</p>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-9">
                                    <select class="form-control">
                                        <option value="#">Comercial 1</option>
                                        <option value="#">Comercial 2</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control">
                                        <option value="#">Año 1</option>
                                        <option value="#">Año 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card card-body">
                            <form action="">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center">
                                        <label for="">Enero</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Febrero</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Marzo</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Abril</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Mayo</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Junio</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Julio</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Agosto</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Septiembre</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Octubre</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Noviembre</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex justify-content-center aling-items-center">
                                        <label for="">Diciembre</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                                <button class="btn bg-gradient-warning"> Guardar cambios </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>    

        {{-- conf --}}
        <div class="col-md-9 col-sm-12" x-show="conf" x-transition x-cloak>
            @livewire('admin.presupuestos.conf')
        </div> 
    </div>
@endsection 
@section('scripts')
    <script> 
        function menu (){
            return {
                lista: true,
                asignar: false,
                conf: false,
                Toggle(item){
                    if (item === "lista"){
                        this.lista = true
                        this.asignar = false
                        this.conf = false
                    }else if(item === "asignar"){
                        this.lista = false
                        this.asignar = true
                        this.conf = false
                    }else if(item === "conf"){
                        this.lista = false
                        this.asignar = false
                        this.conf = true
                    }
                }
            }
        }
    </script>
@endsection