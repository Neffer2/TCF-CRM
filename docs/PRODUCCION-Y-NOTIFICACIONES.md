# Producción, pagos a proveedores, evidencias y notificaciones

Análisis del módulo de producción del BULLCRM (rama `correcciones`, base de prueba del 13-sep-2026): qué gestionan producción y el líder de producción, cómo se paga a proveedores y terceros, qué evidencias se suben y cómo se notifica por correo y SMS. Todo lo dicho aquí sale del código y de los datos reales del dump.

## 1. Resumen ejecutivo

- Producción trabaja sobre **centros de costos** (presupuestos aprobados con `cod_cc`) que el **líder de producción** asigna a cada **productor**. Con ese presupuesto el productor encarga tres tipos de **órdenes de compra**: **jurídica** (proveedores empresa), **natural** (personas naturales, "terceros") y **nómina** (personal contratado por nómina), y puede pedir **anticipos** (sobre una OC jurídica, sobre una OC natural, o un anticipo propio "de productor" para gastos menores).
- Cada orden pasa por una cadena de estados con revisiones de **líder de producción**, **admin/compras**, **controller** y **gerencia**, y termina en **contabilidad** (causación) y **tesorería** (pago con comprobante).
- Las **evidencias** son fotos con fecha y observación: las suben los **terceros** desde un portal público con enlace firmado (OC natural) y los **productores** (anticipos propios). Además se suben remisiones firmadas, órdenes de Helisa en PDF, comprobantes de pago, contratos y los documentos del tercero (cédula, RUT, certificación bancaria, planilla).
- Las **notificaciones** son de dos tipos: **correo** (PHPMailer por SMTP, 17 avisos, destinatarios por área en la tabla `notificacion_destinatarios`) y **SMS** (API de Hablame, 3 mensajes al tercero con el enlace del portal). Se envían de forma síncrona y sin reintentos; si fallan, el sistema sigue y solo queda un registro en el log.
- **Volumen real**: 11.721 órdenes (7.113 jurídicas, 4.608 naturales, 0 nóminas registradas con tipo 3 en el dump), 16.705 evidencias, 2.901 remisiones firmadas, 22.217 terceros, 1.688 proveedores; pero solo **3 anticipos** en toda la historia y **1 comprobante de pago** — el flujo de anticipos casi no se usa.

## 2. Actores y pantallas

| Rol | Pantallas de producción | Qué hace |
|---|---|---|
| **Productor** (rol 7) | Dashboard (sus centros de costos), Órdenes de compra, Anticipos, Firmar remisión, Consumidos, Personal/Terceros, Proveedores (consulta) | Crea OC jurídicas/naturales/nómina, pide anticipos, sube evidencias de sus anticipos, firma remisiones, registra terceros, revisa sus consumidos |
| **Líder de producción** (rol 6) | Dashboard, Asignar proyectos, Órdenes de compra, Anticipos | Asigna centros de costos a productores, revisa órdenes de nómina y anticipos de productor (estado 8), revisa evidencias (estado 10) |
| **Admin / Compras** (rol 1, área compras) | Órdenes de compra, Anticipos, Proveedores, Personal, Consumidos | Aprueba OC jurídicas (con código y PDF de Helisa), genera GR (good receipt), anula, aprueba anticipos jurídicos/naturales, valida evidencias de OC naturales, administra proveedores |
| **Controller** (rol 11) | Presupuestos, Consumidos, Reportes; nómina con permiso `validar-nomina` | Revisión de OC naturales y nóminas (estado 2), validación final de nómina y GR (estado 14 → 5) |
| **Gerencia** (permisos) | Anticipos, Nómina | Aprueba anticipos de productor ≥ $500.000 (permiso `revisar-anticipos-gerencia`) y nóminas (permiso `validar-nomina`, estado 9) |
| **Contabilidad** (rol 9) | Anticipos (lista estado 1) | Causa el anticipo (código causal) o lo rechaza |
| **Tesorería** (rol 8) | Anticipos (lista estado 5) | Sube el comprobante de pago (PDF) |
| **Tercero / contratista** (sin cuenta) | Portal público `/consulta-terceros/{orden}` con enlace firmado | Completa sus datos y documentos, acepta términos, firma, sube evidencias y cuenta de cobro |

## 3. Qué encargan: los objetos del módulo

| Objeto | Tabla | Quién lo crea | Para qué |
|---|---|---|---|
| Orden de compra jurídica (`tipo_oc` 1) | `ordenes_compra` + `oc_items` | Productor | Compra a un proveedor empresa (`proveedores`), con ítems del presupuesto, cotización adjunta (`archivo_cot`) |
| Orden de compra natural (`tipo_oc` 2) | `ordenes_compra` + `natural_info` + `oc_items` | Productor | Contratación de una persona natural (`terceros`); genera contrato PDF, requiere términos y firma del tercero |
| Orden de nómina (`tipo_oc` 3) | `ordenes_compra` + `oc_items` | Productor | Personal por nómina; tiene su propia cadena de revisión (líder → gerencia → controller) |
| Anticipo jurídico | `anticipos` (con `oc_id`) | Productor | % de anticipo sobre una OC jurídica; el total se calcula en servidor |
| Anticipo natural | `anticipos` (con `oc_id`) | Productor | % de anticipo sobre una OC natural |
| Anticipo de productor | `anticipos` (con `presupuesto_id`) + `items_anticipo` + `evidencias_anticipo` | Productor | Dinero para gastos directos del productor con firma digital; luego debe legalizar con evidencias |
| Remisión firmada | `ordenes_compra.archivo_remision` + `archivo_firma` | Productor | Constancia de recibido de una OC aprobada (PDF + firma dibujada) |
| Evidencias de OC natural | `evidencias` | Tercero (portal) | Fotos del trabajo realizado, con fecha y observación, más cuenta de cobro |
| Evidencias de anticipo | `evidencias_anticipo` | Productor | Una evidencia por ítem del anticipo |
| Tercero (contratista) | `terceros` | Productor o el propio tercero (portal) | Datos personales, bancarios y documentos (RUT, cédula, certificación bancaria, planilla, art. 383) |
| Proveedor | `proveedores` | Admin | Empresa proveedora (NIT, contacto, plazo, si acepta anticipo) |
| Solicitud de recursos | (PDF/Excel del presupuesto interno) | Productor | Descarga el presupuesto interno del centro de costos para planear |
| Consumidos | vista sobre `oc_items` vs `items_presupuesto` | Productor / Admin / Controller | Cuánto del presupuesto ya está comprometido por ítem |

## 4. Flujos y estados

Estados de `estados_ordenes_compra`: 1 Aprobado · 2 Revisión · 3 Editable · 4 Recibido · 5 Comprobado · 6 Anulada · 7 Evidencias · 8 Revisión líder producción · 9 Revisión gerencia · 10 Revisión evidencias · 11 Rechazo líder · 12 Rechazo gerencia · 13 Rechazo remisión · 14 Remisión aprobada controller.

### 4.1 Orden de compra jurídica (proveedor empresa)

| Paso | Estado | Quién | Qué pasa | Aviso |
|---|---|---|---|---|
| 1 | 3 Editable | Productor | Crea la orden: proveedor, ítems del presupuesto, cotización PDF | — |
| 2 | 2 Revisión | Productor (`enviarAprobacion`) | Envía a aprobación | — |
| 3 | 1 Aprobado | Admin/Compras (`cambioEstado(1)`) | Registra `cod_oc` y sube la orden de Helisa en PDF | **Correo** "orden aprobada" a compras, comercial, productor; copia a compras_cc y al proveedor; si hay anticipo, copia a contabilidad_pagos |
| 3b | 3 Editable | Admin (`cambioEstado(3)`) | Rechaza con justificación | — |
| 4 | 4 Recibido | Productor (Firmar remisión) | Sube remisión PDF y firma en pantalla; solo si la orden está en 1 y es suya | — |
| 5 | 5 Comprobado | Admin (`cambioEstado(5)`) | Registra el GR (good receipt) | **Correo** "GR generado" a compras, comercial, productor, cc compras_cc y proveedor (pide radicar factura a facturacion.proveedores@) |
| — | 6 Anulada | Admin | Anula con observaciones | **Correo** "orden anulada" a compras, comercial, productor, cc compras_cc |

Volumen: 3.297 aprobadas, 499 recibidas, 2.355 comprobadas, 153 anuladas.

### 4.2 Orden de compra natural (persona natural / tercero)

| Paso | Estado | Quién | Qué pasa | Aviso |
|---|---|---|---|---|
| 1 | 3 Editable | Productor (`uploadOC`) | Elige o crea el tercero, ítems y valores | **SMS** al tercero con el enlace firmado del portal ("completa tu información y acepta los términos") |
| 2 | 3 | Tercero (portal) | Completa datos, sube cédula, certificación bancaria, planilla, RUT (si la orden supera $198.000), acepta términos y firma; se genera el **contrato PDF** | **Correo** "OC natural firmada" al productor, cc producción |
| 3 | 7 Evidencias | Productor (`updateOC`) | Cuando ya hay contrato y aún no hay evidencias, la pasa a "Evidencias" | **SMS** al tercero: "adjunta las evidencias del trabajo" |
| 4 | 3 | Tercero (portal) | Sube fotos con fecha y observación y la **cuenta de cobro** (`saveEvidencia`) | **Correo** "evidencias enviadas" al productor, cc producción |
| 5 | 2 Revisión | Productor (`updateOC`) | Revisa y envía a controller | **Correo** a controller |
| 6 | 5 Comprobado | Admin o productor dueño (`validateEvidencia(5)`) | Aprueba con `cod_oc` + PDF de Helisa | **Correo** a contabilidad |
| 6b | 7 Evidencias | Admin/productor (`validateEvidencia(7)`) | Rechaza evidencias con justificación | **SMS** al tercero + **correo** al productor (si rechaza un admin) |

Volumen: 3.236 comprobadas, 69 en evidencias, 31 editables, 30 en revisión. Desde la corrección de seguridad, el enlace del portal solo funciona **firmado** (`URL::signedRoute`), por eso los botones de WhatsApp y "Copiar enlace" de la lista de admin se corrigieron hoy para generar el enlace firmado.

### 4.3 Orden de nómina

| Paso | Estado | Quién | Qué pasa | Aviso |
|---|---|---|---|---|
| 1 | 8 Revisión líder | Productor (`enviarAprobacion`) | Crea la nómina (proveedor, ítems, cotización) | — |
| 2 | 9 Revisión gerencia | Líder de producción (`cambioEstado(9)`) | Aprueba | — |
| 2b | 11 Rechazo líder | Líder | Rechaza; el productor corrige y reenvía (vuelve a 8) | **Correo** a producción (función creada hoy) |
| 3 | 2 Revisión | Gerencia con `validar-nomina` (`cambioEstado(2)`) | Valida | **Correo** a controller (función creada hoy) |
| 3b | 12 Rechazo gerencia | Gerencia | Rechaza → productor corrige → 8 | — |
| 4 | 1 Aprobado | Admin (`cambioEstado(1)`) | Registra `cod_oc` + PDF Helisa | — |
| 5 | 4 Recibido | Productor | Firma remisión (o corrige si venía de 13) | — |
| 6 | 10 Revisión evidencias | Líder | Revisa la remisión: aprueba (4→14 lo hace admin) o rechaza (13) | — |
| 7 | 14 Remisión aprobada | Admin (`cambioEstado(14)`) | Aprueba la remisión | — |
| 8 | 5 Comprobado | Controller/Gerencia con `validar-nomina` (`cambioEstado(5)`) | Envía GR | — |
| — | 6 Anulada | Admin / validador | Anula | — |

Nota: hasta hoy, reenviar una nómina (pasos 2b y 3) daba **error fatal** porque las dos funciones de correo no existían. Ya existen. En el dump no hay órdenes con `tipo_oc` 3, es decir, este flujo aún no se ha usado en producción.

### 4.4 Anticipos

Estados de `estados_anticipo`: 1 Aprobado – pendiente de causar · 2 Revisión · 3 Editable · 4 Recibido · 5 Causado – pendiente de pago · 6 Anulado · 7 Cargue de evidencias · 8 Revisión líder · 9 Revisión gerencia · 10 Revisión evidencias · 11/12/13 Rechazos.

**Anticipo jurídico / natural** (sobre una OC): el productor indica el % (el total se calcula en servidor sobre los ítems de la OC) → estado 2 → **Admin** aprueba → 1 → Contabilidad causa → 5 → Tesorería paga (comprobante).

**Anticipo de productor** (gastos directos):

| Paso | Estado | Quién | Qué pasa | Aviso |
|---|---|---|---|---|
| 1 | 8 | Productor | Ítems + firma dibujada (se guarda como PNG) | — |
| 2 | 9 ó 7 | Líder de producción | Aprueba con observaciones; si el total es **< $500.000** salta gerencia y va directo a evidencias (7); si no, a gerencia (9) | — |
| 2b | 11 | Líder | Rechaza → productor corrige | — |
| 3 | 7 | Gerencia (`revisar-anticipos-gerencia`) | Aprueba → cargue de evidencias | — (el comentario dice "se envía notificación al productor" pero no hay envío) |
| 3b | 12 | Gerencia | Rechaza | — |
| 4 | 10 | Productor (`enviarEvidencias`) | Sube una evidencia por ítem | — |
| 5 | … | Líder | Revisa evidencias (estado 10) | — |

**Pago** (común a todos los anticipos): **Contabilidad** ve los de estado 1, registra código causal, observación y fecha → 5 (o rechaza → 13 / 2); **Tesorería** ve los de estado 5 y sube el **comprobante de pago** (PDF, ≤10 MB) con fecha. **El estado no cambia después del pago** (sigue en 5) y **no se envía ningún correo**: tanto el aviso a tesorería tras causar como el aviso de "anticipo pagado" están comentados en el código.

Volumen: 3 anticipos en total (oct-2025 a jul-2026), 1 comprobante de pago. El flujo existe pero casi no se usa.

## 5. Pago a proveedores: la cadena completa

1. **Compromiso**: la OC aprobada (jurídica: `cod_oc` + PDF de Helisa) es el compromiso de pago; el correo "orden aprobada" llega al proveedor con copia.
2. **Anticipo opcional**: si la OC lleva anticipo (`proveedores.anticipo`), el productor lo solicita, admin lo aprueba, contabilidad lo causa y tesorería lo paga con comprobante; el correo de orden aprobada copia a `contabilidad_pagos` cuando hay anticipo.
3. **Recepción**: el productor firma la remisión (jurídica) o el tercero sube evidencias + cuenta de cobro (natural).
4. **GR / comprobado**: admin registra el GR; el correo "GR generado" le pide al proveedor radicar la factura en `Facturacion.proveedores@bullmarketing.com.co` con copia a `compras@`.
5. **Pago final**: **no está en el CRM**. La factura, su causación y el pago se hacen fuera (Helisa/tesorería); el CRM solo guarda comprobantes para anticipos. El botón de WhatsApp al tercero dice "puedes seguir el estado de tu pago desde este enlace", pero el portal solo muestra datos, términos y evidencias, no el estado del pago.

## 6. Subida de evidencias y archivos

| Archivo | Quién lo sube | Dónde se guarda (`storage/app/public/…`) | Validación |
|---|---|---|---|
| Cotización del proveedor (`archivo_cot`) | Productor | `ordenes_juridicas/` | PDF |
| Orden de Helisa (`archivo_orden_helisa`) | Admin al aprobar | `ordenes_juridicas_helisa/`, `ordenes_naturales/` | PDF ≤ 2 MB |
| Remisión firmada (`archivo_remision`) + firma (`archivo_firma`) | Productor | `remisiones/`, `firmas_produccion/` | PDF + PNG generado de la firma |
| Evidencias de OC natural (`evidencias.foto_evidencia`) | Tercero (portal) | `evidencias/` | Imagen, fecha y observación; una lista por orden |
| Cuenta de cobro (`archivo_cuenta_cobro`) | Tercero (portal) | `cuentas_cobro/` | Archivo |
| Evidencias de anticipo (`evidencias_anticipo`) | Productor | `evidencias/` | Una por ítem; obligatorio tener todas para enviar |
| Firma del productor (`anticipos.firma_productor`) | Productor | `firmas_productores/{id}.png` | Se reutiliza si ya existe |
| Comprobante de pago (`anticipos.comprobante_pago`) | Tesorería | `anticipos/` | PDF ≤ 10 MB, uno solo |
| Contrato (`natural_info.contrato`) | Generado por el sistema | PDF | Requiere planilla, cédula, cert. bancaria y (si aplica) RUT |
| RUT, cédula, cert. bancaria, planilla, art. 383 (`terceros`) | Tercero / productor | `ruts/`, `copia_cedula/`, `cert_bancarias/`, … | PDF/Excel/imagen ≤ 10 MB |

Datos: 16.705 evidencias, 2.901 remisiones, 801 certificaciones bancarias, 797 cédulas, 584 RUT y 148 planillas sobre 22.217 terceros (la mayoría de terceros son registros antiguos sin documentos).

## 7. Sistema de notificaciones

### 7.1 Correo

- **Cómo se envía**: `App\Traits\Email::sendMail` con **PHPMailer por SMTP**, configuración por `.env` (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`). Se envía **en el mismo request** (sin cola ni reintentos); si falla, se registra en `storage/logs/laravel.log` y la acción continúa. Plantillas HTML en `resources/views/mails/` (index genérico, ordenAprobada, grGenerado, ordenAnulada, anticipoPagado, presupuestos).
- **Destinatarios por área** (tabla `notificacion_destinatarios`, editable por un desarrollador; antes estaban quemados en el código): compras (compras@), compras_cc (Nicol Riaño, Katherine Galvis), contabilidad (Diana Bohórquez, facturacion.proveedores@, auxiliar.contable@), contabilidad_pagos (contadores@, tesoreria@), controller (9 correos), lider_comercial (Nefer Barragán), produccion (Fernando Páez, Geraldin Parada, Jesica Ramírez), tesoreria (tesoreria@, Ligia Torres).

| Evento | Función | Destinatarios | Estado |
|---|---|---|---|
| OC jurídica aprobada | `mailOrdenAprobada` | compras, comercial, productor; cc compras_cc, proveedor, (contabilidad_pagos si hay anticipo) | Activo |
| GR generado | `mailGrGenerado` | igual que la anterior | Activo |
| OC anulada | `mailOrdenAnulada` | compras, comercial, productor; cc compras_cc | Activo |
| OC natural firmada por el tercero | `ocNaturalFirmada` | productor; cc producción | Activo |
| Evidencias enviadas por el tercero | `ocNaturalEvidenciasEnviadas` | productor; cc producción | Activo |
| Evidencias rechazadas | `ocNaturalEvidenciasRechazadas` | productor; cc producción | Activo (solo si rechaza un admin) |
| OC natural a revisión de controller | `ocNaturalRevisionController` | controller | Activo |
| OC natural aprobada → contabilidad | `ocNaturalRevisionContabilidad` | contabilidad | Activo |
| Nómina a revisión de controller | `ocJuridicaRevisionController` | controller | **Creado hoy** (antes no existía y rompía el flujo) |
| Nómina corregida → líder de producción | `ocJuridicaRevisionLiderProd` | producción | **Creado hoy** |
| Anticipo causado → tesorería | `ocNaturalRevisionTesoreria` | tesorería | **Comentado** (no se envía) |
| Anticipo pagado | `mailAnticipoPagado` | compras, comercial, productor; cc compras_cc, proveedor, contabilidad_pagos | **Comentado** (no se envía) |
| Presupuesto: validación líder / aprobación / aprobado / rechazado / actualización controller | 5 funciones | líder_comercial, aprobadores, comercial (+ ejecutivo), controller | Activo (flujo comercial) |

### 7.2 SMS

- **Cómo se envía**: `App\Traits\SMS::sendAction` llama a la API de **Hablame** (`https://www.hablame.co/api/sms/v5/send`) con la clave `SMS_TOKEN` del `.env`, remitente "BUllCRM". Síncrono, sin reintentos y **sin registrar errores** (la respuesta se descarta). En el entorno local no hay `SMS_TOKEN`, así que los SMS no salen (ni avisan).
- **Mensajes**: (1) al crear una OC natural: bienvenida + enlace firmado del portal; (2) al pasar a "Evidencias": enlace para adjuntar evidencias; (3) al rechazar evidencias: enlace para verlas y corregir. Los tres solo si el tercero tiene teléfono. Existe además un SMS de "saludo" a un número quemado (`3134085483`) que no se usa desde ninguna pantalla, y tres SMS del flujo de presupuestos comentados (fueron reemplazados por correo).
- **WhatsApp**: no hay integración; en la lista de órdenes del admin hay un botón que abre `wa.me` con un texto prellenado y el enlace del portal (ahora firmado). Es manual.

### 7.3 Enlace firmado del portal

El portal `/consulta-terceros/{orden}` exige una firma en la URL (`signed`). Los SMS ya lo generan firmado; los botones de la lista de admin se corrigieron hoy. Un enlace sin firma responde 403.

## 8. Hallazgos

**Corregidos hoy (commit en `correcciones`)**
1. `Nomina::enviarAprobacion` llamaba a dos funciones de correo inexistentes → error fatal al reenviar una nómina. Creadas (`ocJuridicaRevisionController`, `ocJuridicaRevisionLiderProd`).
2. Botones WhatsApp y "Copiar enlace" del listado de órdenes generaban el enlace del portal sin firma (403 desde la corrección de seguridad). Ahora van firmados.

**Pendientes de decisión (no se tocaron)**
3. Los correos de la cadena de pago están apagados: causación → tesorería y anticipo pagado. Nadie recibe aviso por correo de que un anticipo se causó o se pagó. Activarlos es quitar dos comentarios; requiere confirmar destinatarios.
4. Tras el pago, el anticipo sigue en "Causado – pendiente de pago" (5): no hay estado "Pagado". Se distingue solo por si tiene comprobante.
5. Gerencia aprueba un anticipo de productor y el código dice "se envía notificación al productor", pero no envía nada. El productor no se entera de que ya puede legalizar.
6. Umbral de $500.000 para saltar gerencia en anticipos de productor: quemado en el código (`AnticipoProductor`), pendiente en el plan de acción.
7. SMS: sin registro de errores ni reintentos; teléfono de saludo quemado; `SMS_TOKEN` debe existir en producción (no está en `.env.example`).
8. Correo: envío síncrono sin cola; un SMTP caído ralentiza cada aprobación y el aviso se pierde (solo queda en el log). Recomendable pasar a colas (`QUEUE_CONNECTION=database`) y reintentos.
9. El texto del WhatsApp promete "seguir el estado de tu pago" en el portal, pero el portal no muestra el pago.
10. El flujo de nómina (tipo 3) no tiene ninguna orden en producción: conviene probarlo completo con `prueba.productor`, `fernando.paez@` (líder) y `j.ariza@` (gerencia) antes de usarlo.
11. Proveedores: 1.688 registros y **0 activos** según la columna `estado` (o el campo no se usa o todos quedaron inactivos); las OC siguen enlazándolos igual.

## 9. Recomendaciones

- Activar los dos correos de pago y añadir el aviso al productor cuando gerencia aprueba su anticipo (tres cambios pequeños).
- Añadir estado "Pagado" a anticipos (o marcar por comprobante en las listas) y una vista de pagos para compras/controller.
- Pasar correo y SMS a colas con reintentos y registrar cada envío (tabla `notificaciones_log`: evento, canal, destinatario, resultado), para poder auditar "quién recibió qué".
- Mostrar en el portal del tercero el estado real de su orden y del pago, que es lo que promete el mensaje.
- Mover el umbral de $500.000 y el número de saludo a configuración.
