CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `vista_activos_lineales` AS
    SELECT 
        `al`.`id` AS `id`,
        `op`.`denominacion` AS `operador`,
        `li`.`denominacion` AS `linea`,
        `di`.`denominacion` AS `division`,
        `crr`.`denominacion` AS `corredor`,
        `rm`.`denominacion_corta` AS `ramal`,
        `cat`.`denominacion` AS `categoria`,
        `al`.`progresiva_inicio_tramo` AS `progresivaInicioTramo`,
        `al`.`progresiva_final_tramo` AS `progresivaFinalTramo`,
        `tv`.`denominacion_corta` AS `tipoVia`,
        (`al`.`progresiva_final_tramo` - `al`.`progresiva_inicio_tramo`) AS `kilometraje`,
        `escon`.`denominacion_corta` AS `estadoConservacion`,
        `ta`.`denominacion` AS `tipoActivo`,
        `e`.`denominacion` AS `estacion`,
        `ei`.`denominacion` AS `estadoInventario`,
        `al`.`zona_via` AS `zonaVia`,
        `ts`.`denominacion_corta` AS `tipoServicio`,
        (SELECT 
                `va`.`denominacion`
            FROM
                ((`activo_lineal_atributo_valor` `alav`
                JOIN `valores_atributo` `va` ON ((`alav`.`id_valor_atributo` = `va`.`id`)))
                JOIN `atributo` `at` ON ((`va`.`id_atributo` = `at`.`id`)))
            WHERE
                ((`at`.`denominacion` = 'Balasto')
                    AND (`alav`.`id_activo_lineal` = `al`.`id`))) AS `balasto`,
        (SELECT 
                `va`.`denominacion`
            FROM
                ((`activo_lineal_atributo_valor` `alav`
                JOIN `valores_atributo` `va` ON ((`alav`.`id_valor_atributo` = `va`.`id`)))
                JOIN `atributo` `at` ON ((`va`.`id_atributo` = `at`.`id`)))
            WHERE
                ((`at`.`denominacion` = 'Rieles')
                    AND (`alav`.`id_activo_lineal` = `al`.`id`))) AS `rieles`,
        (SELECT 
                `va`.`denominacion`
            FROM
                ((`activo_lineal_atributo_valor` `alav`
                JOIN `valores_atributo` `va` ON ((`alav`.`id_valor_atributo` = `va`.`id`)))
                JOIN `atributo` `at` ON ((`va`.`id_atributo` = `at`.`id`)))
            WHERE
                ((`at`.`denominacion` = 'Durmientes')
                    AND (`alav`.`id_activo_lineal` = `al`.`id`))) AS `durmientes`,
        (SELECT 
                `va`.`denominacion`
            FROM
                ((`activo_lineal_atributo_valor` `alav`
                JOIN `valores_atributo` `va` ON ((`alav`.`id_valor_atributo` = `va`.`id`)))
                JOIN `atributo` `at` ON ((`va`.`id_atributo` = `at`.`id`)))
            WHERE
                ((`at`.`denominacion` = 'Capacidad')
                    AND (`alav`.`id_activo_lineal` = `al`.`id`))) AS `capacidad`,
        (SELECT 
                `va`.`denominacion`
            FROM
                ((`activo_lineal_atributo_valor` `alav`
                JOIN `valores_atributo` `va` ON ((`alav`.`id_valor_atributo` = `va`.`id`)))
                JOIN `atributo` `at` ON ((`va`.`id_atributo` = `at`.`id`)))
            WHERE
                ((`at`.`denominacion` = 'Velocidad')
                    AND (`alav`.`id_activo_lineal` = `al`.`id`))) AS `velocidad`
    FROM
        ((((((((((((`activo_lineal` `al`
        JOIN `operador` `op` ON ((`al`.`id_operador` = `op`.`id`)))
        JOIN `linea` `li` ON ((`al`.`id_linea` = `li`.`id`)))
        JOIN `division` `di` ON ((`al`.`id_division` = `di`.`id`)))
        JOIN `corredor` `crr` ON ((`al`.`id_corredor` = `crr`.`id`)))
        LEFT JOIN `ramal` `rm` ON ((`al`.`id_ramal` = `rm`.`id`)))
        JOIN `categorizacion` `cat` ON ((`al`.`id_categorizacion` = `cat`.`id`)))
        JOIN `tipo_via` `tv` ON ((`al`.`id_tipo_via` = `tv`.`id`)))
        LEFT JOIN `estado_conservacion` `escon` ON ((`al`.`id_estado_conservacion` = `escon`.`id`)))
        JOIN `tipo_activo` `ta` ON ((`al`.`id_tipo_activo` = `ta`.`id`)))
        LEFT JOIN `estacion` `e` ON ((`al`.`id_estacion` = `e`.`id`)))
        JOIN `estado_inventario` `ei` ON ((`al`.`id_estado_inventario` = `ei`.`id`)))
        JOIN `tipo_servicio` `ts` ON ((`al`.`id_tipo_servicio` = `ts`.`id`)))
    WHERE
        ISNULL(`al`.`fecha_baja`)
    ORDER BY `operador` , `linea` , `division` , `progresivaInicioTramo`