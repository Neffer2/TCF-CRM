<?php

namespace App\Imports;

use App\Models\Helisa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel; 
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithValidation;

class HelisaContableImport implements ToModel, WithHeadingRow, WithCalculatedFormulas, WithValidation
{
    /**
    * @param array $row
    * 
    * @return \Illuminate\Database\Eloquent\Model|null
    */ 
    public function model(array $row)
    {   
        /* Cuenta: Bull o V2V */
        $comercial = $this->user_validate($row['comercial']); 
        if ($comercial == "ERROR"){
            echo "<b style='color: red;'>Éste usuario no existe, comnuníquese con el administrador<b>";
            dd($row);
        } 
        /* --- */

        if (isset($row['cuenta'])){
            /* Cuenta: Bull o V2V */
            $cuenta = $this->cuenta_validate($row['cuenta']);
            if ($cuenta == "ERROR"){
                echo "<b style='color: red;'>Ésta cuenta no es válida<b>";
                dd($row);
            } 
            /* --- */
        }else {
            $cuenta = 1;
        }

        return new Helisa([
            'fecha' => Date::excelToDateTimeObject($row['fecha']),
            'tipo_doc' => $row['tipo_doc'],
            'num_doc' => $row['numero_doc'], 
            'concepto' => $row['concepto'],
            'identidad' => $row['identidad'],
            'nom_tercero' => $row['nombre_del_tercero'], 
            'centro' => $row['centro_costo'],
            'nom_centro_costo' => $row['nombre_centro_de_costo'],
            'debito' => $row['debito'],
            'credito' => $row['credito'],
            'comercial' => $comercial,
            'id_cuenta' => $cuenta, 
            'participacion' => $row['participacion'],
            'base_factura' => $row['base_factura'],
            'mes' => $row['mes'],
            'año' => $row['ano'],
            'comision' => $row['comision'] 
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required'],
            'tipo_doc' => ['required'],
            'numero_doc' => ['required'],
            'concepto' => ['required'],
            'identidad' => ['required'],
            'nombre_del_tercero' => ['required'],
            'centro_costo' => ['required'],
            'nombre_centro_de_costo' => ['required'],
            'comercial' => ['required'],
            'participacion' => ['required'],
            'base_factura' => ['required'],
            'mes' => ['required'],
            'ano' => ['required'],
            'comision' => ['required']
        ];
    }

    public function user_validate($user){
        $user = User::select('id', 'name')->where('name', $user)->first();
        if ($user){
            return $user->id;
        }
        return "ERROR"; 
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
 