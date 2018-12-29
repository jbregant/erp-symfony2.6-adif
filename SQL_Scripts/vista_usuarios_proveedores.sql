CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`%` 
    SQL SECURITY DEFINER
VIEW `vista_usuarios_proveedores` AS
    SELECT 
        `us`.`id` AS `idUsuario`,
        `p`.`id` AS `idProveedor`,
        `contp`.`nombre` AS `nombre`,
        `cp`.`cuit` AS `cuit`,
        `cp`.`razon_social` AS `razonSocial`,
        LOWER(`dc`.`descripcion`) AS `email`
    FROM
        (((((`adif_compras`.`proveedor` `p`
        JOIN `adif_compras`.`contacto_proveedor` `contp` ON ((`p`.`id` = `contp`.`id_proveedor`)))
        JOIN `adif_compras`.`cliente_proveedor` `cp` ON ((`p`.`id_cliente_proveedor` = `cp`.`id`)))
        JOIN `adif_compras`.`contacto_proveedor_dato_contacto` `cpdc` ON ((`contp`.`id` = `cpdc`.`id_contacto_proveedor`)))
        JOIN `adif_compras`.`dato_contacto` `dc` ON ((`cpdc`.`id_dato_contacto` = `dc`.`id`)))
        LEFT JOIN `adif_proveedores`.`usuario` `us` ON ((`dc`.`descripcion` = `us`.`email`)))
    WHERE
        ((`dc`.`id_tipo_contacto` = 3)
            AND (`p`.`id_estado_proveedor` = 1)
            AND (`dc`.`descripcion` = `us`.`email`))