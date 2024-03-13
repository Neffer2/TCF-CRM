@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection
@section('content')
    <div class="col-12"> 
        @livewire('admin.generales.base-comercial-general', ['requested_filters' => $filtros])
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