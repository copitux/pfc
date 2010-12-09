<?php

/*	init.php
*
*	Checkeos, variables basicas y cositas necesarias para el MVC  
*	Explicadas.
*
*/

//Func para desarrollo - un var_dump pero formateado para ver bien los datos.
function var_dumpa($v) { echo '<pre>'; var_dump($v); echo '</pre>'; }
// E_ALL: Development, E_ERROR: Running
error_reporting(E_ALL);
if (version_compare(PHP_VERSION,'5.2') == -1) exit('You need PHP 5.2 or high (you\'ve '. PHP_VERSION.')');

// Constantes
define ("DIRSEP",DIRECTORY_SEPARATOR);
define ("SITE_PATH",realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP) . DIRSEP); // Rute + DIRSEP

// Carga la clase Config, podria haber creado las variables directamente aqui pero digamos que
// no capte la esencia de la clase Config hasta demasiado tarde. Se explica en la clase alguna otra cosa
require (SITE_PATH . 'config.php');
//Carga las librerias de smarty (bueno... librerias... sus clases y demas mierdas)
require (SITE_PATH . Config::$smarty_libs . DIRSEP . 'Smarty.class.php');

/*
 *	Auto charge of class when need instance
 *
 *	$dirs - Directorios con las clases a instanciar en caso de necesitarlas en cualquier parte
 *			del código.
 *		
 *		Dir 'classes'	- $class_name = $class_name.class.php
 *		Dir 'model'		- Ej: $class_name = Model_alumno / $file = alumno.php
 *
 */
// Esta funcion se podria mejorar, pero vamos a dejarla asi por que me temo que a lo que se toque...
// Sencillamente carga las clases cuando se las instancia en cualquier parte del codigo.
// Algo que se puede resumir haciendo todos los requires() a las clases.
function __autoload($class)
{
	$dirs = array(Config::$classes_path,Config::$model_path);
	
	//Planifica mal, formateando los nombres de los archivos de las clases y tendras chapuzas como estas
	// Solventadas con elegancia si, pero chapuza de ingenieria. Esta explicado arriba en ingles
	// Las clases del controlador se cargan directamente con require() en la clase router.
	foreach ($dirs as $value)
	{
		$class_format = ($value == Config::$model_path)?
			strtolower(array_pop(explode('_',$class))) . '.php'
			:strtolower($class) .'.class.php' ;
		
		$path_file = SITE_PATH . $value . DIRSEP . $class_format;
		if (file_exists($path_file)) include($path_file);
	}
}
function filterStr($string){
preg_match_all('/(?:([a-zA-Z0-9áéíóúñÑÁÉÍÓÚ ,-_]+)|.)/i', $string, $matches);
return implode('', $matches[1]);
}
?>