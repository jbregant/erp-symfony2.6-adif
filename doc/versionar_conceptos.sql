INSERT INTO `concepto_version` (
	`id_concepto`,
	`id_tipo_concepto`,
	`convenios`,
	`codigo`,
	`descripcion`,
	`leyenda`,
	`activo`,
	`aplica_tope`,
	`integra_sac`,
	`integra_ig`,
	`es_novedad`,
	`imprime_recibo`,
	`imprime_ley`,
	`es_porcentaje`,
	`valor`,
	`formula`,
	`fecha_alta`,
	`fecha_baja`,
	`fecha_version`
)
SELECT 
	c.id, 
	c.id_tipo_concepto, 
	GROUP_CONCAT(cc.id_convenio SEPARATOR ','),
	c.codigo,
	c.descripcion,
	c.leyenda,
	c.activo,
	c.aplica_tope,
	c.integra_sac,
	c.integra_ig,
	c.es_novedad,
	c.imprime_recibo,
	c.imprime_ley,
	c.es_porcentaje,
	c.valor,
	c.formula,
	c.fecha_alta,
	c.fecha_baja,
	NOW()
FROM concepto c
	INNER JOIN concepto_convenio cc ON cc.id_concepto = c.id
GROUP BY c.id

