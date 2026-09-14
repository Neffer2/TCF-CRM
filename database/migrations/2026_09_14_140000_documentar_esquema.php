<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * REESTRUCTURACIÓN 5/5 — Documentación dentro de la base de datos.
 *
 * Comentarios (COMMENT) en cada tabla y en las columnas cuyo nombre no se
 * explica solo, para que el esquema sea legible desde cualquier cliente
 * SQL (phpMyAdmin, TablePlus, DBeaver...) sin abrir el código.
 * Los comentarios no cambian tipos, defaults ni datos.
 */
class DocumentarEsquema extends Migration
{
    public function up()
    {
        foreach ($this->tablas() as $tabla => $comentario) {
            $this->comentarTabla($tabla, $comentario);
        }
        foreach ($this->columnas() as $tabla => $cols) {
            foreach ($cols as $col => $comentario) {
                $this->comentarColumna($tabla, $col, $comentario);
            }
        }
    }

    public function down() {}

    private function comentarTabla(string $tabla, string $comentario): void
    {
        try {
            if (!DB::selectOne("SELECT 1 AS x FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$tabla])) return;
            DB::statement("ALTER TABLE `{$tabla}` COMMENT = ".DB::getPdo()->quote($comentario));
        } catch (\Throwable $e) {
            echo "  aviso: no se pudo comentar la tabla {$tabla}: {$e->getMessage()}\n";
        }
    }

    private function comentarColumna(string $tabla, string $col, string $comentario): void
    {
        try {
            $c = DB::selectOne("SELECT COLUMN_TYPE t, IS_NULLABLE n, COLUMN_DEFAULT d, EXTRA e, CHARACTER_SET_NAME cs, COLLATION_NAME co FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$tabla, $col]);
            if (!$c) return;
            $def = "`{$col}` {$c->t}";
            if ($c->cs) { $def .= " CHARACTER SET {$c->cs} COLLATE {$c->co}"; }
            $def .= $c->n === 'YES' ? ' NULL' : ' NOT NULL';
            if ($c->d !== null) {
                $def .= preg_match('/current_timestamp/i', $c->d) ? ' DEFAULT CURRENT_TIMESTAMP' : ' DEFAULT '.DB::getPdo()->quote($c->d);
            } elseif ($c->n === 'YES') {
                $def .= ' DEFAULT NULL';
            }
            if (stripos($c->e, 'auto_increment') !== false) { $def .= ' AUTO_INCREMENT'; }
            if (stripos($c->e, 'on update') !== false) { $def .= ' ON UPDATE CURRENT_TIMESTAMP'; }
            DB::statement("ALTER TABLE `{$tabla}` MODIFY COLUMN {$def} COMMENT ".DB::getPdo()->quote($comentario));
        } catch (\Throwable $e) {
            echo "  aviso: no se pudo comentar {$tabla}.{$col}: {$e->getMessage()}\n";
        }
    }

    private function tablas(): array
    {
        return [
            'users' => 'Cuentas de la plataforma. rol = rol ACTIVO (los roles asignables están en role_user)',
            'roles_user' => 'Catálogo de roles (1 Admin, 2 Comercial, 5 Ejecutivo, 6 Líder producción, 7 Productor, 8 Tesorería, 9 Contabilidad, 10 Centinela, 20 Gerencia)',
            'role_user' => 'Roles que cada cuenta puede usar (multi-rol). El rol activo está en users.rol',
            'permisos' => 'Permisos con nombre (Gates). Reemplazan los ids de usuario que estaban quemados en el código',
            'permiso_rol' => 'Permisos otorgados a un rol',
            'permiso_user' => 'Permisos otorgados directamente a una cuenta',
            'notificacion_destinatarios' => 'A quién se notifica por área (controller, produccion, contabilidad, tesoreria, lider_comercial, compras, compras_cc, contabilidad_pagos)',
            'contactos' => 'Personas de contacto en los clientes (origen de los leads)',
            'clientes' => 'Clientes (empresas) y sus parámetros',
            'cliente_parametros_cc' => 'Parámetros por cliente para la asignación de centros de costos',
            'solicitud_contactos' => 'Solicitudes de creación/actualización de contactos hechas por comerciales',
            'gestion_comercial' => 'Gestión comercial (lead/oportunidad/proyecto). Un registro por proyecto en el embudo; los participantes están en gestion_participantes',
            'gestion_participantes' => 'Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje',
            'estados_gestion_comercial' => 'Etapas del embudo comercial (prospecto, oportunidad, propuesta, decisión, ...)',
            'cuentas' => 'Cuentas/marcas de cliente asociadas a la base comercial',
            'estados_cuenta' => 'Estados de un registro de la base comercial (ejecución por facturar, venta, facturado, ...)',
            'base_comerciales' => 'Base comercial: valor de venta de cada proyecto por comercial. Alimenta los dashboards de venta',
            'presupuestos' => 'Presupuesto (meta) comercial por año/mes/comercial',
            'presupuesto_proyecto' => 'Presupuesto de un proyecto (centro de costos): totales, margen, aprobación y productor asignado',
            'estados_presupuesto' => 'Estados del presupuesto de proyecto (en revisión, aprobado, rechazado, ...)',
            'items_presupuesto' => 'Ítems (líneas) de un presupuesto de proyecto. Sus proveedores están en item_presupuesto_proveedor',
            'item_presupuesto_proveedor' => 'Proveedores asignados a cada ítem de presupuesto (N:M)',
            'historial_items_presupuesto' => 'Snapshot JSON de un ítem antes de cada modificación (auditoría de cambios)',
            'tarifario' => 'Tarifas de referencia para cotizar',
            'anos' => 'Años fiscales',
            'meses' => 'Meses de cada año fiscal con su rango de fechas (f_inicio, f_fin)',
            'helisa' => 'Asientos de facturación exportados/registrados para el contable Helisa. Un documento puede tener varias filas (una por comercial y ajustes)',
            'proveedores' => 'Proveedores (terceros jurídicos). Los ids 1 y 2 son nómina y el 3 es "cuenta de cobro" (persona natural)',
            'categorias_proveedor' => 'Categorías de proveedor',
            'ordenes_compra' => 'Órdenes de compra: tipo_oc 1 jurídica, 2 natural (persona), 3 nómina. estado_id -> estados_ordenes_compra',
            'oc_items' => 'Líneas de una orden de compra (consumo de un ítem de presupuesto)',
            'estados_ordenes_compra' => 'Estados de una orden de compra (1 aprobado, 2 revisión, 3 editable, 4 recibido, 5 comprobado, 6 anulada, 7 evidencias, 8-14 flujo nómina)',
            'tipo_ordenes_compra' => 'Tipos de orden de compra (jurídica, natural, nómina)',
            'natural_info' => 'Datos adicionales de una orden natural: tercero contratado, productor, contrato',
            'terceros' => 'Personas naturales contratadas por producción (datos bancarios, RUT, firma)',
            'estados_tercero' => 'Estados de un tercero',
            'evidencias' => 'Evidencias (archivos) del trabajo de un tercero para una orden natural',
            'anticipos' => 'Anticipos de dinero: jurídicos (oc_id) o de productor (presupuesto_id). estado_id -> estados_anticipo',
            'estados_anticipo' => 'Estados del ciclo de vida de un anticipo',
            'items_anticipo' => 'Ítems de presupuesto incluidos en un anticipo de productor, con valor anticipado y saldo',
            'evidencias_anticipo' => 'Evidencias del productor para cerrar un anticipo',
            'asistentes' => 'Relación ejecutivo de cuenta (asistente_id) -> comercial al que apoya (comercial_id)',
            'lider_comercial_user' => 'Relación líder comercial -> comerciales a su cargo',
            'migrations' => 'Control de migraciones de Laravel',
            'password_resets' => 'Tokens de restablecimiento de contraseña (Laravel)',
            'personal_access_tokens' => 'Tokens de API (Laravel Sanctum)',
            'failed_jobs' => 'Trabajos en cola fallidos (Laravel)',
        ];
    }

    private function columnas(): array
    {
        return [
            'users' => [
                'rol' => 'Rol ACTIVO (FK lógica a roles_user). Los demás roles de la cuenta están en role_user',
                'telefono' => 'Celular (se usa para SMS de notificación)',
                'avatar' => 'Ruta de la foto de perfil en storage',
            ],
            'gestion_comercial' => [
                'id_contacto' => 'Contacto (persona) que origina el lead -> contactos.id',
                'id_user' => 'Comercial responsable (dueño) de la gestión -> users.id',
                'id_estado' => 'Etapa del embudo -> estados_gestion_comercial.id',
                'participaciones' => 'Número de comerciales que participan (1 a 4). El detalle está en gestion_participantes',
                'presto_cot' => 'Valor cotizado (presupuesto de la cotización)',
                'nom_proyecto_cot' => 'Nombre del proyecto en la cotización',
                'fecha_estimada_cot' => 'Fecha estimada de ejecución (cotización)',
                'cotizacion_file' => 'Archivo de la cotización',
                'propuesta_url' => 'Enlace a la propuesta enviada al cliente',
                'presto_prop' => 'Valor de la propuesta',
                'nom_proyecto_prop' => 'Nombre del proyecto en la propuesta',
                'fecha_estimada_prop' => 'Fecha estimada (propuesta)',
                'claro' => '1 si el cliente es Claro (tenía reglas de aprobación propias)',
                'causa' => 'Causa de pérdida/cierre cuando la decisión es negativa',
                'tipo_contacto' => 'Canal por el que llegó el contacto',
                'desc_contacto' => 'Descripción del primer contacto',
            ],
            'gestion_participantes' => [
                'gestion_id' => '-> gestion_comercial.id',
                'user_id' => 'Comercial participante -> users.id',
                'posicion' => '1 = responsable de la gestión; 2..4 = participantes adicionales',
                'porcentaje' => 'Porcentaje de la venta que se le atribuye (la suma debería ser 100)',
            ],
            'presupuesto_proyecto' => [
                'id_gestion' => 'Gestión comercial a la que pertenece -> gestion_comercial.id',
                'comercial_id' => 'Comercial responsable -> users.id',
                'productor' => 'Productor asignado a ejecutar el proyecto -> users.id',
                'cod_cc' => 'Código de centro de costos asignado al aprobar',
                'fecha_cc' => 'Fecha de asignación del centro de costos',
                'estado_id' => '-> estados_presupuesto.id (1 = aprobado)',
                'venta_proy' => 'Valor de venta del proyecto (total al cliente)',
                'margen_proy' => 'Margen % del proyecto. La aprobación exige >= 35 % (regla de Gerencia)',
                'justificacion' => 'Observaciones del comercial al enviar a aprobación',
                'justificacion_compras' => 'Observaciones de compras/gerencia al aprobar o rechazar',
                'justificacion_lider' => 'Observaciones del líder comercial',
                'notificacion_actualizacion' => 'Marca de notificación pendiente por actualización del presupuesto',
            ],
            'items_presupuesto' => [
                'presupuesto_id' => '-> presupuesto_proyecto.id',
                'num_item' => 'Número de ítem visible al usuario (secuencial dentro del presupuesto)',
                'orden' => 'Orden de presentación',
                'cod' => 'Código/categoría del ítem',
                'evento' => '1 = fila de evento (agrupador sin valores), 0 = ítem normal',
                'cantidad' => 'Cantidad de unidades',
                'dia' => 'Número de días',
                'otros' => 'Otro multiplicador (p.ej. personas). Cantidad total = cantidad * dia * otros',
                'v_unitario' => 'Costo unitario interno',
                'v_total' => 'Costo total interno (cantidad * dia * otros * v_unitario)',
                'v_unitario_cot' => 'Precio unitario al cliente',
                'v_total_cot' => 'Precio total al cliente (base del margen)',
                'v_total_cliente' => 'Valor total al cliente (variante usada en cotización)',
                'margen_utilidad' => 'Margen de utilidad del ítem (fracción; 0.35 = 35 %)',
                'rentabilidad' => 'Rentabilidad calculada del ítem',
                'proveedor_legacy' => 'OBSOLETO: valor original del proveedor. Usar item_presupuesto_proveedor',
                'mes' => 'Mes de ejecución -> meses.id',
                'dias' => 'Días de ejecución (dato informativo)',
                'ciudad' => 'Ciudad de ejecución',
                'disponible' => '1 = el ítem puede consumirse en órdenes/anticipos',
                'actualizado' => 'Marca de actualización tras asignado el centro de costos (0..3, ver código)',
                'actualizado_con' => 'Marca de actualización vista por contabilidad',
            ],
            'ordenes_compra' => [
                'tipo_oc' => '1 jurídica (proveedor), 2 natural (persona), 3 nómina -> tipo_ordenes_compra.id',
                'estado_id' => '-> estados_ordenes_compra.id',
                'presupuesto_id' => 'Presupuesto que consume (NULL en órdenes naturales; ver natural_info) -> presupuesto_proyecto.id',
                'proveedor_id' => '-> proveedores.id (3 = cuenta de cobro/persona natural)',
                'cod_oc' => 'Código de la orden asignado al aprobar (OC{id})',
                'gr' => 'Código del Good Receive (GR{id}) cuando se comprueba',
                'archivo_cot' => 'Cotización del proveedor adjunta por el productor',
                'archivo_orden_helisa' => 'PDF de la orden generado al aprobar (para Helisa)',
                'archivo_remision' => 'PDF de la remisión firmada',
                'archivo_firma' => 'Imagen de la firma del productor en la remisión',
                'archivo_comprobante_pago' => 'Comprobante de pago (flujo viejo de anticipos sobre la orden)',
                'cod_causal' => 'Código de causación contable (flujo viejo)',
                'observacion_causal' => 'Observación de contabilidad al causar',
                'fecha_envio_produccion' => 'Fecha en que el productor la envió a aprobación',
                'fecha_aprobacion' => 'Fecha de aprobación',
                'justificacion_rechazo' => 'Motivo del rechazo',
                'observaciones_negociacion' => 'Observaciones del controller al aprobar',
                'observacion_remision' => 'Observaciones al firmar la remisión',
                'observaciones_anulacion' => 'Motivo de anulación',
                'actualizado' => 'Marca de orden actualizada tras aprobación',
            ],
            'oc_items' => [
                'oc_id' => '-> ordenes_compra.id',
                'item_id' => 'Ítem de presupuesto consumido -> items_presupuesto.id',
                'display_item' => 'Número de ítem mostrado al usuario',
                'cant_oc' => 'Cantidad consumida',
                'dias_oc' => 'Días',
                'otros_oc' => 'Otro multiplicador',
                'vunit_oc' => 'Valor unitario',
                'vtotal_oc' => 'Valor total de la línea (descuenta del saldo del ítem)',
                'tipo_servicio' => 'Tipo de servicio (órdenes naturales)',
                'tipo_contrato' => 'Tipo de contrato (órdenes naturales)',
            ],
            'anticipos' => [
                'oc_id' => 'Orden de compra (anticipo JURÍDICO). NULL en anticipos de productor',
                'presupuesto_id' => 'Presupuesto/centro de costos (anticipo de PRODUCTOR). NULL en jurídicos',
                'productor_id' => 'Productor que solicita -> users.id',
                'porcentaje_anticipo' => 'Porcentaje de la orden que se anticipa (jurídico)',
                'total_anticipo' => 'Valor total a anticipar',
                'estado_id' => '-> estados_anticipo.id',
                'firma_productor' => 'Imagen de la firma del productor',
                'cod_causal' => 'Código de causación asignado por contabilidad',
                'comprobante_pago' => 'Comprobante de pago registrado por tesorería (si existe, ya fue pagado)',
                'fecha_comprobante_pago' => 'Fecha del pago',
                'observaciones_revision_lider' => 'Observaciones del líder al aprobar',
                'rechazo_revision_lider' => 'Motivo de rechazo del líder',
                'observaciones_revision_gerencia' => 'Observaciones de gerencia al aprobar',
                'rechazo_revision_gerencia' => 'Motivo de rechazo de gerencia',
                'observaciones_revision_evidencias' => 'Observaciones al revisar evidencias',
                'rechazo_revision_evidencias' => 'Motivo de rechazo de evidencias',
            ],
            'items_anticipo' => [
                'anticipo_id' => '-> anticipos.id',
                'item_id' => 'Ítem de presupuesto -> items_presupuesto.id',
                'vunit' => 'Valor unitario',
                'vtotal' => 'Valor total del ítem',
                'vanticipo' => 'Valor anticipado (<= vtotal)',
                'saldo' => 'vtotal - vanticipo',
            ],
            'base_comerciales' => [
                'id_user' => 'Comercial al que se atribuye la venta -> users.id',
                'id_gestion' => '-> gestion_comercial.id',
                'id_cuenta' => 'Cuenta/marca -> cuentas.id',
                'id_estado' => 'Estado de la venta -> estados_cuenta.id',
                'valor_proyecto' => 'Valor atribuido al comercial (su porcentaje del proyecto)',
                'valor_original' => 'Valor total del proyecto',
                'fecha' => 'Fecha del registro (base de los dashboards por periodo)',
                'centro' => 'Centro de costos',
                'dura_mes' => 'Duración en meses',
            ],
            'helisa' => [
                'centro' => 'Centro de costos',
                'num_doc' => 'Número de documento/factura',
                'tipo_doc' => 'Tipo de documento contable',
                'comercial' => 'Comercial al que se atribuye -> users.id',
                'porcentaje' => 'Porcentaje de participación del comercial en la factura',
                'participacion' => 'Número de comerciales que participan en la factura',
                'comision' => 'Comisión calculada para el comercial (puede ser negativa en ajustes)',
                'debito' => 'Valor débito',
                'credito' => 'Valor crédito',
                'id_cuenta' => 'Cuenta/marca -> cuentas.id',
                'identidad' => 'NIT/cédula del tercero',
                'nom_tercero' => 'Nombre del tercero facturado',
                'nom_centro_costo' => 'Nombre del centro de costos',
                'mes' => 'Mes contable -> meses.id',
                'año' => 'Año contable',
            ],
            'presupuestos' => [
                'comercial_id' => '-> users.id',
                'ano_id' => '-> anos.id',
                'mes_id' => '-> meses.id',
                'valor' => 'Meta de venta del comercial en el mes',
            ],
            'meses' => [
                'identifier' => 'Número del mes (1..12)',
                'ano_id' => '-> anos.id',
                'f_inicio' => 'Primer día del mes',
                'f_fin' => 'Último día del mes',
            ],
            'contactos' => [
                'pbx' => 'Teléfono fijo/PBX',
                'web' => 'Sitio web',
                'celular' => 'Celular',
                'empresa' => 'Empresa/cliente al que pertenece',
                'cargo' => 'Cargo en la empresa',
            ],
            'terceros' => [
                'cedula' => 'Documento de identidad',
                'banco' => 'Banco para el pago',
                'tipo_cuenta' => 'Tipo de cuenta bancaria',
                'num_cuenta' => 'Número de cuenta bancaria',
                'rut' => 'Archivo RUT',
                'num_rut' => 'Número de RUT',
                'cert_bancaria' => 'Certificación bancaria (archivo)',
                'copia_cedula' => 'Copia de la cédula (archivo)',
                'art383' => 'Formato artículo 383 (archivo)',
                'planilla_aportes' => 'Planilla de aportes (archivo)',
                'firma' => 'Imagen de la firma capturada en el portal',
                'estado' => '-> estados_tercero.id',
            ],
            'natural_info' => [
                'oc_id' => '-> ordenes_compra.id',
                'tercero_id' => 'Persona contratada -> terceros.id',
                'productor_id' => 'Productor que contrata -> users.id',
                'contrato' => 'Archivo del contrato firmado',
            ],
            'asistentes' => [
                'asistente_id' => 'Cuenta del ejecutivo de cuenta -> users.id',
                'comercial_id' => 'Comercial al que apoya -> users.id',
            ],
            'notificacion_destinatarios' => [
                'area' => 'Área/evento que notifica',
                'user_id' => 'Usuario de la plataforma (si está suspendido no recibe)',
                'email_externo' => 'Buzón externo cuando no es usuario de la plataforma',
            ],
            'role_user' => [
                'rol_id' => '-> roles_user.id',
            ],
        ];
    }
}
