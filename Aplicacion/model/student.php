<?php
/**
 * student.php - Modelo de alumno
 * 
 * Operaciones a realizar con los alumnos (disco docente)
 * 
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
class Model_student extends Model_user
{
	/**
	 * Datos adicionales de alumno
	 * @var array $kdata_add
	 */
	protected $kdata_add = array('idAlumno');
	
	public function __construct()
	{
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
	}
	
	/**
	 * Enunciados de una asignatura
	 * 
	 * @param object Model_asignatura
	 * @see Model_user::getAsignaturas()
	 * @see Model_asignatura::getProfesor()
	 * @see Model_teacher::getStateDocnt()
	 * @see Model_teacher::getEnun()
	 * @return array Array para Smary de enunciados
	 */
	public function getEnun(Model_asignatura $asig)
	{
		//bendita OO
		$flag = false;
		// check si la asignatura que se le pasa esta en las mias.
		foreach ($this->getAsignaturas() as $v) { if ($v['idAsignatura'] == $asig->idAsignatura) $flag = true; }
		if ($flag == false) throw new Exception('No estas en esta asignatura');
		$t = new Model_teacher($asig->getProfesor());
		if (!$t->getStateDocnt($asig)) throw new Exception($t->nombre.' '.$t->apellido1.' no ha activado los enunciados');
		return $t->getEnun($asig,true);
	}
	
	/**
	 * Subir practica
	 * 
	 * @see Model_teacher::upFile()
	 * @see Model_user::upFile()
	 */
	public function upFile($file,$path,$mode)
	{
		if (is_numeric($path)) {
			$flag = false;
			foreach ($this->getAsignaturas() as $v) { if ($v['idAsignatura'] == $path) $flag = true; }
			if ($flag == false) throw new Exception('No estas en esta asignatura');
			$asign = new Model_asignatura($path);
			$t = new Model_teacher($asign->getProfesor());
			$t->upFile($file,$path,$mode,true);
		}
		else { parent::upFile($file,$path,$mode); }
	}
}

?>