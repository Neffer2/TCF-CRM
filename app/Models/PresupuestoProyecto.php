<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\TieneHistorialEstados;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresupuestoProyecto extends Model
{ 
    use HasFactory, SoftDeletes, TieneHistorialEstados;
    protected $table = "presupuesto_proyecto";
    protected $fillable = [
        'notificacion_actualizacion'
    ];

 
    /**
     * Código puro del centro de costos a partir de lo digitado en cod_cc ("44230702 POSTOBON…", "74230102 - Proyecto"):
     * el primer token, de 6 a 12 caracteres alfanuméricos con al menos 4 dígitos. null si no parece un código.
     */
    public static function codigoPuro(?string $codCc): ?string
    {
        // Se parte por espacios, tabulaciones y guiones ("08230801-TROPAS", "NOMBRE 44231013 PH") y se toma el primer token que parezca código.
        foreach (preg_split('/[\s\-]+/', strtoupper(trim((string) $codCc))) as $token) {
            if (preg_match('/^[A-Z0-9]{6,12}$/', $token) && preg_match_all('/[0-9]/', $token) >= 4) { return $token; }
        }
        return null;
    }

    /** Al guardar cod_cc se llena centro_costos (el código puro, el que usa Helisa). */
    public function setCodCcAttribute($valor)
    {
        $this->attributes['cod_cc'] = $valor;
        $this->attributes['centro_costos'] = self::codigoPuro($valor);
    }

    public function gestion(){ 
        return $this->hasOne(GestionComercial::class, 'id', 'id_gestion'); 
    } 

    public function baseComercial(){ 
        return $this->hasOne(Base_comercial::class, 'id_gestion', 'id_gestion'); 
    } 
   
    public function estado(){
        return $this->hasOne(EstadosPresupuesto::class, 'id', 'estado_id');
    }
 
    public function presupuestoItems(){
        return $this->hasMany(ItemPresupuesto::class, 'presupuesto_id', 'id');
    } 

    public function ordenesCompra(){
        return $this->hasMany(OrdenCompra::class, 'presupuesto_id', 'id'); 
    }

    public function productor_info(){
        return $this->hasOne(User::class, 'id', 'productor');
    }
} 
