<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\TieneHistorialEstados;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GestionComercial extends Model
{
    use HasFactory, SoftDeletes, TieneHistorialEstados;

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
        // Papelera: el presupuesto y el registro de base comercial acompañan a la gestión al borrarla y al restaurarla.
        static::deleting(function (GestionComercial $gestion) {
            if ($gestion->isForceDeleting()) { return; }
            PresupuestoProyecto::where('id_gestion', $gestion->id)->delete();
            Base_comercial::where('id_gestion', $gestion->id)->delete();
        });
        static::restoring(function (GestionComercial $gestion) {
            PresupuestoProyecto::onlyTrashed()->where('id_gestion', $gestion->id)->restore();
            Base_comercial::onlyTrashed()->where('id_gestion', $gestion->id)->restore();
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
