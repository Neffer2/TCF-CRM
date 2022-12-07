@extends('layouts.comercial.main')
  @section('content')
  <div class="row mt-4">
    <div class="col-12">
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