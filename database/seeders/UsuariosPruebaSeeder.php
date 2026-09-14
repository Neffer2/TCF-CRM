<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea usuarios de prueba (uno por rol) para testear la plataforma en LOCAL.
 *
 * Uso:  php artisan db:seed --class=UsuariosPruebaSeeder
 *
 * - SOLO corre en entorno local (APP_ENV=local): en producción aborta.
 * - Todos los usuarios usan la contraseña: Prueba123*
 * - prueba.gerencia tiene los roles Admin + Gerencia (aprueba márgenes).
 * - prueba.multirol tiene Productor + Comercial (para probar el selector).
 * - Reasigna hasta 3 centros de costo activos al productor de prueba para
 *   que pueda crear órdenes y anticipos con datos reales del dump.
 */
class UsuariosPruebaSeeder extends Seeder
{
    const PASSWORD = 'Prueba123*';

    public function run()
    {
        if (!app()->environment('local')) {
            $this->command->error('Este seeder es SOLO para entorno local (APP_ENV=local). Abortado.');
            return;
        }

        $usuarios = [
            // [email, nombre, rol activo, roles asignables]
            ['prueba.gerencia@local.test',     'Prueba Gerencia',     1,  [1, 20]],
            ['prueba.admin@local.test',        'Prueba Admin',        1,  [1]],
            ['prueba.comercial@local.test',    'Prueba Comercial',    2,  [2]],
            ['prueba.ejecutivo@local.test',    'Prueba Ejecutivo',    5,  [5]],
            ['prueba.lider@local.test',        'Prueba Lider',        6,  [6]],
            ['prueba.productor@local.test',    'Prueba Productor',    7,  [7]],
            ['prueba.tesoreria@local.test',    'Prueba Tesoreria',    8,  [8]],
            ['prueba.contabilidad@local.test', 'Prueba Contabilidad', 9,  [9]],
            ['prueba.multirol@local.test',     'Prueba MultiRol',     7,  [7, 2]],
            ['prueba.controller@local.test',   'Prueba Controller',   11, [11]],
            ['prueba.lidercom@local.test',     'Prueba Lider Comercial', 12, [12]],
        ];

        foreach ($usuarios as $i => [$email, $nombre, $rolActivo, $roles]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'telefono' => '300000000'.$i,
                    'password' => Hash::make(self::PASSWORD),
                    'rol' => $rolActivo,
                    'avatar' => 'public/photos/avatar.jpg',
                ]
            );

            if (DB::getSchemaBuilder()->hasTable('role_user')) {
                $user->roles()->syncWithoutDetaching($roles);
            }

            $this->command->info("✓ {$email} (rol activo {$rolActivo}, roles [".implode(',', $roles)."])");
        }

        // Centros de costo para el productor de prueba: reasigna los 3 más
        // recientes en estado Aprobado para que pueda crear OC y anticipos.
        $productor = User::where('email', 'prueba.productor@local.test')->first();
        $centros = DB::table('presupuesto_proyecto')
            ->where('estado_id', 1)
            ->orderByDesc('created_at')
            ->limit(3)
            ->pluck('id');
        if ($productor && $centros->isNotEmpty()) {
            DB::table('presupuesto_proyecto')->whereIn('id', $centros)
                ->update(['productor' => $productor->id]);
            $this->command->info("✓ Centros de costo reasignados a prueba.productor: ".$centros->join(', '));
        } else {
            $this->command->warn('No hay centros de costo en estado Aprobado para reasignar (el productor de prueba no podrá crear órdenes).');
        }

        // Equipo del líder comercial de prueba: el comercial de prueba + dos comerciales reales
        $lider = User::where('email', 'prueba.lidercom@local.test')->first();
        $comercialPrueba = User::where('email', 'prueba.comercial@local.test')->first();
        if ($lider && DB::getSchemaBuilder()->hasTable('lider_comercial_user')) {
            $equipo = array_filter([optional($comercialPrueba)->id, 11, 132]); // Alexandra Niño, Juan Camilo Rodriguez
            foreach ($equipo as $comercialId) {
                if (User::where('id', $comercialId)->exists()) {
                    DB::table('lider_comercial_user')->updateOrInsert(
                        ['lider_id' => $lider->id, 'comercial_id' => $comercialId],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
            $this->command->info('✓ Equipo de prueba.lidercom: comerciales '.implode(', ', $equipo));
        }

        $this->command->line('');
        $this->command->info('Contraseña de TODOS los usuarios de prueba: '.self::PASSWORD);
    }
}
