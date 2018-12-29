DELIMITER $$

USE `adif_compras`$$

DROP VIEW IF EXISTS `vw_ordenes_compra`$$

CREATE SQL SECURITY INVOKER VIEW `vw_ordenes_compra` AS 

SELECT 
	oc.id,
	LPAD(oc.numero_orden_compra,8,'0') AS numeroOrdenCompra,
	DATE_FORMAT(oc.`fecha_orden_compra`, '%d/%m/%Y') AS fechaOrdenCompra,
	oc.`numero_carpeta` AS numeroCarpeta,
	oc.`observacion`,
	tc.`simbolo`,
	oc.`id_proveedor`,
	
	IF(oc.saldo_moneda_extranjera IS NULL,
	( IF (oc.es_oc_abierta IS TRUE, oc.total_actual, ROUND(ren.importePesos, 2) )),
		IF (oc.es_oc_abierta IS TRUE, oc.total_actual, oc.saldo_moneda_extranjera) 
	) AS total,
	
	IF(oc.saldo_moneda_extranjera IS NULL,
	( IF (oc.es_oc_abierta IS TRUE, oc.total_actual, ROUND(ren.importePesos, 2) ) - IFNULL(saldo.montoRestar,0) ),
		IF (oc.es_oc_abierta IS TRUE, oc.total_actual, oc.saldo_moneda_extranjera) 
	) AS saldo,
	ren.saldo_cantidades AS saldoCantidades
FROM `orden_compra` oc
LEFT JOIN `adif_contable`.`tipo_moneda` tc ON oc.`id_tipo_moneda` = tc.id
LEFT JOIN
(
	SELECT 
		roc.id_orden_compra,
		SUM(IFNULL(roc.restante,0)) AS saldo_cantidades,
		SUM((((roc.precio_unitario*IFNULL(ai.valor,0)/100)+roc.precio_unitario)*cantidad)* roc.tipo_cambio) AS importePesos,
		SUM(((roc.precio_unitario*IFNULL(ai.valor,0)/100)+roc.precio_unitario)*cantidad) AS importeReal
	FROM renglon_orden_compra roc
	LEFT JOIN adif_contable.alicuota_iva ai ON ai.id = roc.id_alicuota_iva
	GROUP BY roc.id_orden_compra
) ren ON ren.id_orden_compra = oc.id
LEFT JOIN vw_oc_comprobantes_facturados saldo ON saldo.id_orden_compra = oc.id
WHERE oc.`fecha_baja` IS NULL
AND oc.`fecha_anulacion` IS NULL
AND oc.id_orden_compra_original IS NOT NULL
AND oc.`id_estado_orden_compra` = 1

$$

DELIMITER ;