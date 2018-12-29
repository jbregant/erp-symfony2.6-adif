DELIMITER $$

USE `adif_compras`$$

DROP PROCEDURE IF EXISTS `sp_vista_ordenesCompra`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_vista_ordenesCompra`(IN `estado` VARCHAR(50),IN `fechainicio` DATETIME,IN `fechafin` DATETIME)
BEGIN
DECLARE filtro INT;
IF (estado = 'pendientes-generacion') THEN
	SET filtro = 0;
END IF;
IF (estado = 'generadas') THEN
	SET filtro = 1;
END IF;
SELECT 
oc.id,
LPAD(oc.numero_orden_compra,8,'0') AS numeroOrdenCompra,
oc.numero_calipso AS numeroCalipso,
oc.fecha_orden_compra AS fechaOrdenCompra,
cp.razon_social AS proveedor,
LPAD(co.id,8,'0') AS cotizacion,
r.id AS idRequerimiento,
r.descripcion AS descripcionRequerimiento,
IF(oc.es_oc_abierta IS TRUE, oc.total_actual, ren.importeReal) AS monto,
IF(oc.saldo_moneda_extranjera IS NULL,
	( IF (oc.es_oc_abierta IS TRUE, oc.total_actual, ROUND(ren.importePesos, 2) ) - IFNULL(saldo.montoRestar,0) ),
	  IF (oc.es_oc_abierta IS TRUE, oc.total_actual, oc.saldo_moneda_extranjera) ) AS saldo,
saldo.montoRestar,
eoc.denominacion AS estadoOrdenCompra,
ti.alias AS aliasTipoImportancia,
tm.simbolo AS simboloTipoMoneda,
CONCAT(u.nombre,',',u.apellido) AS nombreUsuario,
oc.id_orden_compra_original AS idordenCompraOriginal,
IF(co.id IS NOT NULL AND r.id IS NOT NULL AND eoc.id <> 2, TRUE, FALSE) AS muestraReporteDesvio,
IF(co.id IS NULL, TRUE, FALSE) AS esServicio,
GROUP_CONCAT(DISTINCT(ru.denominacion) SEPARATOR ',') AS rubros,
GROUP_CONCAT(DISTINCT(be.denominacion) SEPARATOR ',') AS bienes
FROM orden_compra oc
LEFT JOIN cotizacion co ON co.id = oc.id_cotizacion
LEFT JOIN requerimiento r ON r.id = co.id_requerimiento
LEFT JOIN estado_orden_compra eoc ON eoc.id = oc.id_estado_orden_compra
LEFT JOIN tipo_importancia ti ON ti.id = eoc.id_tipo_importancia
LEFT JOIN adif_contable.tipo_moneda tm ON tm.id = oc.id_tipo_moneda
LEFT JOIN proveedor p ON p.id = oc.id_proveedor
LEFT JOIN cliente_proveedor cp ON cp.id = p.id_cliente_proveedor
LEFT JOIN siga_autenticacion.usuario u ON u.id = oc.id_usuario
LEFT JOIN renglon_orden_compra roc ON roc.id_orden_compra = oc.id
LEFT JOIN bien_economico be ON be.id = roc.id_bien_economico
LEFT JOIN rubro ru ON ru.id = be.id_rubro
LEFT JOIN
(
	SELECT 
	roc.id_orden_compra,
	SUM((((roc.precio_unitario*IFNULL(ai.valor,0)/100)+roc.precio_unitario)*cantidad)* roc.tipo_cambio) AS importePesos,
	SUM(((roc.precio_unitario*IFNULL(ai.valor,0)/100)+roc.precio_unitario)*cantidad) AS importeReal
	FROM renglon_orden_compra roc
	LEFT JOIN adif_contable.alicuota_iva ai ON ai.id = roc.id_alicuota_iva
	GROUP BY roc.id_orden_compra
) ren ON ren.id_orden_compra = oc.id
LEFT JOIN vw_oc_comprobantes_facturados saldo ON saldo.id_orden_compra = oc.id
WHERE 
oc.fecha_baja IS NULL
AND (oc.numero_orden_compra IS NULL AND filtro = 0) OR (oc.id_orden_compra_original IS NOT NULL AND filtro = 1)
AND oc.fecha_orden_compra BETWEEN fechainicio AND fechafin
AND co.id IS NOT NULL
GROUP BY oc.id
;
END$$

DELIMITER ;