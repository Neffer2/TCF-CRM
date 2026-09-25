-- Gestiones que ya están en la base comercial (es decir, ya fueron Venta) pero quedaron en Propuesta (4)
-- porque aprobar un presupuesto las devolvía a Propuesta. Revisar primero:
SELECT g.id, g.nom_proyecto_cot, g.id_estado, g.updated_at, COUNT(b.id) filas_base
FROM gestion_comercial g JOIN base_comerciales b ON b.id_gestion = g.id
WHERE g.id_estado = 4 GROUP BY g.id;

-- Cambio:
UPDATE gestion_comercial g
SET g.id_estado = 5
WHERE g.id_estado = 4 AND EXISTS (SELECT 1 FROM base_comerciales b WHERE b.id_gestion = g.id);
