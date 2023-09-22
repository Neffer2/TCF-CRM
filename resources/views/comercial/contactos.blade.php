@extends('layouts.comercial.main')
  @section('profile-card') 
    <div class="container">
      <div class="card shadow-lg">
        <div class="card-body text-center">
          <h4 class="fadeIn1 fadeInBottom"><b>CONTACTOS</b></h4>
        </div>
      </div> 
    </div>
  @endsection  
 
  @section('content')     
    <div class="card card-frame">
      <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">  
                  @livewire('com.gestion-comercial.contactos.nuevo-contacto')
                </div>
            </div>
            <div class="col-md-12">
              <div class="card"> 
                @livewire('com.gestion-comercial.contactos.lista-contactos')
              </div>
            </div>
        </div>        
      </div>
    </div>
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