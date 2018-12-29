INSERT INTO `adif_rrhh`.`g_tipo_concepto_ganancia` (`denominacion`, `orden_aplicacion`) 
VALUES ('Retención otros empleos - F572', '5'); 

update adif_rrhh.`g_concepto_ganancia` 
set `id_tipo_concepto_ganancia` = 15,
`orden_aplicacion` = 5
where id = 77
;