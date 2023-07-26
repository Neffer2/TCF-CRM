@extends('layouts.comercial.main')
  @section('nav-hidden')
      g-sidenav-hidden
  @endsection
  @section('profile-card') 
    <div class="card shadow-lg mx-4 card-profile-bottom mt-5">
      <div class="card-body p-3">
        <div class="row gx-4">
          <div class="col-auto"> 
            <div class="avatar avatar-xl position-relative">
              @php
                $aux = str_replace('public/', '', Auth::user()->avatar);
              @endphp
              <img src="{{ asset("storage/$aux") }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                {{ Auth::user()->name }}
              </h5>
                <p class="mb-0 font-weight-bold text-sm">
                {{ Auth::user()->email }}
                </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endsection 

  @section('content')    
    <div class="card card-frame">
      <div class="card-body">
        <div class="d-flex align-items-baseline" x-data="{ collapse: true}" x-cloak>
          <button class="btn bg-gradient-warning me-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" x-on:click="collapse = !collapse">
            <i class="fas fa-plus text-white" aria-hidden="true" x-show="collapse"></i>
            <i class="fa-solid fa-minus text-white" aria-hidden="true" x-cloak x-show="!collapse"></i>
          </button> 
          <h5>
            <b>Nuevo prospecto</b>
          </h5>
        </div>
        <script src="https://kit.fontawesome.com/15bc5276a1.js" crossorigin="anonymous"></script>
        <div class="collapse" id="collapseExample">
          <div class="card card-body mb-3">
            @livewire('com.gestion-comercial.nuevo-prospecto')       
          </div>          
        </div>
        @livewire('com.gestion-comercial.gestion-list')
      </div>
    </div>
  @endsection
 
  @section('scripts')  
    <script>
        function menu(){ 
          return {
            toggle: [true, false],
              Toggle (index){
                this.toggle.forEach((elem, index) => {
                  this.toggle[index] = false;
                });
                this.toggle[index.detail] = true;
              }
          }
        }

        function new_project(){
          return {
            form_project: false,
            show_form(){
              this.form_project = !this.form_project;
            }
          }
        }
    </script>
  @endsection   