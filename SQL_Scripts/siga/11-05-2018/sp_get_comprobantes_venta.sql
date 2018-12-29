DELIMITER $$

USE `adif_contable`$$

DROP PROCEDURE IF EXISTS `sp_get_comprobantes_venta`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_comprobantes_venta`(
	IN fechaInicio DATETIME, IN fechaFin DATETIME, IN pagina INT, IN porPagina INT, 
	IN filtrarCobro BOOL, IN conSaldo BOOL, IN referencia VARCHAR(50))
BEGIN
DECLARE p_limit INT;
DECLARE p_offset INT;
DECLARE queryCompleta VARCHAR(9000);
DECLARE strSql VARCHAR(5000);
DECLARE strConSaldo VARCHAR(50);
DECLARE strOrderBy VARCHAR(50);
DECLARE strLimit VARCHAR(50);
SET p_limit = porPagina;
SET p_offset = pagina * p_limit;
SET strConSaldo = '';
IF conSaldo IS TRUE THEN
    SET strConSaldo = " AND c.saldo > 0 ";
END IF;
SET strOrderBy = ' ORDER BY c.fecha_comprobante ASC ';
SET strOrderBy = ' ORDER BY c.id ';
SET strLimit = CONCAT(' LIMIT ', p_offset, ',', p_limit);
SET strSql = "
SELECT 
	c.id,
	c.discriminador,
	DATE_FORMAT(c.fecha_comprobante, '%d/%m/%Y') AS fecha_comprobante,
	DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
	tc.nombre AS tipo_comprobante, 
	c.id_tipo_comprobante,
	lc.letra, 
	c.numero, 
	CONCAT( IFNULL(pv.numero,crlp.str_punto_venta), '-', c.numero ) as numero_completo,
	IFNULL(pv.numero,crlp.str_punto_venta) AS punto_venta,
	c.total,
	c.saldo,
	ifnull(cli.id, clic.id) as id_cliente, 
	ifnull(cp.razon_social, cpc.razon_social) as razon_social,
	IFNULL(cp.cuit, cpc.cuit) as cuit, ";
	
IF filtrarCobro IS TRUE THEN
    SET strSql = CONCAT(strSql, "
	cob.id AS id_cobro,
	cob.discriminador AS discriminador_cobro,
	cob.monto AS cobro_monto, 
	-- para que me aparezcan las referencias, que puede ser de banco o cheque - @gluis - 11/05/2018
	IF(rcb.id IS NOT NULL, 
		IF (rcb.`es_manual` IS FALSE, SUBSTR(rcb.`numero_transaccion`, -6), rcb.`numero_transaccion`),
		rcc.`numero`
	) as referencia,
	" );
END IF;
SET strSql = CONCAT(strSql, "
	cupven.numero_cupon,
	cupven.es_cupon_garantia,
	con.numero_contrato,
	DATE_FORMAT(con.fecha_inicio, '%d/%m/%Y') AS contrato_fecha_inicio,
	DATE_FORMAT(con.fecha_fin, '%d/%m/%Y') AS contrato_fecha_fin,
	DATE_FORMAT(lic.fecha_apertura, '%d/%m/%Y') AS licitacion_fecha_apertura,
	cv.codigo_barras
FROM adif_contable.comprobante c
INNER JOIN adif_contable.comprobante_venta cv ON c.id = cv.id
INNER JOIN adif_contable.estado_comprobante ec ON c.id_estado_comprobante = ec.id
INNER JOIN adif_contable.tipo_comprobante tc ON c.id_tipo_comprobante = tc.id
LEFT JOIN adif_contable.letra_comprobante lc ON c.id_letra_comprobante = lc.id
-- comprobantes rendicion liquido producto
LEFT JOIN adif_contable.comprobante_rendicion_liquido_producto crlp ON c.id = crlp.id
LEFT JOIN adif_contable.punto_venta pv ON cv.id_punto_venta = pv.id
-- cupones
LEFT JOIN adif_contable.cupon_garantia cupgar ON cv.id = cupgar.id
LEFT JOIN adif_contable.cupon_pliego cuppli ON cv.id = cuppli.id
left join adif_contable.licitacion lic on cuppli.id_licitacion = lic.id
LEFT JOIN adif_contable.cupon_venta cupven ON cv.id = cupven.id
LEFT JOIN adif_contable.cupon_venta_general cupvg ON cv.id = cupvg.id
LEFT JOIN adif_contable.cupon_venta_plazo cupvp ON cv.id = cupvp.id
-- clientes sin contrato
LEFT JOIN adif_compras.cliente cli ON cv.id_cliente = cli.id
LEFT JOIN adif_compras.cliente_proveedor cp ON cli.id_cliente_proveedor = cp.id
-- clientes con contrato
LEFT JOIN adif_contable.contrato con ON cv.id_contrato = con.id
LEFT JOIN adif_contable.contrato_venta convta ON con.id = convta.id
LEFT JOIN adif_compras.cliente clic ON convta.id_cliente = clic.id
LEFT JOIN adif_compras.cliente_proveedor cpc ON clic.id_cliente_proveedor = cpc.id " );
IF filtrarCobro IS TRUE THEN
    SET strSql = CONCAT(strSql, "
LEFT JOIN adif_contable.comprobante_venta_cobro cvco ON cvco.id_comprobante_venta = cv.id 
LEFT JOIN adif_contable.cobro cob ON cvco.id_cobro = cob.id 
-- para que me aparezcan las referencias, que puede ser de banco o cheque - @gluis - 11/05/2018
LEFT JOIN adif_contable.`cobro_renglon_cobranza_renglon_cobranza` pivot ON cob.id = pivot.`id_cobro_renglon_cobranza`
LEFT JOIN adif_contable.`renglon_cobranza_banco` rcb ON pivot.`id_renglon_cobranza` = rcb.id
LEFT JOIN adif_contable.`renglon_cobranza_cheque` rcc ON pivot.`id_renglon_cobranza` = rcc.id
" );
END IF;
SET strSql = CONCAT(strSql, "
WHERE 1 = 1 
AND c.fecha_baja IS NULL
AND c.id_estado_comprobante <> 3 -- Anulado
AND (cp.fecha_baja IS NULL OR cpc.fecha_baja IS NULL)" );
SET strSql = CONCAT(strSql, " 
AND c.fecha_comprobante BETWEEN '", fechaInicio, "' AND '", fechaFin, "' " );
SET strSQL = CONCAT(strSql, 
"
AND (
-- no sos cupon
(tc.id <> 7) 
OR 
-- o si sos cupon, que seas cupon garantia o cupon venta a plazo
(tc.id = 7 AND cupven.es_cupon_garantia = 1 AND c.discriminador = 'cupon_venta_plazo')
OR 
-- o si sos cupon, y tiene saldar en true
(tc.id = 7 AND cv.saldar = TRUE)
)
");

IF filtrarCobro IS TRUE AND referencia IS NOT NULL THEN
    SET strSql = CONCAT(strSql, "
AND (rcb.`numero_transaccion` LIKE '%", referencia, "%' OR  rcc.`numero` LIKE '%", referencia, "%')");

END IF;

IF filtrarCobro IS TRUE THEN
 SET strSql = CONCAT(strSql, " GROUP BY c.id " );
END IF;
SET strLimit = '';
SET @QUERY = CONCAT(strSql, strConSaldo, strOrderBy, strLimit);
PREPARE stmt FROM @QUERY;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;