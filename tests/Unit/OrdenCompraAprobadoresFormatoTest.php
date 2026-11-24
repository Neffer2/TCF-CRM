<?php

namespace Tests\Unit;

use App\Models\OcItem;
use App\Models\OrdenCompra;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\View\ViewServiceProvider;
use PHPUnit\Framework\TestCase;

class OrdenCompraAprobadoresFormatoTest extends TestCase
{
    private $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = \Illuminate\Container\Container::getInstance();
        $base = dirname(__DIR__, 2);
        $app = new Application($base);
        $app->instance('config', new Repository([
            'view' => ['paths' => [$base . '/resources/views'], 'compiled' => $base . '/storage/framework/views'],
        ]));
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        $app->register(FilesystemServiceProvider::class);
        $app->register(EventServiceProvider::class);
        $app->register(ViewServiceProvider::class);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->previousContainer);
        \Illuminate\Container\Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    /** @dataProvider montos */
    public function test_el_formato_respeta_los_limites_y_el_tipo_de_persona($tipo, $total, $cargos)
    {
        $orden = $this->orden($tipo, $total);

        $this->assertSame($cargos, array_column($orden->aprobacionesParaFormato(), 'cargo'));
        foreach ($orden->aprobacionesParaFormato() as $fila) {
            $this->assertSame('Sin aprobación registrada', $fila['nombre']);
        }
    }

    public function montos(): array
    {
        $juridica = ['Líder de producción', 'Controller'];
        $natural = ['Líder de producción'];

        return [
            [1, 5000000, $juridica],
            [1, 5000001, array_merge($juridica, ['Líder comercial'])],
            [1, 10000000, array_merge($juridica, ['Líder comercial'])],
            [1, 10000001, array_merge($juridica, ['Líder comercial', 'Gerencia'])],
            [2, 2000000, $natural],
            [2, 2000001, array_merge($natural, ['Líder comercial'])],
            [2, 5000000, array_merge($natural, ['Líder comercial'])],
            [2, 5000001, array_merge($natural, ['Líder comercial', 'Gerencia'])],
        ];
    }

    public function test_guarda_la_identidad_y_reinicia_las_etapas_al_aprobar_de_nuevo_en_produccion()
    {
        $orden = $this->orden(1, 12000000);
        $usuario = new User(['name' => 'Ana Producción']);
        $usuario->id = 25;
        $orden->registrarAprobadorFormato('lider_produccion', $usuario);
        $usuario->name = 'Carlos Controller';
        $orden->registrarAprobadorFormato('controller', $usuario);

        $this->assertSame('Ana Producción', $orden->aprobacionesParaFormato()[0]['nombre']);
        $this->assertSame('Carlos Controller', $orden->aprobacionesParaFormato()[1]['nombre']);
        $this->assertSame(25, $orden->aprobadores_formato['controller']['usuario_id']);
        $this->assertNotEmpty($orden->aprobadores_formato['controller']['fecha']);

        $orden->registrarAprobadorFormato('lider_produccion', $usuario);
        $this->assertArrayNotHasKey('controller', $orden->aprobadores_formato);
    }

    public function test_el_pdf_muestra_solo_las_etapas_requeridas_y_escapa_el_nombre()
    {
        $orden = $this->orden(2, 6000000);
        $usuario = new User(['name' => 'Ana <Producción>']);
        $orden->registrarAprobadorFormato('lider_produccion', $usuario);
        $html = view('exports.orden_compra_pdf', ['orden' => $orden, 'esBorrador' => false])->render();

        $this->assertStringContainsString('Ana &lt;Producción&gt;', $html);
        $this->assertStringContainsString('Líder comercial', $html);
        $this->assertStringContainsString('Gerencia', $html);
        $this->assertStringNotContainsString('Controller', $html);
        $this->assertStringContainsString('Sin aprobación registrada', $html);
    }

    private function orden(int $tipo, float $total): OrdenCompra
    {
        $item = new OcItem();
        $item->vtotal_oc = $total;
        $item->setRelation('itemPresupuesto', null);
        $orden = new OrdenCompra(['tipo_oc' => $tipo]);
        $orden->id = 123;
        $orden->setRelation('ordenItems', new Collection([$item]));
        $orden->setRelation('presupuesto', null);
        $orden->setRelation('naturalInfo', null);
        $orden->setRelation('proveedor', null);

        return $orden;
    }
}
