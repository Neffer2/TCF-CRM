<?php

namespace App\Imports;

use App\Models\Helisa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel; 
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class HelisaContableImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    /**
    * @param array $row
    * 
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        $row['comercial'] = $this->user_validate($row['comercial']);
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
            'comercial' => $row['comercial'],
            // 'comercial' => 1,
            'participacion' => $row['participacion'],
            'base_factura' => $row['base_factura'],
            'mes' => $row['mes'],
            'año' => $row['ano'],
            'comision' => $row['comision'] 
        ]);
    }

    public function user_validate($user){
        $user = User::select('id', 'name')->where('name', $user)->first();
        if ($user){
            return $user->id;
        }
        return "ERROR";
    }
}
 