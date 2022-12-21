<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;

    protected $table = "presupuestos";

    protected $fillable = [
        'ano_id',
        'mes_id',
        'valor',
        'id_user'
    ]; 

    public function presupuesto_mes (){
        return $this->hasOne(Mes::class, 'id', 'mes_id');
    }
}
