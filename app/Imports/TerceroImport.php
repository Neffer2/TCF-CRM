<?php
namespace App\Imports;

use App\Models\Tercero;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TerceroImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithMultipleSheets 
{
    /**
     * @param Collection $rows
     * Importa unicamente la primera hoja del archivo excel
     */
    public function sheets(): array
    {
        return [
            new TerceroImport()
        ];
    }

    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|numeric|unique:terceros',
            'correo' => 'required|email|unique:terceros',
            'telefono' => 'required|numeric|unique:terceros',
            'ciudad' => 'required|string'
        ])->validate();

        foreach ($rows as $row) 
        {   
            Tercero::create([
                'nombre' => $row['nombres'],
                'apellido' => $row['apellidos'],
                'cedula' => $row['cedula'],
                'correo' => $row['correo'],
                'telefono' => $row['telefono'],
                'ciudad' => $row['ciudad'],
                'estado' => 1
            ]);
        }
    } 
} 