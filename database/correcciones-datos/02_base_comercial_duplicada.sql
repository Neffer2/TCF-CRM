-- Filas repetidas de base comercial para la misma gestión y el mismo comercial (reenvíos del paso a Venta).
-- Se conserva la fila más reciente de cada par (mayor id). Revisar primero:
SELECT id_gestion, id_user, COUNT(*) filas, GROUP_CONCAT(id ORDER BY id) ids, GROUP_CONCAT(valor_proyecto ORDER BY id) valores, GROUP_CONCAT(id_estado ORDER BY id) estados
FROM base_comerciales
GROUP BY id_gestion, id_user HAVING COUNT(*) > 1;

-- Cambio (borra las copias anteriores; la más reciente se queda):
DELETE b FROM base_comerciales b
JOIN (SELECT id_gestion, id_user, MAX(id) conservar FROM base_comerciales GROUP BY id_gestion, id_user HAVING COUNT(*) > 1) d
  ON d.id_gestion = b.id_gestion AND d.id_user = b.id_user AND b.id < d.conservar;
