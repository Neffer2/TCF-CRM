@extends('layouts.comercial.main')
  @section('content')     
    @livewire('admin.gestion-comercial.actualizaciones-presto', ['rol' => Auth::user()->rol])
  @endsection
 
  {{-- @section('profile-card') 
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
  @endsection  --}}

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