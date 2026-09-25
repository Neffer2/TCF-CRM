<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;

/**
 * MEJORAS DE BASE DE DATOS 7/7 — Documentación completa del esquema.
 *
 * La migración documentar_esquema (14-sep) comentó las tablas y 165 columnas; quedaban 347 columnas sin
 * comentario. Aquí se documentan todas (y las tablas nuevas), para que el esquema se entienda desde cualquier
 * cliente SQL. Solo cambia el COMMENT: la definición de cada columna se repite tal cual (App\Support\EsquemaBd).
 * El diccionario docs/DICCIONARIO-DATOS.md se regenera con: php artisan tinker docs/generar-diccionario.php
 */
class DocumentarEsquemaCompleto extends Migration
{
    public function up()
    {
        foreach ($this->tablas() as $tabla => $c) { EsquemaBd::comentarTabla($tabla, $c); }
        foreach ($this->columnas() as $tabla => $cols) {
            if (!EsquemaBd::tabla($tabla)) { continue; }
            foreach ($cols as $col => $c) {
                try { EsquemaBd::comentarColumna($tabla, $col, $c); }
                catch (\Throwable $e) { echo "  aviso: {$tabla}.{$col}: {$e->getMessage()}\n"; }
            }
        }
    }

    public function down() {}

    private function tablas(): array
    {
        return [
            'anticipo_pasos' => 'Recorrido del anticipo jurídico: cada acción (solicitar, revisar, aprobar, rechazar, causar, pagar, anular, retirar) con quién, cuándo y con qué observación',
            'estados_proveedor' => 'Catálogo de estados del acuerdo con el proveedor (activo, suspendido, etc.)',
            'roles' => 'Catálogo de roles (antes roles_user). users.rol es el rol activo; role_user, los roles que cada cuenta puede usar',
            'failed_jobs' => 'Laravel: trabajos en cola que fallaron (p. ej. envíos de notificaciones)',
            'migrations' => 'Laravel: migraciones aplicadas a esta base',
            'password_resets' => 'Laravel: tokens de restablecimiento de contraseña (el flujo público se retiró; la tabla queda por compatibilidad)',
            'personal_access_tokens' => 'Laravel Sanctum: tokens de API (no se usan en el CRM)',
        ];
    }

    private function columnas(): array
    {
        $id = 'Identificador';
        $creado = 'Creación del registro';
        $modificado = 'Última modificación';
        $ts = ['id' => $id, 'created_at' => $creado, 'updated_at' => $modificado];
        $catalogo = $ts + ['description' => 'Nombre para mostrar'];
        return [
            'actividad_log' => $ts + [
                'user_id' => 'Usuario que hizo la acción (users.id); NULL en intentos de acceso fallidos',
                'rol' => 'Rol activo del usuario en ese momento (roles.id)',
                'metodo' => 'Método HTTP (GET, POST…)',
                'ruta' => 'Ruta visitada o acción ejecutada',
                'estado_http' => 'Código de respuesta HTTP (200, 403, 500…)',
                'duracion_ms' => 'Tiempo de respuesta en milisegundos',
                'ip' => 'Dirección IP del navegador',
                'navegador' => 'Identificación del navegador (User-Agent)',
                'created_at' => 'Momento del registro',
            ],
            'anos' => $ts + ['description' => 'Año (texto, p. ej. 2026)'],
            'ciudades' => $ts,
            'historial_estados' => ['id' => $id],
            'anticipo_pasos' => $ts + [
                'anticipo_id' => 'Anticipo (anticipos.id)',
                'accion' => 'Acción ejecutada: solicitar, aprobar, rechazar, causar, pagar, anular, retirar…',
                'estado_desde' => 'Estado antes de la acción (estados_anticipo.id)',
                'estado_hasta' => 'Estado después de la acción (estados_anticipo.id)',
                'user_id' => 'Quién ejecutó la acción (users.id)',
                'rol' => 'Rol activo con el que la ejecutó (roles.id)',
                'observacion' => 'Observación o justificación escrita en ese paso',
                'created_at' => 'Momento de la acción',
            ],
            'anticipos' => $ts + [
                'tipo' => 'juridico (a un proveedor, sobre una orden de compra) o productor (caja del productor, con evidencias)',
                'proveedor_id' => 'Proveedor beneficiario (proveedores.id), en el anticipo jurídico',
                'valor_base' => 'Valor sobre el que se calcula el porcentaje: la orden de compra vinculada o el valor digitado (borrador)',
                'cod_orden_anticipo' => 'Código de la orden de anticipo (OA-…), único',
                'archivo_orden_anticipo' => 'PDF de la orden de anticipo (ruta en storage)',
                'fecha_orden_anticipo' => 'Fecha de emisión del PDF de la orden de anticipo',
                'fecha_solicitud' => 'Cuándo lo pidió el productor',
                'fecha_aprobacion' => 'Cuándo quedó aprobado',
                'justificacion_rechazo' => 'Motivo del último rechazo (el historial completo está en historial_estados)',
                'observacion_causal' => 'Observación de Contabilidad al causar',
                'observaciones_contabilidad' => 'Observaciones de Contabilidad al devolver el anticipo',
                'fecha_causal' => 'Cuándo se causó',
            ],
            'asistentes' => $ts,
            'base_comerciales' => $ts + [
                'nom_cliente' => 'Nombre del cliente tal como se digitó',
                'nom_proyecto' => 'Nombre del proyecto',
                'cod_cc' => 'Centro de costos tal como se digitó (puede traer el nombre del proyecto); el código puro está en centro_costos',
                'cotizacion_file_actualizacion' => 'Archivo de la cotización actualizada (ruta en storage)',
                'com_1' => 'Participación (%) del primer comercial (histórico; hoy en gestion_participantes)',
                'com_2' => 'Participación (%) del segundo comercial (histórico)',
                'com_3' => 'Participación (%) del tercer comercial (histórico)',
                'fecha_inicio' => 'Inicio del proyecto',
                'fecha_facturacion' => 'Fecha prevista de facturación',
                'id_asistente' => 'Ejecutivo de cuenta que registró la venta (users.id)',
            ],
            'categorias_proveedor' => $ts + ['description' => 'Nombre de la categoría (Nómina, Logística, Impresión…)'],
            'cliente_parametros_cc' => $ts + [
                'nombre_empresa' => 'Nombre del cliente tal como va en el centro de costos',
                'cliente_id' => 'Cliente (clientes.id)',
                'codigo_cc' => 'Prefijo de 2 caracteres del centro de costos del cliente (p. ej. C3, 44), único',
            ],
            'clientes' => $ts + [
                'id_user' => 'Comercial dueño del cliente (users.id)',
                'estado_id' => 'Estado (estados_cuenta.id)',
                'nombre' => 'Nombre comercial',
                'razon_social' => 'Razón social',
                'nit' => 'NIT (único)',
                'direccion' => 'Dirección',
                'telefono' => 'Teléfono principal',
                'numero_telefono' => 'Otro teléfono',
                'cargo' => 'Cargo del contacto principal',
                'correo' => 'Correo principal',
                'pagina_web' => 'Página web',
                'correo_recpcion_facturas' => 'Correo donde el cliente recibe facturas',
                'adjuntar_archivos' => 'Documentos adjuntos del cliente (ruta en storage)',
            ],
            'contactos' => $ts + [
                'nombre' => 'Nombre', 'apellido' => 'Apellido', 'correo' => 'Correo', 'direccion' => 'Dirección',
                'ciudad' => 'Ciudad (texto); el enlace al catálogo está en ciudad_id',
                'id_user' => 'Comercial que lo registró (users.id)',
                'id_cliente' => 'Cliente al que pertenece (clientes.id)',
            ],
            'cuentas' => $ts + ['description' => 'Nombre de la cuenta o línea de negocio (Bull, V2V…)'],
            'estados_anticipo' => $catalogo + ['flujo' => 'Flujo al que aplica el estado: juridico, productor o ambos'],
            'estados_cuenta' => $catalogo,
            'estados_gestion_comercial' => $catalogo,
            'estados_ordenes_compra' => $catalogo,
            'estados_presupuesto' => $catalogo,
            'estados_proveedor' => $ts + ['nombre' => 'Nombre del estado', 'descripcion' => 'Qué significa', 'activo' => '1 = se ofrece en el formulario de proveedores'],
            'estados_tercero' => $ts + ['descripcion' => 'Nombre del estado'],
            'evidencias' => $ts + [
                'oc_id' => 'Orden de compra (ordenes_compra.id)',
                'tercero_id' => 'Persona (terceros.id) a la que corresponde la evidencia',
                'fecha_evidencia' => 'Fecha de la evidencia',
                'foto_evidencia' => 'Foto de la evidencia (ruta en storage)',
                'observacion_evidencia' => 'Observación escrita con la evidencia',
            ],
            'evidencias_anticipo' => $ts + [
                'anticipo_id' => 'Anticipo de productor (anticipos.id)',
                'item_id' => 'Ítem del presupuesto (items_presupuesto.id) que respalda',
                'fecha_evidencia' => 'Fecha de la evidencia',
                'foto_evidencia' => 'Foto o soporte (ruta en storage)',
                'observacion_evidencia' => 'Observación escrita con la evidencia',
            ],
            'failed_jobs' => ['id' => $id, 'uuid' => 'Identificador único del trabajo', 'connection' => 'Conexión de cola', 'queue' => 'Cola', 'payload' => 'Trabajo serializado', 'exception' => 'Error con el que falló', 'failed_at' => 'Cuándo falló'],
            'gestion_comercial' => ['id' => $id, 'created_at' => 'Apertura de la gestión', 'updated_at' => 'Último movimiento'],
            'gestion_participantes' => $ts,
            'helisa' => $ts + [
                'fecha' => 'Fecha del movimiento contable',
                'concepto' => 'Concepto del asiento',
                'base_factura' => 'Base de la factura: valor sobre el que se liquida la venta y la comisión',
            ],
            'historial_items_presupuesto' => $ts + [
                'item_presupuesto_id' => 'Ítem al que pertenece la copia (items_presupuesto.id)',
                'user_id' => 'Quién hizo el cambio (users.id)',
            ],
            'item_presupuesto_proveedor' => ['id' => $id, 'item_presupuesto_id' => 'Ítem del presupuesto', 'proveedor_id' => 'Proveedor asignado al ítem'],
            'items_anticipo' => $ts + [
                'display_item' => 'Número del ítem tal como se muestra en el presupuesto',
                'desc' => 'Descripción del ítem',
                'cant' => 'Cantidad',
                'dias' => 'Días',
                'otros' => 'Otros (meses u otro multiplicador)',
            ],
            'items_presupuesto' => $ts + ['descripcion' => 'Descripción del ítem o del evento'],
            'lider_comercial_user' => $ts + ['lider_id' => 'Líder comercial (users.id)', 'comercial_id' => 'Comercial de su equipo (users.id)'],
            'meses' => $ts + ['description' => 'Nombre del periodo (p. ej. Enero)'],
            'migrations' => ['id' => $id, 'migration' => 'Nombre del archivo de migración', 'batch' => 'Lote en que se aplicó'],
            'natural_info' => $ts + ['terminos' => '1 = la persona aceptó los términos'],
            'notificacion_destinatarios' => $ts + ['nombre' => 'Nombre visible del destinatario', 'activo' => '1 = recibe las notificaciones de su área'],
            'notificaciones_log' => $ts + [
                'copias' => 'Destinatarios en copia (JSON)',
                'asunto' => 'Asunto del correo o texto del SMS',
                'intentos' => 'Intentos de envío realizados',
                'error' => 'Último error de envío',
                'enviado_at' => 'Cuándo se envió',
            ],
            'oc_items' => $ts + ['num_item' => 'Número del ítem en el presupuesto', 'desc_oc' => 'Descripción en la orden', 'cantidad_horas' => 'Horas (órdenes de nómina)'],
            'ordenes_compra' => $ts + [
                'archivo_cuenta_cobro' => 'PDF de la cuenta de cobro (persona natural), ruta en storage',
                'observaciones_revision_lider' => 'Observación del líder de producción en su revisión (histórico; el historial completo está en historial_estados)',
                'rechazo_revision_lider' => 'Motivo de rechazo del líder de producción (histórico)',
                'observaciones_revision_gerencia' => 'Observación de Gerencia en su revisión (histórico)',
                'rechazo_revision_gerencia' => 'Motivo de rechazo de Gerencia (histórico)',
                'observaciones_revision_evidencias' => 'Observación en la revisión de evidencias (histórico)',
                'rechazo_revision_evidencias' => 'Motivo de rechazo de las evidencias (histórico)',
                'fecha_causal' => 'Cuándo se causó',
                'fecha_comprobante_pago' => 'Cuándo se registró el comprobante de pago',
                'observaciones' => 'Observaciones generales de la orden',
            ],
            'password_resets' => ['email' => 'Correo del usuario', 'token' => 'Token de restablecimiento', 'created_at' => 'Cuándo se pidió'],
            'permiso_rol' => ['id' => $id, 'permiso_id' => 'Permiso (permisos.id)', 'rol_id' => 'Rol que lo tiene (roles.id)'],
            'permiso_user' => ['id' => $id, 'permiso_id' => 'Permiso (permisos.id)', 'user_id' => 'Usuario que lo tiene además de los de su rol (users.id)'],
            'permisos' => $ts + ['clave' => 'Clave que usa el código (p. ej. aprobar-presupuestos)', 'descripcion' => 'Qué permite'],
            'personal_access_tokens' => $ts + ['tokenable_type' => 'Modelo dueño del token', 'tokenable_id' => 'id del dueño', 'name' => 'Nombre del token', 'token' => 'Token cifrado', 'abilities' => 'Permisos del token', 'last_used_at' => 'Último uso'],
            'presupuesto_proyecto' => $ts + [
                'margen_general' => 'Margen general (%) calculado a partir de los ítems',
                'costos_proy' => 'Costos totales del proyecto (suma de los ítems)',
                'margen_bruto' => 'Margen bruto en pesos (venta − costos)',
                'cod_cot' => 'Consecutivo de la cotización',
                'imprevistos' => 'Imprevistos (%) que se suman a la venta',
                'administracion' => 'Administración (%) que se suma a la venta',
                'fee' => 'Fee (%) que se suma a la venta',
                'tiempo_factura' => 'Días para facturar',
                'notas' => 'Notas del presupuesto',
                'justificacion_gerencia' => 'Justificación de Gerencia al rechazar (histórico; ver historial_estados)',
                'requiere_revalidacion' => '1 = hubo cambios después de aprobarse y debe volver a validarse',
            ],
            'presupuestos' => $ts + ['id_user' => 'Comercial (users.id) al que corresponde la meta'],
            'proveedores' => $ts + [
                'categoria_id' => 'Categoría (categorias_proveedor.id)',
                'tercero' => 'Nombre o razón social del proveedor',
                'tipo' => 'juridica, natural u Otros',
                'tipo_doc' => 'Tipo de documento: NIT, CEDULA o RUC',
                'documento' => 'Número de documento (único)',
                'dv' => 'Dígito de verificación del NIT',
                'direccion' => 'Dirección', 'departamento' => 'Departamento',
                'ciudad' => 'Ciudad (texto); el enlace al catálogo está en ciudad_id',
                'servicio' => 'Servicio que presta',
                'anticipo' => 'Porcentaje de anticipo pactado',
                'celular' => 'Celular', 'fijo' => 'Teléfono fijo', 'correo' => 'Correo (único)',
                'plazo' => 'Plazo de pago pactado',
                'contacto' => 'Persona de contacto',
                'web' => 'Página web',
                'observaciones' => 'Observaciones',
                'estado_legacy' => 'Estado en texto, antes del catálogo estados_proveedor (histórico)',
                'estado_id' => 'Estado del acuerdo (estados_proveedor.id)',
            ],
            'role_user' => $ts + ['user_id' => 'Cuenta (users.id)'],
            'roles' => $ts + ['description' => 'Nombre del rol'],
            'solicitud_contactos' => $ts + [
                'id_user' => 'Comercial que pidió el cliente (users.id)',
                'nombre' => 'Nombre comercial', 'razon_social' => 'Razón social', 'nit' => 'NIT', 'direccion' => 'Dirección',
                'telefono' => 'Teléfono principal', 'numero_telefono' => 'Otro teléfono', 'cargo' => 'Cargo del contacto',
                'correo' => 'Correo', 'pagina_web' => 'Página web', 'correo_recpcion_facturas' => 'Correo para facturas',
                'adjuntar_archivos' => 'Documentos adjuntos (ruta en storage)',
            ],
            'tarifario' => $ts + ['concepto' => 'Concepto', 'caso' => 'Caso o uso', 'caracteristicas' => 'Características', 'v_unidad' => 'Valor unitario de referencia', 'observacion' => 'Observación'],
            'terceros' => $ts + [
                'nombre' => 'Nombre', 'apellido' => 'Apellido', 'correo' => 'Correo', 'telefono' => 'Teléfono',
                'servicio' => 'Servicio que presta',
                'ciudad' => 'Ciudad (texto); el enlace al catálogo está en ciudad_id',
            ],
            'tipo_ordenes_compra' => $ts + ['description' => 'Jurídica, Natural o Nómina'],
            'users' => $ts + [
                'name' => 'Nombre completo', 'email' => 'Correo (único): es el usuario de acceso',
                'email_verified_at' => 'Cuándo verificó el correo (no se usa)',
                'password' => 'Contraseña cifrada', 'remember_token' => 'Token de "recordarme"',
            ],
        ];
    }
}
