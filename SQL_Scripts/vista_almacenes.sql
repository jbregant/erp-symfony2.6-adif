CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_almacenes` AS
    SELECT 
        `a`.`id` AS `id`,
        `a`.`denominacion`,
        `a`.`tipo` AS `tipo`,
        `a`.`numero_deposito` AS `numeroDeposito`,
        `p`.`nombre` AS `provincia`,
        `a`.`latitud` AS `latitud`,
        `a`.`longitud` AS `longitud`,
        `l`.`denominacion` AS `linea`,
        `e`.`denominacion` AS `estacion`,
        `a`.`zona_via` AS `zonaVia`
    FROM
        (((`almacen` `a`
        LEFT JOIN `adif_rrhh`.`provincia` `p` ON ((`a`.`id_provincia` = `p`.`id`)))
        LEFT JOIN `adif_inventario`.`linea` `l` ON ((`a`.`id_linea` = `l`.`id`)))
        LEFT JOIN `adif_inventario`.`estacion` `e` ON ((`a`.`id_estacion` = `e`.`id`)))
    WHERE
        ISNULL(`a`.`fecha_baja`)