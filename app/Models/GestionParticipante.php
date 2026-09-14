<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Un comercial que participa en una gestión comercial, con su posición (1 = responsable) y porcentaje. */
class GestionParticipante extends Model
{
    protected $table = 'gestion_participantes';
    protected $fillable = ['gestion_id', 'user_id', 'posicion', 'porcentaje'];

    public function gestion()
    {
        return $this->belongsTo(GestionComercial::class, 'gestion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
