<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REESTRUCTURACIÓN 2/5 — Participantes de una gestión comercial.
 *
 * Las participaciones vivían en 7 columnas fijas de gestion_comercial
 * (porcentaje, porcentaje_2..4, comercial_2..4): imposible de validar,
 * y origen de los "participantes fantasma" (porcentaje sin comercial).
 * Pasan a `gestion_participantes`: una fila por comercial con su posición
 * y porcentaje. `id_user` se conserva como el responsable (dueño) de la
 * gestión y `participaciones` como el número de participantes.
 */
class NormalizarParticipantesGestion extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('gestion_participantes')) {
            $this->crearYMigrar();
        }

        // Las columnas viejas tienen FK (comercial_N -> users): se sueltan antes de eliminarlas.
        // Este paso es idempotente para que la migración pueda reanudarse.
        $columnasViejas = ['porcentaje', 'porcentaje_2', 'porcentaje_3', 'porcentaje_4', 'comercial_2', 'comercial_3', 'comercial_4'];
        foreach (['comercial_2', 'comercial_3', 'comercial_4'] as $col) {
            // alias explícito: MySQL devuelve CONSTRAINT_NAME en mayúsculas, MariaDB en minúsculas
            $fk = DB::selectOne("SELECT constraint_name AS constraint_name FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = 'gestion_comercial' AND column_name = ? AND referenced_table_name IS NOT NULL", [$col]);
            if ($fk && $fk->constraint_name) {
                DB::statement("ALTER TABLE gestion_comercial DROP FOREIGN KEY `{$fk->constraint_name}`");
            }
        }
        $existentes = array_values(array_filter($columnasViejas, function ($c) { return Schema::hasColumn('gestion_comercial', $c); }));
        if ($existentes) {
            Schema::table('gestion_comercial', function (Blueprint $table) use ($existentes) {
                $table->dropColumn($existentes);
            });
        }
    }

    private function crearYMigrar()
    {
        Schema::create('gestion_participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gestion_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('posicion')->default(1);
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['gestion_id', 'user_id'], 'gestion_usuario_unico');
            $table->foreign('gestion_id')->references('id')->on('gestion_comercial')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
        DB::statement("ALTER TABLE gestion_participantes COMMENT = 'Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje. Reemplaza porcentaje/porcentaje_2..4/comercial_2..4'");

        $usuarios = DB::table('users')->pluck('id')->flip();
        $insertados = 0; $fantasmas = 0; $duplicados = 0;

        DB::table('gestion_comercial')
            ->select('id', 'id_user', 'porcentaje', 'porcentaje_2', 'porcentaje_3', 'porcentaje_4', 'comercial_2', 'comercial_3', 'comercial_4')
            ->orderBy('id')
            ->chunk(2000, function ($gestiones) use ($usuarios, &$insertados, &$fantasmas, &$duplicados) {
                $filas = [];
                foreach ($gestiones as $g) {
                    $vistos = [];
                    $candidatos = [
                        [1, $g->id_user, $g->porcentaje],
                        [2, $g->comercial_2, $g->porcentaje_2],
                        [3, $g->comercial_3, $g->porcentaje_3],
                        [4, $g->comercial_4, $g->porcentaje_4],
                    ];
                    foreach ($candidatos as [$pos, $userId, $pct]) {
                        if (!$userId) {
                            // porcentaje sin comercial = participante fantasma: no se migra
                            if ($pct > 0) { $fantasmas++; }
                            continue;
                        }
                        if (!isset($usuarios[$userId])) { continue; }
                        if (isset($vistos[$userId])) { $duplicados++; continue; }
                        $vistos[$userId] = true;
                        $filas[] = [
                            'gestion_id' => $g->id, 'user_id' => $userId, 'posicion' => $pos,
                            'porcentaje' => $pct !== null ? round((float) $pct, 2) : 0,
                            'created_at' => now(), 'updated_at' => now(),
                        ];
                        $insertados++;
                    }
                }
                if ($filas) {
                    DB::table('gestion_participantes')->insertOrIgnore($filas);
                }
            });

        echo "  participantes migrados: {$insertados} | fantasmas descartados (porcentaje sin comercial): {$fantasmas} | duplicados del responsable omitidos: {$duplicados}\n";
    }

    public function down()
    {
        Schema::table('gestion_comercial', function (Blueprint $table) {
            $table->decimal('porcentaje', 8, 2)->nullable();
            $table->decimal('porcentaje_2', 8, 2)->nullable();
            $table->decimal('porcentaje_3', 8, 2)->nullable();
            $table->decimal('porcentaje_4', 8, 2)->nullable();
            $table->unsignedBigInteger('comercial_2')->nullable();
            $table->unsignedBigInteger('comercial_3')->nullable();
            $table->unsignedBigInteger('comercial_4')->nullable();
        });
        foreach (DB::table('gestion_participantes')->get() as $p) {
            $col = $p->posicion == 1 ? 'porcentaje' : 'porcentaje_'.$p->posicion;
            $upd = [$col => $p->porcentaje];
            if ($p->posicion > 1) { $upd['comercial_'.$p->posicion] = $p->user_id; }
            DB::table('gestion_comercial')->where('id', $p->gestion_id)->update($upd);
        }
        Schema::dropIfExists('gestion_participantes');
    }
}
