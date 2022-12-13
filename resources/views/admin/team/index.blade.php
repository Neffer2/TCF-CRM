@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection
    @section('profile-card') 
        <div class="card shadow-lg mx-4 card-profile-bottom mt-4">
            <div class="card-body p-3">
                <div class="row gx-4">
                    <div class="col-auto">
                        @php
                            $aux = str_replace('public/', '', Auth::user()->avatar);    
                        @endphp
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset("storage/$aux") }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                            <p class="mb-0 font-weight-bold text-sm">{{ Auth::user()->user_rol->description }}</p> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('content')
        <div x-data="menuTeam">
            <div class="col-12"> 
                <div class="card overflow-scroll">
                    <div class="card-body d-flex">
                        <div x-on:click="Toggle('new-item')" class="col-lg-1 col-md-2 col-sm-3 col-4 text-center">
                            <a href="javascript:;" class="avatar avatar-lg border-1 rounded-circle bg-gradient-warning"><i class="fas fa-plus text-white"></i></a>
                            <p class="mb-0 text-sm" style="margin-top:6px;">Nuevo</p>
                        </div>

                        @foreach ($listUsers as $user)
                            <div x-on:click="Toggle('user-item', {{ $user->id }})" class="col-lg-1 col-md-2 col-sm-3 col-4 text-center">
                                @php
                                    $aux = str_replace('public/', '', $user->avatar);
                                @endphp
                                <a href="javascript:;" class="avatar avatar-lg rounded-circle border border-primary"><img alt="Image placeholder" class="p-1" src="{{ asset("storage/$aux") }}"></a>
                                <p class="mb-0 text-sm">{!! strtok($user->name, ' ') !!}</p>
                            </div>
                        @endforeach  
                    </div>
                </div>
            </div>

            <div class="col-12" x-show="toggle" x-transition x-cloak>
                <div class="col-lg-12 col-12 mx-auto">
                    <div class="card card-body mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <a class="btn bg-gradient-warning" data-bs-toggle="collapse" href="#base-user" role="button" aria-expanded="false" aria-controls="base-user">
                                    Base comercial
                                </a>
                                <a class="btn bg-gradient-primary" data-bs-toggle="collapse" href="#info-user" role="button" aria-expanded="false" aria-controls="info-user">
                                    Informaci&oacute;n
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="collapse mb-3" id="base-user">
                                @livewire('admin.base-com-team', [0])
                            </div> 
                            <div class="collapse" id="info-user">
                                @livewire('admin.user-info', [0]) 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-12" x-show="!toggle" x-transition x-cloak>
                <div class="col-lg-12 col-12 mx-auto">
                    <div class="card card-body mt-4">
                        <h6 class="mb-0">Nuevo miembro</h6>
                        <p class="text-sm mb-0">Agrega un nuevo miembro para tu equipo.</p>
                        <hr class="horizontal dark my-3">
                        @livewire('admin.new-team')   
                    </div>
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

            function menuTeam (item){
                return {
                    toggle: false,
                    activeItem: 'new-item',
                    Toggle (item, user_id = null){
                        if (!(item === this.activeItem)){
                            this.toggle = !this.toggle; 
                            this.activeItem = item;   
                        }

                        // Si el item es del usuaario, entonces envía la señal con el ID
                        if (item == 'user-item'){
                            Livewire.emit('live-base', [user_id]);
                            Livewire.emit('live-info', [user_id]);
                        }
                    }
                }
            }
        </script>
    @endsection 