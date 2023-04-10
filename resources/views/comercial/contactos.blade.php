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