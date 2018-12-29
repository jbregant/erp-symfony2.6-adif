CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_materiales_producidos_obra` AS
    SELECT 
        `mpo`.`id` AS `id`,
        `mpo`.`num` AS `num`,
        `gm`.`denominacion` AS `grupoMaterial`,
        `mpo`.`denominacion` AS `denominacion`,
        `mpo`.`medida` AS `medida`,
        `mpo`.`peso` AS `peso`,
        `mpo`.`volumen` AS `volumen`,
        `um`.`denominacion_corta` AS `unidadMedida`,
        `mpo`.`observacion` AS `observacion`,
        `ei`.`denominacion` AS `estadoInventario`
    FROM
        (((`catalogo_material_producido_obra` `mpo`
        JOIN `grupo_material` `gm` ON ((`mpo`.`id_grupo_material` = `gm`.`id`)))
        LEFT JOIN `unidad_medida` `um` ON ((`mpo`.`id_unidad_medida` = `um`.`id`)))
        LEFT JOIN `estado_inventario` `ei` ON ((`mpo`.`id_estado_inventario` = `ei`.`id`)))
    WHERE
        ISNULL(`mpo`.`fecha_baja`)