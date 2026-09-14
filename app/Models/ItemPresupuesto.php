<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPresupuesto extends Model
{
    use HasFactory;

    protected $table = "items_presupuesto";

    /**
     * Ids de proveedor pendientes de sincronizar con la tabla pivote. Se
     * asignan antes de save()/update() y el evento `saved` los persiste,
     * así los componentes no necesitan conocer el momento exacto del guardado.
     */
    public $proveedoresPendientes = null;

    protected static function booted()
    {
        static::saved(function (ItemPresupuesto $item) {
            if (is_array($item->proveedoresPendientes)) {
                $ids = array_values(array_unique(array_filter(array_map('intval', $item->proveedoresPendientes))));
                $item->proveedores()->sync($ids);
                $item->proveedoresPendientes = null;
                $item->unsetRelation('proveedores');
            }
        });
    }

    // Proveedores del ítem (tabla pivote item_presupuesto_proveedor)
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'item_presupuesto_proveedor', 'item_presupuesto_id', 'proveedor_id');
    }

    /** @return int[] ids de los proveedores del ítem */
    public function proveedorIds(): array
    {
        return $this->proveedores->pluck('id')->map(function ($id) { return (int) $id; })->all();
    }

    public function proveedorPrincipal()
    {
        return $this->proveedores->first();
    }

    /**
     * Copia del ítem para historial_items_presupuesto. Incluye la clave
     * 'proveedor' serializada como antes, para que los reportes de
     * historial sigan leyéndola igual.
     */
    public function snapshotHistorial(): array
    {
        return $this->toArray() + ['proveedor' => serialize(array_map('strval', $this->proveedorIds()))];
    }

    public function mesDescription (){
        return $this->hasOne(Mes::class, 'id', 'mes');
    }

    public function consumidos(){
        return $this->hasMany(OcItem::class, 'item_id', 'id');
    }

    public function consumidos_anticipo(){
        return $this->hasMany(ItemAnticipo::class, 'item_id', 'id');
    }

    public function presto(){
        return $this->hasOne(PresupuestoProyecto::class, 'id', 'presupuesto_id');
    }

    public function displayItem(){
        foreach ($this->presto->presupuestoItems as $key => $item) {
            if ($this->id == $item->id){
                return $key+1;
            }
        }
    }
}
