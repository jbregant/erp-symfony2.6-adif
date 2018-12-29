/*
-- Query: SELECT * FROM adif_inventario.estado_inventario
LIMIT 0, 100

-- Date: 2017-10-13 13:47
*/

USE `adif_inventario`;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `estado_inventario`;

INSERT INTO `estado_inventario` (`denominacion`,`id_empresa`,`fecha_creacion`,`fecha_ultima_actualizacion`,`id_usuario_creacion`,`id_usuario_ultima_modificacion`,`fecha_baja`) VALUES ('Borrador',1,'2017-09-28 10:55:47','2017-09-28 10:55:47',140,140,NULL);
INSERT INTO `estado_inventario` (`denominacion`,`id_empresa`,`fecha_creacion`,`fecha_ultima_actualizacion`,`id_usuario_creacion`,`id_usuario_ultima_modificacion`,`fecha_baja`) VALUES ('Activo',1,'2017-10-05 00:00:00','2017-10-05 00:00:00',140,140,NULL);
INSERT INTO `estado_inventario` (`denominacion`,`id_empresa`,`fecha_creacion`,`fecha_ultima_actualizacion`,`id_usuario_creacion`,`id_usuario_ultima_modificacion`,`fecha_baja`) VALUES ('Inactivo',1,'2017-10-05 00:00:00','2017-10-05 00:00:00',140,140,NULL);

SET FOREIGN_KEY_CHECKS = 1;
