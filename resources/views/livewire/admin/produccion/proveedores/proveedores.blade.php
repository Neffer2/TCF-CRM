<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Proveedores</h3> 
                    <p class="text-sm mb-0">Lista de proveedores.</p>
                </div>
                <div class="col-md-4">  
                    <label for="comercial">Buscar:</label>
                    <input type="text" wire:model="" class="form-control" placeholder="Centro de costos">
                </div>
                <div class="col-md-4">
                    <label for="filtro_fecha">Fecha:</label>
                    <select id="filtro_fecha" class="form-control" wire:model="">
                        <option value="asc">Seleccionar</option>
                        <option value="asc">M&aacute;s antiguos</option>
                        <option value="desc">M&aacute;s recientes</option>
                    </select> 
                </div>
                <div class="col-md-4">
                    <label for="estado">Estados:</label>
                    <select id="estado" class="form-control" wire:model="">
                        <option value="asc">Seleccionar</option>
                    </select> 
                </div>
            </div>
        </div> 
    </div>
</div>
