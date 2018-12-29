DELIMITER $$

USE `adif_compras`$$

DROP PROCEDURE IF EXISTS `sp_reporte_vencimiento_comprobantes`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte_vencimiento_comprobantes`()
BEGIN
SELECT
	IFNULL(cp.cuit, IFNULL(cp.dni,IFNULL(cp.codigo_identificacion,'-'))) AS numeroDocumento, 
	cp.razon_social AS razonSocial,
	cop.tipoProveedor AS tipoProveedor,
	CONCAT(tc.nombre, ' (', l.letra, ')') AS tipoComprobante,
	CONCAT(cop.punto_venta, '-', c.numero) AS numeroComprobante,
	c.`numero_referencia`,
	IF(c.id_tipo_comprobante = 3, c.saldo * -1, c.saldo) AS importe,
	c.fecha_comprobante AS fechaComprobante,
	cop.fecha_ingreso_adif AS fechaIngresoADIF,
	c.fecha_vencimiento AS fechaVencimientoComprobante,
	'-' AS plazoPrevistoPago,
	IF(DATE( c.fecha_vencimiento) < DATE(NOW()), 'Si', 'No') AS estaVencida,
	DATEDIFF(NOW(),c.fecha_vencimiento) AS diasDeVencimiento,
	DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fechaVencimientoComprobanteReal
FROM
	adif_contable.comprobante c
INNER JOIN (
	SELECT c.id, c.id_orden_pago, c.id_proveedor, c.punto_venta, c.fecha_ingreso_adif, IF(oc.id_cotizacion IS NULL, 'Servicio', 'Compra') AS tipoProveedor
	FROM adif_contable.comprobante_compra c
	INNER JOIN orden_compra AS oc ON c.id_orden_compra = oc.id
	UNION
	SELECT c.id, c.id_orden_pago, c.id_proveedor, c.punto_venta, c.fecha_ingreso_adif, 'Obra' AS tipoProveedor
	FROM adif_contable.comprobante_obra c
	INNER JOIN adif_contable.documento_financiero df ON c.id_documento_financiero = df.id
	WHERE df.fecha_baja IS NULL AND df.fecha_anulacion IS NULL
	) cop ON c.id = cop.id
INNER JOIN proveedor p ON cop.id_proveedor = p.id
INNER JOIN cliente_proveedor cp ON cp.id = p.id_cliente_proveedor
INNER JOIN adif_contable.letra_comprobante l ON l.id = c.id_letra_comprobante
INNER JOIN adif_contable.tipo_comprobante tc ON tc.id = c.id_tipo_comprobante
LEFT JOIN (
	SELECT op.id, op.id_estado_orden_pago, eop.denominacion AS denominacion_estado_op
	FROM adif_contable.orden_pago AS op
	INNER JOIN adif_contable.estado_orden_pago AS eop ON eop.id = op.id_estado_orden_pago
	WHERE op.fecha_baja IS NULL
	AND op.discriminador IN ('orden_pago_obra', 'orden_pago_comprobante')
	) op ON op.id = cop.id_orden_pago
WHERE c.id_tipo_comprobante IN (1,2,3,5) -- FC, NC, ND, Ticket factura
AND (op.id IS NULL OR op.id_estado_orden_pago NOT IN (5,4)) -- NOT IN Anulada y Pagada
AND c.fecha_baja IS NULL
AND c.id_estado_comprobante != 3; -- Anulado
   
END$$

DELIMITER ;