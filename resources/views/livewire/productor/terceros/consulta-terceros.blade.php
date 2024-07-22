<div class="row gy-1">         
    <div class="row m-0 p-0 mb-3">
        <div class="col-md-12 mt-0">
            <h3 class="m-0">Portal trabajadores:</h3>
            <p class="text-sm mb-0">Consulta con tu n&uacute;mero de orden el estado de tu pago.</p>
        </div>    
        <div class="form-group col-md-4 mb-0">
            <label for="cedula">Buscar:</label> 
            <input id="cedula" type="text" wire:model="numOrden" class="form-control" placeholder="#ORDEN">
        </div> 
    </div>
    <div class="row m-0 p-0">
        @isset($orden)
            @livewire('productor.terceros.nuevo-personal', ['tercero' => $orden->naturalinfo->tercero], key($orden->id))
        @endisset
    </div>
</div>  
 