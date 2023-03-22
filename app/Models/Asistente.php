<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistente extends Model
{
    use HasFactory;
    protected $table = "asistentes";

    protected $fillable = [
        'asistente_id',
        'comercial_id'
    ];

    public function comercial (){
        return $this->hasOne(User::class, 'id', 'comercial_id');
    }
}
