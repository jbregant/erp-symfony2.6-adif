/**
* Se insertan 3 conceptos nuevos de ganancias:
* - Aportes jubilatorios otros empleos
* - Aportes obra social otros empleos
* - Cuota sindical otros empleos 
*
* Ya que cuando se importa los F572 todo lo que tiene que ver a "otros empleadores"
* va a parar al concepto ganancia "Remuneraciones informadas de otro empleador" y no tengo forma de 
* distingir estas 3 deducciones por separado
*/

INSERT INTO `g_concepto_ganancia` (`id_tipo_concepto_ganancia`, `denominacion`, `orden_aplicacion`, `aplica_formulario_572`, `es_carga_familiar`, `codigo_572`, `indica_sac`, `aplica_ganancia_anual`, `f572_sobreescribe`, `tiene_detalle`)
VALUES (13, 'Aportes jubilatorios otros empleos', 1, 1, 0, 'e_7', 0, 0, 1, 1);

INSERT INTO `g_concepto_ganancia` (`id_tipo_concepto_ganancia`, `denominacion`, `orden_aplicacion`, `aplica_formulario_572`, `es_carga_familiar`, `codigo_572`, `indica_sac`, `aplica_ganancia_anual`, `f572_sobreescribe`, `tiene_detalle`)
VALUES (13, 'Aportes obra social otros empleos', 1, 1, 0, 'e_8', 0, 0, 1, 1);

INSERT INTO `g_concepto_ganancia` (`id_tipo_concepto_ganancia`, `denominacion`, `orden_aplicacion`, `aplica_formulario_572`, `es_carga_familiar`, `codigo_572`, `indica_sac`, `aplica_ganancia_anual`, `f572_sobreescribe`, `tiene_detalle`)
VALUES (13, 'Cuota sindical otros empleos', 1, 1, 0, 'e_9', 0, 0, 1, 1);

INSERT INTO `g_concepto_ganancia` (`id_tipo_concepto_ganancia`, `denominacion`, `orden_aplicacion`, `aplica_formulario_572`, `es_carga_familiar`, `codigo_572`, `indica_sac`, `aplica_ganancia_anual`, `f572_sobreescribe`, `tiene_detalle`)
VALUES (13, 'Retenciones otros empleos', 1, 1, 0, 'e_10', 0, 0, 1, 1);
