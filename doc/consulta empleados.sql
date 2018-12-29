SELECT  e.nro_legajo, p.apellido, p.nombre, p.cuil, p.fecha_nacimiento, 
	DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(p.fecha_nacimiento)), '%Y')+0 AS edad, 
	ec.nombre AS estado_civil, ultimo_contrato.nombre AS tipo_contrato,
	c.nombre AS categoria, s.nombre AS subcategoria, con.nombre AS convenio,
	primer_tipo_contrato.fecha_desde AS ingreso_planta,
	DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(primer_tipo_contrato.fecha_desde)), '%Y')+0 AS antiguedad,
	t.titulos AS titulo,
	t.nivel AS nivel_educacion,
	DATE_FORMAT(primer_tipo_contrato.fecha_desde,'%d/%m/%Y') AS inicio_primer_contrato,
	DATE_FORMAT(etc.fecha_desde,'%d/%m/%Y') AS inicio_ultimo_contrato,
	DATE_FORMAT(etc.fecha_hasta,'%d/%m/%Y') AS fin_ultimo_contrato,
	'----' AS periodos_contratados,
	g.nombre AS gerencia,
	subg.nombre AS subgerencia,
	a.nombre AS area,
	liq.bruto AS bruto,
	liq.ganancias AS retencion_ganancias,
	liq.neto AS neto
FROM empleado e 
	INNER JOIN persona p ON p.id = e.id_persona
	INNER JOIN estado_civil ec ON ec.id = p.id_estado_civil
	INNER JOIN (SELECT etc_1.id_empleado, id_tipo_contrato, fecha_desde, fecha_hasta
                            FROM empleado_tipo_contrato etc_1
					INNER JOIN (    
							SELECT id_empleado, MAX(fecha_desde) AS fecha
							FROM empleado_tipo_contrato
							GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
	) etc ON e.id = etc.id_empleado
	INNER JOIN tipo_contrato ultimo_contrato ON ultimo_contrato.id = etc.id_tipo_contrato
	INNER JOIN (SELECT etc_1.id_empleado, id_tipo_contrato, etc_1.fecha_desde
				FROM empleado_tipo_contrato etc_1
					INNER JOIN (
							SELECT id_empleado, MIN(fecha_desde) AS fecha
							FROM empleado_tipo_contrato
							GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
				) primer_tipo_contrato ON e.id = primer_tipo_contrato.id_empleado	
	INNER JOIN subcategoria s ON s.id = e.id_subcategoria
	INNER JOIN categoria c ON c.id = s.id_categoria
	INNER JOIN convenio con ON con.id = c.id_convenio
	LEFT JOIN (SELECT e_e.id_empleado, n.nombre AS nivel, GROUP_CONCAT(t.nombre SEPARATOR ' - ') AS titulos
				FROM estudio_empleado e_e
					INNER JOIN (SELECT id_empleado, MAX(id_nivel_estudio) AS id_nivel
											FROM estudio_empleado e
												INNER JOIN titulo_universitario t ON e.id_titulo_universitario = t.id
											GROUP BY id_empleado) e_e1 ON e_e1.id_empleado = e_e.id_empleado AND e_e.id_nivel_estudio = e_e1.id_nivel
					INNER JOIN titulo_universitario t ON t.id = e_e.id_titulo_universitario
					INNER JOIN nivel_estudio n ON n.id = e_e.id_nivel_estudio
				GROUP BY e_e.id_empleado, e_e.id_nivel_estudio) t ON t.id_empleado = e.id
	LEFT JOIN gerencia g ON g.id = e.id_gerencia
	LEFT JOIN subgerencia subg ON subg.id = e.id_subgerencia
	LEFT JOIN area a ON a.id = e.id_area
	LEFT JOIN (SELECT le.bruto_1 + le.bruto_2 AS bruto, le.neto AS neto, le.id_empleado, g.saldo_impuesto_mes AS ganancias
				FROM liquidacion_empleado le
					INNER JOIN (
							SELECT l.id, MAX(fecha_cierre_novedades) AS fecha, id_empleado
							FROM liquidacion l
								INNER JOIN liquidacion_empleado le ON l.id = le.id_liquidacion
							WHERE l.id_tipo_liquidacion = 1
							GROUP BY id_empleado) lle ON lle.id_empleado = le.id_empleado AND lle.id = le.id_liquidacion
					LEFT JOIN g_ganancia_empleado g ON le.id_ganancia_empleado = g.id
		) liq ON e.id = liq.id_empleado
ORDER BY nro_legajo