CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_estaciones` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`denominacion` AS `denominacion`,
        `e`.`numero` AS `numero`,
        `l`.`denominacion` AS `linea`,
        `r`.`denominacion` AS `ramal`
    FROM
        ((`estacion` `e`
        JOIN `linea` `l` ON ((`e`.`id_linea` = `l`.`id`)))
        LEFT JOIN `ramal` `r` ON ((`e`.`id_ramal` = `r`.`id`)))
    WHERE
        ISNULL(`e`.`fecha_baja`)