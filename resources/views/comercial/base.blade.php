@extends('layouts.comercial.main')
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
    <div x-data="menu" @fire-toggle.window="Toggle"> 
 
      <div class="row mt-4" x-show="!toggle[1]" x-transition> 
        {{-- DEPRECATED --}}
        {{-- @livewire('com.new-proyecto') --}}
        @livewire('com.base-list', ['user_id' => Auth::user()->id])  
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