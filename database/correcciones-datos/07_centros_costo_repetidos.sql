-- 07 · Centros de costo repetidos (solo lectura: lista para decidir).
-- Con la mejora de base de datos del 25-sep, presupuesto_proyecto.centro_costos guarda el código puro del
-- centro de costos (el que usa Helisa). Diez códigos están asignados a más de un proyecto, todos anteriores a la
-- validación del CRM. Mientras existan no se puede poner la restricción UNIQUE en la base; la regla de la
-- aplicación ya impide que se repitan de aquí en adelante (también contra lo que está en la papelera).
-- Decisión que toca a Contabilidad/Comercial: cuál de los proyectos conserva el código y qué código llevan los demás.
SELECT p.centro_costos AS codigo, COUNT(*) AS proyectos,
       GROUP_CONCAT(CONCAT('gestión ', p.id_gestion, ': ', IFNULL(g.nom_proyecto_cot, ''), ' (estado ', p.estado_id, ', ', IFNULL(DATE(p.fecha_cc), 'sin fecha'), ')') ORDER BY p.id SEPARATOR ' | ') AS detalle
FROM presupuesto_proyecto p
LEFT JOIN gestion_comercial g ON g.id = p.id_gestion
WHERE p.centro_costos IS NOT NULL
GROUP BY p.centro_costos
HAVING COUNT(*) > 1
ORDER BY p.centro_costos;

-- Cuando queden resueltos (la consulta de arriba no devuelve filas), un desarrollador pone la restricción:
-- ALTER TABLE presupuesto_proyecto ADD UNIQUE INDEX presupuesto_proyecto_centro_costos_unique (centro_costos);
