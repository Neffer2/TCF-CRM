<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model 
{
    use HasFactory;
    protected $table = "ordenes_compra";
    protected $fillable = [
        'tipo_oc',
        'estado_id',
        'presupuesto_id', 
        'proveedor_id'  
    ];

    public function ordenItems(){
        return $this->hasMany(OcItem::class, 'oc_id', 'id'); 
    } 
 
    public function estado_oc(){ 
        return $this->hasOne(EstadoOrdenesCompra::class, 'id', 'estado_id');
    }

    public function tipo(){
        return $this->hasOne(TipoOrdenCompra::class, 'id', 'tipo_oc');
    }

    public function presupuesto(){ 
        return $this->hasOne(PresupuestoProyecto::class, 'id', 'presupuesto_id');
    }

    public function proveedor(){
        return $this->hasOne(Proveedor::class, 'id', 'proveedor_id');
    }

    public function naturalInfo(){
        return $this->hasOne(NaturalInfo::class, 'oc_id', 'id');
    }
}   
