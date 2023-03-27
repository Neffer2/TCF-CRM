<?php

namespace App\Imports;

use App\Models\Base_comercial;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithValidation;

class BaseComercialImport implements ToModel, WithHeadingRow, WithCalculatedFormulas, WithValidation
{
    /** 
    * @param array $row
    * 
    * @return \Illuminate\Database\Eloquent\Model|null
    */ 
    public function model(array $row)    
    {   
        // Éste código es compatible con el nuevo y antiguo formato.
        // El formato establecido sigue siedo el mismo. Es decir, el antiguo.

        /* id estado converter */
        $estado = $this->estado_validate($row['estado']);
        if ($estado == "ERROR"){
            echo "<b style='color: red;'>Éste estado no es válido<b>";
            dd($row);
        } 
        /* --- */

        /* Cuenta: Bull o V2V */
        if (isset($row['cuenta'])){
            $cuenta = $this->cuenta_validate($row['cuenta']);
            if ($cuenta == "ERROR"){
                echo "<b style='color: red;'>Ésta cuenta no es válida<b>";
                dd($row);
            } 
        }else {
            $cuenta = 1;
        }
        
        /* --- */

        return new Base_comercial([ 
            'fecha' => Date::excelToDateTimeObject($row['fecha']),
            'nom_cliente' => $row['cliente'],
            'nom_proyecto' => $row['nom_proy'], 
            'cod_cc' => $row['cod_cc'],
            'valor_proyecto' => $row['vr_proy'],
            'com_1' => $row['com1'],
            'com_2' => $row['com2'],
            'com_3' => $row['com3'],
            'id_estado' => $estado,
            'id_cuenta' => $cuenta, 
            'fecha_inicio' => Date::excelToDateTimeObject($row['f_inicio']),
            'dura_mes' => Date::excelToDateTimeObject($row['dura_mes']), 
            'id_user' => Auth::user()->id,
        ]); 
    } 
 
    public function rules(): array
    {
        return [
            'fecha' => ['required'],
            'vr_proy' => ['numeric'],
        ];
    }

    public function estado_validate ($estado){
        switch ($estado){
            case "CERRADO":
                $estado = 1;
                break;
            case "COTIZACION":
                $estado = 2;
                break;
            case "EJECUCIONXFACTURAR":
                $estado = 3;
                break;
            case "PERDIDO":
                $estado = 4;
                break;
            case "PROPUESTA":
                $estado = 5;
                break; 
            case "VENTA":
                $estado = 6;
                break;
            case "VENTAEJECUCION":
                $estado = 7;
                break;
            default: 
                $estado = "ERROR";
            break;
        }
        return $estado;
    }

    public function cuenta_validate ($cuenta){
        switch ($cuenta){
            case "BULL MARKETING":
                $cuenta = 1;
                break;
            case "V2V":
                $cuenta = 2;
                break;
            case "":
                $cuenta = 1;
                break;
        }
        return $cuenta;
    }
}
