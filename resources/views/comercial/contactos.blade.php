@extends('layouts.comercial.main')
  @section('content')     
    <div class="card">
      <div class="card-header p-0 px-3 mt-3 position-relative z-index-1">
        <h3 class="mb-0">Contactos</h3> 
        <p class="text-sm mb-0">Crea y gestiona tus contactos.</p>
      </div>
      <div class="row px-3 mt-1">
          <div class="col-md-12 mb-3">  
            @livewire('com.gestion-comercial.contactos.nuevo-contacto')
          </div> 
          <div class="col-md-12">
            @livewire('com.gestion-comercial.contactos.lista-contactos')
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