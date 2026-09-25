-- Enero empezaba en diciembre del año anterior (2025: 1-dic-2024; 2026: 15-dic-2025): esos días se contaban
-- en los dos años. Enero empieza el 1 de enero. Revisar primero:
SELECT a.description anio, m.description mes, m.f_inicio, m.f_fin
FROM meses m JOIN anos a ON a.id = m.ano_id
WHERE m.identifier = '1' AND m.f_inicio < CONCAT(a.description, '-01-01');

-- Cambio:
UPDATE meses m JOIN anos a ON a.id = m.ano_id
SET m.f_inicio = CONCAT(a.description, '-01-01')
WHERE m.identifier = '1' AND m.f_inicio < CONCAT(a.description, '-01-01');
