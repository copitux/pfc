<?php
/**
 * controller_base.class.php - Controlador base
 * 
 * Controlador del que heredan todos los controladores.
 * Clase abstracta donde se realiza la instanciacion de Smarty asi como otros detalles.
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package base
 */

abstract class Controller_base
{

	/**
	 * 
	 * @var object Instancia de Smarty
	 */
	protected $smarty;
	
	
	/**
	 * 
	 * @var integer milisegundos de carga de pagina
	 */
	protected $init;
	
	/**
	 * Semaforo de ejecucion del metodo del controlador
	 * 
	 * @see Router::delegate()
	 * @var boolean
	 */
	public $call_action = true;
	
	/**
	 * Constructor
	 * 
	 * Configura smarty y lo instancia.
	 */
	function __construct()
	{
		//que hora es en milisegundos al iniciar la pagina?
		$this->init = microtime();
		
		// Instanciacion y cambio de rutas necesario para smarty
		$this->smarty = new Smarty();
		$this->smarty->template_dir = Config::$smarty.DIRSEP.'templates/';
		$this->smarty->compile_dir = Config::$smarty.DIRSEP.'templates_c/';
		$this->smarty->config_dir = Config::$smarty.DIRSEP.'configs/';
		$this->smarty->cache_dir = Config::$smarty.DIRSEP.'cache/';
		
		$this->smarty->assign('az_title',Config::$title_page);
		$this->smarty->assign('css_files',Config::getCssFiles());
	}
	/**
	 * Carga la plantilla 'foot' para mostrar tiempo de carga
	 */
	public function __destruct()
	{
		$this->smarty->assign('time',microtime() - $this->init);
		$this->smarty->display('basic/foot.tpl');
	}
	/**
	 * 404 Page
	 */
	public function _404()
	{
		$this->smarty->display('404.tpl');
	}
}

?>
