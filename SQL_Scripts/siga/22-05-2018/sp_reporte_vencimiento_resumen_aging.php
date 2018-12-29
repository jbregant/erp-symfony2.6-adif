DELIMITER $$

USE `adif_compras`$$

DROP PROCEDURE IF EXISTS `sp_reporte_vencimiento_resumen_aging`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte_vencimiento_resumen_aging`()
BEGIN
	SELECT
		cons.Menor60,
		cons.Menor45,
		cons.Menor35,
		cons.Menor25,
		cons.Menor15,
		cons.Menor10,
		cons.Menor0,
		cons.Mayor0,
		cons.Mayor5,
		cons.Mayor10,
		cons.Mayor15,
		cons.Mayor25,
		cons.Mayor35,
		cons.Mayor45,
		cons.Mayor60, 
		cons.SinVto,
		(cons.Menor60+cons.Menor45+cons.Menor35+cons.Menor25+cons.Menor15+cons.Menor10+cons.Menor0+cons.Mayor0+cons.Mayor5+
		cons.Mayor10+cons.Mayor15+cons.Mayor25+cons.Mayor35+cons.Mayor45+cons.Mayor60+cons.SinVto) AS suma_total
		
	FROM (
	SELECT 
		SUM(IF(com.diasDeVencimiento < -60, com.total, 0)) AS 'Menor60',
		SUM(IF(com.diasDeVencimiento < -45 AND com.diasDeVencimiento >= -60 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor45',
		SUM(IF(com.diasDeVencimiento < -35 AND com.diasDeVencimiento >= -45 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor35',
		SUM(IF(com.diasDeVencimiento < -25 AND com.diasDeVencimiento >= -35 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor25',
		SUM(IF(com.diasDeVencimiento < -15 AND com.diasDeVencimiento >= -25 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor15',
		SUM(IF(com.diasDeVencimiento < -10 AND com.diasDeVencimiento >= -15 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor10',
		SUM(IF(com.diasDeVencimiento < 0   AND com.diasDeVencimiento >= -10 AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Menor0',
		SUM(IF(com.diasDeVencimiento >= 0  AND com.diasDeVencimiento <= 5   AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor0',
		SUM(IF(com.diasDeVencimiento > 5   AND com.diasDeVencimiento <= 10  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor5',
		SUM(IF(com.diasDeVencimiento > 10  AND com.diasDeVencimiento <= 15  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor10',
		SUM(IF(com.diasDeVencimiento > 15  AND com.diasDeVencimiento <= 25  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor15',
		SUM(IF(com.diasDeVencimiento > 25  AND com.diasDeVencimiento <= 35  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor25',
		SUM(IF(com.diasDeVencimiento > 35  AND com.diasDeVencimiento <= 45  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor35',
		SUM(IF(com.diasDeVencimiento > 45  AND com.diasDeVencimiento <= 60  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor45',
		SUM(IF(com.diasDeVencimiento > 60  AND com.diasDeVencimiento != 'Sin plazo de vencimiento', com.total, 0)) AS 'Mayor60',
		SUM(IF(com.diasDeVencimiento = 'Sin plazo de vencimiento', com.total, 0)) AS 'SinVto'
		
	FROM 
		(
		SELECT
			SUM(IF(c.id_tipo_comprobante = 3, c.saldo * - 1, c.saldo)) AS total,
			IFNULL(DATEDIFF(NOW(), c.fecha_vencimiento) * -1,
				'Sin plazo de vencimiento'			
			) AS diasDeVencimiento
		FROM
			adif_contable.comprobante c
		INNER JOIN (
			SELECT
				c.id, c.id_orden_pago, c.fecha_ingreso_adif
			FROM
				
				adif_contable.comprobante_compra c
			UNION
				SELECT
					c.id, c.id_orden_pago, df.fecha_ingreso_adif
				FROM
					adif_contable.comprobante_obra c
				INNER JOIN 
					adif_contable.documento_financiero df ON c.id_documento_financiero = df.id
				WHERE
					df.fecha_baja IS NULL
				AND df.fecha_anulacion IS NULL
		) cop ON c.id = cop.id
		LEFT JOIN (
			SELECT
				op.id, op.id_estado_orden_pago, eop.denominacion AS denominacion_estado_op
			FROM
				adif_contable.orden_pago AS op
			INNER JOIN adif_contable.estado_orden_pago AS eop ON eop.id = op.id_estado_orden_pago
			WHERE
				op.fecha_baja IS NULL
			AND op.discriminador IN ('orden_pago_obra', 'orden_pago_comprobante')
		) op ON op.id = cop.id_orden_pago
	WHERE
		c.id_tipo_comprobante IN (1,2,3,5) -- FC, NC, ND, Ticket factura
		AND (op.id IS NULL OR op.id_estado_orden_pago NOT IN (5,4)) -- NOT IN Anulada y Pagada
		AND c.fecha_baja IS NULL
		AND c.id_estado_comprobante != 3
		GROUP BY diasDeVencimiento) com
	) cons;
END$$

DELIMITER ;