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
        try {
            Validator::make($rows->toArray(), [
                '*.nombres' => 'required|string|max:255',
                '*.apellidos' => 'required|string|max:255',
                '*.cedula' => 'required|numeric|unique:terceros',
                '*.correo' => 'required|email|unique:terceros',
                '*.telefono' => 'required|numeric|unique:terceros',
                '*.ciudad' => 'required|string'
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

            return redirect()->route('personal')->with('success', 'Personal importado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Personaliza los mensajes de error
            $customErrors = [];
            foreach ($e->errors() as $key => $messages) {
                // Extrae el índice de la fila y el campo
                preg_match('/\d+/', $key, $matches);
                $rowIndex = isset($matches[0]) ? $matches[0] + 2 : 'desconocida'; // Suma 1 porque las filas empiezan en 0
                $field = explode('.', $key)[1] ?? 'campo';

                // Reemplaza el mensaje con un formato personalizado
                foreach ($messages as $message) {
                    $customErrors[] = str_replace($key, "$field de la fila $rowIndex", $message);
                }

                session()->flash('import_error', $customErrors);
            }
        }
    }
}
