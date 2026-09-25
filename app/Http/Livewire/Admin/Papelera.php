<?php

namespace App\Http\Livewire\Admin;

use App\Models\Base_comercial;
use App\Models\clientes;
use App\Models\Contacto;
use App\Models\GestionComercial;
use App\Models\Helisa;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Tercero;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Papelera: lo que se "eliminó" desde las pantallas sigue en la base con deleted_at (borrado lógico).
 * Desde aquí Administración lo ve y lo restaura. Nada se borra de verdad.
 */
class Papelera extends Component
{
    public $tipo = 'gestiones';
    public $buscar = '';
    public $mensaje = '';

    const TIPOS = [
        'gestiones' => ['modelo' => GestionComercial::class, 'titulo' => 'Proyectos (gestiones comerciales)', 'campos' => ['nom_proyecto_cot', 'nom_proyecto_prop'], 'extra' => 'comercial.name', 'nota' => 'Al restaurar vuelven también su presupuesto y su registro de base comercial.'],
            'clientes' => ['modelo' => clientes::class, 'titulo' => 'Clientes', 'campos' => ['nombre', 'nit'], 'extra' => null, 'nota' => ''],
        'contactos' => ['modelo' => Contacto::class, 'titulo' => 'Contactos', 'campos' => ['nombre', 'apellido', 'correo'], 'extra' => null, 'nota' => ''],
        'proveedores' => ['modelo' => Proveedor::class, 'titulo' => 'Proveedores', 'campos' => ['tercero', 'documento'], 'extra' => null, 'nota' => 'Un proveedor con el mismo documento no se puede crear de nuevo mientras esté aquí: restáuralo.'],
        'terceros' => ['modelo' => Tercero::class, 'titulo' => 'Personal (terceros)', 'campos' => ['nombre', 'apellido', 'cedula'], 'extra' => null, 'nota' => ''],
        'ordenes' => ['modelo' => OrdenCompra::class, 'titulo' => 'Órdenes de compra (borradores)', 'campos' => ['id', 'cod_oc'], 'extra' => 'proveedor.tercero', 'nota' => 'Solo llegan aquí borradores o nóminas rechazadas; las órdenes aprobadas se anulan, no se borran.'],
        'base' => ['modelo' => Base_comercial::class, 'titulo' => 'Base comercial', 'campos' => ['nom_cliente', 'nom_proyecto', 'cod_cc'], 'extra' => null, 'nota' => ''],
        'helisa' => ['modelo' => Helisa::class, 'titulo' => 'Registros Helisa', 'campos' => ['centro', 'num_doc', 'nom_tercero'], 'extra' => null, 'nota' => ''],
    ];

    public function updatingTipo() { $this->mensaje = ''; }

    public function render()
    {
        abort_unless((int) Auth::user()->rol === 1, 403);
        $def = self::TIPOS[$this->tipo] ?? self::TIPOS['gestiones'];
        $q = $def['modelo']::onlyTrashed()->orderByDesc('deleted_at');
        $texto = trim($this->buscar);
        if ($texto !== '') {
            $q->where(function ($w) use ($def, $texto) {
                foreach ($def['campos'] as $c) { $w->orWhere($c, 'like', '%'.$texto.'%'); }
            });
        }
        $filas = $q->limit(100)->get()->map(function ($f) use ($def) {
            $partes = [];
            foreach ($def['campos'] as $c) { if ($f->{$c} !== null && $f->{$c} !== '') { $partes[] = $f->{$c}; } }
            if ($def['extra'] && ($v = data_get($f, $def['extra']))) { $partes[] = $v; }
            return ['id' => $f->id, 'resumen' => implode(' · ', $partes) ?: ('#'.$f->id), 'borrado' => $f->deleted_at];
        });
        $conteos = collect(self::TIPOS)->map(function ($d) { return $d['modelo']::onlyTrashed()->count(); });
        return view('livewire.admin.papelera', ['filas' => $filas, 'def' => $def, 'conteos' => $conteos, 'tipos' => self::TIPOS]);
    }

    public function restaurar($id)
    {
        abort_unless((int) Auth::user()->rol === 1, 403);
        $def = self::TIPOS[$this->tipo] ?? null;
        abort_unless($def, 404);
        $registro = $def['modelo']::onlyTrashed()->find((int) $id);
        abort_unless($registro, 404);
        $registro->restore();
        $this->mensaje = 'Restaurado: vuelve a verse en sus pantallas.';
    }
}
