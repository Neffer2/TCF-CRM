<?php

namespace App\Services;

use App\Models\PresupuestoProyecto;
use App\Models\ItemPresupuesto;
use App\Models\OcItem;


class ImportOcResolver {
    protected $presupuestoCache = [];

    public function resolverPresupuesto($codCc){
        if (!$codCc) {
            return null;
        }

        $codCc = trim($codCc);

        if (isset($this->presupuestoCache[$codCc])) {
            return $this->presupuestoCache[$codCc];
        }

        $presupuesto = PresupuestoProyecto::where('cod_cc', 'LIKE', $codCc . ' -%')->first();

        $this->presupuestoCache[$codCc] = $presupuesto;

        return $presupuesto;
    }

    public function resolveItem(PresupuestoProyecto $presupuesto, $itemNumero)
    {
        if (!is_numeric($itemNumero)) {
            return null;
        }

        $ordenBuscado = ((int) $itemNumero);

        return ItemPresupuesto::where('presupuesto_id', $presupuesto->id)
            ->where('num_item', $ordenBuscado)
            ->first();
    }

    public function validarDisponibilidad(ItemPresupuesto $item, $cantidadExcel, $valorTotalExcel)
    {
        $errores = [];

        $ocsExistentes = OcItem::where('item_id', $item->id)->get();

        $cantidadOcs = $ocsExistentes->pluck('oc_id')->unique()->count();
        if ($cantidadOcs >= 2) {
            $errores[] = "El item #{$item->orden} ya tiene {$cantidadOcs} órdenes de compra (máximo permitido: 2).";
        }

        $cantidadConsumida = $ocsExistentes->sum('cant_oc');
        $valorConsumido = $ocsExistentes->sum('vtotal_oc');

        $cantidadDisponible = $item->cantidad - $cantidadConsumida;
        $valorDisponible = $item->v_total - $valorConsumido;

        if ($cantidadExcel > $cantidadDisponible) {
            $errores[] = "Cantidad ({$cantidadExcel}) excede la disponible ({$cantidadDisponible}).";
        }

        if ($valorTotalExcel > $valorDisponible) {
            $errores[] = "Valor total ({$valorTotalExcel}) excede el disponible (" . number_format($valorDisponible, 2) . ").";
        }

        return $errores;
    }
}
