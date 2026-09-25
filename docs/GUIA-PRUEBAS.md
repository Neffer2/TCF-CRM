# Guía de pruebas — TCF-CRM (rama `correcciones`)
**Para:** desarrollador/tester que valida la reestructuración antes del paso a producción.
**Rama a probar:** `correcciones` · **NUNCA toques la rama `main`** (subir a main despliega a producción automáticamente).
**Versión de esta guía:** 25 de septiembre de 2026 (commit más reciente de `correcciones`).

> **Actualización del 24-sep (anticipo jurídico):** si ya tenías el entorno montado, corre esto en la raíz del proyecto y pasa a **B6b**. Todo es solo para tu base local; no lo corras en ningún servidor.
>
> ```bash
> git pull origin correcciones
> php artisan migrate
> php artisan db:seed --class=AnticiposJuridicosDemoSeeder
> php artisan view:clear
> ```
>
> `migrate` aplica `2026_09_24_120000_anticipo_juridico_flujo`; el seeder crea dos anticipos de demostración y te muestra sus números. Recarga el navegador con Ctrl+F5 para tomar los estilos nuevos.

---

# PARTE 0 — Qué cambió (resumen para ubicarse)

Todo lo que sigue está en la rama `correcciones`, commit por commit desde el 11-sep-2026. Si algo de esta lista no se comporta como se describe, es un hallazgo.

**Seguridad y estabilidad**
- Registro público, "olvidé mi clave" y verificación de correo **eliminados**: las cuentas las crea un admin y las claves las cambia un desarrollador. Páginas 404/403/500 propias con la marca.
- Rutas de borrado y de otros roles protegidas por middleware (`admin`, `rol:1,11,12`, productor, tesorería, contabilidad…). Gerencia (rol 20) no es conmutable y se trata como Admin.
- 12 rutas de anticipos que el código invocaba sin existir, ahora definidas; rutas muertas (métodos inexistentes) retiradas. Barrido de 62 rutas × 10 roles sin ningún error 500.
- Portal público de terceros solo con enlace firmado (403 sin firma).

**Roles y cuentas**
- Una sola cuenta puede tener varios roles (`role_user`) y cambiar entre ellos con el selector de la barra superior.
- Roles nuevos y propios: **Controller (11)** y **Líder comercial (12)**, cada uno con su dashboard y su menú; Gerencia = Jony Ariza y Alejandro Rodríguez.
- **Mi perfil** para todos los roles (foto grande, cargo, datos, roles, equipo). "Ajustes" eliminado.

**Comercial y presupuestos**
- Dashboard de gerencia v3: seis indicadores, gráfica mensual con **pronóstico de cierre**, ranking de comerciales y de líderes, filtros por líder y por comercial, panel a pantalla completa por estado de venta (líder → comercial → proyecto).
- Base comercial general y Helisa general rediseñadas (resumen, fichas por estado, orden por columna, búsqueda, export = lo filtrado).
- Presupuesto: **misma pantalla de siempre** (cuatro tarjetas: cifras, datos del proyecto, recargos y notas) más un botón "?" al lado de cada cifra que explica la fórmula con los números reales; cabeceras de la tabla legibles.
- Corregidos: edición de contacto que cruzaba PBX/Web, Helisa que editaba el documento equivocado, participaciones que no se limpiaban, % por cumplir negativo, margen 35 exacto que daba 500, exports que no respetaban filtros.

**Producción, pagos y notificaciones**
- Motor de transiciones de anticipos y órdenes con topes de dinero validados en servidor; candado anti doble pago; estado **Pagado (14)** para anticipos; tipo de orden **Nómina (3)**.
- **Anticipo jurídico según la matriz de Gerencia (24-sep)**: productor → líder de producción → **Controller** → si supera **$10.000.000**, Gerencia. Cada revisor puede devolverlo con motivo; al aprobarse se genera la **orden de anticipo (PDF)**; Contabilidad causa (o lo devuelve al Controller) y Tesorería paga. Se puede pedir con orden de compra o **sin orden previa** (borrador con soporte). Una sola pantalla para todos los roles con el recorrido de seis etapas, y bandeja nueva del Controller (menú Producción → Anticipos jurídicos). Ver **B6b**.
- 13 correos que el código llamaba y no existían, creados. Todo correo/SMS pasa por `Notificador`: queda registrado en **Acciones → Notificaciones** con reintentos, y si falla se alerta a desarrollo (`CRM_ALERTA_*`).
- **Registro de actividad** (`/actividad`): sesiones, páginas, acciones con datos y cambios de registros (antes/después).
- Inicio de **Tesorería** y **Contabilidad** (antes en blanco): órdenes por pagar/causar, valor, procesadas por mes, anticipos pendientes, accesos directos.
- Font Awesome ahora es local (el kit externo devolvía 403 y por eso salían círculos naranjas vacíos); botones de ítems con texto.

**Base de datos**
- Fase 6 de reestructuración: normalización, llaves e integridad, esquema documentado (`docs/`), migraciones nuevas (`roles_controller_lider_comercial`, `notificaciones_log_y_estado_pagado`, `tipo_orden_nomina`, `actividad_log`, `documentar_esquema`).
- La base de pruebas (`bullcrm.sql`) se entrega **aparte, no en el repositorio**: tiene datos personales reales y no debe quedar en el historial de git, donde cada clon guarda una copia. El `tfc_crm.sql` que está en el repositorio es un volcado viejo (mar-2025): no lo uses. Ver A3.

**Visual**
- Login rediseñado con foto de marca; capa visual Bull coherente y animada en todos los roles (tipografía Manrope, naranja solo en acentos, cabecera con velo carbón). Favicon original intacto.

---

# PARTE A — Montaje del entorno (una sola vez)

## A1. Requisitos

| Herramienta | Versión | Nota |
|---|---|---|
| PHP | **8.0 o 8.1** (no 8.2+) | con extensiones: pdo_mysql, gd, zip, mbstring, bcmath |
| Composer | 2.x | |
| MySQL o MariaDB | 8+ / 10.6+ | una instancia local — **jamás la de producción** |
| Node.js + npm | 18+ | |
| Git | | |

## A2. Clonar y preparar el código

```bash
git clone https://github.com/Neffer2/TCF-CRM.git
cd TCF-CRM
git checkout correcciones
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
npm install
npm run production
```

## A3. Base de datos: importar `bullcrm.sql`

El archivo **`bullcrm.sql`** (~43 MB, o `bullcrm.sql.gz` de 9 MB) te llega en el paquete de QA, **no está en el repositorio**: la base trae datos reales de personas (cédulas, cuentas bancarias, correos) y no debe quedar en el historial de git. No lo subas a GitHub ni lo compartas. Es una copia de la base de pruebas ya lista: datos reales de producción + migraciones aplicadas hasta el 23-sep + **usuarios de prueba creados**. No necesitas entrar a Hostinger ni a producción para nada. Las instrucciones detalladas (también para Windows) están en `COMO-IMPORTAR-LA-BASE.md`, dentro del mismo paquete.

> Producción (`u630167073_CRM` en Hostinger) es **solo lectura y no se toca**. Si algún día necesitas datos más frescos, expórtala desde phpMyAdmin (Exportar → Rápido → SQL) a `db-snapshots/` (carpeta ignorada por git) y sigue el mismo procedimiento; en ese caso sí tendrás que correr `php artisan migrate` después.

Configura primero el `.env` con tu MySQL local:

```
DB_HOST=127.0.0.1
DB_PORT=3306        # el puerto de TU MySQL local
DB_DATABASE=tfc_crm
DB_USERNAME=root
DB_PASSWORD=tu_clave_local
MAIL_MAILER=log     # ¡importante! así ningún correo real sale de tu máquina
SMS_MODO=log        # ídem para los SMS
QUEUE_CONNECTION=sync
```

> ⚠ Verifica dos veces que `DB_HOST` apunta a tu máquina. Si apunta a producción, DETENTE.

Importa. El archivo **crea la base él mismo** (empieza con `DROP DATABASE IF EXISTS tfc_crm`, así que reemplaza la que tengas) y viene sin `DEFINER` ni líneas de GTID, así que entra limpio en MySQL 8/9 y MariaDB:

```bash
mysql -u root -p < bullcrm.sql
```

Debe terminar sin errores. Comprueba: `SELECT COUNT(*) FROM users;` → **215** usuarios; `SELECT COUNT(*) FROM migrations;` → **54** (55 después de A4).

## A4. Verificar migraciones y usuarios de prueba

```bash
php artisan migrate                          # aplica lo posterior al volcado (hoy: el anticipo jurídico), solo en tu base local
php artisan migrate:status | grep "| No"     # después de migrar no debe mostrar nada
php artisan db:seed --class=UsuariosPruebaSeeder          # opcional: reescribe las claves de prueba si alguien las cambió
php artisan db:seed --class=AnticiposJuridicosDemoSeeder  # anticipos jurídicos de demostración para B6b (se puede repetir: no duplica)
```

Los seeders **solo corren en entorno local**. Los usuarios de prueba ya vienen en el dump (contraseña de todos: **`Prueba123*`**):

| Correo | Rol | Para probar |
|---|---|---|
| prueba.gerencia@local.test | Admin + **Gerencia** | aprobaciones de margen, validación de nómina y anticipos (jurídicos de más de $10 M) |
| prueba.admin@local.test | Admin (sin gerencia) | que un admin común **NO** pueda aprobar márgenes |
| prueba.comercial@local.test | Comercial | todo el flujo comercial |
| prueba.ejecutivo@local.test | Ejecutivo de cuenta | módulo asistente |
| prueba.lider@local.test | Líder producción | validación de anticipos e OC |
| prueba.productor@local.test | Productor | órdenes, anticipos, remisiones (ya tiene 3 centros de costo asignados) |
| prueba.tesoreria@local.test | Tesorería | pago de anticipos, inicio de tesorería |
| prueba.contabilidad@local.test | Contabilidad | causación, inicio de contabilidad |
| prueba.multirol@local.test | Productor + Comercial | el selector de cambio de rol |
| prueba.controller@local.test | **Controller** | dashboard de revisión, presupuestos, actualizaciones, consumidos, reportes y **revisión de anticipos jurídicos** |
| prueba.lidercom@local.test | **Líder comercial** | dashboard de su equipo (prueba.comercial, Alexandra Niño, Juan Camilo Rodríguez), validaciones |

Los `prueba.*` sirven para probar flujos desde cero, pero casi no tienen datos. Para ver cada pantalla **con datos reales**, estas cuentas reales también tienen la clave `Prueba123*` (solo en la base local; sus correos y datos no cambian):

| Correo real | Rol | Por qué este |
|---|---|---|
| crm.Alejandra.Ortiz@bullmarketing.com.co | Comercial | la mayor facturación de 2026 (231 movimientos Helisa) |
| alexandra.nino@bullmarketing.com.co | Comercial | del equipo de Leonardo Guarin; también es del equipo de prueba.lidercom |
| lady.ortiz@bullmarketing.com.co | Líder comercial | 4 comerciales a cargo |
| Lider.Controller@bullmarketing.com.co | Controller | Astrid Morales, con permiso de validar nómina |
| j.ariza@bullmarketing.com.co | Gerencia | Jony Ariza (Admin + Gerencia) |
| compras@bullmarketing.com.co | Admin (compras) | recibe las OC |
| andre.aguirre@bullmarketing.com.co | Productor | la que más centros de costo tiene (616) |
| fernando.paez@bullmarketing.com.co | Líder de producción | |
| geraldin.parada2@bullmarketing.com.co | Ejecutivo de cuenta | |
| tesoreria@bullmarketing.com.co | Tesorería | |
| contadores@bullmarketing.com.co | Contabilidad | |

**Multicuenta:** `prueba.multirol` entra como Productor y con el selector de la barra superior pasa a Comercial sin cerrar sesión. Las cuentas reales de Controller y Líder comercial conservan Admin como rol secundario en ese mismo selector.

Todos los roles tienen **Mi perfil** (nombre en la barra superior): foto, cargo, datos, roles, permisos y equipo, solo lectura. La sección "Ajustes / Actualizar perfil" se eliminó: los datos y las claves los cambia un desarrollador.

## A5. Correos, SMS y alertas en local

Con `MAIL_MAILER=log` y `SMS_MODO=log` nada sale de tu máquina: cada envío queda como "Enviado" en **Acciones → Notificaciones** (admin) y en `storage/logs/laravel.log` como "[correo simulado]" / "[sms simulado]". Las variables `CRM_ALERTA_CORREOS` / `CRM_ALERTA_TELEFONOS` (a quién avisar cuando un envío falla) y `CRM_ANTICIPO_UMBRAL_GERENCIA` están documentadas en `.env.example`.

## A6. Arrancar

```bash
php artisan serve --port=8140
```

Abre **http://127.0.0.1:8140** e inicia sesión con `prueba.admin@local.test` / `Prueba123*`. Si ves el dashboard, el montaje quedó bien.

> Si una página da 500 por una tabla o columna inexistente, tu dump puede ser viejo. Reporta el error exacto (aparece en `storage/logs/laravel.log`).

---

# PARTE B — Matriz de casos de prueba

Marca cada caso: ✅ pasó · ❌ falló (adjunta evidencia) · ⏭ no aplicable.
Después de cada caso de "bloqueo" verifica que la acción **realmente no ocurrió** (recarga y confirma el estado).

## B1. Seguridad de acceso

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 1.1 | Sin registro público | Cierra sesión → visita `/register`, `/forgot-password`, `/verify-email` | Todas dan la página 404 de marca: las cuentas las crea un admin en "Mi equipo" y las claves las restablece un desarrollador |
| 1.2 | Rutas de otro rol | Como `prueba.productor` visita `/dashboard-admin`, `/lista-anticipos-admin`, `/reporte-plano-helisa/3` | En todas: redirigido a su propio dashboard, nunca ve el contenido |
| 1.3 | Plano contable protegido | Como `prueba.contabilidad` visita `/reporte-plano-helisa/3` | Descarga el Excel (contabilidad SÍ puede) |
| 1.4 | Deletes protegidos | Como `prueba.productor`, con la consola del navegador o Postman haz POST a `/delete-proyecto/1` (con el token CSRF de cualquier página) | Redirigido; **ningún** proyecto se borra |

## B2. Multi-rol y selector

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 2.1 | Selector visible solo con 2+ roles | Entra como `prueba.multirol` y luego como `prueba.comercial` | multirol ve el selector de rol en la barra superior; comercial NO lo ve |
| 2.2 | Cambio de rol | Como `prueba.multirol` (entra como Productor) → selector → **Comercial** | Cae en el dashboard comercial sin cerrar sesión; el selector ahora marca ✓ Comercial |
| 2.3 | Rol no asignado | Con la sesión de multirol haz POST manual a `/cambiar-rol/1` | Rechazado ("No tienes asignado ese rol"); sigue en su rol |

## B3. Permisos de Gerencia

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 3.1 | Admin común no aprueba | Como `prueba.admin` abre un presupuesto en validación (estado 5) | NO ve los botones de aprobar/rechazar |
| 3.2 | Gerencia sí aprueba | Mismo presupuesto como `prueba.gerencia` | SÍ ve aprobar/rechazar |
| 3.3 | Menú avanzado | Compara el menú lateral admin entre `prueba.admin` y un usuario con el permiso ver-menu-admin-avanzado (Alejandro/Jony del dump) | La sección extra solo aparece para quien tiene el permiso |
| 3.4 | Correo de aprobación → Gerencia | Como `prueba.comercial` envía un presupuesto a aprobación; revisa `storage/logs/laravel.log` | El intento de correo va dirigido a Jony y Alejandro (no a Luz/Sebastian) — con MAIL_MAILER=log el "envío" queda en el log |

## B4. Flujo comercial

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 4.1 | Editar contacto no cruza campos | Como comercial: Contactos → edita uno cambiando SOLO el cargo → guarda → reabre | PBX y Web quedan **iguales** que antes (no intercambiados) |
| 4.2 | Helisa edita el documento correcto | Gestión Helisa: busca un centro de costos con 2+ documentos → edita uno | El formulario carga los porcentajes/comisiones de ESE documento (verifica contra la lista), no del primero del centro |
| 4.3 | Reducir participaciones limpia | Gestión → proyecto con 2 participantes → edita la propuesta bajando a 1 → guarda | El comercial retirado desaparece del proyecto (verifica en la BD: `comercial_2` y `porcentaje_2` en NULL para esa gestión) |
| 4.4 | Filtro de mes Helisa | Lista Helisa → filtra por Enero | Solo registros de Enero (antes mezclaba Oct/Nov/Dic) |
| 4.5 | Export = lo que ves | Base comercial: filtra por centro de costos → Exportar | El Excel contiene SOLO lo filtrado |
| 4.6 | % por cumplir | Dashboard comercial | "% por cumplir" está entre 0 y 100, nunca negativo |
| 4.7 | Margen exactamente 35 | Crea un presupuesto cuyo margen dé 35.00 exacto → envíalo a aprobación → apruébalo como gerencia | La aprobación funciona sin error 500 |

## B5. Producción — órdenes de compra

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 5.1 | Tope de precio real | Como `prueba.productor`: nueva OC natural → ítem → intenta valor unitario mayor al del ítem del presupuesto | Rechazado con error de validación |
| 5.2 | Editar no evade topes | Agrega un ítem válido → edítalo intentando ponerle un valor total superior al saldo | Rechazado (antes la edición aceptaba cualquier valor) |
| 5.3 | No borrar orden aprobada | Busca una OC en estado Aprobado del dump → como su productor (o admin) intenta eliminarla | Bloqueado: "usa la anulación" |
| 5.4 | Remisión solo del dueño | Como `prueba.productor` visita `/firmar-remision/{id}` con el id de una orden de OTRO productor y trata de firmarla | Bloqueado: "no pertenece a tu producción" |
| 5.5 | Remisión solo Aprobadas | Ídem con una orden propia en estado Revisión | Bloqueado: solo órdenes Aprobadas |
| 5.6 | Nómina siempre pasa por revisión | Crea una orden de nómina pequeña (<$1M) → como `prueba.lider` apruébala | Va a revisión de gerencia (estado 9), NO se auto-aprueba |
| 5.7 | Aprobación de nómina completa | Como gerencia/controller aprueba la nómina | La orden queda con código OC, PDF Helisa y fecha de aprobación (verifica en el detalle) — antes quedaba "aprobada" vacía |

## B6. Anticipos — flujo completo (el más importante)

Ejecuta la cadena completa con estos usuarios en orden:

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 6.1 | Solicitud | `prueba.productor` → Solicitar anticipo → ítems de su centro de costo → intenta `valor_anticipo` mayor al valor total del ítem | Rechazado. Con valores válidos, el anticipo se crea (estado Revisión líder) |
| 6.2 | No saltarse al líder | Con la consola del navegador invoca el método Livewire `revisionAnticipoProductor(1)` sobre su propio anticipo | Rechazado ("transición no permitida" / "sin permisos"); el estado NO cambia |
| 6.3 | Revisión líder | `prueba.lider` → lista de anticipos → aprueba con observaciones | Pasa a revisión de gerencia **sin error 500** y la página redirige bien |
| 6.4 | Revisión gerencia | `prueba.gerencia` → aprueba | Pasa a cargue de evidencias; el productor recibe la notificación (log) |
| 6.5 | Evidencias | `prueba.productor` → sube evidencias del anticipo → envía | Estado avanza; intenta borrar una evidencia de OTRO anticipo por consola → bloqueado |
| 6.6 | Causación | `prueba.contabilidad` → lista anticipos (estado aprobado) → causa con código | Pasa a causado. Intenta causarlo de nuevo → bloqueado |
| 6.7 | Pago | `prueba.tesoreria` → **lista-anticipos-tesoreria** (¡esta pantalla antes no existía!) → detalle → registra comprobante | Pago registrado. **Intenta pagarlo otra vez → "Este anticipo ya fue pagado"** (candado anti doble pago) |
| 6.8 | Anticipo jurídico | Tiene flujo propio desde el 24-sep | Ver **B6b** |

## B6b. Anticipo jurídico — matriz de Gerencia (24-sep-2026)

Regla de la matriz: **Productor → Líder de producción → Controller → hasta $10.000.000 queda aprobado; más de $10.000.000 lo aprueba Gerencia**. Después: orden de anticipo (PDF) → Contabilidad causa → Tesorería paga.

Datos de demostración: `php artisan db:seed --class=AnticiposJuridicosDemoSeeder` (paso A4) crea dos anticipos de `prueba.productor` y muestra sus números: uno de **$12.000.000** sin orden de compra, ya aprobado por líder, Controller y Gerencia (está en Contabilidad con su orden de anticipo en PDF), y uno de **$1.000.000** que espera al líder. Si quieres empezar de cero, vuelve a importar `bullcrm.sql` y repite A4. En el ambiente local los correos no salen: se ven en **Acciones → Notificaciones** (Admin) y en `storage/logs/laravel.log`.

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 6b.1 | Pantalla que se explica sola | `prueba.productor` → Solicitar anticipo → pestaña Anticipo Jurídico | Arriba dice qué es y quién lo revisa; debajo el recorrido de 6 etapas (Productor, Líder, Controller, Gerencia, Contabilidad, Tesorería) |
| 6b.2 | Sin orden de compra | "Aún no hay orden de compra" → proyecto → busca el proveedor por nombre o NIT → valor base 30.000.000 → 40 % | Calcula **$12.000.000** y avisa que **pasa por Gerencia**; si el acuerdo con el proveedor es menor, lo advierte. Enviar sin adjuntar soporte → error "hay que adjuntar al menos un soporte" |
| 6b.3 | Validaciones | Valor base mayor al costo del proyecto; porcentaje 0 o 150; proveedor persona natural; archivo .exe | Cada uno rechazado con su mensaje en el campo |
| 6b.4 | Envío | Adjunta una cotización (PDF o imagen, puedes adjuntar varias y quitar una) → Enviar a revisión | Abre la ficha del anticipo con el mensaje de confirmación; estado **Revisión líder de producción** |
| 6b.5 | Líder devuelve | `prueba.lider` → Anticipos (ahora sí aparecen los jurídicos) → Revisar → "Devolver al productor" sin escribir motivo; luego con motivo | Sin motivo: error. Con motivo: **Rechazo líder** y correo al productor con el motivo |
| 6b.6 | Productor corrige | `prueba.productor` → abre el anticipo | Ve quién lo devolvió y por qué; el formulario viene lleno. Reenviar sin decir qué corrigió → error; con la explicación → vuelve a **Revisión líder** |
| 6b.7 | Líder aprueba | `prueba.lider` aprueba | Pasa a **Revisión controller**; el recorrido muestra quién aprobó y su observación |
| 6b.8 | Controller: umbral | `prueba.controller` → Producción → Anticipos jurídicos → Revisar | El panel dice qué pasa al aprobar: "queda aprobado" (≤ $10 M) o "pasa a Gerencia" (> $10 M). Aprueba |
| 6b.9 | ≤ $10 M no pasa por Gerencia | Con el de $1.000.000: apruébalo como `prueba.lider` y luego como `prueba.controller` | Queda **Aprobado** directo, sin pasar por Gerencia, y se genera su **orden de anticipo** (OA- más su número; PDF en "Soportes y documentos") |
| 6b.10 | Gerencia | Con uno de más de $10 M en Revisión gerencia: entra como `prueba.admin` y luego como `prueba.gerencia` | `prueba.admin` (sin permiso de Gerencia) solo lo consulta: "Le toca a Gerencia". `prueba.gerencia` ve "Te toca · Gerencia", aprueba → orden de anticipo |
| 6b.11 | Orden de anticipo | Abre el PDF de la orden | Datos de Bull, proveedor, proyecto, base × % = valor y la tabla de aprobaciones (quién, cuándo, observación) solo de la última ronda |
| 6b.12 | Contabilidad devuelve al Controller | `prueba.contabilidad` → el de $12.000.000 → "Devolver al Controller" con motivo | Queda **Rechazo contabilidad**; lo ve el Controller en su bandeja (no el productor) con el motivo. El Controller lo aprueba de nuevo → vuelve a Contabilidad con la misma orden |
| 6b.13 | Causar | `prueba.contabilidad` → número con letras; luego solo dígitos | Con letras: error. Con dígitos: **Causado** y correo a tesorería |
| 6b.14 | Pagar | `prueba.tesoreria` → Anticipos → Pagar → sin comprobante; luego con un PDF | Sin archivo: error. Con PDF: **Pagado**; la ficha dice "Flujo terminado" y muestra el comprobante |
| 6b.15 | Nadie se salta pasos | Como Controller abre uno en Revisión líder; como Tesorería uno Aprobado; como productor uno de otro productor (`/anticipo-prod/2`) | Sin botones ("¿Qué sigue? Le toca a…"); el de otro productor da **403** |
| 6b.16 | Anticipos anteriores | Abre como admin `/anticipo-admin/1`, `/2` y `/3` | Abren sin error; el #1 figura **Pagado** (ya tenía comprobante) y el #2 y #3 están en Revisión líder |

## B7. Portal público de terceros

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 7.1 | Sin firma no entra | Abre `http://127.0.0.1:8140/consulta-terceros?orden=5` en incógnito | **403** |
| 7.2 | Enlace firmado sí | Genera un enlace válido: `php artisan tinker --execute='echo URL::signedRoute("consulta-terceros", ["orden" => ID]);'` (usa el id de una orden natural en estado 3 o 7) → ábrelo en incógnito | El portal carga con los datos de ESA orden |
| 7.3 | Firma no transferible | Toma el enlace válido y cambia el número de orden en la URL | **403** |

## B8. Reportes

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8.1 | Consumidos | Como admin → reporte de consumidos | Headers CANT / DIAS / OTROS coinciden con los datos; la columna SALDO descuenta TODOS los consumos del ítem |
| 8.2 | Export Helisa general | Admin → Exportar Helisa sin elegir comercial | Descarga el reporte completo sin error 500 |
| 8.3 | Listas de contabilidad/tesorería | `prueba.contabilidad` → anticipos (flujo viejo) → pagina y filtra varias veces | No crashea y los filtros se acumulan (año + centro + productor a la vez) |
| 8.4 | Excel = pantalla | Comercial → un presupuesto → compara el margen en pantalla vs el del Excel exportado | Mismo número |

## B8b. Dashboard de gerencia — filtros por líder y comercial

Los líderes comerciales salen de la tabla `lider_comercial_user` (líder → comerciales a su cargo). En el dump de producción hay 3: Lady Ortiz (4 comerciales), Leonardo Guarin (5) y Paula Garnica (7).

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8b.1 | Filtro por líder | `prueba.gerencia` → Dashboard → **Líder comercial: Lady Ortiz** (sin elegir año) | El año pasa solo al más reciente; todos los KPI, la gráfica y el ranking dicen "Equipo de Lady Ortiz"; la venta consolidada del equipo = suma de la de sus 4 comerciales |
| 8b.2 | Comercial dentro del equipo | Con Lady elegida abre **Comercial** | La lista solo muestra a Alejandra Ortiz, Brayan Moreno, Kristel Rey y Viviana Triana; al elegir una, los KPI son solo de ella |
| 8b.3 | Cambio de líder limpia comercial | Con Alexandra Niño elegida (equipo de Leonardo) cambia el líder a Lady | El comercial se vacía y el dashboard muestra el equipo de Lady |
| 8b.4 | Ranking por líder | Ranking → **Por líder** | 3 filas (Leonardo, Lady, Paula) con venta y meta de todo su equipo y % de cumplimiento; el líder filtrado aparece resaltado |
| 8b.5 | Sin líder | Líder comercial: **Todos los equipos** | Vuelven los totales generales y el buscador lista a todos los comerciales |
| 8b.6 | Detalle de un estado | En "Estado de facturación" pulsa **Ejecución por facturar** (o Venta en ejecución / Venta) | Se abre un panel a pantalla completa (no redirige): proyectos del periodo en ese estado agrupados por líder comercial → comercial → proyecto, con totales y barras; los dos primeros líderes vienen abiertos; se cierra con ×, con Esc o pulsando fuera |
| 8b.7 | Detalle respeta filtros | Elige líder Lady Ortiz y luego abre "Venta en ejecución" | Solo aparece el equipo de Lady; con un comercial elegido, solo ese comercial |

## B8c. Base comercial general (listado rediseñado)

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8c.1 | Carga por defecto | `prueba.gerencia` → Inicio → **Base comercial general** | Año = el más reciente, "Todo el año"; resumen (valor, registros, clientes, comerciales) y fichas de estado con conteos; tabla ordenada por fecha descendente |
| 8c.2 | Ficha de estado | Pulsa la ficha **Facturado** | Solo filas "Facturado", el select Estado cambia a Facturado, el KPI pasa a "Valor del estado"; pulsarla otra vez quita el filtro |
| 8c.3 | Orden por columna | Pulsa **Valor** en la cabecera | Mayor valor primero y flecha ↓; segundo clic invierte el orden |
| 8c.4 | Búsqueda de texto | Escribe `pepsico` en Buscar | Filtra por centro de costos, cliente o proyecto; las fichas y el resumen se recalculan; una búsqueda sin resultados muestra "Sin resultados" con enlace para limpiar |
| 8c.5 | Comercial | Escribe `ale` en Comercial y elige uno | Lista filtrada mientras escribes; al elegir, solo sus registros |
| 8c.6 | Desde el dashboard | Dashboard → Estado de facturación → clic en "Ejecución por facturar" | Abre esta pantalla con año, mes, comercial y estado ya aplicados (el año llega como "2026" y se reconoce igual) |
| 8c.7 | Exportar | Con cualquier filtro → **Exportar Excel** | Descarga `Reporte Base Comercial.xlsx` con exactamente las filas filtradas |
| 8c.8 | Paginación | Cambia "por página" a 50 y ve a la página 2 | El contador "Mostrando X–Y de Z" y las filas coinciden |

## B8d. Roles Controller y Líder comercial (nuevos)

Controller (rol 11) y Líder comercial (rol 12) dejan de ser cuentas Admin. En el dump, la migración `roles_controller_lider_comercial` convierte a Adriana Trujillo, Laura Álvarez y Astrid Morales en Controller, y a Lady Ortiz, Leonardo Guarin y Paula Garnica en Líder comercial (todos conservan Admin como rol secundario en el selector). Usuarios de prueba: `prueba.controller@local.test` y `prueba.lidercom@local.test` (su equipo: prueba.comercial, Alexandra Niño y Juan Camilo Rodríguez).

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8d.1 | Dashboard Controller | Entra como `prueba.controller` | Cae en `/dashboard-controller`: KPIs (cambios por revisar, en validación, aprobados del mes, órdenes en revisión) y tres listas con enlace "Ver" al presupuesto |
| 8d.2 | Menú Controller | Recorre el menú | Solo: Dashboard, Base comercial general, Helisa general, Presupuestos, Actualizaciones, Consumidos, Reporte de consumidos, Plano Helisa, Actualizar perfil |
| 8d.3 | Bloqueos Controller | Visita `/validaciones`, `/mi-equpo`, `/ordenes-compra`, `/dashboard-admin` | Redirige a su dashboard en todos |
| 8d.4 | Presupuesto como Controller | Presupuestos → Ver uno | Ve el presupuesto en modo lectura (parámetros deshabilitados) y la sección de actualización; no puede validar como líder ni gerencia |
| 8d.5 | Dashboard Líder | Entra como `prueba.lidercom` | `/dashboard-lider-comercial` con el filtro de líder fijo "Mi equipo (3)"; KPIs, gráfica y ranking solo de su equipo; el buscador de comercial solo lista a su equipo |
| 8d.6 | Menú Líder | Recorre el menú | Solo: Dashboard del equipo, Base comercial del equipo, Helisa general, Validaciones, Presupuestos del equipo, Actualizar perfil |
| 8d.7 | Base del equipo | Líder → Base comercial del equipo → Exportar | Registros y Excel solo de sus comerciales |
| 8d.8 | Validación de líder | Líder → Validaciones → un presupuesto en estado 4 de su equipo → Validar / Rechazar | Funciona igual que antes para un admin líder; `/actualizaciones` y `/consumidos` le redirigen |
| 8d.9 | Admin sigue igual | Entra como `prueba.admin` | Menú completo, dashboard con filtro de líder seleccionable, validaciones y actualizaciones accesibles |

## B8f. Presupuesto (detalle): pantalla clásica con ayudas "?"

La pantalla del presupuesto se mantiene **igual a la que la gente ya conoce** (cuatro tarjetas arriba: cifras, datos del proyecto, recargos y notas; tabla de ítems; formulario abajo). Lo nuevo es discreto: un botón "?" al frente de cada cifra que explica cómo se calcula con los números reales del presupuesto.

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8f.1 | Nada cambió de sitio | Como `crm.Alejandra.Ortiz@…` abre uno de sus presupuestos (p. ej. `/presupuesto/6733`; desde la auditoría del 24-sep un comercial ya no abre presupuestos ajenos) | Arriba: MARGEN ITEMS, VENTA PROYECTO, COSTOS DEL PROYECTO, MARGEN DEL PROYECTO, MARGEN BRUTO (PESOS) · CONTACTO, CLIENTE, PROYECTO, CENTRO DE COSTOS, CIUDAD · IMPREVISTOS, ADMINISTRACIÓN, FEE AGENCIA, TIEMPO · NOTAS. Mismo orden y mismos nombres de siempre |
| 8f.2 | Ayuda de una cifra | Pulsa el "?" de MARGEN DEL PROYECTO | Se abre un recuadro oscuro **por encima de todo** (nunca detrás de la tabla) con la explicación, la fórmula con los valores reales y el resultado; solo puede haber un recuadro abierto a la vez |
| 8f.3 | Cerrar la ayuda | Pulsa fuera del recuadro, o Esc, o de nuevo el "?" | Se cierra |
| 8f.4 | Recargos | Pulsa el "?" al lado de IMPREVISTOS | Explica imprevistos, administración, fee y tiempo, y cuánto suman hoy en pesos |
| 8f.5 | Recalcular | Como comercial cambia imprevistos a 5 y sal del campo | Venta del proyecto, márgenes y los números dentro de los "?" se recalculan |
| 8f.6 | Tabla de ítems | Pasa el ratón por las cabeceras | Cabeceras legibles (texto blanco sobre carbón/verde) con tooltip que explica cada columna; filas visibles y editables como antes |
| 8f.7 | Nada perdido | Compara con la versión anterior | Siguen la justificación, validaciones de líder/gerencia, acciones de admin, exportar historial y cotizaciones PDF/Excel |

## B8h. Gestión comercial por KAM

Inicio → **Gestión por KAM** (`/gestion-kam`). Para Admin, Gerencia, Controller y Líder comercial (el líder solo ve a su equipo). Mide cómo trabaja cada comercial su embudo y cuánto de lo que vende ya se facturó. La pantalla se explica sola: cada cifra lleva su pregunta y su cálculo, sin iconos decorativos.

**Cómo se clasifica una gestión** (verificado en el código de los formularios; `App\Services\GestionKamDatos`):

| Etapa en el CRM | Se muestra como | Cuenta como |
|---|---|---|
| 1 Prospecto | Prospecto · contacto identificado | En curso |
| 2 Oportunidad | Oportunidad · hay una necesidad concreta | En curso |
| 7 Presupuesto | **Cotizando** · armando el presupuesto | En curso (NO es venta) |
| 4 Propuesta | **Propuesta enviada** · presupuesto aprobado, esperando al cliente | En curso |
| 5 Venta | Venta · el cliente aceptó | **Venta** (única etapa ganada) |
| 6 Perdido | Perdida · no se dio | Perdida |

**Cómo se calcula la facturación.** La tabla de Helisa del CRM **no** es la contabilidad completa (en 2026, 176 ventas marcadas como facturadas no aparecen en ella). Por eso el estado de cobro sale del estado de cada venta en la base comercial, el mismo del dashboard: *facturada o cerrada*, *facturada en parte*, *ejecutada por facturar*, *en ejecución*. Helisa se muestra como verificación por proyecto.

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8h.1 | El año en una frase | `prueba.gerencia` → Gestión por KAM | Arriba, dos frases con las cifras del año: gestiones, ventas, perdidas, en curso, quietas; y cuánto de lo vendido está facturado, ejecutado sin facturar y en ejecución |
| 8h.2 | Cuatro preguntas | Mira las cuatro tarjetas | ¿Cuánto abrieron? · ¿Cuánto se volvió venta? · ¿Cuánto de lo vendido ya se facturó? (con barra por estado) · ¿Qué se está quedando quieto? |
| 8h.3 | Embudo | Sección "Dónde está cada gestión" | Pasos de izquierda a derecha con flecha: Prospecto → Oportunidad → Cotizando → Propuesta enviada → Venta; Perdida aparte. Cada paso con su explicación, su conteo y su valor |
| 8h.4 | Leyenda | Arriba a la derecha de la tabla | Verde = listo, gris = en proceso, ámbar = pide atención, rojo = problema; vale para las dos barras de cada fila |
| 8h.5 | Ordenar | Pulsa el título **Se volvió venta** y luego otra vez | Ordena de mayor a menor y luego al revés; la flecha lo indica. Quien abrió menos de 10 gestiones queda al final con "pocas gestiones" |
| 8h.6 | Equipo | Elige **Equipo de Lady Ortiz** | Solo su equipo; bajo cada nombre dice su líder real (nunca un usuario de pruebas si hay uno real) |
| 8h.7 | Líder comercial | Entra como `prueba.lidercom` | El filtro de equipo está fijo en el suyo y deshabilitado |
| 8h.8 | Panel del comercial | Pulsa una fila | Panel a pantalla completa: su año en una frase, cuatro cifras y siete pestañas con su conteo: Todas, Por facturar, En ejecución, Quietas, En curso, Perdidas, Revisar datos |
| 8h.9 | Por facturar | En el panel → **Por facturar** | Ventas ejecutadas sin facturar o facturadas en parte, de mayor a menor saldo, con "$X por facturar" |
| 8h.10 | Revisar datos | En el panel → **Revisar datos** | Cada fila explica el motivo: "marcada como facturada pero Helisa no registra nada" o "la base comercial la marca como perdida aunque la gestión dice venta" |
| 8h.11 | Buscar | Escribe un cliente en el buscador del panel | Filtra; los conteos de las pestañas se recalculan |
| 8h.12 | Ir al presupuesto | Pulsa el código de centro de costos de un proyecto | Abre el presupuesto en otra pestaña |
| 8h.13 | Cerrar | ×, Esc o clic fuera | Se cierra sin dejar velo y la tabla sigue usable |
| 8h.14 | Roles sin acceso | Comercial, productor, tesorería visitan `/gestion-kam` | Redirige a su dashboard |

## B8e. Piel visual global y Helisa general

Todas las pantallas del espacio administrativo y comercial comparten ahora la misma capa visual (cabecera con foto de marca, tipografía Manrope, botones y tablas en la paleta Bull, entrada animada de tarjetas y filas). Helisa general fue rediseñada como Base comercial general.

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8e.1 | Coherencia | Como admin recorre Presupuestos, Actualizaciones, Órdenes de compra, Consumidos, Proveedores, Personal, Anticipos, Mi equipo | Todas con la foto de marca arriba, título de sección en la miga de pan, botones naranja Bull (ninguno azul/morado de Argon), tablas con cabecera limpia y filas que aparecen escalonadas |
| 8e.2 | Helisa general | Inicio → Helisa general | Resumen (base facturada, movimientos, terceros, comisión), fichas por tipo de documento (FEVT, NC…), tabla ordenable; las notas crédito se ven en rojo con base negativa |
| 8e.3 | Export coherente | Filtra 2026 + un mes + un tipo → Exportar Excel | El Excel trae solo esos movimientos (antes ignoraba año y mes) |
| 8e.4 | Búsqueda | Escribe un tercero (p. ej. `SPB`) en Buscar | Filtra por centro, nombre del centro, tercero, concepto o n.º de documento |
| 8e.5 | Líder en Helisa | Como `prueba.lidercom` → Helisa general | Solo movimientos y comerciales de su equipo |
| 8e.6 | Formularios | Abre un presupuesto y una orden de compra | Campos con borde suave y foco naranja; nada ilegible ni superpuesto |
| 8e.7 | Modales por encima de todo | Proveedores → Editar; Helisa → Editar; Base comercial → Editar; Contactos → Editar/Eliminar; Prospecto → Eliminar; Presupuesto → Enviar a aprobación; Solicitudes → Ver detalles | La ventana emergente aparece centrada, **encima** del fondo oscuro y se puede usar (antes quedaba debajo del velo por la animación de entrada de las tarjetas y no se podía hacer clic) |
| 8e.8 | Modal abierto durante una acción | Presupuesto → abre Exportar y, con el modal abierto, cambia un campo de recargos (o cualquier acción Livewire) | El modal se cierra o refresca, el velo oscuro desaparece y la página sigue usable (antes quedaba el velo sin modal y había que recargar) |
| 8e.9 | Número de ítem coherente | Comercial: abre un presupuesto y anota los números de la columna ÍTEM; exporta el PDF y el Excel internos; como productor abre una orden natural de ese centro de costos | El número es el mismo en las tres pantallas y corresponde al guardado en la base (`num_item`), no a la posición en la lista. **Ojo:** en presupuestos donde se borraron ítems la numeración tiene huecos (ítem 2, 3, 4…) y no empieza en 1: es lo esperado |
| 8e.10 | Aviso de cambios al validar | Líder comercial valida un presupuesto (estado 4) y luego Gerencia valida uno en estado 5 | En ambos casos el presupuesto queda marcado con cambios pendientes y sale el correo al controller (`actualizacionControllerPresupuesto` en Acciones → Notificaciones) |
| 8e.11 | Crear evento en un presupuesto | Comercial: presupuesto editable → botón **Evento** → escribe una descripción → guardar | El evento se crea y aparece como fila azul a todo lo ancho. Antes fallaba con "Unknown column 'proveedor'": la columna se eliminó al normalizar y el código seguía escribiéndola |
| 8e.12 | Peso de cada acción en el presupuesto | Comercial: abre un presupuesto grande y cambia un recargo o edita un ítem | Responde en menos de un segundo. El desplegable de proveedores conserva el mismo texto ("Proveedor - CATEGORÍA") agrupado por categoría. Antes cada acción lanzaba 1.723 consultas a la base; ahora son 35 |

## B8g. Notificaciones, actividad, pagos y nómina

Local: `MAIL_MAILER=log` y `SMS_MODO=log` → nada sale de verdad; cada envío queda en **Acciones → Notificaciones** como "Enviado" y en `storage/logs/laravel.log` como "[correo simulado]" / "[sms simulado]".

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8g.1 | Registro de envíos | Admin → Acciones → Notificaciones | Tarjetas (fallidas / enviadas 24 h, modo correo y SMS), tabla con evento, destinatarios, estado e intentos; "Ver" muestra el cuerpo |
| 8g.2 | Fallo y alerta | En `.env` pon `MAIL_MAILER=smtp` con un host inexistente, aprueba una OC jurídica | El registro queda "Fallido" con 3 intentos y el error; el dashboard de admin/gerencia muestra el aviso rojo; en el log aparece "Alerta a desarrollo"; "Reintentar" vuelve a intentarlo |
| 8g.3 | Nómina completa | `prueba.productor` crea una orden de nómina → `prueba.lider` la aprueba (estado 9) → `prueba.gerencia` la valida | Queda **Aprobada** con el PDF de la orden y llegan los correos (producción, gerencia, compras/productor/comercial). Antes no podía ni crearse |
| 8g.4 | Pago de anticipo | `prueba.contabilidad` causa un **anticipo jurídico** aprobado → `prueba.tesoreria` sube el comprobante | Sale el correo a tesorería (`anticipoCausado`); tesorería lo ve en "Por pagar", tras el comprobante pasa a "Pagados" (estado Pagado) y sale el correo con el comprobante adjunto. (El anticipo de productor no llega a contabilidad: ver 8g.5) |
| 8g.5 | Anticipo de productor | `prueba.productor` crea un anticipo → `prueba.lider` aprueba → (si ≥ $500.000) `prueba.gerencia` aprueba | Cada paso genera su correo (producción, gerencia, productor). **Corrección 24-sep:** el umbral de $500.000 está fijo en el código; cambiar `CRM_ANTICIPO_UMBRAL_GERENCIA` **no** tiene efecto (pendiente conocido, no lo reportes como hallazgo nuevo). Tras las evidencias el anticipo queda en "Revisión de evidencias" y ninguna pantalla lo avanza (pendiente conocido) |
| 8g.6 | Portal del contratista | Abre el enlace firmado de una orden natural (botón "Copiar enlace" en Órdenes de compra) | Arriba se ve "Estado de tu orden" con los pasos cumplidos y el estado del pago |
| 8g.7 | Registro de actividad | Admin → Acciones → Registro de actividad | Sesiones, páginas, acciones (con "Datos": método y campos enviados) y cambios de datos (campo, antes, después); filtros por usuario, tipo, fechas y texto |
| 8g.8 | Pronóstico | Dashboard de gerencia → gráfica | Línea punteada "Pronóstico" desde el último mes cerrado hasta diciembre y tarjeta "Pronóstico de cierre" con % vs presupuesto y vs año anterior; el "?" explica el cálculo |
| 8g.9 | Presupuesto | Abre un presupuesto | Ver B8f: pantalla clásica con "?" al frente de cada cifra |
| 8g.10 | Perfil visible | Cualquier rol | Tarjeta con foto grande, nombre y cargo en la barra lateral; Gerencia dice "Gerencia" |
| 8g.11 | Iconos | Cualquier rol: breadcrumb (casita), botón ☰, corazón del pie de página, menú de Tesorería | Se ven los iconos (Font Awesome ahora es local en `public/assets/fontawesome`; el kit de fontawesome.com devolvía 403 y por eso salían círculos naranjas vacíos) |
| 8g.12 | Botones de ítems | `prueba.productor` → Orden natural / nómina / jurídica; `prueba.ejecutivo` → nuevo proyecto / Helisa | Los botones dicen "Agregar ítem" / "Guardar cambios" / "Nuevo" con el signo dibujado; ya no hay círculos naranjas sin icono |
| 8g.13 | Inicio de Tesorería | `prueba.tesoreria` → Inicio | Saludo, 4 tarjetas (órdenes por pagar del año y total histórico, valor, pagadas este mes, anticipos por pagar), tabla de las 8 órdenes más recientes con botón "Pagar", barras de 6 meses y anticipos de producción; el menú tiene "Ordenes de compra" y "Anticipos" |
| 8g.14 | Inicio de Contabilidad | `prueba.contabilidad` → Inicio | Igual que 8g.13 pero con "por causar" / "causadas" y botón "Causar"; las barras muestran las órdenes causadas por mes (las órdenes viejas no tienen fecha de causal: se usa su última actualización) |

## B8i. Auditoría 360 (24-sep-2026): permisos y correcciones

Todo esto antes se podía hacer desde la consola del navegador (en Livewire cualquier método o propiedad pública se puede llamar o cambiar). Ahora el servidor aplica las mismas reglas que muestran los botones. Para los casos "desde la consola", abre las herramientas del navegador y usa `Livewire.first().call('metodo')` o `Livewire.first().set('propiedad', valor)`.

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8i.1 | Presupuesto ajeno | Como `prueba.comercial` abre `/presupuesto/6733` (de Alejandra Ortiz) | 403. Como admin o controller sí abre; como `prueba.lidercom`, solo los de su equipo (`/presupuesto/8575` sí, `/presupuesto/6733` no) |
| 8i.2 | Aprobar sin permiso | Como comercial dueño de un presupuesto, desde la consola: `call('validacionGerencia')` o `call('updateCentro')` | 403; el estado no cambia |
| 8i.3 | Flujo < 30 % | Comercial envía → líder aprueba con centro de costos (margen < 30 %) → Gerencia aprueba | Pasa a Validación Gerencia y **llega el correo a Gerencia**; al aprobar Gerencia, el comercial recibe "PRESUPUESTO … APROBADO" y el valor llega a la gestión y a la base comercial |
| 8i.4 | Rechazo de Gerencia | Con `prueba.gerencia` (no es el usuario 8 ni el 10) en un presupuesto en Validación Gerencia | El botón Rechazar está habilitado y funciona |
| 8i.5 | Utilidad con coma | En un ítem escribe utilidad `0,7` | Queda 0.7 (precio = costo ÷ 0,7). Con `70` sale el aviso de que va de 0 a 2 |
| 8i.6 | Recargos en uno aprobado | En un presupuesto aprobado cambia el fee | Vuelve a Editable y hay que enviarlo otra vez (antes cambiaba la venta aprobada sin pasar por nadie) |
| 8i.7 | Listas de validación | Como `prueba.lidercom` en Validaciones, elige "Aprobado" en uno con margen < 30 % | Queda en Validación Gerencia, no aprobado |
| 8i.8 | Cotización ajena | Como `prueba.tesoreria` abre `/cotizacion/8573/x/0` | 403 (antes cualquiera bajaba la versión interna con costos) |
| 8i.9 | Orden jurídica | Como productor, desde la consola en una orden aprobada: `call('enviarAprobacion')` | 403; la orden sigue aprobada. `call('deleteItems', 123)` ya no existe desde el navegador |
| 8i.10 | Nómina | `prueba.productor` abre `/ordenes-nomina-prod/11432` (es una orden natural) | 404 (antes error 500). En una nómina rechazada, el botón Eliminar funciona |
| 8i.11 | PDF de la orden | Admin → Órdenes de compra → Ver PDF de una orden con ítems | Subtotal y total con el valor real (antes 0) |
| 8i.12 | Portal del tercero | Abre el enlace firmado de una orden natural, completa los datos y guarda | Vuelve al portal sin error 403 |
| 8i.13 | Anticipo jurídico: anular | Como `prueba.controller` en un anticipo en revisión → "Anular el anticipo" con motivo | Queda Anulado, la orden de compra queda libre y llega el aviso. El productor puede "Retirar mi solicitud" mientras está con el líder o devuelta |
| 8i.14 | Orden anulada con anticipo | Anula (Compras) una orden jurídica que tiene un anticipo aprobado | El anticipo queda anulado con ella; si ya estaba causado o pagado, no se toca y Contabilidad y Tesorería reciben el aviso |
| 8i.15 | Pago de una orden | `prueba.tesoreria` → Anticipos → registra el pago de una orden causada | Llega el correo "OC: …" con el comprobante (antes no avisaba a nadie). Una orden anulada ya no aparece para pagar |
| 8i.16 | Anticipo de productor | Crea un anticipo, corrígelo tras un rechazo cambiando ítems | El total que ve el líder es la suma de los ítems nuevos. "Borrar firma" ya no borra la firma de los anticipos anteriores |
| 8i.17 | Tablero del líder comercial | Como `prueba.lidercom` en el tablero | Solo su equipo, aunque se cambien los filtros desde la consola |
| 8i.18 | Estado de facturación | `/estado-facturacion?año=2099` como admin | Carga con el año más reciente (antes error 500) |
| 8i.19 | Validación de clientes | Aprueba una solicitud de un cliente cuyo nombre empiece por F | Se aprueba con el código **FA** (después del 9 siguen las letras, siempre 2 caracteres). Antes la pantalla se caía con un volcado de error. Un NIT repetido se avisa |

## B8j. Segunda ronda de la auditoría (25-sep-2026): decisiones aplicadas y pendientes cerrados

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| 8j.1 | Umbral de Gerencia 30 % | Presupuesto con margen menor al 30 % (p. ej. 25 %): el líder lo aprueba con centro de costos | Pasa a Validación Gerencia y llega el correo a Gerencia. Con 30 % exacto o más (p. ej. 33 %) queda aprobado sin Gerencia. El mismo 30 % manda en las listas, en el filtro de Gestión de presupuestos y en la copia del correo a Gerencia (antes decían 35 %) |
| 8j.2 | Gerencia rechaza y libera el código | Presupuesto nuevo en Validación Gerencia → Gerencia lo rechaza | Vuelve a Editable **sin** centro de costos (el código queda libre); si el presupuesto ya había sido aprobado antes, conserva su código |
| 8j.3 | "Otros (meses)" multiplica | En un ítem pon cantidad 2, día 1, otros 3, unitario 10.000 | Total 60.000 (antes 20.000: "otros" se capturaba pero no entraba en la cuenta) |
| 8j.4 | Archivos solo con sesión | Sin iniciar sesión abre `/storage/ordenes_juridicas_helisa/<archivo>.pdf` (copia la dirección de cualquier PDF del CRM) | 403. Con sesión abre. Como `prueba.comercial`, un archivo de `/storage/ruts/…` (documentos de terceros) da 403; como productor o admin abre |
| 8j.5 | Enlace del portal vencido | Cambia la fecha del enlace firmado de un tercero (o usa uno de hace más de 30 días) | Página "Este enlace ya no sirve" en vez del error 403 genérico. "Copiar enlace" en Órdenes de compra genera uno nuevo |
| 8j.6 | Anticipo jurídico repetido | Con un borrador vigente a un proveedor en un proyecto, pide otro anticipo sobre la orden de compra de ese proveedor | Se rechaza: "Ya hay un anticipo sin orden de compra (#N)…". Al corregir el borrador aparece "vincúlala" con esa orden: al vincular, el anticipo se calcula sobre el valor de la orden |
| 8j.7 | Anticipo de productor completo | `prueba.productor` crea anticipo → `prueba.lider` aprueba → (≥ $500.000) `prueba.gerencia` aprueba → productor sube evidencias y envía → **`prueba.lider` revisa las evidencias** (nuevo) → `prueba.contabilidad` causa → `prueba.tesoreria` paga | Cada paso cambia el estado y avisa por correo. Contabilidad y Tesorería tienen pantalla propia para este anticipo (antes error 500). Si Contabilidad lo devuelve, el productor ve el motivo, corrige evidencias y reenvía |
| 8j.8 | Inicio del líder de producción | Entra como `prueba.lider` y elige un productor | Carga en menos de un segundo; los proyectos del productor aparecen al elegirlo (antes cargaba los 44 productores a la vez) |
| 8j.9 | Inicio del productor | Entra como `prueba.productor` → elige un proyecto → pestaña GENERADAS → "ver" en una orden | La orden se abre al pedirla (antes se cargaban todas las órdenes de todos los proyectos al entrar) |
| 8j.10 | Meses sin solape | Admin → Configuración de meses: pon enero de 2026 empezando el 15-dic-2025 | Aviso: enero empieza antes de que termine diciembre del año anterior; no guarda |
| 8j.11 | Tablero de Gerencia, año pasado | Tablero con año 2025 sin mes | Venta acumulada de enero a diciembre contra presupuesto de enero a diciembre (antes 12 meses de venta contra 9 de presupuesto) |
| 8j.12 | Registro Helisa con fecha imposible | Nuevo registro con fecha del año 0026 | Aviso "Revisa el año de la fecha" |
| 8j.13 | Pruebas automáticas | `./crear-bd-testing.sh` y luego `php artisan test` | Corre los escenarios de la auditoría (10 escenarios, más de 320 casos) sobre `tfc_crm_testing`; sin esa base se omiten y **no tocan** la base del .env. El correo y los SMS quedan simulados (solo en el log), pase lo que pase con el .env |
| 8j.14 | Gerencia abre un anticipo de productor | Como `prueba.gerencia`, abre desde Anticipos uno de productor en "Revisión Gerencia" (estado 9) | La ficha abre (antes error 500), muestra la observación del líder y Gerencia puede aprobarlo o rechazarlo |
| 8j.15 | Nómina con proveedor fuera de la lista | Como `prueba.productor`, abre una nómina cuyo proveedor no es de la categoría Nómina | Abre y muestra el nombre del proveedor (antes error 500) |


## B9. Regresión general (nada se rompió)

Con cada usuario de prueba, recorre su menú completo clic por clic. **Ninguna página debe dar 500.** Presta atención especial a: dashboards, listas con paginación, formularios de creación, y los PDF/Excel de descarga.

---

# PARTE C — Cómo reportar

Para cada ❌, reporta en un documento/issue:

1. **Número del caso** (ej. B6.7) o "regresión" + página.
2. **Usuario** con el que probabas y **pasos exactos**.
3. **Qué esperabas** vs **qué pasó** (pantallazo).
4. Si hubo error 500: el bloque correspondiente de `storage/logs/laravel.log` (las últimas ~30 líneas del error).
5. Estado de la BD si aplica (ej: "el anticipo 123 quedó en estado X").

**Notas finales:**
- Los correos NO salen de tu máquina (`MAIL_MAILER=log`): el "envío" se verifica en `storage/logs/laravel.log`.
- Los SMS no salen (`SMS_MODO=log`).
- Si dañas los datos de prueba, reimporta `bullcrm.sql` (paso A3) y repite A4.

---

# PARTE D — Estructura de la base de datos (reestructuración de sep-2026)

La rama `correcciones` trae 5 migraciones que normalizan el esquema. En `bullcrm.sql` ya vienen aplicadas (paso A3); si importas un dump fresco de producción se aplican con `php artisan migrate`, **solo en tu base local**.

| Antes | Ahora |
|---|---|
| `items_presupuesto.proveedor` con un array PHP serializado | tabla `item_presupuesto_proveedor` (una fila por proveedor del ítem); la columna vieja se llama `proveedor_legacy` y es solo auditoría |
| `gestion_comercial.comercial_2..4` y `porcentaje_1..4` | tabla `gestion_participantes` (posición 1 = responsable, porcentaje por comercial) |
| `anticipos.estado_id` apuntando al catálogo de órdenes | catálogo propio `estados_anticipo` (mismos ids + 13 "Rechazo contabilidad") |
| ítems huérfanos, FKs faltantes, contactos con pbx/web cruzados | limpiados, FKs e índices creados |
| `proveedores.estado` texto libre | catálogo `estados_proveedor` + `proveedores.estado_id` (FK); el texto viejo en `estado_legacy` |

**Diccionario de datos:** `docs/DICCIONARIO-DATOS.md` describe cada tabla y columna (se regenera con `php artisan tinker docs/generar-diccionario.php`). Además, cada tabla y columna tiene su COMMENT en la base: en phpMyAdmin, TablePlus o DBeaver aparece la descripción al lado del campo.

**Casos de prueba adicionales:**

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| D.1 | Proveedores de un ítem | Como comercial: presupuesto → agrega un ítem con 2 proveedores → guarda → reabre el ítem | Los 2 proveedores aparecen en la tabla del presupuesto y en el formulario de edición (tabla `item_presupuesto_proveedor`) |
| D.2 | Ítems por tipo de proveedor | Como productor: nueva orden natural / nómina / anticipo | Solo aparecen ítems cuyo proveedor es "Cuenta de cobro" (natural/anticipo) o "Nómina" (nómina) |
| D.3 | Participantes | Como comercial: cotización con 2 participantes al 60/40 → guarda → reabre | Los porcentajes se conservan; baja a 1 participante → guarda → el segundo desaparece (tabla `gestion_participantes`) |
| D.4 | Rechazo contable | Como contabilidad rechaza un anticipo de productor | Queda en estado "Rechazo contabilidad" y el productor lo ve en su lista para corregir y reenviar |
| D.5 | Exports | Cotización PDF/Excel e historial de cambios de un presupuesto | Los proveedores se listan igual que antes |
| D.6 | Estado de proveedor por catálogo | Admin → Proveedores → Nuevo/Editar → Estado | El select solo ofrece CONFIRMADO, CONFIRMADO - COMUNICADO, NO APLICA y SE CANCELO ACUERDO (tabla `estados_proveedor`); el filtro de la lista usa los mismos valores y cada fila muestra su estado. En la BD `proveedores.estado_id` es llave foránea; el texto viejo quedó en `estado_legacy` |
