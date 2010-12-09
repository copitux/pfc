/*
SQLyog Enterprise - MySQL GUI v7.11 
MySQL - 4.1.20 : Database - proyecto
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`proyecto` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `proyecto`;

/*Table structure for table `administrador` */

DROP TABLE IF EXISTS `administrador`;

CREATE TABLE `administrador` (
  `idAdministrador` int(11) NOT NULL auto_increment,
  `login` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `pass` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idAdministrador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `administrador` */

/*Table structure for table `alumnos` */

DROP TABLE IF EXISTS `alumnos`;

CREATE TABLE `alumnos` (
  `idAlumno` int(10) NOT NULL auto_increment,
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  PRIMARY KEY  (`idAlumno`,`usuarios_idUsuario`),
  KEY `Alumnos_FKIndex1` (`usuarios_idUsuario`),
  CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `alumnos` */

insert  into `alumnos`(`idAlumno`,`usuarios_idUsuario`) values (1,27),(31,28),(32,29),(33,30),(34,31),(35,32),(36,33),(37,34),(38,35),(39,36),(40,37),(41,38),(42,39),(43,40),(44,41),(45,42),(46,43),(47,44),(48,45),(49,46),(50,47),(51,48),(52,49),(53,50),(54,51),(55,52),(56,53),(57,54),(58,55),(59,56);

/*Table structure for table `amigos` */

DROP TABLE IF EXISTS `amigos`;

CREATE TABLE `amigos` (
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  `idAmigo` int(10) NOT NULL default '0',
  PRIMARY KEY  (`usuarios_idUsuario`,`idAmigo`),
  CONSTRAINT `amigos_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `amigos` */

/*Table structure for table `asignaturas` */

DROP TABLE IF EXISTS `asignaturas`;

CREATE TABLE `asignaturas` (
  `idAsignatura` int(10) unsigned NOT NULL auto_increment,
  `idCarrera` int(10) unsigned NOT NULL default '0',
  `cursos_idCurso` int(10) unsigned NOT NULL default '0',
  `codAsignatura` int(4) NOT NULL default '0',
  `nombreAsignatura` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `tipo` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `creditos` int(3) unsigned NOT NULL default '0',
  `periodo` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idAsignatura`,`cursos_idCurso`),
  KEY `Asignaturas_FKIndex1` (`cursos_idCurso`),
  KEY `idcarrera` (`idCarrera`),
  CONSTRAINT `asignaturas_ibfk_1` FOREIGN KEY (`cursos_idCurso`) REFERENCES `cursos` (`idCurso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `asignaturas` */

insert  into `asignaturas`(`idAsignatura`,`idCarrera`,`cursos_idCurso`,`codAsignatura`,`nombreAsignatura`,`tipo`,`creditos`,`periodo`) values (1,11,1,1,'ProgramaciÃ³n bÃ¡sica','a',6,'a'),(2,11,1,1,'MetodologÃ­a de la programaciÃ³n','a',6,'a'),(3,11,1,1,'EconomÃ­a','a',6,'a'),(4,11,1,1,'Cisco I','a',6,'a'),(5,11,2,1,'Cisco II','a',6,'a'),(6,11,2,1,'IngenierÃ­a del software','a',6,'a'),(7,11,2,1,'ProgramaciÃ³n web','a',6,'a'),(8,11,2,0,'Bases de datos','a',6,'a'),(9,11,2,1,'Marketing','a',6,'a'),(10,11,3,1,'Oracle','a',6,'a'),(11,11,3,1,'Linux','a',6,'a'),(12,11,3,1,'Windows','a',6,'a'),(13,12,1,1,'Historia del turismo','a',6,'a'),(14,12,1,1,'Turismo en espaÃ±a','a',6,'a'),(15,12,1,1,'Gestiones comerciales','a',6,'a'),(16,12,2,1,'Publicidad','a',6,'a'),(17,12,2,1,'EconomÃ­a','a',6,'a'),(18,12,2,1,'Marketing','a',6,'a'),(19,12,2,1,'Altos vuelos','a',6,'a'),(20,12,3,1,'Tecnicas de marketing e-clicker','a',6,'a'),(21,12,3,1,'Auxiliares de vuelo','a',6,'a'),(22,12,3,1,'Pilotos y cabina','a',6,'a'),(23,13,1,1,'Historia','a',6,'a'),(24,13,1,1,'Lo que era el periodismo','a',6,'a'),(25,13,1,1,'El corazon mata','a',6,'a'),(26,13,2,1,'La radio','a',6,'a'),(27,13,2,1,'La tele','a',6,'a'),(28,13,2,1,'Internet','a',6,'a'),(29,13,2,1,'El walkitalki','a',6,'a'),(30,13,3,1,'Tele basura','a',6,'a'),(31,13,3,1,'Noticias y telediario','a',6,'a'),(32,13,3,1,'Informe semanal','a',6,'a'),(33,13,4,1,'Edicion de prensa','a',6,'a'),(34,13,4,1,'Mass media','a',6,'a'),(35,13,5,1,'Contactos','a',6,'a');

/*Table structure for table `carreras` */

DROP TABLE IF EXISTS `carreras`;

CREATE TABLE `carreras` (
  `idCarrera` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idCarrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `carreras` */

insert  into `carreras`(`idCarrera`,`nombre`) values (11,'Informatica'),(12,'Turismo'),(13,'Periodismo');

/*Table structure for table `cursos` */

DROP TABLE IF EXISTS `cursos`;

CREATE TABLE `cursos` (
  `idCurso` int(10) unsigned NOT NULL default '0',
  `nombreCurso` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idCurso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cursos` */

insert  into `cursos`(`idCurso`,`nombreCurso`) values (1,'Primero'),(2,'Segundo'),(3,'Tercero'),(4,'Cuarto'),(5,'Quinto');

/*Table structure for table `ficheros` */

DROP TABLE IF EXISTS `ficheros`;

CREATE TABLE `ficheros` (
  `idFichero` int(10) NOT NULL auto_increment,
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  `grupos_idGrupo` int(10) NOT NULL default '0',
  `padre` int(10) NOT NULL default '0',
  `ruta` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`idFichero`,`usuarios_idUsuario`,`grupos_idGrupo`),
  KEY `Ficheros_FKIndex1` (`usuarios_idUsuario`),
  KEY `grupos_idGrupo` (`grupos_idGrupo`),
  CONSTRAINT `ficheros_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ficheros_ibfk_2` FOREIGN KEY (`grupos_idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `ficheros` */

/*Table structure for table `grupos` */

DROP TABLE IF EXISTS `grupos`;

CREATE TABLE `grupos` (
  `idGrupo` int(10) NOT NULL auto_increment,
  `perfiles_idPerfil` int(10) NOT NULL default '0',
  `nombreGrupo` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idGrupo`,`perfiles_idPerfil`),
  KEY `perfiles_idPerfil` (`perfiles_idPerfil`),
  CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`perfiles_idPerfil`) REFERENCES `perfiles` (`idPerfil`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `grupos` */

/*Table structure for table `perfiles` */

DROP TABLE IF EXISTS `perfiles`;

CREATE TABLE `perfiles` (
  `idPerfil` int(10) NOT NULL default '0',
  `eliminar` tinyint(1) NOT NULL default '0',
  `renombrar` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idPerfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `perfiles` */

insert  into `perfiles`(`idPerfil`,`eliminar`,`renombrar`) values (1,1,1),(2,1,0),(3,0,1),(4,0,0);

/*Table structure for table `profesores` */

DROP TABLE IF EXISTS `profesores`;

CREATE TABLE `profesores` (
  `idProfesor` int(10) NOT NULL auto_increment,
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  `despacho` int(3) default NULL,
  PRIMARY KEY  (`idProfesor`,`usuarios_idUsuario`),
  KEY `Profesores_FKIndex1` (`usuarios_idUsuario`),
  CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `profesores` */

insert  into `profesores`(`idProfesor`,`usuarios_idUsuario`,`despacho`) values (1,12,NULL),(2,25,NULL),(3,13,NULL),(4,14,NULL),(5,15,NULL),(6,16,NULL),(7,17,NULL),(8,18,NULL),(9,19,NULL),(10,20,NULL),(11,21,NULL),(12,22,NULL),(13,23,NULL),(14,24,NULL),(15,26,NULL);

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `idUsuario` int(10) NOT NULL auto_increment,
  `nombre` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `apellido1` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `apellido2` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `correo` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `login` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `pass` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `usuarios` */

INSERT INTO `usuarios` (`idUsuario`, `nombre`, `apellido1`, `apellido2`, `correo`, `login`, `pass`) VALUES
(12, 'Francisco', 'profesor', 'informatica', 'francisco@uemc.edu', 'pfran689', '7b6581ee30595c57d61332654cf30b0b'),
(13, 'Susana', 'profesor', 'informatica', 'asd', 'susan689', '7b6581ee30595c57d61332654cf30b0b'),
(14, 'Noelia', 'profesor', 'informatica', 'lsdfjk', 'noel689', '7b6581ee30595c57d61332654cf30b0b'),
(15, 'Chema', 'profesor', 'informatica', 'asdsa', 'joche689', '7b6581ee30595c57d61332654cf30b0b'),
(16, 'Gregorio', 'profesor', 'turismo', 'asdas', 'greg689', '7b6581ee30595c57d61332654cf30b0b'),
(17, 'Sanchez', 'profesor', 'turismo', 'sddasasd', 'sanch689', '7b6581ee30595c57d61332654cf30b0b'),
(18, 'Javier', 'profesor', 'turismo', 'asdas', 'javi689', '7b6581ee30595c57d61332654cf30b0b'),
(19, 'Davidprof', 'profesor', 'periodismo', 'sadasd', 'pdavid689', '7b6581ee30595c57d61332654cf30b0b'),
(20, 'Dani', 'profesor', 'periodismo', 'asddads', 'pdani689', '7b6581ee30595c57d61332654cf30b0b'),
(21, 'Fernando', 'profesor', 'periodismo', 'asdads', 'pfer689', '7b6581ee30595c57d61332654cf30b0b'),
(22, 'Laura', 'profesor', 'periodismo', 'asdasd', 'plaura689', '7b6581ee30595c57d61332654cf30b0b'),
(23, 'Cesar', 'profesor', 'periodismo', 'asdasd', 'pcesar689', '7b6581ee30595c57d61332654cf30b0b'),
(24, 'Aitor', 'profesor', 'periodismo', 'asddas', 'paitor689', '7b6581ee30595c57d61332654cf30b0b'),
(25, 'Simal', 'profesor', 'periodismo', 'addas', 'psimal689', '7b6581ee30595c57d61332654cf30b0b'),
(26, 'Concha', 'profesor', 'turismo', 'asdasdads', 'con689', '7b6581ee30595c57d61332654cf30b0b'),
(27, 'David', 'student', 'informatica', 'asdsad', 'dmedina689', '7b6581ee30595c57d61332654cf30b0b'),
(28, 'fer', 'student', 'informatica', 'sadf', 'fer689', '7b6581ee30595c57d61332654cf30b0b'),
(29, 'laura', 'student', 'informatica', 'asdc', 'laura689', '7b6581ee30595c57d61332654cf30b0b'),
(30, 'fran', 'student', 'informatica', 'asd', 'fran689', '7b6581ee30595c57d61332654cf30b0b'),
(31, 'ara', 'student', 'informatica', 'sad', 'ara689', '7b6581ee30595c57d61332654cf30b0b'),
(32, 'tasio', 'student', 'informatica', 'sdaasd', 'tasio689', '7b6581ee30595c57d61332654cf30b0b'),
(33, 'raul', 'student', 'informatica', 'asdasd', 'raul689', '7b6581ee30595c57d61332654cf30b0b'),
(34, 'angel', 'student', 'informatica', 'asdasd', 'angel689', '7b6581ee30595c57d61332654cf30b0b'),
(35, 'maria', 'student', 'turismo', 'adsdas', 'maria689', '7b6581ee30595c57d61332654cf30b0b'),
(36, 'simal', 'student', 'turismo', 'asdsad', 'simal689', '7b6581ee30595c57d61332654cf30b0b'),
(37, 'dani', 'student', 'turismo', 'asdsdadsa', 'dani689', '7b6581ee30595c57d61332654cf30b0b'),
(38, 'aitor', 'student', 'turismo', 'asddas', 'aitor689', '7b6581ee30595c57d61332654cf30b0b'),
(39, 'alvaro', 'student', 'turismo', 'asdasd', 'alvaro689', '7b6581ee30595c57d61332654cf30b0b'),
(40, 'mesi', 'student', 'turismo', 'asdsad', 'mesi689', '7b6581ee30595c57d61332654cf30b0b'),
(41, 'raulgonz', 'student', 'turismo', 'asdasd', 'raulgonz689', '7b6581ee30595c57d61332654cf30b0b'),
(42, 'eva', 'student', 'turismo', 'asddsa', 'eva689', '7b6581ee30595c57d61332654cf30b0b'),
(43, 'krys', 'student', 'periodismo', 'adsasd', 'krys689', '7b6581ee30595c57d61332654cf30b0b'),
(44, 'elena', 'student', 'periodismo', 'asdads', 'elena689', '7b6581ee30595c57d61332654cf30b0b'),
(45, 'tereseta', 'student', 'periodismo', 'asdasdads', 'tereseta689', '7b6581ee30595c57d61332654cf30b0b'),
(46, 'silvia', 'student', 'periodismo', 'asdasd', 'silvia689', '7b6581ee30595c57d61332654cf30b0b'),
(47, 'adamez', 'student', 'periodismo', 'asddsads', 'adamez689', '7b6581ee30595c57d61332654cf30b0b'),
(48, 'patricia', 'student', 'periodismo', 'adasd', 'patricia689', '7b6581ee30595c57d61332654cf30b0b'),
(49, 'nacho', 'student', 'periodismo', 'asdasd', 'nacho689', '7b6581ee30595c57d61332654cf30b0b'),
(50, 'cruji', 'student', 'periodismo', 'asdsad', 'cruji689', '7b6581ee30595c57d61332654cf30b0b'),
(51, 'guayo', 'student', 'periodismo', 'asdsdadsa', 'guayo689', '7b6581ee30595c57d61332654cf30b0b'),
(52, 'goyo', 'student', 'periodismo', 'asdasddas', 'goyo689', '7b6581ee30595c57d61332654cf30b0b'),
(53, 'ivan', 'student', 'periodismo', 'asads', 'ivan689', '7b6581ee30595c57d61332654cf30b0b'),
(54, 'urko', 'student', 'periodismo', 'asddas', 'urko689', '7b6581ee30595c57d61332654cf30b0b'),
(55, 'ariana', 'student', 'periodismo', 'sdasaddas', 'ariana689', '7b6581ee30595c57d61332654cf30b0b'),
(56, 'suco', 'student', 'periodismo', 'asddsaads', 'suco689', '7b6581ee30595c57d61332654cf30b0b');

/*Table structure for table `usuarios_has_asignaturas` */

DROP TABLE IF EXISTS `usuarios_has_asignaturas`;

CREATE TABLE `usuarios_has_asignaturas` (
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  `asignaturas_idAsignatura` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usuarios_idUsuario`,`asignaturas_idAsignatura`),
  KEY `Usuarios_has_Asignaturas_FKIndex1` (`usuarios_idUsuario`),
  KEY `Usuarios_has_Asignaturas_FKIndex2` (`asignaturas_idAsignatura`),
  CONSTRAINT `usuarios_has_asignaturas_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuarios_has_asignaturas_ibfk_3` FOREIGN KEY (`asignaturas_idAsignatura`) REFERENCES `asignaturas` (`idAsignatura`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `usuarios_has_asignaturas` */

insert  into `usuarios_has_asignaturas`(`usuarios_idUsuario`,`asignaturas_idAsignatura`) values (12,1),(12,2),(12,6),(12,11),(13,3),(13,8),(14,7),(14,9),(14,10),(14,35),(15,4),(15,5),(15,12),(16,13),(16,19),(16,30),(17,14),(17,18),(17,26),(18,16),(18,17),(18,20),(18,33),(19,23),(20,24),(21,28),(22,29),(23,31),(24,25),(24,34),(25,27),(26,15),(26,21),(26,22),(26,32),(27,1),(27,5),(27,6),(27,7),(27,8),(27,10),(27,11),(28,3),(28,7),(28,8),(28,10),(28,11),(28,12),(29,1),(29,2),(29,3),(29,6),(29,11),(30,4),(30,5),(30,8),(30,9),(30,11),(31,2),(31,4),(31,7),(31,9),(31,11),(32,1),(32,5),(32,9),(32,12),(33,3),(33,7),(33,9),(33,11),(33,12),(34,1),(34,3),(34,4),(34,6),(34,8),(34,10),(34,11),(35,14),(35,16),(35,20),(35,22),(36,13),(36,14),(36,16),(36,17),(36,22),(37,18),(37,19),(37,21),(37,22),(38,15),(38,16),(38,20),(39,17),(39,18),(39,19),(39,22),(40,13),(40,15),(40,19),(40,21),(41,15),(41,16),(41,22),(42,13),(42,17),(42,18),(42,19),(42,21),(43,23),(43,27),(43,30),(43,32),(43,34),(44,25),(44,29),(44,30),(44,32),(44,34),(45,24),(45,28),(45,32),(45,34),(45,35),(46,24),(47,26),(48,23),(48,26),(48,27),(49,27),(49,28),(49,33),(50,27),(50,29),(51,23),(51,30),(52,28),(52,30),(52,31),(52,33),(53,24),(53,25),(53,29),(53,32),(53,35),(54,29),(54,31),(54,33),(55,25),(55,26),(55,34),(55,35),(56,23),(56,26),(56,27),(56,31),(56,33),(56,35);

/*Table structure for table `usuarios_has_grupos` */

DROP TABLE IF EXISTS `usuarios_has_grupos`;

CREATE TABLE `usuarios_has_grupos` (
  `usuarios_idUsuario` int(10) NOT NULL default '0',
  `grupos_idGrupo` int(10) NOT NULL default '0',
  PRIMARY KEY  (`usuarios_idUsuario`,`grupos_idGrupo`),
  KEY `Usuarios_has_Grupos_FKIndex1` (`usuarios_idUsuario`),
  KEY `Usuarios_has_Grupos_FKIndex2` (`grupos_idGrupo`),
  CONSTRAINT `usuarios_has_grupos_ibfk_1` FOREIGN KEY (`usuarios_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuarios_has_grupos_ibfk_2` FOREIGN KEY (`grupos_idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `usuarios_has_grupos` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=root@localhost PROCEDURE checkLogin(IN l VARCHAR(20), IN p VARCHAR(20))
SELECT * FROM usuarios WHERE login = l AND pass = p$$

DELIMITER ;
