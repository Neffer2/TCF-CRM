<?php

namespace Tests\Feature;

use App\Http\Livewire\Productor\Terceros\NuevoPersonal;
use App\Models\Tercero;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ActualizarTerceroTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            foreach (['nombre', 'apellido', 'cedula', 'correo', 'telefono', 'ciudad',
                'servicio', 'banco', 'num_rut', 'tipo_cuenta', 'num_cuenta',
                'copia_cedula', 'cert_bancaria', 'rut', 'planilla_aportes'] as $campo) {
                $table->string($campo)->nullable();
            }
            $table->integer('estado')->default(1);
            $table->timestamps();
        });

        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn((object) ['rol' => 1]);
    }

    private function componente(): NuevoPersonal
    {
        $tercero = Tercero::create([
            'nombre' => 'Ana', 'apellido' => 'Perez', 'cedula' => '123456',
            'correo' => 'ana@example.com', 'telefono' => '3001234567',
            'ciudad' => 'BOGOTA D.C', 'servicio' => 'Logistica', 'estado' => 1,
        ]);
        $tercero->rut = 'public/ruts/actual.pdf';
        $tercero->planilla_aportes = 'public/planillas_aportes/actual.pdf';
        $tercero->save();

        $componente = new NuevoPersonal();
        $componente->tercero = $tercero;
        $componente->fillForm();

        return $componente;
    }

    public function test_actualiza_conservando_archivos_y_rechaza_duplicados_de_otro_tercero()
    {
        $componente = $this->componente();
        $componente->nombre = 'Ana Maria';
        $componente->correo = ' ana@example.com ';
        $componente->actualizarTercero();

        $tercero = $componente->tercero->fresh();
        $this->assertSame('Ana Maria', $tercero->nombre);
        $this->assertSame('ana@example.com', $tercero->correo);
        $this->assertSame('public/ruts/actual.pdf', $tercero->rut);
        $this->assertSame('public/planillas_aportes/actual.pdf', $tercero->planilla_aportes);
        Tercero::create(['correo' => 'otro@example.com']);
        $componente->correo = 'otro@example.com';
        $componente->nombre = 'Nombre sin guardar';

        try {
            $componente->actualizarTercero();
            $this->fail('Se esperaba un error de validación.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('correo', $exception->errors());
            $this->assertSame('Ana Maria', $componente->tercero->fresh()->nombre);
        }
    }
}
