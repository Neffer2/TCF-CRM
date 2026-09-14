# Diccionario de datos — TCF-CRM (BULLCRM)

Generado automáticamente desde el esquema de la base `tfc_crm` el 2026-09-13 19:04 con `php artisan tinker docs/generar-diccionario.php`.
Los comentarios provienen de los COMMENT del esquema (migración `2026_09_14_140000_documentar_esquema`).

## Mapa rápido del flujo

```
contactos ─► gestion_comercial (+ gestion_participantes) ─► presupuesto_proyecto ─► items_presupuesto (+ item_presupuesto_proveedor)
   └► base_comerciales (venta por comercial)      └► ordenes_compra ─► oc_items ─► anticipos ─► items_anticipo / evidencias_anticipo
helisa (facturación y comisiones)                 └► natural_info ─► terceros ─► evidencias
```

## Tablas

| Tabla | Filas aprox. | Descripción |
|---|---:|---|
| [`anos`](#anos) | 3 | Años fiscales |
| [`anticipos`](#anticipos) | 3 | Anticipos de dinero: jurídicos (oc_id) o de productor (presupuesto_id). estado_id -> estados_anticipo |
| [`asistentes`](#asistentes) | 10 | Relación ejecutivo de cuenta (asistente_id) -> comercial al que apoya (comercial_id) |
| [`base_comerciales`](#basecomerciales) | 6,844 | Base comercial: valor de venta de cada proyecto por comercial. Alimenta los dashboards de venta |
| [`categorias_proveedor`](#categoriasproveedor) | 119 | Categorías de proveedor |
| [`cliente_parametros_cc`](#clienteparametroscc) | 84 | Parámetros por cliente para la asignación de centros de costos |
| [`clientes`](#clientes) | 0 | Clientes (empresas) y sus parámetros |
| [`contactos`](#contactos) | 1,303 | Personas de contacto en los clientes (origen de los leads) |
| [`cuentas`](#cuentas) | 2 | Cuentas/marcas de cliente asociadas a la base comercial |
| [`estados_anticipo`](#estadosanticipo) | 2 | Estados del ciclo de vida de un anticipo |
| [`estados_cuenta`](#estadoscuenta) | 10 | Estados de un registro de la base comercial (ejecución por facturar, venta, facturado, ...) |
| [`estados_gestion_comercial`](#estadosgestioncomercial) | 7 | Etapas del embudo comercial (prospecto, oportunidad, propuesta, decisión, ...) |
| [`estados_ordenes_compra`](#estadosordenescompra) | 14 | Estados de una orden de compra (1 aprobado, 2 revisión, 3 editable, 4 recibido, 5 comprobado, 6 anulada, 7 evidencias, 8-14 flujo nómina) |
| [`estados_presupuesto`](#estadospresupuesto) | 5 | Estados del presupuesto de proyecto (en revisión, aprobado, rechazado, ...) |
| [`estados_tercero`](#estadostercero) | 0 | Estados de un tercero |
| [`evidencias`](#evidencias) | 16,985 | Evidencias (archivos) del trabajo de un tercero para una orden natural |
| [`evidencias_anticipo`](#evidenciasanticipo) | 0 | Evidencias del productor para cerrar un anticipo |
| [`failed_jobs`](#failedjobs) | 0 | Trabajos en cola fallidos (Laravel) |
| [`gestion_comercial`](#gestioncomercial) | 7,876 | Gestión comercial (lead/oportunidad/proyecto). Un registro por proyecto en el embudo; los participantes están en gestion_participantes |
| [`gestion_participantes`](#gestionparticipantes) | 9,049 | Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje |
| [`helisa`](#helisa) | 5,792 | Asientos de facturación exportados/registrados para el contable Helisa. Un documento puede tener varias filas (una por comercial y ajustes) |
| [`historial_items_presupuesto`](#historialitemspresupuesto) | 1,252 | Snapshot JSON de un ítem antes de cada modificación (auditoría de cambios) |
| [`item_presupuesto_proveedor`](#itempresupuestoproveedor) | 50,208 | Proveedores asignados a cada ítem de presupuesto (N:M) |
| [`items_anticipo`](#itemsanticipo) | 0 | Ítems de presupuesto incluidos en un anticipo de productor, con valor anticipado y saldo |
| [`items_presupuesto`](#itemspresupuesto) | 56,800 | Ítems (líneas) de un presupuesto de proyecto. Sus proveedores están en item_presupuesto_proveedor |
| [`lider_comercial_user`](#lidercomercialuser) | 16 | Relación líder comercial -> comerciales a su cargo |
| [`meses`](#meses) | 48 | Meses de cada año fiscal con su rango de fechas (f_inicio, f_fin) |
| [`migrations`](#migrations) | 31 | Control de migraciones de Laravel |
| [`natural_info`](#naturalinfo) | 3,274 | Datos adicionales de una orden natural: tercero contratado, productor, contrato |
| [`notificacion_destinatarios`](#notificaciondestinatarios) | 23 | A quién se notifica por área (controller, produccion, contabilidad, tesoreria, lider_comercial, compras, compras_cc, contabilidad_pagos) |
| [`oc_items`](#ocitems) | 16,583 | Líneas de una orden de compra (consumo de un ítem de presupuesto) |
| [`ordenes_compra`](#ordenescompra) | 9,774 | Órdenes de compra: tipo_oc 1 jurídica, 2 natural (persona), 3 nómina. estado_id -> estados_ordenes_compra |
| [`password_resets`](#passwordresets) | 0 | Tokens de restablecimiento de contraseña (Laravel) |
| [`permiso_rol`](#permisorol) | 7 | Permisos otorgados a un rol |
| [`permiso_user`](#permisouser) | 24 | Permisos otorgados directamente a una cuenta |
| [`permisos`](#permisos) | 7 | Permisos con nombre (Gates). Reemplazan los ids de usuario que estaban quemados en el código |
| [`personal_access_tokens`](#personalaccesstokens) | 0 | Tokens de API (Laravel Sanctum) |
| [`presupuesto_proyecto`](#presupuestoproyecto) | 6,508 | Presupuesto de un proyecto (centro de costos): totales, margen, aprobación y productor asignado |
| [`presupuestos`](#presupuestos) | 1,032 | Presupuesto (meta) comercial por año/mes/comercial |
| [`proveedores`](#proveedores) | 1,631 | Proveedores (terceros jurídicos). Los ids 1 y 2 son nómina y el 3 es "cuenta de cobro" (persona natural) |
| [`role_user`](#roleuser) | 216 | Roles que cada cuenta puede usar (multi-rol). El rol activo está en users.rol |
| [`roles_user`](#rolesuser) | 10 | Catálogo de roles (1 Admin, 2 Comercial, 5 Ejecutivo, 6 Líder producción, 7 Productor, 8 Tesorería, 9 Contabilidad, 10 Centinela, 20 Gerencia) |
| [`solicitud_contactos`](#solicitudcontactos) | 2 | Solicitudes de creación/actualización de contactos hechas por comerciales |
| [`tarifario`](#tarifario) | 145 | Tarifas de referencia para cotizar |
| [`terceros`](#terceros) | 21,482 | Personas naturales contratadas por producción (datos bancarios, RUT, firma) |
| [`tipo_ordenes_compra`](#tipoordenescompra) | 2 | Tipos de orden de compra (jurídica, natural, nómina) |
| [`users`](#users) | 212 | Cuentas de la plataforma. rol = rol ACTIVO (los roles asignables están en role_user) |

### anos

Años fiscales

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### anticipos

Anticipos de dinero: jurídicos (oc_id) o de productor (presupuesto_id). estado_id -> estados_anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| oc_id | `bigint unsigned` | sí |  | → `ordenes_compra.id` | Orden de compra (anticipo JURÍDICO). NULL en anticipos de productor |
| presupuesto_id | `bigint unsigned` | sí |  | → `presupuesto_proyecto.id` | Presupuesto/centro de costos (anticipo de PRODUCTOR). NULL en jurídicos |
| porcentaje_anticipo | `decimal(5,2)` | sí |  |  | Porcentaje de la orden que se anticipa (jurídico) |
| total_anticipo | `decimal(15,2)` | no |  |  | Valor total a anticipar |
| fecha_solicitud | `datetime` | sí |  |  |  |
| fecha_aprobacion | `datetime` | sí |  |  |  |
| justificacion_rechazo | `varchar(255)` | sí |  |  |  |
| productor_id | `bigint unsigned` | no |  | → `users.id` | Productor que solicita -> users.id |
| firma_productor | `varchar(255)` | sí |  |  | Imagen de la firma del productor |
| cod_causal | `varchar(255)` | sí |  |  | Código de causación asignado por contabilidad |
| observacion_causal | `text` | sí |  |  |  |
| fecha_causal | `datetime` | sí |  |  |  |
| comprobante_pago | `varchar(255)` | sí |  |  | Comprobante de pago registrado por tesorería (si existe, ya fue pagado) |
| fecha_comprobante_pago | `datetime` | sí |  |  | Fecha del pago |
| observaciones_revision_lider | `varchar(255)` | sí |  |  | Observaciones del líder al aprobar |
| rechazo_revision_lider | `varchar(255)` | sí |  |  | Motivo de rechazo del líder |
| observaciones_revision_gerencia | `varchar(255)` | sí |  |  | Observaciones de gerencia al aprobar |
| rechazo_revision_gerencia | `varchar(255)` | sí |  |  | Motivo de rechazo de gerencia |
| observaciones_revision_evidencias | `varchar(255)` | sí |  |  | Observaciones al revisar evidencias |
| rechazo_revision_evidencias | `varchar(255)` | sí |  |  | Motivo de rechazo de evidencias |
| estado_id | `bigint unsigned` | no | `2` | → `estados_anticipo.id` | -> estados_anticipo.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### asistentes

Relación ejecutivo de cuenta (asistente_id) -> comercial al que apoya (comercial_id)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| asistente_id | `bigint unsigned` | no |  | → `users.id` | Cuenta del ejecutivo de cuenta -> users.id |
| comercial_id | `bigint unsigned` | no |  | → `users.id` | Comercial al que apoya -> users.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### base_comerciales

Base comercial: valor de venta de cada proyecto por comercial. Alimenta los dashboards de venta

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| fecha | `date` | no |  |  | Fecha del registro (base de los dashboards por periodo) |
| nom_cliente | `varchar(255)` | no |  |  |  |
| nom_proyecto | `varchar(255)` | no |  |  |  |
| cod_cc | `varchar(255)` | sí |  |  |  |
| valor_original | `decimal(15,2)` | sí | `0.00` |  | Valor total del proyecto |
| cotizacion_file_actualizacion | `varchar(255)` | sí |  |  |  |
| porcentaje | `varchar(255)` | sí |  |  |  |
| valor_proyecto | `decimal(15,2)` | sí | `0.00` |  | Valor atribuido al comercial (su porcentaje del proyecto) |
| com_1 | `varchar(255)` | sí |  |  |  |
| com_2 | `varchar(255)` | sí |  |  |  |
| com_3 | `varchar(255)` | sí |  |  |  |
| id_estado | `bigint unsigned` | no |  | → `estados_cuenta.id` | Estado de la venta -> estados_cuenta.id |
| fecha_inicio | `date` | sí |  |  |  |
| dura_mes | `date` | sí |  |  | Duración en meses |
| fecha_facturacion | `date` | sí |  |  |  |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial al que se atribuye la venta -> users.id |
| id_asistente | `bigint unsigned` | sí |  | → `users.id` |  |
| id_cuenta | `bigint unsigned` | no | `1` | → `cuentas.id` | Cuenta/marca -> cuentas.id |
| id_gestion | `bigint unsigned` | sí |  | → `gestion_comercial.id` | -> gestion_comercial.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### categorias_proveedor

Categorías de proveedor

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### cliente_parametros_cc

Parámetros por cliente para la asignación de centros de costos

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| nombre_empresa | `varchar(255)` | no |  |  |  |
| cliente_id | `bigint unsigned` | sí |  | → `clientes.id` |  |
| codigo_cc | `varchar(2)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### clientes

Clientes (empresas) y sus parámetros

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| id_user | `bigint unsigned` | no |  | → `users.id` |  |
| estado_id | `bigint unsigned` | no |  | → `estados_cuenta.id` |  |
| nombre | `varchar(255)` | no |  |  |  |
| razon_social | `varchar(255)` | no |  |  |  |
| nit | `varchar(50)` | no |  |  |  |
| direccion | `varchar(255)` | no |  |  |  |
| telefono | `varchar(50)` | sí |  |  |  |
| numero_telefono | `varchar(50)` | no |  |  |  |
| cargo | `varchar(100)` | no |  |  |  |
| correo | `varchar(255)` | no |  |  |  |
| pagina_web | `varchar(255)` | sí |  |  |  |
| correo_recpcion_facturas | `varchar(255)` | no |  |  |  |
| adjuntar_archivos | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  |  |
| updated_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  |  |

### contactos

Personas de contacto en los clientes (origen de los leads)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| nombre | `varchar(255)` | no |  |  |  |
| apellido | `varchar(255)` | no |  |  |  |
| empresa | `varchar(255)` | no |  |  | Empresa/cliente al que pertenece |
| cargo | `varchar(255)` | sí |  |  | Cargo en la empresa |
| celular | `varchar(255)` | sí |  |  | Celular |
| correo | `varchar(255)` | sí |  |  |  |
| web | `varchar(255)` | sí |  |  | Sitio web |
| pbx | `varchar(255)` | sí |  |  | Teléfono fijo/PBX |
| direccion | `varchar(255)` | sí |  |  |  |
| ciudad | `varchar(255)` | sí |  |  |  |
| id_user | `bigint unsigned` | no |  | → `users.id` |  |
| id_cliente | `bigint unsigned` | sí |  | → `clientes.id` |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### cuentas

Cuentas/marcas de cliente asociadas a la base comercial

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_anticipo

Estados del ciclo de vida de un anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| flujo | `varchar(20)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_cuenta

Estados de un registro de la base comercial (ejecución por facturar, venta, facturado, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_gestion_comercial

Etapas del embudo comercial (prospecto, oportunidad, propuesta, decisión, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_ordenes_compra

Estados de una orden de compra (1 aprobado, 2 revisión, 3 editable, 4 recibido, 5 comprobado, 6 anulada, 7 evidencias, 8-14 flujo nómina)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_presupuesto

Estados del presupuesto de proyecto (en revisión, aprobado, rechazado, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### estados_tercero

Estados de un tercero

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| descripcion | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### evidencias

Evidencias (archivos) del trabajo de un tercero para una orden natural

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` |  |
| tercero_id | `bigint unsigned` | no |  | → `terceros.id` |  |
| fecha_evidencia | `date` | sí |  |  |  |
| foto_evidencia | `text` | sí |  |  |  |
| observacion_evidencia | `text` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### evidencias_anticipo

Evidencias del productor para cerrar un anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| anticipo_id | `bigint unsigned` | no |  | → `anticipos.id` |  |
| item_id | `bigint unsigned` | no |  | → `items_presupuesto.id` |  |
| fecha_evidencia | `date` | sí |  |  |  |
| foto_evidencia | `varchar(255)` | sí |  |  |  |
| observacion_evidencia | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### failed_jobs

Trabajos en cola fallidos (Laravel)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| uuid | `varchar(255)` | no |  |  |  |
| connection | `text` | no |  |  |  |
| queue | `text` | no |  |  |  |
| payload | `longtext` | no |  |  |  |
| exception | `longtext` | no |  |  |  |
| failed_at | `timestamp` | no | `CURRENT_TIMESTAMP` |  |  |

### gestion_comercial

Gestión comercial (lead/oportunidad/proyecto). Un registro por proyecto en el embudo; los participantes están en gestion_participantes

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| id_contacto | `bigint unsigned` | no |  | → `contactos.id` | Contacto (persona) que origina el lead -> contactos.id |
| tipo_contacto | `varchar(255)` | sí |  |  | Canal por el que llegó el contacto |
| desc_contacto | `varchar(255)` | sí |  |  | Descripción del primer contacto |
| presto_cot | `decimal(15,2)` | no | `0.00` |  | Valor cotizado (presupuesto de la cotización) |
| participaciones | `decimal(8,2)` | no | `1.00` |  | Número de comerciales que participan (1 a 4). El detalle está en gestion_participantes |
| nom_proyecto_cot | `varchar(255)` | sí |  |  | Nombre del proyecto en la cotización |
| fecha_estimada_cot | `date` | sí |  |  | Fecha estimada de ejecución (cotización) |
| cotizacion_file | `varchar(255)` | sí |  |  | Archivo de la cotización |
| claro | `tinyint(1)` | sí |  |  | 1 si el cliente es Claro (tenía reglas de aprobación propias) |
| presto_prop | `decimal(15,2)` | no | `0.00` |  | Valor de la propuesta |
| nom_proyecto_prop | `varchar(255)` | sí |  |  | Nombre del proyecto en la propuesta |
| fecha_estimada_prop | `date` | sí |  |  | Fecha estimada (propuesta) |
| propuesta_url | `varchar(255)` | sí |  |  | Enlace a la propuesta enviada al cliente |
| causa | `varchar(255)` | sí |  |  | Causa de pérdida/cierre cuando la decisión es negativa |
| id_estado | `bigint unsigned` | no | `1` | → `estados_gestion_comercial.id` | Etapa del embudo -> estados_gestion_comercial.id |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial responsable (dueño) de la gestión -> users.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### gestion_participantes

Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| gestion_id | `bigint unsigned` | no |  | → `gestion_comercial.id` | -> gestion_comercial.id |
| user_id | `bigint unsigned` | no |  | → `users.id` | Comercial participante -> users.id |
| posicion | `tinyint unsigned` | no | `1` |  | 1 = responsable de la gestión; 2..4 = participantes adicionales |
| porcentaje | `decimal(5,2)` | no | `0.00` |  | Porcentaje de la venta que se le atribuye (la suma debería ser 100) |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### helisa

Asientos de facturación exportados/registrados para el contable Helisa. Un documento puede tener varias filas (una por comercial y ajustes)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| fecha | `date` | no |  |  |  |
| tipo_doc | `varchar(255)` | no |  |  | Tipo de documento contable |
| num_doc | `varchar(255)` | no |  |  | Número de documento/factura |
| concepto | `varchar(255)` | sí |  |  |  |
| identidad | `varchar(255)` | no |  |  | NIT/cédula del tercero |
| nom_tercero | `varchar(255)` | no |  |  | Nombre del tercero facturado |
| centro | `varchar(255)` | no |  |  | Centro de costos |
| nom_centro_costo | `varchar(255)` | no |  |  | Nombre del centro de costos |
| debito | `decimal(12,2)` | sí |  |  | Valor débito |
| credito | `decimal(12,2)` | sí |  |  | Valor crédito |
| porcentaje | `varchar(255)` | no | `100` |  | Porcentaje de participación del comercial en la factura |
| comercial | `bigint unsigned` | no |  | → `users.id` | Comercial al que se atribuye -> users.id |
| id_cuenta | `bigint unsigned` | no | `1` | → `cuentas.id` | Cuenta/marca -> cuentas.id |
| participacion | `varchar(255)` | no |  |  | Número de comerciales que participan en la factura |
| base_factura | `decimal(12,2)` | no |  |  |  |
| mes | `varchar(255)` | no |  |  | Mes contable -> meses.id |
| año | `varchar(255)` | no |  |  | Año contable |
| comision | `decimal(12,2)` | no |  |  | Comisión calculada para el comercial (puede ser negativa en ajustes) |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### historial_items_presupuesto

Snapshot JSON de un ítem antes de cada modificación (auditoría de cambios)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| item_presupuesto_id | `bigint unsigned` | no |  | → `items_presupuesto.id` |  |
| valores_anteriores | `longtext` | no |  |  |  |
| user_id | `bigint unsigned` | sí |  | → `users.id` |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### item_presupuesto_proveedor

Proveedores asignados a cada ítem de presupuesto (N:M)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| item_presupuesto_id | `bigint unsigned` | no |  | → `items_presupuesto.id` |  |
| proveedor_id | `bigint unsigned` | no |  | → `proveedores.id` |  |

### items_anticipo

Ítems de presupuesto incluidos en un anticipo de productor, con valor anticipado y saldo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| anticipo_id | `bigint unsigned` | no |  | → `anticipos.id` | -> anticipos.id |
| item_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem de presupuesto -> items_presupuesto.id |
| display_item | `varchar(255)` | no |  |  |  |
| desc | `varchar(255)` | no |  |  |  |
| cant | `int` | no |  |  |  |
| dias | `int` | no |  |  |  |
| otros | `int` | no |  |  |  |
| vunit | `decimal(15,2)` | no | `0.00` |  | Valor unitario |
| vtotal | `decimal(15,2)` | no | `0.00` |  | Valor total del ítem |
| vanticipo | `decimal(15,2)` | no | `0.00` |  | Valor anticipado (<= vtotal) |
| saldo | `decimal(15,2)` | no | `0.00` |  | vtotal - vanticipo |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### items_presupuesto

Ítems (líneas) de un presupuesto de proyecto. Sus proveedores están en item_presupuesto_proveedor

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| presupuesto_id | `bigint unsigned` | no |  | → `presupuesto_proyecto.id` | -> presupuesto_proyecto.id |
| num_item | `int` | sí |  |  | Número de ítem visible al usuario (secuencial dentro del presupuesto) |
| orden | `int` | sí |  |  | Orden de presentación |
| cod | `int` | no |  |  | Código/categoría del ítem |
| evento | `varchar(255)` | no | `0` |  | 1 = fila de evento (agrupador sin valores), 0 = ítem normal |
| cantidad | `int` | no |  |  | Cantidad de unidades |
| dia | `int` | no |  |  | Número de días |
| otros | `int` | no |  |  | Otro multiplicador (p.ej. personas). Cantidad total = cantidad * dia * otros |
| descripcion | `varchar(2000)` | no |  |  |  |
| v_unitario | `decimal(15,2)` | no | `0.00` |  | Costo unitario interno |
| v_total | `decimal(15,2)` | no | `0.00` |  | Costo total interno (cantidad * dia * otros * v_unitario) |
| v_total_cliente | `decimal(15,2)` | no | `0.00` |  | Valor total al cliente (variante usada en cotización) |
| proveedor_legacy | `varchar(255)` | sí |  |  | OBSOLETO: valor original del proveedor. Usar item_presupuesto_proveedor |
| margen_utilidad | `double(15,10)` | no | `0.0000000000` |  | Margen de utilidad del ítem (fracción; 0.35 = 35 %) |
| mes | `varchar(255)` | no |  |  | Mes de ejecución -> meses.id |
| dias | `int` | no |  |  | Días de ejecución (dato informativo) |
| ciudad | `varchar(255)` | no |  |  | Ciudad de ejecución |
| v_unitario_cot | `decimal(15,2)` | no | `0.00` |  | Precio unitario al cliente |
| v_total_cot | `decimal(15,2)` | no | `0.00` |  | Precio total al cliente (base del margen) |
| rentabilidad | `decimal(15,2)` | no | `0.00` |  | Rentabilidad calculada del ítem |
| actualizado | `tinyint(1)` | no | `0` |  | Marca de actualización tras asignado el centro de costos (0..3, ver código) |
| actualizado_con | `tinyint` | no | `0` |  | Marca de actualización vista por contabilidad |
| disponible | `tinyint(1)` | no | `1` |  | 1 = el ítem puede consumirse en órdenes/anticipos |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### lider_comercial_user

Relación líder comercial -> comerciales a su cargo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| lider_id | `bigint unsigned` | no |  | → `users.id` |  |
| comercial_id | `bigint unsigned` | no |  | → `users.id` |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### meses

Meses de cada año fiscal con su rango de fechas (f_inicio, f_fin)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| identifier | `varchar(255)` | no |  |  | Número del mes (1..12) |
| ano_id | `bigint unsigned` | no |  | → `anos.id` | -> anos.id |
| f_inicio | `date` | sí |  |  | Primer día del mes |
| f_fin | `date` | sí |  |  | Último día del mes |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### migrations

Control de migraciones de Laravel

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `int unsigned` | no |  |  |  |
| migration | `varchar(255)` | no |  |  |  |
| batch | `int` | no |  |  |  |

### natural_info

Datos adicionales de una orden natural: tercero contratado, productor, contrato

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` | -> ordenes_compra.id |
| tercero_id | `bigint unsigned` | no |  | → `terceros.id` | Persona contratada -> terceros.id |
| productor_id | `bigint unsigned` | no |  | → `users.id` | Productor que contrata -> users.id |
| contrato | `varchar(255)` | sí |  |  | Archivo del contrato firmado |
| terminos | `tinyint(1)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### notificacion_destinatarios

A quién se notifica por área (controller, produccion, contabilidad, tesoreria, lider_comercial, compras, compras_cc, contabilidad_pagos)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| area | `varchar(255)` | no |  |  | Área/evento que notifica |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Usuario de la plataforma (si está suspendido no recibe) |
| email_externo | `varchar(255)` | sí |  |  | Buzón externo cuando no es usuario de la plataforma |
| nombre | `varchar(255)` | sí |  |  |  |
| activo | `tinyint(1)` | no | `1` |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### oc_items

Líneas de una orden de compra (consumo de un ítem de presupuesto)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` | -> ordenes_compra.id |
| item_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem de presupuesto consumido -> items_presupuesto.id |
| num_item | `varchar(255)` | sí |  |  |  |
| desc_oc | `varchar(2000)` | no |  |  |  |
| cant_oc | `int` | no |  |  | Cantidad consumida |
| dias_oc | `int` | no |  |  | Días |
| otros_oc | `int` | no |  |  | Otro multiplicador |
| vunit_oc | `decimal(15,2)` | no | `0.00` |  | Valor unitario |
| vtotal_oc | `decimal(15,2)` | no | `0.00` |  | Valor total de la línea (descuenta del saldo del ítem) |
| tipo_servicio | `varchar(255)` | sí |  |  | Tipo de servicio (órdenes naturales) |
| tipo_contrato | `varchar(255)` | sí |  |  | Tipo de contrato (órdenes naturales) |
| cantidad_horas | `int` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### ordenes_compra

Órdenes de compra: tipo_oc 1 jurídica, 2 natural (persona), 3 nómina. estado_id -> estados_ordenes_compra

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| tipo_oc | `bigint unsigned` | no |  | → `tipo_ordenes_compra.id` | 1 jurídica (proveedor), 2 natural (persona), 3 nómina -> tipo_ordenes_compra.id |
| estado_id | `bigint unsigned` | no | `2` | → `estados_ordenes_compra.id` | -> estados_ordenes_compra.id |
| fecha_aprobacion | `datetime` | sí |  |  | Fecha de aprobación |
| fecha_envio_produccion | `datetime` | sí |  |  | Fecha en que el productor la envió a aprobación |
| presupuesto_id | `bigint unsigned` | sí |  | → `presupuesto_proyecto.id` | Presupuesto que consume (NULL en órdenes naturales; ver natural_info) -> presupuesto_proyecto.id |
| proveedor_id | `bigint unsigned` | no |  | → `proveedores.id` | -> proveedores.id (3 = cuenta de cobro/persona natural) |
| justificacion_rechazo | `varchar(255)` | sí |  |  | Motivo del rechazo |
| observaciones_negociacion | `varchar(255)` | sí |  |  | Observaciones del controller al aprobar |
| archivo_cot | `varchar(255)` | sí |  |  | Cotización del proveedor adjunta por el productor |
| archivo_orden_helisa | `varchar(255)` | sí |  |  | PDF de la orden generado al aprobar (para Helisa) |
| cod_causal | `varchar(255)` | sí |  |  | Código de causación contable (flujo viejo) |
| observacion_causal | `varchar(255)` | sí |  |  | Observación de contabilidad al causar |
| archivo_comprobante_pago | `varchar(255)` | sí |  |  | Comprobante de pago (flujo viejo de anticipos sobre la orden) |
| archivo_remision | `varchar(255)` | sí |  |  | PDF de la remisión firmada |
| archivo_cuenta_cobro | `varchar(255)` | sí |  |  |  |
| observacion_remision | `varchar(255)` | sí |  |  | Observaciones al firmar la remisión |
| archivo_firma | `varchar(255)` | sí |  |  | Imagen de la firma del productor en la remisión |
| cod_oc | `varchar(255)` | sí |  |  | Código de la orden asignado al aprobar (OC{id}) |
| gr | `varchar(255)` | sí |  |  | Código del Good Receive (GR{id}) cuando se comprueba |
| actualizado | `tinyint(1)` | no | `0` |  | Marca de orden actualizada tras aprobación |
| observaciones_anulacion | `varchar(255)` | sí |  |  | Motivo de anulación |
| observaciones_revision_lider | `varchar(255)` | sí |  |  |  |
| rechazo_revision_lider | `varchar(255)` | sí |  |  |  |
| observaciones_revision_gerencia | `varchar(255)` | sí |  |  |  |
| rechazo_revision_gerencia | `varchar(255)` | sí |  |  |  |
| observaciones_revision_evidencias | `varchar(255)` | sí |  |  |  |
| rechazo_revision_evidencias | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### password_resets

Tokens de restablecimiento de contraseña (Laravel)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| email | `varchar(255)` | no |  |  |  |
| token | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |

### permiso_rol

Permisos otorgados a un rol

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| permiso_id | `bigint unsigned` | no |  | → `permisos.id` |  |
| rol_id | `bigint unsigned` | no |  | → `roles_user.id` |  |

### permiso_user

Permisos otorgados directamente a una cuenta

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| permiso_id | `bigint unsigned` | no |  | → `permisos.id` |  |
| user_id | `bigint unsigned` | no |  | → `users.id` |  |

### permisos

Permisos con nombre (Gates). Reemplazan los ids de usuario que estaban quemados en el código

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| clave | `varchar(255)` | no |  |  |  |
| descripcion | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### personal_access_tokens

Tokens de API (Laravel Sanctum)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| tokenable_type | `varchar(255)` | no |  |  |  |
| tokenable_id | `bigint unsigned` | no |  |  |  |
| name | `varchar(255)` | no |  |  |  |
| token | `varchar(64)` | no |  |  |  |
| abilities | `text` | sí |  |  |  |
| last_used_at | `timestamp` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### presupuesto_proyecto

Presupuesto de un proyecto (centro de costos): totales, margen, aprobación y productor asignado

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| id_gestion | `bigint unsigned` | no |  | → `gestion_comercial.id` | Gestión comercial a la que pertenece -> gestion_comercial.id |
| estado_id | `bigint unsigned` | no | `3` | → `estados_presupuesto.id` | -> estados_presupuesto.id (1 = aprobado) |
| margen_general | `double(15,10)` | no | `0.0000000000` |  |  |
| venta_proy | `decimal(15,2)` | no | `0.00` |  | Valor de venta del proyecto (total al cliente) |
| costos_proy | `decimal(15,2)` | no | `0.00` |  |  |
| margen_proy | `decimal(15,2)` | no | `0.00` |  | Margen % del proyecto. La aprobación exige >= 35 % (regla de Gerencia) |
| margen_bruto | `decimal(15,2)` | no | `0.00` |  |  |
| cod_cot | `int` | no |  |  |  |
| cod_cc | `varchar(255)` | sí |  |  | Código de centro de costos asignado al aprobar |
| fecha_cc | `date` | sí |  |  | Fecha de asignación del centro de costos |
| imprevistos | `decimal(15,2)` | no | `0.00` |  |  |
| administracion | `decimal(15,2)` | no | `0.00` |  |  |
| fee | `decimal(15,2)` | no | `0.00` |  |  |
| tiempo_factura | `int` | no | `30` |  |  |
| notas | `varchar(255)` | sí |  |  |  |
| justificacion | `varchar(255)` | sí |  |  | Observaciones del comercial al enviar a aprobación |
| justificacion_compras | `varchar(255)` | sí |  |  | Observaciones de compras/gerencia al aprobar o rechazar |
| justificacion_lider | `varchar(255)` | sí |  |  | Observaciones del líder comercial |
| justificacion_gerencia | `varchar(255)` | sí |  |  |  |
| productor | `bigint unsigned` | sí |  | → `users.id` | Productor asignado a ejecutar el proyecto -> users.id |
| notificacion_actualizacion | `tinyint(1)` | sí |  |  | Marca de notificación pendiente por actualización del presupuesto |
| requiere_revalidacion | `bigint` | sí | `0` |  |  |
| comercial_id | `bigint unsigned` | sí |  | → `users.id` | Comercial responsable -> users.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### presupuestos

Presupuesto (meta) comercial por año/mes/comercial

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| ano_id | `bigint unsigned` | no |  | → `anos.id` | -> anos.id |
| mes_id | `bigint unsigned` | no |  | → `meses.id` | -> meses.id |
| valor | `decimal(15,2)` | no |  |  | Meta de venta del comercial en el mes |
| id_user | `bigint unsigned` | no |  | → `users.id` |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### proveedores

Proveedores (terceros jurídicos). Los ids 1 y 2 son nómina y el 3 es "cuenta de cobro" (persona natural)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| categoria_id | `bigint unsigned` | no |  | → `categorias_proveedor.id` |  |
| tercero | `varchar(255)` | no |  |  |  |
| tipo | `varchar(255)` | no |  |  |  |
| tipo_doc | `varchar(255)` | no |  |  |  |
| documento | `varchar(255)` | no |  |  |  |
| dv | `varchar(255)` | no |  |  |  |
| direccion | `varchar(255)` | sí |  |  |  |
| departamento | `varchar(255)` | no |  |  |  |
| ciudad | `varchar(255)` | no |  |  |  |
| servicio | `varchar(255)` | no |  |  |  |
| anticipo | `varchar(255)` | no |  |  |  |
| celular | `varchar(255)` | no |  |  |  |
| fijo | `varchar(255)` | sí |  |  |  |
| correo | `varchar(255)` | no |  |  |  |
| plazo | `varchar(255)` | sí |  |  |  |
| contacto | `varchar(255)` | no |  |  |  |
| web | `varchar(255)` | sí |  |  |  |
| observaciones | `varchar(255)` | sí |  |  |  |
| estado | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### role_user

Roles que cada cuenta puede usar (multi-rol). El rol activo está en users.rol

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| user_id | `bigint unsigned` | no |  | → `users.id` |  |
| rol_id | `bigint unsigned` | no |  | → `roles_user.id` | -> roles_user.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### roles_user

Catálogo de roles (1 Admin, 2 Comercial, 5 Ejecutivo, 6 Líder producción, 7 Productor, 8 Tesorería, 9 Contabilidad, 10 Centinela, 20 Gerencia)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### solicitud_contactos

Solicitudes de creación/actualización de contactos hechas por comerciales

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| id_user | `bigint unsigned` | no |  | → `users.id` |  |
| estado | `enum('Pendiente','En revisión','Aprobado','Rechazado')` | sí | `Pendiente` |  |  |
| nombre | `varchar(255)` | no |  |  |  |
| razon_social | `varchar(255)` | no |  |  |  |
| nit | `varchar(50)` | no |  |  |  |
| direccion | `varchar(255)` | no |  |  |  |
| telefono | `varchar(50)` | sí |  |  |  |
| numero_telefono | `varchar(50)` | no |  |  |  |
| cargo | `varchar(100)` | no |  |  |  |
| correo | `varchar(255)` | no |  |  |  |
| pagina_web | `varchar(255)` | sí |  |  |  |
| correo_recpcion_facturas | `varchar(255)` | no |  |  |  |
| adjuntar_archivos | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  |  |
| updated_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  |  |

### tarifario

Tarifas de referencia para cotizar

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| concepto | `varchar(255)` | sí |  |  |  |
| caso | `varchar(255)` | sí |  |  |  |
| caracteristicas | `varchar(255)` | sí |  |  |  |
| v_unidad | `decimal(15,2)` | no | `0.00` |  |  |
| observacion | `varchar(255)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### terceros

Personas naturales contratadas por producción (datos bancarios, RUT, firma)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| nombre | `varchar(255)` | sí |  |  |  |
| apellido | `varchar(255)` | sí |  |  |  |
| cedula | `varchar(255)` | sí |  |  | Documento de identidad |
| correo | `varchar(255)` | sí |  |  |  |
| telefono | `varchar(255)` | sí |  |  |  |
| servicio | `varchar(255)` | sí |  |  |  |
| ciudad | `varchar(255)` | sí |  |  |  |
| banco | `varchar(255)` | sí |  |  | Banco para el pago |
| tipo_cuenta | `varchar(255)` | sí |  |  | Tipo de cuenta bancaria |
| num_cuenta | `varchar(255)` | sí |  |  | Número de cuenta bancaria |
| num_rut | `varchar(255)` | sí |  |  | Número de RUT |
| rut | `varchar(255)` | sí |  |  | Archivo RUT |
| cert_bancaria | `varchar(255)` | sí |  |  | Certificación bancaria (archivo) |
| copia_cedula | `varchar(255)` | sí |  |  | Copia de la cédula (archivo) |
| art383 | `varchar(255)` | sí |  |  | Formato artículo 383 (archivo) |
| planilla_aportes | `varchar(255)` | sí |  |  | Planilla de aportes (archivo) |
| firma | `varchar(255)` | sí |  |  | Imagen de la firma capturada en el portal |
| estado | `bigint unsigned` | no | `1` | → `estados_tercero.id` | -> estados_tercero.id |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### tipo_ordenes_compra

Tipos de orden de compra (jurídica, natural, nómina)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| description | `varchar(255)` | no |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

### users

Cuentas de la plataforma. rol = rol ACTIVO (los roles asignables están en role_user)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  |  |
| name | `varchar(255)` | no |  |  |  |
| email | `varchar(255)` | no |  |  |  |
| telefono | `varchar(255)` | no |  |  | Celular (se usa para SMS de notificación) |
| email_verified_at | `timestamp` | sí |  |  |  |
| password | `varchar(255)` | no |  |  |  |
| rol | `bigint unsigned` | no | `2` | → `roles_user.id` | Rol ACTIVO (FK lógica a roles_user). Los demás roles de la cuenta están en role_user |
| avatar | `varchar(255)` | sí | `public/photos/avatar.jpg` |  | Ruta de la foto de perfil en storage |
| remember_token | `varchar(100)` | sí |  |  |  |
| created_at | `timestamp` | sí |  |  |  |
| updated_at | `timestamp` | sí |  |  |  |

