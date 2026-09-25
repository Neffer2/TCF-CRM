# Correcciones de datos (auditoría 360, sep-2026)

Scripts SQL para arreglar datos que ya estaban mal guardados antes de las correcciones del código
(el código ya evita que se repitan). Cada uno empieza con una consulta que muestra lo que va a cambiar:
**revisarla antes de ejecutar el bloque de cambios**. Ninguno se ejecuta solo: los corre un desarrollador,
primero en la base de pruebas y, con el visto bueno de negocio, en producción (con respaldo previo).

| Script | Qué corrige | Necesita visto bueno de |
|---|---|---|
| 01_meses_sin_solape.sql | Enero 2025 y enero 2026 empezaban en diciembre del año anterior | Gerencia (cifras anuales) |
| 02_base_comercial_duplicada.sql | 80 filas repetidas de base comercial (31 pares gestión–comercial) | Comercial |
| 03_gestiones_en_propuesta_con_base.sql | 24 gestiones en Propuesta que ya tienen base comercial → Venta | Comercial |
| 04_utilidad_mal_digitada.sql | Ítems con utilidad 7, 65, 70, 75, 100… (era 0,7, 0,65…) | Comercial y Controller (cambia venta de presupuestos) |
| 05_helisa_fechas_imposibles.sql | 2 registros Helisa con año 0026 y 0204 | Contabilidad (confirmar la fecha real) |
| 06_ordenes_anuladas_causadas.sql | 6 órdenes anuladas con causación (solo listado) | Contabilidad |
