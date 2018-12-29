DELIMITER $$

USE `adif_rrhh`$$

DROP PROCEDURE IF EXISTS `sp_empleados_extendido`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_empleados_extendido`(IN permiso_ver_sueldos INT)
BEGIN
 SELECT  
	e.id,
	e.id AS id2,
	e.nro_legajo,                 
	p.apellido, 
	p.nombre, 
	p.cuil,
	p.telefono,
	p.celular,
	CONCAT( IF(dom.calle IS NULL, '', dom.calle) , ' ', IF(dom.numero IS NULL, '', dom.numero), ' ', IF (dom.piso IS NULL, '', dom.piso), IF(dom.cod_postal IS NULL, '', CONCAT( ' (CP ', dom.cod_postal, ')' )) ) AS domicilio, 
	loc.nombre AS localidad,
	DATE_FORMAT(p.fecha_nacimiento,'%d/%m/%Y') AS fecha_nacimiento, 
	DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(p.fecha_nacimiento)), '%Y')+0 AS edad, 
	ec.nombre AS estado_civil, 
	ultimo_contrato.nombre AS tipo_contrato,
	c.nombre AS categoria, 
	s.nombre AS subcategoria, 
	con.nombre AS convenio,
	pu.denominacion AS puesto, 
	CONCAT(psup.apellido,', ', psup.nombre) AS superior,
	b.nombre AS banco,
	tc.nombre AS tipo_cuenta,
	CONCAT('''' , cu.cbu) AS cbu,
	CONCAT(os.codigo, ' - ', os.nombre) AS obra_social,
	DATE_FORMAT(primer_tipo_contrato_planta.fecha_desde,'%d/%m/%Y') AS ingreso_planta,
	DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(primer_tipo_contrato.fecha_desde)), '%Y')+0 AS antiguedad,
	t.titulos AS titulo,
	t.nivel AS nivel_educacion,
	DATE_FORMAT(primer_tipo_contrato.fecha_desde,'%d/%m/%Y') AS inicio_primer_contrato,
	DATE_FORMAT(etc.fecha_desde,'%d/%m/%Y') AS inicio_ultimo_contrato,
	IFNULL(DATE_FORMAT(etc.fecha_hasta,'%d/%m/%Y'),'') AS fin_ultimo_contrato,
	DATE_FORMAT(e.fecha_inicio_antiguedad,'%d/%m/%Y') AS fecha_inicio_antiguedad, 
	'----' AS periodos_contratados,
	IFNULL(g.nombre,'') AS gerencia,
	IFNULL(subg.nombre,'') AS subgerencia,
	IFNULL(a.nombre,'') AS `area`,
	IF(nivel.descripcion IS NULL, nivel.denominacion, CONCAT(nivel.denominacion, ' - ', nivel.descripcion)  ) AS nivel_organizacional,
	IF(permiso_ver_sueldos = 1, 
		REPLACE(REPLACE(REPLACE(FORMAT(IFNULL(liq.bruto, 0), 2), ".", "@"), ",","."), "@", ","),
		'---') AS bruto,
	IF (permiso_ver_sueldos = 1, 
		REPLACE(REPLACE(REPLACE(FORMAT(IFNULL(liq.ganancias, 0), 2), ".", "@"), ",", "."), "@", ","),
		'---') AS retencion_ganancias,
	IF (permiso_ver_sueldos = 1, 
		REPLACE(REPLACE(REPLACE(FORMAT(IFNULL(liq.neto, 0), 2), ".", "@"), ",", "."), "@", ","),
		'---') AS neto,    
	IF(rr.monto_desde = 0, CONCAT('< ', rr.monto_hasta), IF(rr.monto_hasta = 9999999, CONCAT('> ', rr.monto_desde), CONCAT(rr.monto_desde, ' - ', rr.monto_hasta))) AS rango_remuneracion,
	DATE_FORMAT(e.fecha_egreso, '%d/%m/%Y') AS fecha_egreso,
	me.nombre AS motivo_egreso,
	IF(afiliacion.id = 382, 'Si', 'No') AS afiliacion_uf,
	IF(afiliacion.id = 387, 'Si', 'No') AS afiliacion_apdfa,
	conyuge.tipo_documento AS conyuge_tipo_documento,
	conyuge.nro_documento AS conyuge_nro_documento,
	conyuge.fecha_nacimiento AS conyuge_fecha_nacimiento,
	conyuge.nombre AS conyuge_nombre,
	conyuge.apellido AS conyuge_apellido,
	conyuge.genero AS conyuge_genero,
	arrhh.denominacion AS area_rrhh,
	tmt.denominacion AS tablero_mt,
	p.email
FROM empleado e 
	LEFT JOIN tablero_mt tmt ON e.id_tablero_mt = tmt.id
	LEFT JOIN area_rrhh arrhh ON e.id_area_rrhh = arrhh.id		
	INNER JOIN persona p ON p.id = e.id_persona
	INNER JOIN g_rango_remuneracion rr ON e.id_rango_remuneracion = rr.id
	INNER JOIN estado_civil ec ON ec.id = p.id_estado_civil
	INNER JOIN (SELECT etc_1.id_empleado, id_tipo_contrato, fecha_desde, fecha_hasta
				FROM empleado_tipo_contrato etc_1
					INNER JOIN (    
						SELECT id_empleado, MAX(fecha_desde) AS fecha
						FROM empleado_tipo_contrato
						WHERE fecha_baja IS NULL
						GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
				WHERE fecha_baja IS NULL
	) etc ON e.id = etc.id_empleado
	INNER JOIN tipo_contrato ultimo_contrato ON ultimo_contrato.id = etc.id_tipo_contrato
	INNER JOIN (SELECT etc_1.id_empleado, id_tipo_contrato, etc_1.fecha_desde
				FROM empleado_tipo_contrato etc_1
				INNER JOIN (
					SELECT id_empleado, MIN(fecha_desde) AS fecha
					FROM empleado_tipo_contrato
					WHERE fecha_baja IS NULL 
					GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
				WHERE fecha_baja IS NULL    
		) primer_tipo_contrato ON e.id = primer_tipo_contrato.id_empleado	
	LEFT JOIN (SELECT etc_1.id_empleado, id_tipo_contrato, etc_1.fecha_desde
				FROM empleado_tipo_contrato etc_1
				INNER JOIN (
					SELECT id_empleado, MIN(fecha_desde) AS fecha
					FROM empleado_tipo_contrato
					WHERE fecha_baja IS NULL AND id_tipo_contrato = 3
					GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
				WHERE fecha_baja IS NULL    
		) primer_tipo_contrato_planta ON e.id = primer_tipo_contrato_planta.id_empleado
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
	LEFT JOIN `area` a ON a.id = e.id_area
	LEFT JOIN (SELECT le.bruto_1 + le.bruto_2 AS bruto, le.neto AS neto, le.id_empleado, g.saldo_impuesto_mes AS ganancias
				FROM liquidacion_empleado le
					INNER JOIN liquidacion l ON l.id = le.id_liquidacion
					INNER JOIN (
						SELECT l.id, MAX(fecha_cierre_novedades) AS fecha, id_empleado
						FROM liquidacion l
							INNER JOIN liquidacion_empleado le ON l.id = le.id_liquidacion
						WHERE l.id_tipo_liquidacion = 1
						GROUP BY id_empleado) lle ON lle.id_empleado = le.id_empleado AND l.fecha_cierre_novedades = lle.fecha
					LEFT JOIN g_ganancia_empleado g ON le.id_ganancia_empleado = g.id
			) liq ON e.id = liq.id_empleado
	-- Fix @gluis 20-01-2015
	LEFT JOIN empleado_obra_social eos ON e.id = eos.id_empleado AND (eos.fecha_hasta IS NULL OR eos.fecha_hasta >= NOW())
	LEFT JOIN obra_social os ON eos.id_obra_social = os.id
	-- Fin Fix @gluis 20-01-2015
	-- Fix @gluis 28-01-2015
	LEFT JOIN motivo_egreso me ON e.id_motivo_egreso = me.id
	-- Fin Fix @gluis 28-01-2015
	-- Fix @gluis 13-03-2015
	LEFT JOIN cuenta cu ON e.id_cuenta = cu.id
	LEFT JOIN banco b ON cu.id_banco = b.id
	LEFT JOIN tipo_cuenta tc ON cu.id_tipo_cuenta = tc.id
	-- Fin Fix @gluis 13-03-2015
	-- Fix @gluis 30-04-2015 --
	LEFT JOIN domicilio dom ON p.id_domicilio = dom.id
	LEFT JOIN localidad loc ON dom.id_localidad = loc.id
	-- Fin Fix @gluis 30-04-2015 --
	LEFT JOIN puesto pu ON e.id_puesto = pu.id
	LEFT JOIN empleado esup ON e.id_superior = esup.id
	LEFT JOIN persona psup ON esup.id_persona = psup.id
	LEFT JOIN (
		SELECT 
			c.id, 
			c.descripcion,
			ec.empleado_id
		FROM empleado_concepto ec
		INNER JOIN concepto c ON ec.concepto_id = c.id
		WHERE c.id IN (382,387) -- Cuota Sindical UF y APDFA Cuota Sindical
	) afiliacion ON e.id = afiliacion.empleado_id
	LEFT JOIN nivel_organizacional nivel ON e.id_nivel_organizacional = nivel.id
	LEFT JOIN (
		SELECT 
			f.`id_empleado`, 
			td.`nombre` AS tipo_documento,
			p.`nro_documento`,
			p.`fecha_nacimiento`,
			p.`nombre`, 
			p.`apellido`, 
			p.`sexo` AS genero
		FROM familiar f 
		INNER JOIN `persona` p ON f.`id_persona` = p.id
		INNER JOIN `tipo_documento` td ON p.`id_tipo_documento` = td.id
		WHERE f.`id_tipo_relacion` IN (1,3) -- Conyuge y concubino/a
	) conyuge ON e.id = conyuge.id_empleado
WHERE e.activo = 1
GROUP BY e.nro_legajo
ORDER BY e.nro_legajo
;
END$$

DELIMITER ;