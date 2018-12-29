SELECT
	mr.id, gr.denominacion as grupoRodante, tr.denominacion as tipoRodante, mr.numero_vehiculo as numeroVehiculo,
    m.denominacion as marca, mo.denominacion as modelo, ec.denominacion_corta estadoConservacion,
    es.denominacion as estadoServicio, ct.codigo as codigoTrafico, l.denominacion as linea,
    o.denominacion as operador, mr.ubicacion, ei.denominacion as estadoInventario
FROM
	catalogo_material_rodante mr
INNER JOIN
	grupo_rodante gr
	ON
		mr.id_grupo_rodante = gr.id
LEFT JOIN
	tipo_rodante tr
    ON
		mr.id_tipo_rodante = tr.id
LEFT JOIN
	marca m
    ON
		mr.id_marca = m.id
LEFT JOIN
	modelo mo
    ON
		mr.id_modelo = mo.id
INNER JOIN
	estado_conservacion ec
    ON
		mr.id_estado_conservacion = ec.id
LEFT JOIN
	estado_servicio es
    ON
		mr.id_estado_servicio = es.id
LEFT JOIN
	codigo_trafico ct
    ON
		mr.id_codigo_trafico = ct.id
INNER JOIN
	linea l
    ON
		mr.id_linea = l.id
LEFT JOIN
	operador o
    ON
		mr.id_operador = o.id
LEFT JOIN
	estado_inventario ei
    ON
		mr.id_estado_inventario = ei.id
WHERE
	mr.fecha_baja IS NULL
