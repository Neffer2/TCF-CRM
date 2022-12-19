<div class="card card-body">
    <div class="col-md-12">
        <h6 class="mb-0">Nuevo año</h5> 
        <p class="text-sm mb-0">Generar un nuevo año.</p>
    </div>
    <form wire:submit.prevent="nuevo_año">  
        <div class="form-group"> 
            <label for="">Año</label>
            <input type="text" class="form-control @error('description') is-invalid @enderror" wire:model="description">
            @error('description') 
                <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
            @enderror
        </div> 
        <button class="btn bg-gradient-warning" @if (!$enableSubmit) disabled @endif>Crear</button>
    </form>
    @if (session('success'))
        <script>
            Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
            );
        </script> 
    @endif
</div>