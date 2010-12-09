<?php

/**
 * index.php - Controlador de bienvenida
 * 
 * Mero tramite para centralizar a la aplicacion en torno a los usuarios
 * 
 * Controlador por defecto.
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package controllers
 */

class Controller_index extends Controller_base
{
	function index()
	{
		$this->smarty->display('index.tpl');
	}

}

?>