<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturalInfo extends Model
{
    use HasFactory;
    protected $table = 'natural_info';

    protected $fillable = [
        'oc_id',
        'tercero_id', 
        'productor_id' 
    ];

    public function OrdenCompra(){
        return $this->belongsTo(OrdenCompra::class, 'oc_id', 'id');
    }

    public function tercero(){
        return $this->hasOne(Tercero::class, 'id', 'tercero_id');
    }
}
