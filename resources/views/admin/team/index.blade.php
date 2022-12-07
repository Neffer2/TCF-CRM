@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection
    @section('profile-card') 
        <div class="card shadow-lg mx-4 card-profile-bottom ">
            <div class="card-body p-3">
                <div class="row gx-4">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="../../../assets/img/team-1.jpg" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">Sayo Kravits</h5>
                            <p class="mb-0 font-weight-bold text-sm">Public Relations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('content')
        <div class="col-12"> 
            <div class="card overflow-scroll">
                <div class="card-body d-flex">
                    <div class="col-lg-1 col-md-2 col-sm-3 col-4 text-center">
                        <a href="javascript:;" class="avatar avatar-lg border-1 rounded-circle bg-gradient-primary"><i class="fas fa-plus text-white"></i></a>
                        <p class="mb-0 text-sm" style="margin-top:6px;">Nuevo</p>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-3 col-4 text-center">
                        <a href="javascript:;" class="avatar avatar-lg rounded-circle border border-primary"><img alt="Image placeholder" class="p-1" src="../../../assets/img/team-1.jpg"></a>
                        <p class="mb-0 text-sm">Abbie W</p>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-3 col-4 text-center">
                        <a href="javascript:;" class="avatar avatar-lg rounded-circle border border-primary"><img alt="Image placeholder" class="p-1" src="../../../assets/img/team-2.jpg"></a>
                        <p class="mb-0 text-sm">Boris U</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <h5 class="mb-0">Base comercial</h5>
                </div>
                    <div class="table-responsive">
                        <table class="table table-flush" id="datatable-search">
                            <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Proyecto</th>
                                <th>COD_CC</th>
                                <th>Valor</th>
                                <th>Estado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                            </tr>
                            </thead>
                            <tbody>
                                @livewire('base-list')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-12">
            <div class="col-lg-12 col-12 mx-auto">
                <div class="card card-body mt-4">
                    <h6 class="mb-0">Nuevo miembro</h6>
                    <p class="text-sm mb-0">Agrega un nuevo miembro para tu equipo.</p>
                    <hr class="horizontal dark my-3">
                    @livewire('new-team') 
                </div>
            </div>
        </div>
    @endsection 

    @section('scripts-imports')
        <script src="{{ asset('assets/js/plugins/datatables.js') }}"></script>
    @endsection
    @section('scripts')
        <script>
            const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
            searchable: true,
            fixedHeight: true
            });
        </script>
    @endsection 