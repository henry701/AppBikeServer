/*
CREATE SCHEMA IF NOT EXISTS `app_mobilidade`;
USE `app_mobilidade`;
*/

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */

SET `FOREIGN_KEY_CHECKS` = 0;

	DROP TABLE IF EXISTS
		`appb_usuarios`,
		`appb_pareamentos`,
		`appb_push_regs`
	;

SET `FOREIGN_KEY_CHECKS` = 1;

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */


CREATE TABLE `appb_usuarios`
( 	
	`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,

	`email` VARCHAR(140) UNIQUE NOT NULL, 
	`senha` CHAR(60) CHARSET `ASCII` NOT NULL, -- Crypto string functions.php

	`nome` VARCHAR(190) NOT NULL,
    
    `latitude` DECIMAL(32, 30) NULL DEFAULT NULL,
    `longitude` DECIMAL(33, 30) NULL DEFAULT NULL,
    `data_localizacao` DATETIME NULL DEFAULT NULL, 

	INDEX(`id`), INDEX(`email`)
)
CHARSET=`UTF8` ENGINE=`INNODB`;



CREATE TABLE `appb_pareamentos`
(
	`id_rastreado` BIGINT UNSIGNED NOT NULL,
	`id_rastreador` BIGINT UNSIGNED NOT NULL,
	`habilitado` BOOL NOT NULL DEFAULT TRUE,
	`aceito` BOOL NOT NULL DEFAULT FALSE,

	PRIMARY KEY(`id_rastreado`,`id_rastreador`), 
	INDEX(`id_rastreado`), INDEX(`id_rastreador`), INDEX(`id_rastreado`, `id_rastreador`), INDEX(`id_rastreado`, `habilitado`), INDEX(`id_rastreador`, `habilitado`), 
	FOREIGN KEY (`id_rastreado`) REFERENCES `appb_usuarios`(`id`),	FOREIGN KEY (`id_rastreador`) REFERENCES `appb_usuarios`(`id`)
)
CHARSET=`UTF8` ENGINE=`INNODB`;



CREATE TABLE `appb_push_regs`
(
	`id_usuario` BIGINT UNSIGNED NOT NULL,
	`regId` VARCHAR(200) NOT NULL,
	PRIMARY KEY (`id_usuario`, `regId`),
	INDEX(`regId`), 
	FOREIGN KEY (`id_usuario`) REFERENCES `appb_usuarios`(`id`)
)
CHARSET=`UTF8` ENGINE=`INNODB`;


/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */


INSERT INTO `appb_usuarios`
(
	`email`,
	`senha`,
	`nome`
)
VALUES
(
	'henry_tuori@hotmail.com', 
	'$2y$10$rrvh.cj2fc0kqbu1d6dlreXou/jFN9OWgvAqi5pm6WsnViPkcBXWC', -- apps
	'Henrique Borsatto de Campos'
),
(
	'zampa', 
	'$2y$10$rrvh.cj2fc0kqbu1d6dlreXou/jFN9OWgvAqi5pm6WsnViPkcBXWC', -- apps
	'Victor Zampieri Marinho'
),
(
	'teste', 
	'$2y$10$rrvh.cj2fc0kqbu1d6dlreXou/jFN9OWgvAqi5pm6WsnViPkcBXWC', -- apps
	'teste'
)
;




INSERT INTO `appb_pareamentos`
(`id_rastreado`, `id_rastreador`,`habilitado`,`aceito`)
VALUES
(1, 2, true, true)
,(2, 1, false, true)
,(2, 3, false, true)
,(3, 2, false, false)
,(3, 1, false, true)
;


SELECT
	tbl.id_cara as id,
    tbl.habilitado as habilitado,
    tbl.aceito as aceito,
    tbl.rastreado as rastreado,
    tbl.email as email
FROM
(
	(
		SELECT
			IF(tb_p.id_rastreado=1, tb_p.id_rastreador, tb_p.id_rastreado) as id_cara, 
			tb_p.habilitado as habilitado, 
			tb_p.aceito as aceito, 
			(tb_p.id_rastreado=1) as rastreado, 
			(SELECT tb_u.email FROM appb_usuarios as tb_u WHERE tb_u.id = id_cara LIMIT 1) as email
		FROM
			appb_pareamentos as tb_p
		WHERE
			id_rastreado = 1 OR id_rastreador = 1
	) tbl
)
;

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */



-- Selects exemplos vem aqui depois
