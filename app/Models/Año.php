<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Año extends Model
{
    use HasFactory;
    protected $table = "anos";

    protected $fillable = [
        'description'
    ];

    public function Meses (){
        return $this->hasMany(Mes::class, 'ano_id', 'id');
    }
}
