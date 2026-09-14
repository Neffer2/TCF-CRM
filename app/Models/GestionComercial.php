<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionComercial extends Model
{
    use HasFactory;

    protected $table = 'gestion_comercial';

    /**
     * Participantes pendientes de guardar: [[posicion, user_id, porcentaje], ...].
     * Se asignan antes de save()/update() y el evento `saved` los persiste en
     * gestion_participantes (reemplaza a las columnas comercial_2..4 / porcentaje_N).
     */
    public $participantesPendientes = null;

    protected static function booted()
    {
        static::saved(function (GestionComercial $gestion) {
            if (is_array($gestion->participantesPendientes)) {
                $gestion->guardarParticipantes($gestion->participantesPendientes);
                $gestion->participantesPendientes = null;
            }
        });
    }

    public function contacto (){
        return $this->hasOne(Contacto::class, 'id', 'id_contacto');
    }

    // Comercial responsable (dueño) de la gestión
    public function comercial (){
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    // Todos los comerciales que participan, ordenados por posición (1 = responsable)
    public function participantes (){
        return $this->hasMany(GestionParticipante::class, 'gestion_id', 'id')->orderBy('posicion');
    }

    public function participante(int $posicion)
    {
        return $this->participantes->firstWhere('posicion', $posicion);
    }

    public function participanteUserId(int $posicion)
    {
        return optional($this->participante($posicion))->user_id;
    }

    public function participantePorcentaje(int $posicion)
    {
        return optional($this->participante($posicion))->porcentaje;
    }

    /**
     * Reemplaza los participantes. $lista = [[posicion, user_id, porcentaje], ...].
     * Se ignoran las posiciones sin comercial (antes quedaban "fantasmas") y un
     * mismo comercial no se repite. La posición 1 es siempre el responsable.
     */
    public function guardarParticipantes(array $lista): void
    {
        $this->participantes()->delete();
        $vistos = [];
        foreach ($lista as [$posicion, $userId, $porcentaje]) {
            if ($posicion == 1 && !$userId) { $userId = $this->id_user; }
            if (!$userId || isset($vistos[$userId])) { continue; }
            $vistos[$userId] = true;
            GestionParticipante::create([
                'gestion_id' => $this->id,
                'user_id' => $userId,
                'posicion' => $posicion,
                'porcentaje' => $porcentaje !== null && $porcentaje !== '' ? round((float) $porcentaje, 2) : 0,
            ]);
        }
        $this->unsetRelation('participantes');
    }

    public function presupuesto (){
        return $this->hasOne(PresupuestoProyecto::class, 'id_gestion', 'id');
    }

    public function baseComercial (){
        return $this->hasMany(Base_comercial::class, 'id_gestion', 'id');
    }
}
