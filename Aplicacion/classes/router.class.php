<?php

/**
 * router.class.php - Clase Router
 *
 * Encargada de cargar el controller que se pida
 * 
 * $router format: controller/action
 *		
 * Nota: Debido a su formato se permiten subdirectorios, pero no los usamos
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package base
 */

class Router
{
/**
 * Directorio de los controladores
 * 
 * @var string
 */
	public $path;
	
/**
 * Constructor
 * 
 * @see setPath()
 */
	public function __construct() { if (func_num_args() >= 1) $this->setPath(func_get_arg(0)); }
	
/**
 * Magic method - Devuelve $var
 * 
 * @param mixed Variable que se pide
 * @return mixed Valor de dicha variable
 */
	public function __get($v) { return $this->$v; }
	
/**
 * Carga en $path la ruta de los controladores, check incluido
 * 
 * @param string Directorio de controladores
 * @return mixed Exception si falla
 */
	public function setPath($path)
	{
		$com_path = SITE_PATH . $path . DIRSEP;
		if (file_exists($com_path)) $this->path = $com_path;
		else throw new Exception('(!!) MVC model: \''.$com_path.'\' no existe');
	}

/**
 * Analiza y extrae datos del controlador
 * 
 * Coge a $route y la analiza para ver cual es el controller y cual es el action
 * 
 * Los chekeos tienen algunos puntos
 * 1. Puede que no quieras cargar controlador (pagina principal) => se carga el controller_index
 * 2. Puede que solo indiques el controlador sin funcion => su funcion sera index
 * 3. Si metes datos que no existen, ej: $route=controler_malo/funcion_malota los cogera pero delegate() se encargara de ella
 * 
 * @see delegate()
 * @param string Archivo donde se define el controlador
 * @param string nombre del controlador
 * @param string metodo del controlador
 */
	public function getController(&$file,&$controller,&$action)
	{
		$route = (empty($_GET['route']))?'index':$_GET['route'];
		$route = trim($route,'/\\');
		$parts = explode('/',$route);
		$cmd_path = $this->path;
		
		foreach ($parts as $p)
		{
			$fullpath = $cmd_path . $p;
			if (is_dir($fullpath))
			{
				$cmd_path .= $p . DIRSEP;
				array_shift($parts);
				continue;
			}
			if (is_file($fullpath . '.php'))
			{
				$controller = $p;
				array_shift($parts);
				break;
			}
			
		}
		$controller = (empty($controller))?'index':$controller;
		$action = array_shift($parts);
		$action = (empty($action))?'index':$action;
		$file = $cmd_path . $controller . '.php';
	}
	
/**
 * Carga el controlador
 * 
 * Con los datos extraidos de getController() hacemos unos ultimos chequeos e instanciamos el controlador 
 * 
 * @see getController()
 */
	public function delegate()
	{
		$this->getController($file,$controller,$action);
		// A partir de aqui es como si tienes 3 variables
		
			//$file=	/ruta/to/controler.php
			//$nombre=	nombre del controlador a cargar
			//$action=	funcion del controlador a ejecutar.
		
		if (!is_readable($file)) { $file = $this->path . 'index' . '.php'; $controller = 'index'; }
		
		include ($file);
		$class = 'Controller_' . $controller;
		$controller = new $class();
		
		//go 404 si no existe
		if (!is_callable(array($controller,$action))) $action = '_404';
		// Todos los controladores tienen esa variable 'call_action', es un semaforo por si quiero
		// ejecutar $action o no (unicamente usado de momento en c_student y teacher)
		// Intimamente relacionado con llamar a funciones del controlador desde el controlador (ocasiones especiales)
		if ($controller->call_action === true) $controller->$action();
	}
}

?>
