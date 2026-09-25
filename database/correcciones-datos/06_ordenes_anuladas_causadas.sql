-- Órdenes anuladas que quedaron causadas (3 sin pagar sumaban $9.730.000 en el panel de Tesorería).
-- El CRM ya no deja causar ni pagar una anulada; estas las revisa Contabilidad (reversar la causación en Helisa
-- y, si corresponde, dejar cod_causal en NULL aquí).
SELECT o.id, o.cod_oc, o.cod_causal, o.fecha_causal, o.archivo_comprobante_pago, o.observaciones_anulacion,
       (SELECT SUM(vtotal_oc) FROM oc_items i WHERE i.oc_id = o.id) valor
FROM ordenes_compra o WHERE o.estado_id = 6 AND o.cod_causal IS NOT NULL;
