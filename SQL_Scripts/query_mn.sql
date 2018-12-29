SELECT 
	mn.id, mn.num, gm.denominacion as grupoMaterial, mn.denominacion as descripcion, 
    mn.medida, mn.peso, mn.volumen, um.denominacion_corta as unidadMedida, mn.valor,
    mn.tipo_valor as tipoValor, f.denominacion as fabricante, mn.observacion, 
    ei.denominacion as estadoInventario
    
FROM 
	catalogo_material_nuevo mn
INNER JOIN
	grupo_material gm
    ON
		mn.id_grupo_material = gm.id
LEFT JOIN
	unidad_medida um
    ON
		mn.id_unidad_medida = um.id
LEFT JOIN
	fabricante f
    ON
		mn.id_fabricante = f.id
LEFT JOIN
	estado_inventario ei
    ON
		mn.id_estado_inventario = ei.id
WHERE
	mn.fecha_baja IS NULL
;