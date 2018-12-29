CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_materiales_rodantes` AS
    SELECT 
        `mr`.`id` AS `id`,
        `gr`.`denominacion` AS `grupoRodante`,
        `tr`.`denominacion` AS `tipoRodante`,
        `mr`.`numero_vehiculo` AS `numeroVehiculo`,
        `m`.`denominacion` AS `marca`,
        `mo`.`denominacion` AS `modelo`,
        `ec`.`denominacion_corta` AS `estadoConservacion`,
        `es`.`denominacion` AS `estadoServicio`,
        `ct`.`codigo` AS `codigoTrafico`,
        `l`.`denominacion` AS `linea`,
        `o`.`denominacion` AS `operador`,
        `mr`.`ubicacion` AS `ubicacion`,
        `ei`.`denominacion` AS `estadoInventario`
    FROM
        ((((((((((`catalogo_material_rodante` `mr`
        JOIN `grupo_rodante` `gr` ON ((`mr`.`id_grupo_rodante` = `gr`.`id`)))
        LEFT JOIN `tipo_rodante` `tr` ON ((`mr`.`id_tipo_rodante` = `tr`.`id`)))
        LEFT JOIN `marca` `m` ON ((`mr`.`id_marca` = `m`.`id`)))
        LEFT JOIN `modelo` `mo` ON ((`mr`.`id_modelo` = `mo`.`id`)))
        JOIN `estado_conservacion` `ec` ON ((`mr`.`id_estado_conservacion` = `ec`.`id`)))
        LEFT JOIN `servicio` `es` ON ((`mr`.`id_estado_servicio` = `es`.`id`)))
        LEFT JOIN `codigo_trafico` `ct` ON ((`mr`.`id_codigo_trafico` = `ct`.`id`)))
        LEFT JOIN `linea` `l` ON ((`mr`.`id_linea` = `l`.`id`)))
        LEFT JOIN `operador` `o` ON ((`mr`.`id_operador` = `o`.`id`)))
        LEFT JOIN `estado_inventario` `ei` ON ((`mr`.`id_estado_inventario` = `ei`.`id`)))
    WHERE
        ISNULL(`mr`.`fecha_baja`)