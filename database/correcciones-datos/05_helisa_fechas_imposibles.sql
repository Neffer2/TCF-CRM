-- Registros Helisa con fechas de los años 0026 y 0204: quedan fuera de todos los rangos y no suman.
-- Confirmar la fecha real con Contabilidad antes de cambiarla (el número de documento ayuda a ubicarla).
SELECT id, fecha, tipo_doc, num_doc, centro, nom_tercero, base_factura, mes, año, comercial
FROM helisa WHERE fecha < '2015-01-01';

-- Cambio (ejemplo, con las fechas confirmadas):
-- UPDATE helisa SET fecha = '2026-12-06' WHERE id = ... AND fecha = '0026-12-06';
-- UPDATE helisa SET fecha = '2024-02-17' WHERE id = ... AND fecha = '0204-02-17';
