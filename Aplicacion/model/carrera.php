<?php
/**
 * carrera.php - Modelo de carrera
 * 
 * Operaciones a realizar con las carreras
 *
 * Por desarrollar
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */

class Model_carrera extends Model_base
{
	protected $data = array('idCarrera','nombre');
	
	public function __construct()
	{
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
	}
}

?>