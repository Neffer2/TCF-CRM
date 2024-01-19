<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProveedor extends Model
{
    use HasFactory;
    protected $table = 'categorias_proveedor';

    public function proveedores(){
        return $this->hasMany(Proveedor::class, 'categoria_id', 'id');
    }
}
