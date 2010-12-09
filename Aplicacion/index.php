<?php

require('include/init.php');
session_start();

//Clases basicas del MVC estan en /classes, router es una de ellas. go ahead.

// Debido a que en delegate() se instancia el controller y este realiza funciones e instancia al modelo
// se cogen las excepciones que puedan surgir.
try {
	$router = new Router(Config::$controllers_path);
	$router->delegate();
}
// Excepciones lanzadas por PDO (consultas sql mal echas, DB problems ...)
catch (PDOException $e) { echo '<strong>PDO:</strong> '.$e->getMessage(); }
// Excepciones lanzadas en general (puede que sean de un tipo concreto de excepcion (hay btts)
// pero Exception es su clase base asi que, aqui se quedan, no se escapan!! yuhu.
catch (Exception $e) { echo '<strong>MVC:</strong> '.$e->getMessage(); }

?>
