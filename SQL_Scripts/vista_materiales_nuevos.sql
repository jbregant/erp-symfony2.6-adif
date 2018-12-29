CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_materiales_nuevos` AS
    SELECT 
        `mn`.`id` AS `id`,
        `mn`.`num` AS `num`,
        `gm`.`denominacion` AS `grupoMaterial`,
        `mn`.`denominacion` AS `descripcion`,
        `mn`.`medida` AS `medida`,
        `mn`.`peso` AS `peso`,
        `mn`.`volumen` AS `volumen`,
        `um`.`denominacion_corta` AS `unidadMedida`,
        `mn`.`valor` AS `valor`,
        `mn`.`tipo_valor` AS `tipoValor`,
        `f`.`denominacion` AS `fabricante`,
        `mn`.`observacion` AS `observacion`,
        `ei`.`denominacion` AS `estadoInventario`
    FROM
        ((((`catalogo_material_nuevo` `mn`
        JOIN `grupo_material` `gm` ON ((`mn`.`id_grupo_material` = `gm`.`id`)))
        LEFT JOIN `unidad_medida` `um` ON ((`mn`.`id_unidad_medida` = `um`.`id`)))
        LEFT JOIN `fabricante` `f` ON ((`mn`.`id_fabricante` = `f`.`id`)))
        LEFT JOIN `estado_inventario` `ei` ON ((`mn`.`id_estado_inventario` = `ei`.`id`)))
    WHERE
        ISNULL(`mn`.`fecha_baja`)