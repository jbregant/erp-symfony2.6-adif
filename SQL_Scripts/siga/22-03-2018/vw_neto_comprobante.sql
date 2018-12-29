DELIMITER $$

USE `adif_contable`$$

DROP VIEW IF EXISTS `vw_neto_comprobante`$$

CREATE SQL SECURITY DEFINER VIEW `vw_neto_comprobante` AS 

SELECT 
	c.id, 
	ROUND( SUM( (IFNULL(rc.`cantidad`,0) * IFNULL(rc.`precio_unitario`,0)) + (IFNULL(rc.`cantidad`,0) * IFNULL(rc.`precio_unitario`,0) * IFNULL(iva.`valor`,0) / 100) ), 2) AS neto	
FROM `adif_contable`.`comprobante` c
INNER JOIN `adif_contable`.`comprobante_compra` cc ON c.id = cc.id
INNER JOIN `adif_contable`.`renglon_comprobante` rc ON c.id = rc.`id_comprobante`
LEFT JOIN `adif_contable`.`alicuota_iva` iva ON rc.`id_alicuota_iva` = iva.id
WHERE c.`fecha_baja` IS NULL
AND cc.`fecha_anulacion` IS NULL 
AND c.`id_estado_comprobante` <> 3
GROUP BY c.id

$$

DELIMITER ;