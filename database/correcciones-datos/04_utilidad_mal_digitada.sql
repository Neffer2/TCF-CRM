-- La utilidad de un ítem es costo ÷ precio (0,7 = 30 % de margen). Al escribir "0,7" el CRM guardaba 7;
-- "0,65" → 65; "1,00" → 100. Quedaron ítems con precio al cliente 10 o 100 veces menor.
-- La corrección divide por 10^(dígitos) los valores enteros mayores a 2 y recalcula precio, venta y rentabilidad.
-- OJO: cambia la venta de presupuestos ya aprobados (con centro de costos): revisar con Comercial y Controller.
SELECT i.id, i.presupuesto_id, p.cod_cc, i.descripcion, i.margen_utilidad,
       i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED))) utilidad_corregida,
       i.v_total costo, i.v_total_cot precio_actual,
       i.v_total / (i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED)))) precio_corregido
FROM items_presupuesto i JOIN presupuesto_proyecto p ON p.id = i.presupuesto_id
WHERE i.evento = 0 AND i.margen_utilidad > 2 AND i.margen_utilidad = FLOOR(i.margen_utilidad)
ORDER BY p.cod_cc IS NOT NULL, i.id;

-- Cambio (solo los enteros mayores a 2; los decimales raros, como 85714.28, se revisan a mano):
UPDATE items_presupuesto i
SET i.margen_utilidad = i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED))),
    i.v_unitario_cot = i.v_unitario / (i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED)))),
    i.v_total_cot = i.cantidad * i.dia * GREATEST(i.otros, 1) * (i.v_unitario / (i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED))))),
    i.rentabilidad = (i.cantidad * i.dia * GREATEST(i.otros, 1) * (i.v_unitario / (i.margen_utilidad / POW(10, LENGTH(CAST(i.margen_utilidad AS UNSIGNED)))))) - i.v_total
WHERE i.evento = 0 AND i.margen_utilidad > 2 AND i.margen_utilidad = FLOOR(i.margen_utilidad);

-- Después: abrir cada presupuesto afectado (o guardar un cambio) para que recalcule venta y márgenes del proyecto.
