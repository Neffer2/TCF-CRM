<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Helisa extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'helisa';

    protected $fillable = [
        'fecha',
        'tipo_doc',  
        'num_doc',
        'concepto',
        'identidad',  
        'nom_tercero',
        'centro',
        'nom_centro_costo',
        'debito',
        'credito',
        'porcentaje',
        'comercial',
        'id_cuenta',
        'participacion',
        'base_factura',
        'mes',
        'año',
        'comision'
    ]; 

    public function comercial_user (){
        return $this->hasOne(User::class, 'id', 'comercial');
    } 

    /* Cuenta (Bull o V2V) */
    public function cuenta (){
        return $this->hasOne(Cuenta::class, 'id', 'id_cuenta');
    }
}
