@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection
@section('content')
    <div class="col-12"> 
        <div class="col-lg-12 col-12 mx-auto">
            <div class="card card-body mt-4">
                <h6 class="mb-0">Base comercial general</h6>
                <p class="text-sm mb-0">Aqu&iacute; encontrar&aacute;s toda la base comercial almacenada en sistema.</p>
                <hr class="horizontal dark my-3"> 
                    @livewire('admin.base-comercial')
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