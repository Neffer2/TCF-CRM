
    <div class="col-12"> 
        @livewire('admin.generales.base-comercial-general')
    </div>
@endsection 
    @php
        // dd($filters);
    @endphp
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