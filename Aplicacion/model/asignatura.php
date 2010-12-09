<?php
/**
 * asignatura.php - Modelo de asignatura
 * 
 * Operaciones a realizar con las asignaturas
 *
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */

class Model_asignatura extends Model_base
{
	/**
	 * Datos de la tupla en orden
	 * 
	 * @var array Campos de tupla
	 */
	protected $data = array('idAsignatura','carreras_idCarrera','cursos_idCurso','codAsignatura',
	'nombreAsignatura','tipo','creditos','periodo');
	
	public function __construct()
	{
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
	}
	// @param [$all] Por defecto false
	// return Por defecto devuelve un array de ids, si le pasamos 'all' de arrays del Model_student(data_mode).
	
	/**
	 * Alumnos de una asignatura
	 * 
	 * Devuelve un array de alumnos formateados segun el parametro
	 * @param mixed $all Por defecto false
	 * @return array Por defecto devuelve un array de ID's, en caso contrario, array de Model_student object's
	 */
	public function getAlumnos($all = false)
	{
		$idp = $this->getProfesor();
		$sql = "SELECT usuarios_idUsuario from usuarios_has_asignaturas 
		WHERE asignaturas_idAsignatura = ".$this->idAsignatura." 
		AND usuarios_idUsuario != ".$idp;
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($st as $k => $v) $st[$k] = (int) $st[$k]['usuarios_idUsuario'];
		if ($all != false)
		{
			$alumnos_all = array();
			foreach($st as $v)
			{
				$alumno = new Model_student($v);
				$alumnos_all[] = $alumno->_getAll();
			}
			return $alumnos_all;
		}
		else { return $st; }
		
	}
	/**
	 * ID del profesor
	 * 
	 * @return integer ID_profesor
	 */
	public function getProfesor()
	{
		$sql = "SELECT UHG.usuarios_idUsuario FROM usuarios_has_asignaturas UHG, profesores P
		WHERE UHG.usuarios_idUsuario = P.usuarios_idUsuario
		AND UHG.asignaturas_idAsignatura = ".$this->idAsignatura;
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return (int) $st[0]['usuarios_idUsuario'];
	}
}

?>