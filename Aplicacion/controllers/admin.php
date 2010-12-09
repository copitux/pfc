<?php
/**
 * admin.php - Controlador de administrador
 * 
 * Controlador encargado del administrador.
 * Setup*
 * 
 * Optimizable
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package controllers
 */
class Controller_admin extends Controller_base
{
	public function __construct()
	{
		parent::__construct();
		$subm_options = array(
				array('enl' => 'index.php?route=admin/istudent','tit' => 'iAlumno'),
				array('enl' => 'index.php?route=admin/iteacher','tit' => 'iProfesor'),
				array('enl' => 'index.php?route=admin/struct','tit' => 'Crear directorio')
			);
		$menu = array(
			array('menu' => 'opciones','submenu' => $subm_options)
		);
		$this->smarty->assign('menu',$menu);
	}
	function index()
	{
		$this->smarty->display('index.tpl');	
	}
	/**
	 * Crea la estructura basica
	 * 
	 * Forma la estructura de directorios para los usuarios de la base de datos diferenciando entre alumnos 
	 * y profesores. Realiza diferentes chequeos para los directorios.
	 * 
	 * A su vez forma los grupos basicos para el sistema docente.
	 * 
	 * Unica funcionalidad del administrador. Optimizable
	 * 
	 */
	public function struct()
	{
		//DB
		
		$admin = new Model_admin();
		
		// Primeros checks
		echo '<pre>';
		$dir_principal = SITE_PATH.Config::$files_path;
		$dir_student = Config::$files_path.DIRSEP.'student';
		$dir_teacher = Config::$files_path.DIRSEP.'teacher';
		echo "Dir principal (absoluta):\t'$dir_principal'\t";
		if (!file_exists($dir_principal)) { echo '...ERROR'; throw new Exception('Error'); } else echo '...OK<br />';
		
		echo "Dir estudiante: ";
		if (!file_exists(Config::$files_path.DIRSEP.'student'))
		{
			mkdir(Config::$files_path.DIRSEP.'student');
			echo "...OK<br />";
		} else echo "...Existe<br />";
		echo "Dir teacher: ";
		if (!file_exists(Config::$files_path.DIRSEP.'teacher'))
		{
			mkdir(Config::$files_path.DIRSEP.'teacher');
			echo "...OK<br />";
		} else echo "...Existe<br />";
		
		//students
		echo "<hr>Estudiantes (local)<br /><br/>";
		
		$db = $admin->getDB();
		$stmt = $db->query("select usuarios_idUsuario from alumnos");
		$ids_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = null;
		foreach($ids_students as $v)
		{
			$student = new Model_student($v['usuarios_idUsuario']);
			$ruta_a_crear = $student->login;
			
			echo "Creando... '$dir_student".DIRSEP."$ruta_a_crear' ";
			if (!file_exists($dir_student.DIRSEP.$ruta_a_crear))
			{
				mkdir($dir_student.DIRSEP.$ruta_a_crear);
				mkdir($dir_student.DIRSEP.$ruta_a_crear.DIRSEP.'local');
				echo "...OK<br />";
			}
			else echo "...Existe<br />";
		}
		//teachers
		echo "<hr>Profesores (local)<br /><br/>";
		
		$stmt = $db->query("select usuarios_idUsuario from profesores");
		$ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($ids as $v)
		{
			$user = new Model_teacher($v['usuarios_idUsuario']);
			$user->crearGrupo();
			$ruta_a_crear = $user->login;
			
			echo "Creando... '$dir_teacher".DIRSEP."$ruta_a_crear' <br>";
			if (!file_exists($dir_teacher.DIRSEP.$ruta_a_crear))
			{
				mkdir($dir_teacher.DIRSEP.$ruta_a_crear);
				echo "Creando... '$dir_teacher".DIRSEP."$ruta_a_crear".DIRSEP."local' <br>";
				mkdir($dir_teacher.DIRSEP.$ruta_a_crear.DIRSEP.'local');
				echo "Creando... '$dir_teacher".DIRSEP."$ruta_a_crear".DIRSEP."docente' <br>";
				mkdir($dir_teacher.DIRSEP.$ruta_a_crear.DIRSEP.'docente');
				
				$doc_path = $dir_teacher.DIRSEP.$ruta_a_crear.DIRSEP.'docente'.DIRSEP;
				
				foreach ($user->getAsignaturas() as $v)
				{
					//$asignatura = utf8_encode($v['nombreAsignatura']);
					$asignatura = $v['nombreAsignatura'];
					echo "Creando... '$doc_path".$v['nombreAsignatura']."' <br>";
					mkdir($doc_path.$asignatura);
					echo "Creando... '$doc_path".$v['nombreAsignatura'].DIRSEP."enunciados' <br>";
					mkdir($doc_path.$asignatura.DIRSEP.'enunciados');
					echo "Creando... '$doc_path".$v['nombreAsignatura'].DIRSEP."practicas' <br>";
					mkdir($doc_path.$asignatura.DIRSEP.'practicas');
				}
				
				echo "...OK<br /><br />";
			}
			else echo "...Existe<br /><br />";
		}
		
		echo '</pre>';
	}
	/**
	 * Inserta alumno
	 * 
	 * Por desarrollar. Lo inserta pero no lo relaciona
	 */
	public function istudent()
	{
		if (empty($_POST)) $this->smarty->display('admin/istudent.tpl');
		else
		{
			$std = new Model_student($_POST);
			$std->_dbInsert();
			$this->smarty->assign('mode','a');
			$this->smarty->display('admin/index.tpl');
		}
	}
	/*
	 * Inserta profesor
	 * 
	 * Por desarrollar. Lo inserta pero no lo relaciona
	 */
	public function iteacher()
	{
		if (empty($_POST)) $this->smarty->display('admin/iteacher.tpl');
		else
		{
			$std = new Model_teacher($_POST);
			$std->_dbInsert();
			$this->smarty->assign('mode','a');
			$this->smarty->display('admin/index.tpl');
		}
	}
}

?>