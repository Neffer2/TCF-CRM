<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 2 de la reestructuración: multi-rol por cuenta única, permisos con
 * nombre y destinatarios de notificación administrables desde la BD.
 *
 * - role_user: roles que cada cuenta PUEDE usar (users.rol sigue siendo el
 *   rol ACTIVO, así el código existente no cambia).
 * - Rol GERENCIA con id fijo 20 (id explícito para no chocar con roles
 *   creados directamente en producción, p.ej. el rol 10).
 * - permisos / permiso_rol / permiso_user: reemplazan los IDs quemados en
 *   vistas y traits. La siembra reproduce EXACTAMENTE el comportamiento
 *   actual (los IDs de hoy quedan como filas), para que el despliegue no
 *   cambie quién puede hacer qué: a partir de ahí se administra en BD.
 * - notificacion_destinatarios: reemplaza el directorio de correos quemado
 *   en app/Traits/Email.php.
 */
class CreateRolesPermisosNotificaciones extends Migration
{
    public function up()
    {
        // --- Rol GERENCIA (id fijo 20) ---
        if (!DB::table('roles_user')->where('id', 20)->exists()) {
            DB::table('roles_user')->insert([
                'id' => 20,
                'description' => 'Gerencia',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- Multi-rol por cuenta ---
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('rol_id');
                $table->timestamps();
                $table->unique(['user_id', 'rol_id']);
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('rol_id')->references('id')->on('roles_user');
            });

            // Siembra: cada cuenta puede usar el rol que ya tiene activo
            DB::statement('
                INSERT INTO role_user (user_id, rol_id, created_at, updated_at)
                SELECT u.id, u.rol, NOW(), NOW() FROM users u
                WHERE u.rol IS NOT NULL
                  AND EXISTS (SELECT 1 FROM roles_user r WHERE r.id = u.rol)
            ');

            // Gerencia (decisión 11-sep-2026): Jony Ariza y Alejandro
            // Rodriguez, únicamente — en todas sus cuentas conocidas
            // (8/26 Alejandro, 10/71 Jony) hasta la consolidación de cuentas.
            foreach ([8, 26, 10, 71] as $userId) {
                if (DB::table('users')->where('id', $userId)->exists()) {
                    DB::table('role_user')->updateOrInsert(
                        ['user_id' => $userId, 'rol_id' => 20],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // --- Permisos con nombre ---
        if (!Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->id();
                $table->string('clave')->unique();
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });

            Schema::create('permiso_rol', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permiso_id');
                $table->unsignedBigInteger('rol_id');
                $table->unique(['permiso_id', 'rol_id']);
                $table->foreign('permiso_id')->references('id')->on('permisos');
                $table->foreign('rol_id')->references('id')->on('roles_user');
            });

            Schema::create('permiso_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permiso_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['permiso_id', 'user_id']);
                $table->foreign('permiso_id')->references('id')->on('permisos');
                $table->foreign('user_id')->references('id')->on('users');
            });

            $permisos = [
                'aprobar-presupuestos' => 'Aprobar/rechazar presupuestos en validación (estado 5)',
                'aprobador-margen' => 'Recibir y decidir las aprobaciones de margen (regla del 35%)',
                'gerente-comercial' => 'Actuar como gerente en validaciones y actualizaciones de presupuesto',
                'validar-nomina' => 'Validar órdenes de nómina (gerencia/remisión)',
                'revisar-anticipos-gerencia' => 'Revisión de gerencia sobre anticipos de productor',
                'gestionar-presupuestos-especiales' => 'Acciones especiales sobre presupuestos',
                'ver-menu-admin-avanzado' => 'Ver la sección avanzada del menú admin',
            ];
            foreach ($permisos as $clave => $desc) {
                DB::table('permisos')->insert([
                    'clave' => $clave, 'descripcion' => $desc,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $idDe = function ($clave) {
                return DB::table('permisos')->where('clave', $clave)->value('id');
            };

            // El rol GERENCIA concentra las aprobaciones
            foreach (['aprobar-presupuestos', 'aprobador-margen', 'gerente-comercial',
                      'validar-nomina', 'revisar-anticipos-gerencia',
                      'gestionar-presupuestos-especiales'] as $clave) {
                DB::table('permiso_rol')->insert(['permiso_id' => $idDe($clave), 'rol_id' => 20]);
            }
            // El menú avanzado queda en Admin
            DB::table('permiso_rol')->insert(['permiso_id' => $idDe('ver-menu-admin-avanzado'), 'rol_id' => 1]);

            // SIEMBRA DE FIDELIDAD: los IDs hoy quemados en el código pasan a
            // filas, para que el despliegue no cambie el comportamiento.
            // (aprobador-margen NO se siembra por usuario: por decisión de
            // negocio queda EXCLUSIVO del rol Gerencia.)
            $fidelidad = [
                'gestionar-presupuestos-especiales' => [2, 145, 171, 181, 197, 206, 208, 210, 214],
                'aprobar-presupuestos' => [8, 10, 192],
                'validar-nomina' => [8, 10, 181],
                'revisar-anticipos-gerencia' => [8, 10],
                'ver-menu-admin-avanzado' => [8, 10],
                'gerente-comercial' => [8, 10, 26, 71, 198],
            ];
            foreach ($fidelidad as $clave => $userIds) {
                foreach ($userIds as $userId) {
                    if (DB::table('users')->where('id', $userId)->exists()) {
                        DB::table('permiso_user')->updateOrInsert(
                            ['permiso_id' => $idDe($clave), 'user_id' => $userId], []
                        );
                    }
                }
            }
        }

        // --- Destinatarios de notificación por área ---
        if (!Schema::hasTable('notificacion_destinatarios')) {
            Schema::create('notificacion_destinatarios', function (Blueprint $table) {
                $table->id();
                $table->string('area')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email_externo')->nullable();
                $table->string('nombre')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users');
            });

            // Siembra desde el directorio hoy quemado en app/Traits/Email.php.
            // Si existe un usuario con ese correo se referencia por user_id
            // (así los suspendidos dejan de recibir automáticamente);
            // si no, queda como buzón externo.
            $directorio = [
                'controller' => [
                    ['Lider Controller', 'Lider.Controller@bullmarketing.com.co'],
                    ['Equipo Controller', 'controller@bullmarketing.com.co'],
                    ['Susana Bautista', 'Susana.Bautista@bullmarketing.com.co'],
                    ['Auxiliar Comercial', 'Auxiliar.Comercial@bullmarketing.com.co'],
                    ['Maria Guerrero', 'Maria.Guerrero@bullmarketing.com.co'],
                    ['Coordinador Proyectos', 'Coordinador.Proyectos@bullmarketing.com.co'],
                    ['Katherine Galvis', 'Katherine.Galvis@bullmarketing.com.co'],
                    ['Carlos Gómez', 'Carlos.Gomez@bullmarketing.com.co'],
                    ['Brandon Vega', 'Brandon.Vega@bullmarketing.com.co'],
                ],
                'produccion' => [
                    ['Fernando Paez', 'fernando.paez@bullmarketing.com.co'],
                    ['Geraldin Parada', 'geraldin.parada@bullmarketing.com.co'],
                    ['Jesica Ramirez', 'jesica.ramirez@bullmarketing.com.co'],
                ],
                'contabilidad' => [
                    ['Diana Bohorquez', 'diana.bohorquez@bullmarketing.com.co'],
                    ['Facturación Proveedores', 'facturacion.proveedores@bullmarketing.com.co'],
                    ['Auxiliar Contable', 'auxiliar.contable@bullmarketing.com.co'],
                ],
                'lider_comercial' => [
                    ['Nefer Barragan', 'Neffer.Barragan@bullmarketing.com.co'],
                ],
                'tesoreria' => [
                    ['Tesorería', 'tesoreria@bullmarketing.com.co'],
                    ['Ligia Torres', 'Ligia.Torres@bullmarketing.com.co'],
                ],
                'compras' => [
                    ['Luz Melo', 'Compras@bullmarketing.com.co'],
                ],
                'compras_cc' => [
                    ['Nicol Riaño', 'nicol.riano@bullmarketing.com.co'],
                    ['Katherine Galvis', 'katherine.galvis@bullmarketing.com.co'],
                ],
                'contabilidad_pagos' => [
                    ['Contadores', 'contadores@bullmarketing.com.co'],
                    ['Tesorería', 'tesoreria@bullmarketing.com.co'],
                ],
            ];
            foreach ($directorio as $area => $personas) {
                foreach ($personas as [$nombre, $email]) {
                    $user = DB::table('users')->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
                    DB::table('notificacion_destinatarios')->insert([
                        'area' => $area,
                        'user_id' => $user->id ?? null,
                        'email_externo' => $user ? null : $email,
                        'nombre' => $nombre,
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('notificacion_destinatarios');
        Schema::dropIfExists('permiso_user');
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('role_user');
        DB::table('roles_user')->where('id', 20)->delete();
    }
}
