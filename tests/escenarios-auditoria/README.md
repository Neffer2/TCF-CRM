# Escenarios de la auditoría 360 (24-sep-2026)

Scripts que recorren los flujos por rol con los usuarios `prueba.*@local.test` del volcado `bullcrm.sql`.
Cada uno trabaja dentro de una transacción que se deshace al final y borra los archivos que creó.
Se detienen solos si no están en la base local `tfc_crm` con `MAIL_MAILER=log`.

```bash
php -d memory_limit=2G artisan tinker tests/escenarios-auditoria/presupuesto_permisos.php < /dev/null
```

También corren todos juntos con `php artisan test` sobre una copia de la base llamada `tfc_crm_testing`
(`./crear-bd-testing.sh` la crea; sin ella los escenarios se marcan como omitidos).

| Script | Qué prueba | Casos |
|---|---|---|
| presupuesto_permisos.php | acceso al presupuesto, edición, aprobaciones y rechazos por rol | 47 |
| listas_presupuesto.php | Validaciones, Actualizaciones y Gestión de presupuestos | 16 |
| orden_juridica_e2e.php | orden de compra jurídica de punta a punta | 21 |
| anticipo_juridico.php | flujo del anticipo jurídico por rol | 62 |
| anticipo_juridico_pantalla.php | la ficha del anticipo jurídico | 44 |
| anticipo_juridico_anular.php | anular, retirar y orden de compra anulada | 17 |
| correcciones_generales.php | nómina, órdenes, proveedores, anticipo de productor, tableros, clientes | 30 |
| correos.php | todas las funciones de correo generan su mensaje | 44 |
| anticipo_productor_flujo.php | anticipo de productor de punta a punta: líder, Gerencia, evidencias, revisión de evidencias, Contabilidad, Tesorería | 31 |
| anticipo_juridico_repetidos.php | la misma compra no se anticipa dos veces; vincular un borrador a su orden | 9 |
| base_datos.php | mejoras de base de datos (25-sep): roles, código puro del centro de costos, meses y ciudades por catálogo, historial de estados, papelera | 36 |

Usan IDs reales de la base de pruebas (gestiones 6733, 8563, 8575; órdenes 11426, 11427, 11433): con otro volcado hay que ajustarlos.
