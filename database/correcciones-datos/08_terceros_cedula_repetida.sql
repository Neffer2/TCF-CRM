-- 08 · Personas (terceros) con la misma cédula (solo lectura: lista para decidir).
-- terceros es el maestro de personas que trabajan en las órdenes (una fila por persona, reutilizada en las
-- evidencias de varias órdenes). El CRM valida que la cédula no se repita, pero seis cédulas quedaron repetidas
-- antes de esa validación, algunas con nombres distintos. Hay que decidir cuál fila se conserva y pasar a ella
-- las evidencias de la otra (evidencias.tercero_id) antes de poner UNIQUE en terceros.cedula.
SELECT t.cedula, COUNT(*) AS filas,
       GROUP_CONCAT(CONCAT('#', t.id, ' ', IFNULL(t.nombre, ''), ' ', IFNULL(t.apellido, ''), ' (', IFNULL(t.telefono, 'sin tel.'), ', evidencias: ',
                           (SELECT COUNT(*) FROM evidencias e WHERE e.tercero_id = t.id), ')') ORDER BY t.id SEPARATOR ' | ') AS detalle
FROM terceros t
WHERE t.cedula IS NOT NULL AND t.cedula <> ''
GROUP BY t.cedula
HAVING COUNT(*) > 1
ORDER BY t.cedula;

-- Ejemplo de unificación (reemplazar X por la fila que se conserva y Y por la que sobra):
-- UPDATE evidencias SET tercero_id = X WHERE tercero_id = Y;
-- UPDATE terceros SET deleted_at = NOW() WHERE id = Y;   -- a la papelera, no se borra
-- Al final, sin repetidos: ALTER TABLE terceros ADD UNIQUE INDEX terceros_cedula_unique (cedula);
