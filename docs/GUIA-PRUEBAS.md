# Guía de pruebas — TCF-CRM (rama `correcciones`)
**Para:** desarrollador/tester que valida la reestructuración antes del paso a producción.
**Rama a probar:** `correcciones` · **NUNCA toques la rama `main`** (subir a main despliega a producción automáticamente).

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

## A3. Extraer la base de datos de PRODUCCIÓN (solo lectura)

> Esto solo **lee** producción; nunca escribas nada allá.

1. Entra al panel de **Hostinger** → **Bases de datos** → busca la base **`u630167073_CRM`** → clic en **Entrar a phpMyAdmin**.
2. En phpMyAdmin, selecciona la base en el panel izquierdo.
3. Pestaña **Exportar** → método **Rápido** → formato **SQL** → botón **Exportar**.
4. Guarda el archivo como `tfc_crm_YYYY-MM-DD.sql` (con la fecha del día).
5. Cópialo a la carpeta `db-snapshots/` dentro del proyecto (créala si no existe). **Ese archivo NUNCA se sube a git.**

## A4. Importar el dump a tu MySQL local

Configura primero el `.env` con tu MySQL local:

```
DB_HOST=127.0.0.1
DB_PORT=3306        # el puerto de TU MySQL local
DB_DATABASE=tfc_crm
DB_USERNAME=root
DB_PASSWORD=tu_clave_local
MAIL_MAILER=log     # ¡importante! así ningún correo real sale de tu máquina
```

> ⚠ Verifica dos veces que `DB_HOST` apunta a tu máquina. Si apunta a producción, DETENTE.

Importa. El `sed` limpia dos cosas que MySQL local rechaza: los `DEFINER` del hosting y el `sql_mode` `NO_AUTO_CREATE_USER` de MariaDB (sin esto la importación se corta a mitad con "Variable 'sql_mode' can't be set"):

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS tfc_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sed -E 's/DEFINER=`[^`]+`@`[^`]+`//g; s/NO_AUTO_CREATE_USER,?//g' db-snapshots/tfc_crm_YYYY-MM-DD.sql | mysql -u root -p tfc_crm
```

## A5. Migraciones y usuarios de prueba

**Paso previo obligatorio.** En producción hay 3 migraciones cuyo esquema ya fue aplicado a mano pero nunca quedaron registradas en la tabla `migrations`; si corres `migrate` directo, fallan con "Duplicate column" y abortan todo. Regístralas primero:

```bash
mysql -u root -p tfc_crm -e "INSERT INTO migrations (migration, batch) SELECT m, (SELECT MAX(batch)+1 FROM migrations) FROM (SELECT '2026_02_25_150215_add_column_justificacion_lider_to_presupuesto_proyecto_table' m UNION SELECT '2026_04_10_095206_create_lider_comercial_user_table' UNION SELECT '2026_05_07_180055_add_column_num_item_and_orden_to_items_presupuesto_table') x WHERE m NOT IN (SELECT migration FROM migrations);"
```

Ahora sí:

```bash
php artisan migrate
php artisan migrate:status | grep "| No"     # no debe mostrar nada
php artisan db:seed --class=UsuariosPruebaSeeder
```

`migrate` aplica, además de las nuevas de la reestructuración, **6 migraciones del flujo de anticipos que en producción nunca se corrieron** (la tabla `anticipos` real tiene la estructura vieja) y crea los **estados 8–14** del catálogo que ese flujo necesita. Es normal ver ~10 migraciones aplicándose.

El seeder **solo corre en entorno local** y crea estos usuarios (contraseña de todos: **`Prueba123*`**):

| Correo | Rol | Para probar |
|---|---|---|
| prueba.gerencia@local.test | Admin + **Gerencia** | aprobaciones de margen, validación de nómina y anticipos |
| prueba.admin@local.test | Admin (sin gerencia) | que un admin común **NO** pueda aprobar márgenes |
| prueba.comercial@local.test | Comercial | todo el flujo comercial |
| prueba.ejecutivo@local.test | Ejecutivo de cuenta | módulo asistente |
| prueba.lider@local.test | Líder producción | validación de anticipos e OC |
| prueba.productor@local.test | Productor | órdenes, anticipos, remisiones (ya tiene 3 centros de costo asignados) |
| prueba.tesoreria@local.test | Tesorería | pago de anticipos |
| prueba.contabilidad@local.test | Contabilidad | causación |
| prueba.multirol@local.test | Productor + Comercial | el selector de cambio de rol |
| prueba.controller@local.test | **Controller** | dashboard de revisión, presupuestos, actualizaciones, consumidos, reportes |
| prueba.lidercom@local.test | **Líder comercial** | dashboard de su equipo (prueba.comercial, Alexandra Niño, Juan Camilo Rodríguez), validaciones |

Los `prueba.*` sirven para probar flujos desde cero, pero casi no tienen datos. Para ver cada pantalla **con datos reales**, el seeder también deja con la clave `Prueba123*` a un usuario real de cada rol (solo en la base local; sus correos y datos no cambian):

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

Todos los roles tienen **Mi perfil** (nombre en la barra superior): foto, cargo, datos, roles, permisos y equipo, solo lectura. La sección "Ajustes / Actualizar perfil" se eliminó: los datos y las claves los cambia un desarrollador.

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
| 1.1 | Registro no regala acceso | Cierra sesión → `/register` → crea una cuenta nueva | La cuenta se crea pero al entrar es **expulsada al login** con "cuenta pendiente de activación". No entra a ningún módulo |
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
| 6.8 | Anticipo jurídico coherente | Como productor: anticipo jurídico → elige una OC, pon 50% → cambia a OTRA OC | El total se recalcula con la orden nueva (antes conservaba el total de la anterior) |

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
- Los SMS no salen (no hay `SMS_TOKEN` en tu `.env`).
- Si dañas los datos de prueba, reimporta el dump (paso A4) y vuelve a correr A5.

---

# PARTE D — Estructura de la base de datos (reestructuración de sep-2026)

La rama `correcciones` trae 5 migraciones que normalizan el esquema. Se aplican solas con `php artisan migrate` (paso A5) y **solo en tu base local**.

| Antes | Ahora |
|---|---|
| `items_presupuesto.proveedor` con un array PHP serializado | tabla `item_presupuesto_proveedor` (una fila por proveedor del ítem); la columna vieja se llama `proveedor_legacy` y es solo auditoría |
| `gestion_comercial.comercial_2..4` y `porcentaje_1..4` | tabla `gestion_participantes` (posición 1 = responsable, porcentaje por comercial) |
| `anticipos.estado_id` apuntando al catálogo de órdenes | catálogo propio `estados_anticipo` (mismos ids + 13 "Rechazo contabilidad") |
| ítems huérfanos, FKs faltantes, contactos con pbx/web cruzados | limpiados, FKs e índices creados |

**Diccionario de datos:** `docs/DICCIONARIO-DATOS.md` describe cada tabla y columna (se regenera con `php artisan tinker docs/generar-diccionario.php`). Además, cada tabla y columna tiene su COMMENT en la base: en phpMyAdmin, TablePlus o DBeaver aparece la descripción al lado del campo.

**Casos de prueba adicionales:**

| # | Caso | Pasos | Resultado esperado |
|---|---|---|---|
| D.1 | Proveedores de un ítem | Como comercial: presupuesto → agrega un ítem con 2 proveedores → guarda → reabre el ítem | Los 2 proveedores aparecen en la tabla del presupuesto y en el formulario de edición (tabla `item_presupuesto_proveedor`) |
| D.2 | Ítems por tipo de proveedor | Como productor: nueva orden natural / nómina / anticipo | Solo aparecen ítems cuyo proveedor es "Cuenta de cobro" (natural/anticipo) o "Nómina" (nómina) |
| D.3 | Participantes | Como comercial: cotización con 2 participantes al 60/40 → guarda → reabre | Los porcentajes se conservan; baja a 1 participante → guarda → el segundo desaparece (tabla `gestion_participantes`) |
| D.4 | Rechazo contable | Como contabilidad rechaza un anticipo de productor | Queda en estado "Rechazo contabilidad" y el productor lo ve en su lista para corregir y reenviar |
| D.5 | Exports | Cotización PDF/Excel e historial de cambios de un presupuesto | Los proveedores se listan igual que antes |
