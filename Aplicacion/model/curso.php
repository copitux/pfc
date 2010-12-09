<?php
/**
 * curso.php - Modelo de curso
 * 
 * Operaciones a realizar con las cursos
 *
 * Por desarrollar
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */

class Model_curso extends Model_base
{
	protected $data = array('idCurso','nombreCurso');
	
	public function __construct()
	{
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
	}
}

?>