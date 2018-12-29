use `siga_autenticacion`;

CREATE TABLE AccessToken (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT NOT NULL, INDEX IDX_B39617F5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE AccessToken ADD CONSTRAINT FK_B39617F5A76ED395 FOREIGN KEY (user_id) REFERENCES usuario (id);
ALTER TABLE AccessToken CHANGE expires_at expires_at VARCHAR(255) NOT NULL;

CREATE TABLE API_UpdateTables (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, hoja_ruta_id INT DEFAULT NULL, download TINYINT(1) DEFAULT 0, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('265', '1');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('265', '2');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('265', '3');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('265', '4');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('140', '5');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('140', '6');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('140', '7');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('266', '8');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('266', '9');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('266', '10');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('266', '11');
INSERT INTO API_UpdateTables (user_id, hoja_ruta_id) VALUES ('266', '12');
