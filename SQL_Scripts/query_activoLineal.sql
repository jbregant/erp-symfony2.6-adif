SELECT 
	al.id, op.denominacion as operador, li.denominacion as linea, di.denominacion as division, 
    crr.denominacion as corredor, rm.denominacion as ramal, cat.denominacion as categoria,
    al.progresiva_inicio_tramo as progresivaInicioTramo , al.progresiva_final_tramo as progresivaFinalTramo,
    tv.denominacion as tipoVia, (al.progresiva_final_tramo - al.progresiva_inicio_tramo) as kilometraje, 
    escon.denominacion_corta as estadoConservacion, ta.denominacion as tipoActivo, e.denominacion as estacion,
    ei.denominacion as estadoInventario, al.zona_via as zonaVia,
    
    (SELECT va.denominacion FROM activo_lineal_atributo_valor alav 
	 JOIN valores_atributo va ON	alav.id_valor_atributo = va.id
	 JOIN atributo at ON	va.id_atributo = at.id
	 WHERE at.denominacion = 'Balasto' AND alav.id_activo_lineal = al.id) as balasto,
    (SELECT va.denominacion FROM activo_lineal_atributo_valor alav 
	 JOIN valores_atributo va ON	alav.id_valor_atributo = va.id
	 JOIN atributo at ON	va.id_atributo = at.id
	 WHERE at.denominacion = 'Rieles' AND alav.id_activo_lineal = al.id) as rieles,
    (SELECT va.denominacion FROM activo_lineal_atributo_valor alav 
	 JOIN valores_atributo va ON	alav.id_valor_atributo = va.id
	 JOIN atributo at ON	va.id_atributo = at.id
	 WHERE at.denominacion = 'Durmientes' AND alav.id_activo_lineal = al.id) as durmientes,
    (SELECT va.denominacion FROM activo_lineal_atributo_valor alav 
	 JOIN valores_atributo va ON	alav.id_valor_atributo = va.id
	 JOIN atributo at ON	va.id_atributo = at.id
	 WHERE at.denominacion = 'Capacidad' AND alav.id_activo_lineal = al.id) as capacidad,     
    (SELECT va.denominacion FROM activo_lineal_atributo_valor alav 
	 JOIN valores_atributo va ON	alav.id_valor_atributo = va.id
	 JOIN atributo at ON	va.id_atributo = at.id
	 WHERE at.denominacion = 'Velocidad' AND alav.id_activo_lineal = al.id) as velocidad
    
FROM 
	activo_lineal al
INNER JOIN
	operador op
	ON
		al.id_operador = op.id
INNER JOIN
	linea li
    ON
		al.id_linea = li.id
INNER JOIN
	division di
    ON
		al.id_division = di.id
INNER JOIN
	corredor crr
    ON
		al.id_corredor = crr.id
LEFT JOIN
	ramal rm
    ON
		al.id_ramal = rm.id
INNER JOIN
	categorizacion cat
    ON
		al.id_categorizacion = cat.id
INNER JOIN
	tipo_via tv
    ON
		al.id_tipo_via = tv.id
LEFT JOIN
	estado_conservacion escon
    ON 
		al.id_estado_conservacion = escon.id
INNER JOIN
	tipo_activo ta
    ON 
		al.id_tipo_activo = ta.id
LEFT JOIN
	estacion e
	ON
		al.id_estacion = e.id
INNER JOIN
	estado_inventario ei
    ON
		al.id_estado_inventario = ei.id
WHERE
	al.fecha_baja IS NULL
ORDER BY 
	operador , linea , division , progresivaInicioTramo
;