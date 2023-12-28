@extends('layouts.comercial.main')
  @section('content')     
    <div class="row" x-show="!toggle[1]" x-transition>
      <div class="col-md-12 pb-4">
        @livewire('com.helisa.new-registro')     
      </div>
      <div class="col-md-12">
        @livewire('com.helisa.helisa-list')
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
              console.log(this.form_project);
              this.form_project = !this.form_project;
            }
          }
        }
    </script>
  @endsection   