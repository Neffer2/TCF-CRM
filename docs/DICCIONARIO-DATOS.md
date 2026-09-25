# Diccionario de datos — TCF-CRM (BULLCRM)

Generado automáticamente desde el esquema de la base `tfc_crm` el 2026-09-25 12:17 con `php artisan tinker docs/generar-diccionario.php`.
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
| [`actividad_log`](#actividadlog) | 1,534 | Auditoría: páginas, acciones, sesiones y cambios de datos por usuario |
| [`anos`](#anos) | 4 | Años fiscales |
| [`anticipo_pasos`](#anticipopasos) | 8 | Recorrido del anticipo jurídico: cada acción (solicitar, revisar, aprobar, rechazar, causar, pagar, anular, retirar) con quién, cuándo y con qué observación |
| [`anticipos`](#anticipos) | 5 | Anticipos de dinero: jurídicos (oc_id) o de productor (presupuesto_id). estado_id -> estados_anticipo |
| [`asistentes`](#asistentes) | 11 | Relación ejecutivo de cuenta (asistente_id) -> comercial al que apoya (comercial_id) |
| [`base_comerciales`](#basecomerciales) | 6,637 | Base comercial: valor de venta de cada proyecto por comercial. Alimenta los dashboards de venta |
| [`categorias_proveedor`](#categoriasproveedor) | 119 | Categorías de proveedor |
| [`ciudades`](#ciudades) | 100 | Catálogo de ciudades: reemplaza la lista fija del código y unifica el texto libre de ítems, proveedores, terceros y contactos |
| [`cliente_parametros_cc`](#clienteparametroscc) | 84 | Parámetros por cliente para la asignación de centros de costos |
| [`clientes`](#clientes) | 0 | Clientes (empresas) y sus parámetros |
| [`contactos`](#contactos) | 1,303 | Personas de contacto en los clientes (origen de los leads) |
| [`cuentas`](#cuentas) | 2 | Cuentas/marcas de cliente asociadas a la base comercial |
| [`estados_anticipo`](#estadosanticipo) | 16 | Estados del ciclo de vida de un anticipo |
| [`estados_cuenta`](#estadoscuenta) | 10 | Estados de un registro de la base comercial (ejecución por facturar, venta, facturado, ...) |
| [`estados_gestion_comercial`](#estadosgestioncomercial) | 7 | Etapas del embudo comercial (prospecto, oportunidad, propuesta, decisión, ...) |
| [`estados_ordenes_compra`](#estadosordenescompra) | 14 | Estados de una orden de compra (1 aprobado, 2 revisión, 3 editable, 4 recibido, 5 comprobado, 6 anulada, 7 evidencias, 8-14 flujo nómina) |
| [`estados_presupuesto`](#estadospresupuesto) | 5 | Estados del presupuesto de proyecto (en revisión, aprobado, rechazado, ...) |
| [`estados_proveedor`](#estadosproveedor) | 4 | Catálogo de estados del acuerdo con el proveedor (activo, suspendido, etc.) |
| [`estados_tercero`](#estadostercero) | 0 | Estados de un tercero |
| [`evidencias`](#evidencias) | 16,783 | Evidencias (archivos) del trabajo de un tercero para una orden natural |
| [`evidencias_anticipo`](#evidenciasanticipo) | 0 | Evidencias del productor para cerrar un anticipo |
| [`failed_jobs`](#failedjobs) | 0 | Laravel: trabajos en cola que fallaron (p. ej. envíos de notificaciones) |
| [`gestion_comercial`](#gestioncomercial) | 7,553 | Gestión comercial (lead/oportunidad/proyecto). Un registro por proyecto en el embudo; los participantes están en gestion_participantes |
| [`gestion_participantes`](#gestionparticipantes) | 9,049 | Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje |
| [`helisa`](#helisa) | 5,853 | Asientos de facturación exportados/registrados para el contable Helisa. Un documento puede tener varias filas (una por comercial y ajustes) |
| [`historial_estados`](#historialestados) | 46,333 | Historial de cambios de estado (una fila por cambio) de órdenes, anticipos, presupuestos, gestiones y terceros; lo escribe HistorialEstadoObserver |
| [`historial_items_presupuesto`](#historialitemspresupuesto) | 1,206 | Snapshot JSON de un ítem antes de cada modificación (auditoría de cambios) |
| [`item_presupuesto_proveedor`](#itempresupuestoproveedor) | 50,329 | Proveedores asignados a cada ítem de presupuesto (N:M) |
| [`items_anticipo`](#itemsanticipo) | 0 | Ítems de presupuesto incluidos en un anticipo de productor, con valor anticipado y saldo |
| [`items_presupuesto`](#itemspresupuesto) | 57,009 | Ítems (líneas) de un presupuesto de proyecto. Sus proveedores están en item_presupuesto_proveedor |
| [`lider_comercial_user`](#lidercomercialuser) | 19 | Relación líder comercial -> comerciales a su cargo |
| [`meses`](#meses) | 48 | Meses de cada año fiscal con su rango de fechas (f_inicio, f_fin) |
| [`migrations`](#migrations) | 54 | Laravel: migraciones aplicadas a esta base |
| [`natural_info`](#naturalinfo) | 3,272 | Datos adicionales de una orden natural: tercero contratado, productor, contrato |
| [`notificacion_destinatarios`](#notificaciondestinatarios) | 23 | A quién se notifica por área (controller, produccion, contabilidad, tesoreria, lider_comercial, compras, compras_cc, contabilidad_pagos) |
| [`notificaciones_log`](#notificacioneslog) | 12 | Registro de correos y SMS enviados por el CRM, con resultado y reintentos |
| [`oc_items`](#ocitems) | 16,067 | Líneas de una orden de compra (consumo de un ítem de presupuesto) |
| [`ordenes_compra`](#ordenescompra) | 9,412 | Órdenes de compra: tipo_oc 1 jurídica, 2 natural (persona), 3 nómina. estado_id -> estados_ordenes_compra |
| [`password_resets`](#passwordresets) | 0 | Laravel: tokens de restablecimiento de contraseña (el flujo público se retiró; la tabla queda por compatibilidad) |
| [`permiso_rol`](#permisorol) | 7 | Permisos otorgados a un rol |
| [`permiso_user`](#permisouser) | 24 | Permisos otorgados directamente a una cuenta |
| [`permisos`](#permisos) | 7 | Permisos con nombre (Gates). Reemplazan los ids de usuario que estaban quemados en el código |
| [`personal_access_tokens`](#personalaccesstokens) | 0 | Laravel Sanctum: tokens de API (no se usan en el CRM) |
| [`presupuesto_proyecto`](#presupuestoproyecto) | 6,808 | Presupuesto de un proyecto (centro de costos): totales, margen, aprobación y productor asignado |
| [`presupuestos`](#presupuestos) | 1,032 | Presupuesto (meta) comercial por año/mes/comercial |
| [`proveedores`](#proveedores) | 1,681 | Proveedores (terceros jurídicos). Los ids 1 y 2 son nómina y el 3 es "cuenta de cobro" (persona natural) |
| [`role_user`](#roleuser) | 226 | Roles que cada cuenta puede usar (multi-rol). El rol activo está en users.rol |
| [`roles`](#roles) | 13 | Catálogo de roles (antes roles_user). users.rol es el rol activo; role_user, los roles que cada cuenta puede usar |
| [`solicitud_contactos`](#solicitudcontactos) | 2 | Solicitudes de creación/actualización de contactos hechas por comerciales |
| [`tarifario`](#tarifario) | 0 | Tarifas de referencia para cotizar |
| [`terceros`](#terceros) | 0 | Personas naturales contratadas por producción (datos bancarios, RUT, firma) |
| [`tipo_ordenes_compra`](#tipoordenescompra) | 0 | Tipos de orden de compra (jurídica, natural, nómina) |
| [`users`](#users) | 0 | Cuentas de la plataforma. rol = rol ACTIVO (los roles asignables están en role_user) |

### actividad_log

Auditoría: páginas, acciones, sesiones y cambios de datos por usuario

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Usuario que hizo la acción (users.id); NULL en intentos de acceso fallidos |
| usuario | `varchar(120)` | sí |  |  | Nombre en el momento (por si la cuenta cambia) |
| rol | `bigint unsigned` | sí |  | → `roles.id` | Rol activo del usuario en ese momento (roles.id) |
| tipo | `varchar(12)` | no |  |  | sesion \| pagina \| accion \| datos |
| accion | `varchar(160)` | no |  |  | Qué hizo: ruta, componente.metodo, modelo:evento, login/logout |
| objeto | `varchar(120)` | sí |  |  | Registro afectado, p. ej. ordenes_compra:123 |
| detalle | `json` | sí |  |  | Datos enviados o cambios (antes -> después), sin claves |
| metodo | `varchar(8)` | sí |  |  | Método HTTP (GET, POST…) |
| ruta | `varchar(255)` | sí |  |  | Ruta visitada o acción ejecutada |
| estado_http | `smallint unsigned` | sí |  |  | Código de respuesta HTTP (200, 403, 500…) |
| duracion_ms | `int unsigned` | sí |  |  | Tiempo de respuesta en milisegundos |
| ip | `varchar(45)` | sí |  |  | Dirección IP del navegador |
| navegador | `varchar(255)` | sí |  |  | Identificación del navegador (User-Agent) |
| created_at | `timestamp` | no | `CURRENT_TIMESTAMP` |  | Creación del registro |

### anos

Años fiscales

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Año (texto, p. ej. 2026) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### anticipo_pasos

Recorrido del anticipo jurídico: cada acción (solicitar, revisar, aprobar, rechazar, causar, pagar, anular, retirar) con quién, cuándo y con qué observación

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| anticipo_id | `bigint unsigned` | no |  | → `anticipos.id` | Anticipo (anticipos.id) |
| accion | `varchar(30)` | no |  |  | Acción ejecutada: solicitar, aprobar, rechazar, causar, pagar, anular, retirar… |
| estado_desde | `bigint unsigned` | sí |  |  | Estado antes de la acción (estados_anticipo.id) |
| estado_hasta | `bigint unsigned` | no |  |  | Estado después de la acción (estados_anticipo.id) |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Quién ejecutó la acción (users.id) |
| rol | `bigint unsigned` | sí |  | → `roles.id` | Rol activo con el que la ejecutó (roles.id) |
| observacion | `text` | sí |  |  | Observación o justificación escrita en ese paso |
| created_at | `timestamp` | sí |  |  | Creación del registro |

### anticipos

Anticipos de dinero: jurídicos (oc_id) o de productor (presupuesto_id). estado_id -> estados_anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| tipo | `varchar(12)` | no | `productor` |  | juridico (a un proveedor, sobre una orden de compra) o productor (caja del productor, con evidencias) |
| oc_id | `bigint unsigned` | sí |  | → `ordenes_compra.id` | Orden de compra (anticipo JURÍDICO). NULL en anticipos de productor |
| presupuesto_id | `bigint unsigned` | sí |  | → `presupuesto_proyecto.id` | Presupuesto/centro de costos (anticipo de PRODUCTOR). NULL en jurídicos |
| proveedor_id | `bigint unsigned` | sí |  | → `proveedores.id` | Proveedor beneficiario (proveedores.id), en el anticipo jurídico |
| porcentaje_anticipo | `decimal(5,2)` | sí |  |  | Porcentaje de la orden que se anticipa (jurídico) |
| valor_base | `decimal(15,2)` | sí |  |  | Valor sobre el que se calcula el porcentaje: la orden de compra vinculada o el valor digitado (borrador) |
| total_anticipo | `decimal(15,2)` | no |  |  | Valor total a anticipar |
| cod_orden_anticipo | `varchar(20)` | sí |  |  | Código de la orden de anticipo (OA-…), único |
| archivo_orden_anticipo | `varchar(255)` | sí |  |  | PDF de la orden de anticipo (ruta en storage) |
| fecha_orden_anticipo | `datetime` | sí |  |  | Fecha de emisión del PDF de la orden de anticipo |
| fecha_solicitud | `datetime` | sí |  |  | Cuándo lo pidió el productor |
| fecha_aprobacion | `datetime` | sí |  |  | Cuándo quedó aprobado |
| justificacion_rechazo | `varchar(255)` | sí |  |  | Motivo del último rechazo (el historial completo está en historial_estados) |
| productor_id | `bigint unsigned` | no |  | → `users.id` | Productor que solicita -> users.id |
| firma_productor | `varchar(255)` | sí |  |  | Imagen de la firma del productor |
| cod_causal | `varchar(255)` | sí |  |  | Código de causación asignado por contabilidad |
| observacion_causal | `text` | sí |  |  | Observación de Contabilidad al causar |
| observaciones_contabilidad | `text` | sí |  |  | Observaciones de Contabilidad al devolver el anticipo |
| fecha_causal | `datetime` | sí |  |  | Cuándo se causó |
| comprobante_pago | `varchar(255)` | sí |  |  | Comprobante de pago registrado por tesorería (si existe, ya fue pagado) |
| fecha_comprobante_pago | `datetime` | sí |  |  | Fecha del pago |
| observaciones_revision_lider | `varchar(255)` | sí |  |  | Observaciones del líder al aprobar |
| rechazo_revision_lider | `varchar(255)` | sí |  |  | Motivo de rechazo del líder |
| observaciones_revision_gerencia | `varchar(255)` | sí |  |  | Observaciones de gerencia al aprobar |
| rechazo_revision_gerencia | `varchar(255)` | sí |  |  | Motivo de rechazo de gerencia |
| observaciones_revision_evidencias | `varchar(255)` | sí |  |  | Observaciones al revisar evidencias |
| rechazo_revision_evidencias | `varchar(255)` | sí |  |  | Motivo de rechazo de evidencias |
| estado_id | `bigint unsigned` | no | `2` | → `estados_anticipo.id` | -> estados_anticipo.id |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### asistentes

Relación ejecutivo de cuenta (asistente_id) -> comercial al que apoya (comercial_id)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| asistente_id | `bigint unsigned` | no |  | → `users.id` | Cuenta del ejecutivo de cuenta -> users.id |
| comercial_id | `bigint unsigned` | no |  | → `users.id` | Comercial al que apoya -> users.id |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### base_comerciales

Base comercial: valor de venta de cada proyecto por comercial. Alimenta los dashboards de venta

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| fecha | `date` | no |  |  | Fecha del registro (base de los dashboards por periodo) |
| nom_cliente | `varchar(255)` | no |  |  | Nombre del cliente tal como se digitó |
| nom_proyecto | `varchar(255)` | no |  |  | Nombre del proyecto |
| cod_cc | `varchar(255)` | sí |  |  | Centro de costos tal como se digitó (puede traer el nombre del proyecto); el código puro está en centro_costos |
| centro_costos | `varchar(12)` | sí |  |  | Código puro del centro de costos (el que usa Helisa), sin el nombre del proyecto; se llena solo al guardar cod_cc |
| valor_original | `decimal(15,2)` | sí | `0.00` |  | Valor total del proyecto |
| cotizacion_file_actualizacion | `varchar(255)` | sí |  |  | Archivo de la cotización actualizada (ruta en storage) |
| porcentaje | `decimal(9,4)` | sí |  |  | Participación del comercial en el proyecto (%) |
| valor_proyecto | `decimal(15,2)` | sí | `0.00` |  | Valor atribuido al comercial (su porcentaje del proyecto) |
| com_1 | `varchar(255)` | sí |  |  | Participación (%) del primer comercial (histórico; hoy en gestion_participantes) |
| com_2 | `varchar(255)` | sí |  |  | Participación (%) del segundo comercial (histórico) |
| com_3 | `varchar(255)` | sí |  |  | Participación (%) del tercer comercial (histórico) |
| id_estado | `bigint unsigned` | no |  | → `estados_cuenta.id` | Estado de la venta -> estados_cuenta.id |
| fecha_inicio | `date` | sí |  |  | Inicio del proyecto |
| dura_mes | `date` | sí |  |  | Duración en meses |
| fecha_facturacion | `date` | sí |  |  | Fecha prevista de facturación |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial al que se atribuye la venta -> users.id |
| id_asistente | `bigint unsigned` | sí |  | → `users.id` | Ejecutivo de cuenta que registró la venta (users.id) |
| id_cuenta | `bigint unsigned` | no | `1` | → `cuentas.id` | Cuenta/marca -> cuentas.id |
| id_gestion | `bigint unsigned` | sí |  | → `gestion_comercial.id` | -> gestion_comercial.id |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### categorias_proveedor

Categorías de proveedor

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre de la categoría (Nómina, Logística, Impresión…) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### ciudades

Catálogo de ciudades: reemplaza la lista fija del código y unifica el texto libre de ítems, proveedores, terceros y contactos

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| nombre | `varchar(120)` | no |  |  | Nombre para mostrar (mayúsculas, como en los formularios) |
| clave | `varchar(120)` | no |  |  | Nombre normalizado (sin tildes, puntos ni espacios dobles): une variantes como BOGOTA D.C, BOGOTÁ D.C. y BOGOTA DC |
| orden | `smallint unsigned` | no | `999` |  | Posición en los desplegables (la lista oficial primero) |
| activa | `tinyint(1)` | no | `1` |  | 1 = se ofrece en los desplegables; 0 = variante vieja de los datos, solo para enlazar filas antiguas |
| created_at | `timestamp` | sí |  |  | CreaciÃ³n del registro |
| updated_at | `timestamp` | sí |  |  | Ãšltima modificaciÃ³n |

### cliente_parametros_cc

Parámetros por cliente para la asignación de centros de costos

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| nombre_empresa | `varchar(255)` | no |  |  | Nombre del cliente tal como va en el centro de costos |
| cliente_id | `bigint unsigned` | sí |  | → `clientes.id` | Cliente (clientes.id) |
| codigo_cc | `varchar(2)` | no |  |  | Prefijo de 2 caracteres del centro de costos del cliente (p. ej. C3, 44), único |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### clientes

Clientes (empresas) y sus parámetros

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial dueño del cliente (users.id) |
| estado_id | `bigint unsigned` | no |  | → `estados_cuenta.id` | Estado (estados_cuenta.id) |
| nombre | `varchar(255)` | no |  |  | Nombre comercial |
| razon_social | `varchar(255)` | no |  |  | Razón social |
| nit | `varchar(50)` | no |  |  | NIT (único) |
| direccion | `varchar(255)` | no |  |  | Dirección |
| telefono | `varchar(50)` | sí |  |  | Teléfono principal |
| numero_telefono | `varchar(50)` | no |  |  | Otro teléfono |
| cargo | `varchar(100)` | no |  |  | Cargo del contacto principal |
| correo | `varchar(255)` | no |  |  | Correo principal |
| pagina_web | `varchar(255)` | sí |  |  | Página web |
| correo_recpcion_facturas | `varchar(255)` | no |  |  | Correo donde el cliente recibe facturas |
| adjuntar_archivos | `varchar(255)` | sí |  |  | Documentos adjuntos del cliente (ruta en storage) |
| created_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  | Creación del registro |
| updated_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### contactos

Personas de contacto en los clientes (origen de los leads)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| nombre | `varchar(255)` | no |  |  | Nombre |
| apellido | `varchar(255)` | no |  |  | Apellido |
| empresa | `varchar(255)` | no |  |  | Empresa/cliente al que pertenece |
| cargo | `varchar(255)` | sí |  |  | Cargo en la empresa |
| celular | `varchar(255)` | sí |  |  | Celular |
| correo | `varchar(255)` | sí |  |  | Correo |
| web | `varchar(255)` | sí |  |  | Sitio web |
| pbx | `varchar(255)` | sí |  |  | Teléfono fijo/PBX |
| direccion | `varchar(255)` | sí |  |  | Dirección |
| ciudad | `varchar(255)` | sí |  |  | Ciudad (texto); el enlace al catálogo está en ciudad_id |
| ciudad_id | `bigint unsigned` | sí |  | → `ciudades.id` | Ciudad del catálogo (ciudades.id) que corresponde al texto de la columna ciudad |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial que lo registró (users.id) |
| id_cliente | `bigint unsigned` | sí |  | → `clientes.id` | Cliente al que pertenece (clientes.id) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### cuentas

Cuentas/marcas de cliente asociadas a la base comercial

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre de la cuenta o línea de negocio (Bull, V2V…) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_anticipo

Estados del ciclo de vida de un anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre para mostrar |
| flujo | `varchar(20)` | sí |  |  | Flujo al que aplica el estado: juridico, productor o ambos |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_cuenta

Estados de un registro de la base comercial (ejecución por facturar, venta, facturado, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre para mostrar |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_gestion_comercial

Etapas del embudo comercial (prospecto, oportunidad, propuesta, decisión, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre para mostrar |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_ordenes_compra

Estados de una orden de compra (1 aprobado, 2 revisión, 3 editable, 4 recibido, 5 comprobado, 6 anulada, 7 evidencias, 8-14 flujo nómina)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre para mostrar |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_presupuesto

Estados del presupuesto de proyecto (en revisión, aprobado, rechazado, ...)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre para mostrar |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_proveedor

Catálogo de estados del acuerdo con el proveedor (activo, suspendido, etc.)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| nombre | `varchar(60)` | no |  |  | Nombre del estado |
| descripcion | `varchar(200)` | sí |  |  | Qué significa |
| activo | `tinyint(1)` | no | `1` |  | 1 = se ofrece en el formulario de proveedores |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### estados_tercero

Estados de un tercero

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| descripcion | `varchar(255)` | no |  |  | Nombre del estado |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### evidencias

Evidencias (archivos) del trabajo de un tercero para una orden natural

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` | Orden de compra (ordenes_compra.id) |
| tercero_id | `bigint unsigned` | no |  | → `terceros.id` | Persona (terceros.id) a la que corresponde la evidencia |
| fecha_evidencia | `date` | sí |  |  | Fecha de la evidencia |
| foto_evidencia | `text` | sí |  |  | Foto de la evidencia (ruta en storage) |
| observacion_evidencia | `text` | sí |  |  | Observación escrita con la evidencia |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### evidencias_anticipo

Evidencias del productor para cerrar un anticipo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| anticipo_id | `bigint unsigned` | no |  | → `anticipos.id` | Anticipo de productor (anticipos.id) |
| item_id | `bigint unsigned` | sí |  | → `items_presupuesto.id` | Ítem del presupuesto (items_presupuesto.id) que respalda |
| fecha_evidencia | `date` | sí |  |  | Fecha de la evidencia |
| foto_evidencia | `varchar(255)` | sí |  |  | Foto o soporte (ruta en storage) |
| observacion_evidencia | `varchar(255)` | sí |  |  | Observación escrita con la evidencia |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### failed_jobs

Laravel: trabajos en cola que fallaron (p. ej. envíos de notificaciones)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| uuid | `varchar(255)` | no |  |  | Identificador único del trabajo |
| connection | `text` | no |  |  | Conexión de cola |
| queue | `text` | no |  |  | Cola |
| payload | `longtext` | no |  |  | Trabajo serializado |
| exception | `longtext` | no |  |  | Error con el que falló |
| failed_at | `timestamp` | no | `CURRENT_TIMESTAMP` |  | Cuándo falló |

### gestion_comercial

Gestión comercial (lead/oportunidad/proyecto). Un registro por proyecto en el embudo; los participantes están en gestion_participantes

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
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
| created_at | `timestamp` | sí |  |  | Apertura de la gestión |
| updated_at | `timestamp` | sí |  |  | Último movimiento |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### gestion_participantes

Comerciales que participan en una gestión (posición 1 = responsable) con su porcentaje

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| gestion_id | `bigint unsigned` | no |  | → `gestion_comercial.id` | -> gestion_comercial.id |
| user_id | `bigint unsigned` | no |  | → `users.id` | Comercial participante -> users.id |
| posicion | `tinyint unsigned` | no | `1` |  | 1 = responsable de la gestión; 2..4 = participantes adicionales |
| porcentaje | `decimal(5,2)` | no | `0.00` |  | Porcentaje de la venta que se le atribuye (la suma debería ser 100) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### helisa

Asientos de facturación exportados/registrados para el contable Helisa. Un documento puede tener varias filas (una por comercial y ajustes)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| fecha | `date` | no |  |  | Fecha del movimiento contable |
| tipo_doc | `varchar(255)` | no |  |  | Tipo de documento contable |
| num_doc | `varchar(255)` | no |  |  | Número de documento/factura |
| concepto | `varchar(255)` | sí |  |  | Concepto del asiento |
| identidad | `varchar(255)` | no |  |  | NIT/cédula del tercero |
| nom_tercero | `varchar(255)` | no |  |  | Nombre del tercero facturado |
| centro | `varchar(255)` | no |  |  | Centro de costos |
| nom_centro_costo | `varchar(255)` | no |  |  | Nombre del centro de costos |
| debito | `decimal(12,2)` | sí |  |  | Valor débito |
| credito | `decimal(12,2)` | sí |  |  | Valor crédito |
| porcentaje | `decimal(9,4)` | sí |  |  | Participación del comercial en la factura (%) |
| comercial | `bigint unsigned` | no |  | → `users.id` | Comercial al que se atribuye -> users.id |
| id_cuenta | `bigint unsigned` | no | `1` | → `cuentas.id` | Cuenta/marca -> cuentas.id |
| participacion | `varchar(255)` | no |  |  | Número de comerciales que participan en la factura |
| base_factura | `decimal(12,2)` | no |  |  | Base de la factura: valor sobre el que se liquida la venta y la comisión |
| mes | `varchar(255)` | no |  |  | Mes contable -> meses.id |
| año | `varchar(255)` | no |  |  | Año contable |
| comision | `decimal(12,2)` | no |  |  | Comisión calculada para el comercial (puede ser negativa en ajustes) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### historial_estados

Historial de cambios de estado (una fila por cambio) de órdenes, anticipos, presupuestos, gestiones y terceros; lo escribe HistorialEstadoObserver

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| tabla | `varchar(40)` | no |  |  | Tabla del registro: ordenes_compra, anticipos, presupuesto_proyecto, gestion_comercial o terceros |
| registro_id | `bigint unsigned` | no |  |  | id del registro en esa tabla |
| estado_anterior | `bigint unsigned` | sí |  |  | Estado antes del cambio (NULL al crear el registro o en la carga inicial) |
| estado_nuevo | `bigint unsigned` | no |  |  | Estado después del cambio (id del catálogo de estados de esa tabla) |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Quién hizo el cambio (users.id); NULL = proceso automático o carga inicial |
| rol | `bigint unsigned` | sí |  | → `roles.id` | Rol activo de quien hizo el cambio (roles.id) |
| observacion | `text` | sí |  |  | Justificación u observación escrita en ese paso, si la hubo |
| origen | `varchar(160)` | sí |  |  | Ruta de la pantalla desde la que se hizo, "consola" o "carga inicial" |
| created_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  | Momento del cambio |

### historial_items_presupuesto

Snapshot JSON de un ítem antes de cada modificación (auditoría de cambios)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| item_presupuesto_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem al que pertenece la copia (items_presupuesto.id) |
| valores_anteriores | `json` | no |  |  | Copia del ítem antes del cambio (JSON); con tipo_registro = snapshot_aprobacion guarda todos los ítems al aprobar |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Quién hizo el cambio (users.id) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### item_presupuesto_proveedor

Proveedores asignados a cada ítem de presupuesto (N:M)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| item_presupuesto_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem del presupuesto |
| proveedor_id | `bigint unsigned` | no |  | → `proveedores.id` | Proveedor asignado al ítem |

### items_anticipo

Ítems de presupuesto incluidos en un anticipo de productor, con valor anticipado y saldo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| anticipo_id | `bigint unsigned` | no |  | → `anticipos.id` | -> anticipos.id |
| item_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem de presupuesto -> items_presupuesto.id |
| display_item | `varchar(255)` | no |  |  | Número del ítem tal como se muestra en el presupuesto |
| desc | `varchar(255)` | no |  |  | Descripción del ítem |
| cant | `int` | no |  |  | Cantidad |
| dias | `int` | no |  |  | Días |
| otros | `int` | no |  |  | Otros (meses u otro multiplicador) |
| vunit | `decimal(15,2)` | no | `0.00` |  | Valor unitario |
| vtotal | `decimal(15,2)` | no | `0.00` |  | Valor total del ítem |
| vanticipo | `decimal(15,2)` | no | `0.00` |  | Valor anticipado (<= vtotal) |
| saldo | `decimal(15,2)` | no | `0.00` |  | vtotal - vanticipo |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### items_presupuesto

Ítems (líneas) de un presupuesto de proyecto. Sus proveedores están en item_presupuesto_proveedor

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| presupuesto_id | `bigint unsigned` | no |  | → `presupuesto_proyecto.id` | -> presupuesto_proyecto.id |
| num_item | `int` | sí |  |  | Número de ítem visible al usuario (secuencial dentro del presupuesto) |
| orden | `int` | sí |  |  | Orden de presentación |
| cod | `int` | no |  |  | Código/categoría del ítem |
| evento | `varchar(255)` | no | `0` |  | 1 = fila de evento (agrupador sin valores), 0 = ítem normal |
| cantidad | `int` | no |  |  | Cantidad de unidades |
| dia | `int` | no |  |  | Número de días |
| otros | `int` | no |  |  | Otro multiplicador (p.ej. personas). Cantidad total = cantidad * dia * otros |
| descripcion | `varchar(2000)` | no |  |  | Descripción del ítem o del evento |
| v_unitario | `decimal(15,2)` | no | `0.00` |  | Costo unitario interno |
| v_total | `decimal(15,2)` | no | `0.00` |  | Costo total interno (cantidad * dia * otros * v_unitario) |
| v_total_cliente | `decimal(15,2)` | no | `0.00` |  | Valor total al cliente (variante usada en cotización) |
| proveedor_legacy | `varchar(255)` | sí |  |  | OBSOLETO: valor original del proveedor. Usar item_presupuesto_proveedor |
| margen_utilidad | `double(15,10)` | no | `0.0000000000` |  | Factor costo ÷ precio del ítem (p. ej. 0,70): el precio al cliente es el costo ÷ este factor y el margen del ítem es 1 − factor (0,70 → 30 %) |
| mes_id | `bigint unsigned` | sí |  | → `meses.id` | Periodo (meses.id) en que se ejecuta el ítem; NULL = sin mes (eventos) |
| dias | `int` | no |  |  | Días de ejecución (dato informativo) |
| ciudad | `varchar(255)` | no |  |  | Ciudad de ejecución |
| ciudad_id | `bigint unsigned` | sí |  | → `ciudades.id` | Ciudad del catálogo (ciudades.id) que corresponde al texto de la columna ciudad |
| v_unitario_cot | `decimal(15,2)` | no | `0.00` |  | Precio unitario al cliente |
| v_total_cot | `decimal(15,2)` | no | `0.00` |  | Precio total al cliente (base del margen) |
| rentabilidad | `decimal(15,2)` | no | `0.00` |  | Rentabilidad calculada del ítem |
| actualizado | `tinyint(1)` | no | `0` |  | Marca de actualización tras asignado el centro de costos (0..3, ver código) |
| actualizado_con | `tinyint` | no | `0` |  | Marca de actualización vista por contabilidad |
| disponible | `tinyint(1)` | no | `1` |  | 1 = el ítem puede consumirse en órdenes/anticipos |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### lider_comercial_user

Relación líder comercial -> comerciales a su cargo

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| lider_id | `bigint unsigned` | no |  | → `users.id` | Líder comercial (users.id) |
| comercial_id | `bigint unsigned` | no |  | → `users.id` | Comercial de su equipo (users.id) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### meses

Meses de cada año fiscal con su rango de fechas (f_inicio, f_fin)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre del periodo (p. ej. Enero) |
| identifier | `varchar(255)` | no |  |  | Número del mes (1..12) |
| ano_id | `bigint unsigned` | no |  | → `anos.id` | -> anos.id |
| f_inicio | `date` | sí |  |  | Primer día del mes |
| f_fin | `date` | sí |  |  | Último día del mes |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### migrations

Laravel: migraciones aplicadas a esta base

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `int unsigned` | no |  |  | Identificador |
| migration | `varchar(255)` | no |  |  | Nombre del archivo de migración |
| batch | `int` | no |  |  | Lote en que se aplicó |

### natural_info

Datos adicionales de una orden natural: tercero contratado, productor, contrato

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` | -> ordenes_compra.id |
| tercero_id | `bigint unsigned` | no |  | → `terceros.id` | Persona contratada -> terceros.id |
| productor_id | `bigint unsigned` | no |  | → `users.id` | Productor que contrata -> users.id |
| contrato | `varchar(255)` | sí |  |  | Archivo del contrato firmado |
| terminos | `tinyint(1)` | sí |  |  | 1 = la persona aceptó los términos |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### notificacion_destinatarios

A quién se notifica por área (controller, produccion, contabilidad, tesoreria, lider_comercial, compras, compras_cc, contabilidad_pagos)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| area | `varchar(255)` | no |  |  | Área/evento que notifica |
| user_id | `bigint unsigned` | sí |  | → `users.id` | Usuario de la plataforma (si está suspendido no recibe) |
| email_externo | `varchar(255)` | sí |  |  | Buzón externo cuando no es usuario de la plataforma |
| nombre | `varchar(255)` | sí |  |  | Nombre visible del destinatario |
| activo | `tinyint(1)` | no | `1` |  | 1 = recibe las notificaciones de su área |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### notificaciones_log

Registro de correos y SMS enviados por el CRM, con resultado y reintentos

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| canal | `varchar(10)` | no |  |  | correo \| sms |
| evento | `varchar(80)` | no |  |  | Función que originó el aviso (p. ej. mailOrdenAprobada) |
| referencia | `varchar(120)` | sí |  |  | Objeto relacionado, p. ej. oc:123, anticipo:4 |
| destinatarios | `json` | no |  |  | Correos/teléfonos a los que iba dirigido |
| copias | `json` | sí |  |  | Destinatarios en copia (JSON) |
| asunto | `varchar(255)` | sí |  |  | Asunto del correo o texto del SMS |
| cuerpo | `longtext` | no |  |  | HTML del correo o texto del SMS |
| adjuntos | `json` | sí |  |  | Rutas en storage de los adjuntos |
| estado | `varchar(12)` | no | `pendiente` |  | pendiente \| enviado \| fallido |
| intentos | `tinyint unsigned` | no | `0` |  | Intentos de envío realizados |
| error | `text` | sí |  |  | Último error de envío |
| enviado_at | `timestamp` | sí |  |  | Cuándo se envió |
| alertado_at | `timestamp` | sí |  |  | Cuándo se avisó a desarrollo del fallo |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### oc_items

Líneas de una orden de compra (consumo de un ítem de presupuesto)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| oc_id | `bigint unsigned` | no |  | → `ordenes_compra.id` | -> ordenes_compra.id |
| item_id | `bigint unsigned` | no |  | → `items_presupuesto.id` | Ítem de presupuesto consumido -> items_presupuesto.id |
| num_item | `varchar(255)` | sí |  |  | Número del ítem en el presupuesto |
| desc_oc | `varchar(2000)` | no |  |  | Descripción en la orden |
| cant_oc | `int` | no |  |  | Cantidad consumida |
| dias_oc | `int` | no |  |  | Días |
| otros_oc | `int` | no |  |  | Otro multiplicador |
| vunit_oc | `decimal(15,2)` | no | `0.00` |  | Valor unitario |
| vtotal_oc | `decimal(15,2)` | no | `0.00` |  | Valor total de la línea (descuenta del saldo del ítem) |
| tipo_servicio | `varchar(255)` | sí |  |  | Tipo de servicio (órdenes naturales) |
| tipo_contrato | `varchar(255)` | sí |  |  | Tipo de contrato (órdenes naturales) |
| cantidad_horas | `int` | sí |  |  | Horas (órdenes de nómina) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### ordenes_compra

Órdenes de compra: tipo_oc 1 jurídica, 2 natural (persona), 3 nómina. estado_id -> estados_ordenes_compra

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
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
| archivo_cuenta_cobro | `varchar(255)` | sí |  |  | PDF de la cuenta de cobro (persona natural), ruta en storage |
| observacion_remision | `varchar(255)` | sí |  |  | Observaciones al firmar la remisión |
| archivo_firma | `varchar(255)` | sí |  |  | Imagen de la firma del productor en la remisión |
| cod_oc | `varchar(255)` | sí |  |  | Código de la orden asignado al aprobar (OC{id}) |
| gr | `varchar(255)` | sí |  |  | Código del Good Receive (GR{id}) cuando se comprueba |
| actualizado | `tinyint(1)` | no | `0` |  | Marca de orden actualizada tras aprobación |
| observaciones_anulacion | `varchar(255)` | sí |  |  | Motivo de anulación |
| observaciones_revision_lider | `varchar(255)` | sí |  |  | Observación del líder de producción en su revisión (histórico; el historial completo está en historial_estados) |
| rechazo_revision_lider | `varchar(255)` | sí |  |  | Motivo de rechazo del líder de producción (histórico) |
| observaciones_revision_gerencia | `varchar(255)` | sí |  |  | Observación de Gerencia en su revisión (histórico) |
| rechazo_revision_gerencia | `varchar(255)` | sí |  |  | Motivo de rechazo de Gerencia (histórico) |
| observaciones_revision_evidencias | `varchar(255)` | sí |  |  | Observación en la revisión de evidencias (histórico) |
| rechazo_revision_evidencias | `varchar(255)` | sí |  |  | Motivo de rechazo de las evidencias (histórico) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| fecha_causal | `datetime` | sí |  |  | Cuándo se causó |
| fecha_comprobante_pago | `datetime` | sí |  |  | Cuándo se registró el comprobante de pago |
| observaciones | `text` | sí |  |  | Observaciones generales de la orden |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### password_resets

Laravel: tokens de restablecimiento de contraseña (el flujo público se retiró; la tabla queda por compatibilidad)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **email** (PK) | `varchar(255)` | no |  |  | Correo del usuario |
| token | `varchar(255)` | no |  |  | Token de restablecimiento |
| created_at | `timestamp` | sí |  |  | Cuándo se pidió |

### permiso_rol

Permisos otorgados a un rol

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| permiso_id | `bigint unsigned` | no |  | → `permisos.id` | Permiso (permisos.id) |
| rol_id | `bigint unsigned` | no |  | → `roles.id` | Rol que lo tiene (roles.id) |

### permiso_user

Permisos otorgados directamente a una cuenta

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| permiso_id | `bigint unsigned` | no |  | → `permisos.id` | Permiso (permisos.id) |
| user_id | `bigint unsigned` | no |  | → `users.id` | Usuario que lo tiene además de los de su rol (users.id) |

### permisos

Permisos con nombre (Gates). Reemplazan los ids de usuario que estaban quemados en el código

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| clave | `varchar(255)` | no |  |  | Clave que usa el código (p. ej. aprobar-presupuestos) |
| descripcion | `varchar(255)` | sí |  |  | Qué permite |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### personal_access_tokens

Laravel Sanctum: tokens de API (no se usan en el CRM)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| tokenable_type | `varchar(255)` | no |  |  | Modelo dueño del token |
| tokenable_id | `bigint unsigned` | no |  |  | id del dueño |
| name | `varchar(255)` | no |  |  | Nombre del token |
| token | `varchar(64)` | no |  |  | Token cifrado |
| abilities | `text` | sí |  |  | Permisos del token |
| last_used_at | `timestamp` | sí |  |  | Último uso |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### presupuesto_proyecto

Presupuesto de un proyecto (centro de costos): totales, margen, aprobación y productor asignado

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| id_gestion | `bigint unsigned` | no |  | → `gestion_comercial.id` | Gestión comercial a la que pertenece -> gestion_comercial.id |
| estado_id | `bigint unsigned` | no | `3` | → `estados_presupuesto.id` | -> estados_presupuesto.id (1 = aprobado) |
| margen_general | `double(15,10)` | no | `0.0000000000` |  | Margen general (%) calculado a partir de los ítems |
| venta_proy | `decimal(15,2)` | no | `0.00` |  | Valor de venta del proyecto (total al cliente) |
| costos_proy | `decimal(15,2)` | no | `0.00` |  | Costos totales del proyecto (suma de los ítems) |
| margen_proy | `decimal(15,2)` | no | `0.00` |  | Margen % del proyecto. Con menos del 30 % la aprobación es de Gerencia (AprobacionPresupuesto::MARGEN_GERENCIA) |
| margen_bruto | `decimal(15,2)` | no | `0.00` |  | Margen bruto en pesos (venta − costos) |
| cod_cot | `int` | no |  |  | Consecutivo de la cotización |
| cod_cc | `varchar(255)` | sí |  |  | Código de centro de costos asignado al aprobar |
| centro_costos | `varchar(12)` | sí |  |  | Código puro del centro de costos (el que usa Helisa), sin el nombre del proyecto; se llena solo al guardar cod_cc |
| fecha_cc | `date` | sí |  |  | Fecha de asignación del centro de costos |
| imprevistos | `decimal(15,2)` | no | `0.00` |  | Imprevistos (%) que se suman a la venta |
| administracion | `decimal(15,2)` | no | `0.00` |  | Administración (%) que se suma a la venta |
| fee | `decimal(15,2)` | no | `0.00` |  | Fee (%) que se suma a la venta |
| tiempo_factura | `int` | no | `30` |  | Días para facturar |
| notas | `varchar(255)` | sí |  |  | Notas del presupuesto |
| justificacion | `varchar(255)` | sí |  |  | Observaciones del comercial al enviar a aprobación |
| justificacion_compras | `varchar(255)` | sí |  |  | Observaciones de compras/gerencia al aprobar o rechazar |
| justificacion_lider | `varchar(255)` | sí |  |  | Observaciones del líder comercial |
| justificacion_gerencia | `varchar(255)` | sí |  |  | Justificación de Gerencia al rechazar (histórico; ver historial_estados) |
| productor | `bigint unsigned` | sí |  | → `users.id` | Productor asignado a ejecutar el proyecto -> users.id |
| notificacion_actualizacion | `tinyint(1)` | sí |  |  | Marca de notificación pendiente por actualización del presupuesto |
| requiere_revalidacion | `bigint` | sí | `0` |  | 1 = hubo cambios después de aprobarse y debe volver a validarse |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### presupuestos

Presupuesto (meta) comercial por año/mes/comercial

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| ano_id | `bigint unsigned` | no |  | → `anos.id` | -> anos.id |
| mes_id | `bigint unsigned` | no |  | → `meses.id` | -> meses.id |
| valor | `decimal(15,2)` | no |  |  | Meta de venta del comercial en el mes |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial (users.id) al que corresponde la meta |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### proveedores

Proveedores (terceros jurídicos). Los ids 1 y 2 son nómina y el 3 es "cuenta de cobro" (persona natural)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| categoria_id | `bigint unsigned` | no |  | → `categorias_proveedor.id` | Categoría (categorias_proveedor.id) |
| tercero | `varchar(255)` | no |  |  | Nombre o razón social del proveedor |
| tipo | `varchar(255)` | no |  |  | juridica, natural u Otros |
| tipo_doc | `varchar(255)` | no |  |  | Tipo de documento: NIT, CEDULA o RUC |
| documento | `varchar(255)` | no |  |  | Número de documento (único) |
| dv | `varchar(255)` | no |  |  | Dígito de verificación del NIT |
| direccion | `varchar(255)` | sí |  |  | Dirección |
| departamento | `varchar(255)` | no |  |  | Departamento |
| ciudad | `varchar(255)` | no |  |  | Ciudad (texto); el enlace al catálogo está en ciudad_id |
| ciudad_id | `bigint unsigned` | sí |  | → `ciudades.id` | Ciudad del catálogo (ciudades.id) que corresponde al texto de la columna ciudad |
| servicio | `varchar(255)` | no |  |  | Servicio que presta |
| anticipo | `varchar(255)` | no |  |  | Porcentaje de anticipo pactado |
| celular | `varchar(255)` | no |  |  | Celular |
| fijo | `varchar(255)` | sí |  |  | Teléfono fijo |
| correo | `varchar(255)` | no |  |  | Correo (único) |
| plazo | `varchar(255)` | sí |  |  | Plazo de pago pactado |
| contacto | `varchar(255)` | no |  |  | Persona de contacto |
| web | `varchar(255)` | sí |  |  | Página web |
| observaciones | `varchar(255)` | sí |  |  | Observaciones |
| estado_legacy | `varchar(255)` | no |  |  | Estado en texto, antes del catálogo estados_proveedor (histórico) |
| estado_id | `bigint unsigned` | no |  | → `estados_proveedor.id` | Estado del acuerdo (estados_proveedor.id) |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### role_user

Roles que cada cuenta puede usar (multi-rol). El rol activo está en users.rol

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| user_id | `bigint unsigned` | no |  | → `users.id` | Cuenta (users.id) |
| rol_id | `bigint unsigned` | no |  | → `roles.id` | -> roles_user.id |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### roles

Catálogo de roles (antes roles_user). users.rol es el rol activo; role_user, los roles que cada cuenta puede usar

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Nombre del rol |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### solicitud_contactos

Solicitudes de creación/actualización de contactos hechas por comerciales

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| id_user | `bigint unsigned` | no |  | → `users.id` | Comercial que pidió el cliente (users.id) |
| estado | `enum('Pendiente','En revisión','Aprobado','Rechazado')` | sí | `Pendiente` |  | Pendiente, En revisión, Aprobado o Rechazado |
| nombre | `varchar(255)` | no |  |  | Nombre comercial |
| razon_social | `varchar(255)` | no |  |  | Razón social |
| nit | `varchar(50)` | no |  |  | NIT |
| direccion | `varchar(255)` | no |  |  | Dirección |
| telefono | `varchar(50)` | sí |  |  | Teléfono principal |
| numero_telefono | `varchar(50)` | no |  |  | Otro teléfono |
| cargo | `varchar(100)` | no |  |  | Cargo del contacto |
| correo | `varchar(255)` | no |  |  | Correo |
| pagina_web | `varchar(255)` | sí |  |  | Página web |
| correo_recpcion_facturas | `varchar(255)` | no |  |  | Correo para facturas |
| adjuntar_archivos | `varchar(255)` | sí |  |  | Documentos adjuntos (ruta en storage) |
| created_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  | Creación del registro |
| updated_at | `timestamp` | sí | `CURRENT_TIMESTAMP` |  | Última modificación |

### tarifario

Tarifas de referencia para cotizar

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| concepto | `varchar(255)` | sí |  |  | Concepto |
| caso | `varchar(255)` | sí |  |  | Caso o uso |
| caracteristicas | `varchar(255)` | sí |  |  | Características |
| v_unidad | `decimal(15,2)` | no | `0.00` |  | Valor unitario de referencia |
| observacion | `varchar(255)` | sí |  |  | Observación |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### terceros

Personas naturales contratadas por producción (datos bancarios, RUT, firma)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| nombre | `varchar(255)` | sí |  |  | Nombre |
| apellido | `varchar(255)` | sí |  |  | Apellido |
| cedula | `varchar(255)` | sí |  |  | Documento de identidad |
| correo | `varchar(255)` | sí |  |  | Correo |
| telefono | `varchar(255)` | sí |  |  | Teléfono |
| servicio | `varchar(255)` | sí |  |  | Servicio que presta |
| ciudad | `varchar(255)` | sí |  |  | Ciudad (texto); el enlace al catálogo está en ciudad_id |
| ciudad_id | `bigint unsigned` | sí |  | → `ciudades.id` | Ciudad del catálogo (ciudades.id) que corresponde al texto de la columna ciudad |
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
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |
| deleted_at | `timestamp` | sí |  |  | Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera |

### tipo_ordenes_compra

Tipos de orden de compra (jurídica, natural, nómina)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| description | `varchar(255)` | no |  |  | Jurídica, Natural o Nómina |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

### users

Cuentas de la plataforma. rol = rol ACTIVO (los roles asignables están en role_user)

| Columna | Tipo | Nulo | Default | Referencia | Descripción |
|---|---|---|---|---|---|
| **id** (PK) | `bigint unsigned` | no |  |  | Identificador |
| name | `varchar(255)` | no |  |  | Nombre completo |
| email | `varchar(255)` | no |  |  | Correo (único): es el usuario de acceso |
| telefono | `varchar(255)` | no |  |  | Celular (se usa para SMS de notificación) |
| email_verified_at | `timestamp` | sí |  |  | Cuándo verificó el correo (no se usa) |
| password | `varchar(255)` | no |  |  | Contraseña cifrada |
| rol | `bigint unsigned` | no | `2` | → `roles.id` | Rol ACTIVO (FK lógica a roles_user). Los demás roles de la cuenta están en role_user |
| avatar | `varchar(255)` | sí | `public/photos/avatar.jpg` |  | Ruta de la foto de perfil en storage |
| remember_token | `varchar(100)` | sí |  |  | Token de "recordarme" |
| created_at | `timestamp` | sí |  |  | Creación del registro |
| updated_at | `timestamp` | sí |  |  | Última modificación |

