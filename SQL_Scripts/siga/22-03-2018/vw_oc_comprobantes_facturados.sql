DELIMITER $$

USE `adif_compras`$$

DROP VIEW IF EXISTS `vw_oc_comprobantes_facturados`$$

CREATE SQL SECURITY INVOKER VIEW `vw_oc_comprobantes_facturados` AS 

SELECT 
	cc.`id_orden_compra`,
	IFNULL(SUM(IF(c.id_tipo_comprobante = 3, nc.neto * -1, nc.neto)),0) AS montoRestar
FROM `adif_contable`.`comprobante` c
INNER JOIN adif_contable.`comprobante_compra` cc ON c.id = cc.id
INNER JOIN adif_contable.vw_neto_comprobante nc ON c.id = nc.`id`
WHERE 1 = 1 
AND c.`id_estado_comprobante` <> 3
AND cc.`fecha_anulacion` IS NULL
AND c.`fecha_baja` IS NULL
GROUP BY cc.`id_orden_compra`

$$

DELIMITER ;