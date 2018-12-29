SELECT
	mpo.id, mpo.num, gm.denominacion as grupoMaterial, mpo.denominacion, mpo.medida,
    mpo.peso, mpo.volumen, um.denominacion_corta as unidadMedida, mpo.observacion,
    ei.denominacion as estadoInventario
FROM 
	catalogo_material_producido_obra mpo
INNER JOIN
	grupo_material gm
    ON
		mpo.id_grupo_material = gm.id
LEFT JOIN
	unidad_medida um
    ON
		mpo.id_unidad_medida = um.id
LEFT JOIN
	estado_inventario ei
    ON
		mpo.id_estado_inventario = ei.id
WHERE
	mpo.fecha_baja IS NULL
        
;