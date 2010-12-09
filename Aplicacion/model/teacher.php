<?php
/**
 * student.php - Modelo de profesor
 * 
 * Operaciones a realizar con los profesores (disco docente)
 * 
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
class Model_teacher extends Model_user
{
	/**
	 * Datos adicionales de profesor
	 * @var array $kdata_add
	 */
	protected $kdata_add = array('idProfesor','despacho');
	
	/**
	 * Ruta del profesor. Variable con enunciados y practicas
	 * @var stirng $path_teacher
	 */
	protected $path_teacher = '';

	/**
	 * Inserta funcionalidad de rutas profesor
	 * 
	 * @see $path_teacher
	 */
	public function __construct()
	{
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
		$this->path_teacher = Config::$files_path.DIRSEP.'teacher'.DIRSEP.$this->login.
		DIRSEP.'docente'.DIRSEP;
	}
	public function getPathTeacher() { return $this->path_teacher; }
	
	/**
	 * Activa docencia
	 * 
	 * Crea un grupo con el nombre formateado bajo el patron 'IDusuario_IDasignatura'.
	 * 
	 * Juega con active record para lograrlo
	 * 
	 */
	public function crearGrupo()
	{
		$asignaturas = $this->getAsignaturas();
		foreach ($asignaturas as $v)
		{
			$asig = new Model_asignatura($v['idAsignatura']);
			$nombre_grupo = $this->idUsuario.'_'.$asig->idAsignatura;
			$perfil = 4;
			//creando grupo con perfil 4 (no permisivo)
			$grupo = new Model_group();
			$grupo->perfiles_idPerfil = $perfil;
			$grupo->nombreGrupo = $nombre_grupo;
			$check = $grupo->getGroupState();
			if (is_array($check))
			{
				$grupo->idGrupo = $check['idGrupo'];
				$grupo->_dbDelete();
				$grupo->idGrupo = null;
			}
			$grupo->idGrupo = $grupo->_dbInsert();
			//$grupo->assignUsers($asig->getAlumnos());
		
		}
	}

	/**
	 * Estado de la docencia
	 * 
	 * @param $asi - Model_asignatura para chequear si tiene grupo asociado.
	 * @return - True si la tiene activada, false si no.
	 */
	public function getStateDocnt($asig)
	{
		$nombre_grupo = $this->idUsuario.'_'.$asig->idAsignatura;
		$sql = "SELECT idGrupo FROM grupos WHERE nombreGrupo = '$nombre_grupo'";
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($st)) return false; else return true;
	}
	
	/**
	 * Listado de enunciados
	 * 
	 * Recorre el directorio de enunciados del profesor y forma el array para smarty
	 * 
	 * Funcion usada relativamente por alumnos. Optimizable en seguridad
	 * 
	 * @param object Model_asignatura $asig
	 * @param boolean $alm Semaforo para asignar controladores adecuados
	 * @return array Array de elementos
	 */
	public function getEnun(Model_asignatura $asig,$alm = false)
	{
		$path = $this->path_teacher.$asig->nombreAsignatura.DIRSEP.'enunciados';
		if (!file_exists($path)) throw new Exception('(!) MVC: getEnun(), ruta inexistente');
		
		$controller = ($alm == false)?'teacher':'student';
		
		$data = new IteratorIterator(new DirectoryIterator($path));
		$files = array();
		foreach($data as $v) {
			if ($v->isDot()) continue;
		
			$file_data = array(
				'file' => $v->getFileName(),
				'file_enl' => 'index.php?route='.$controller.'/download&ida='.$asig->idAsignatura.'&file='.$v->getFileName(),
				'size' => $v->getSize() / 1024,
				'date' => $v->getMTime(),
				'delete_href' => 'index.php?route='.$controller.'/dlFile&ida='.$asig->idAsignatura.'&file='.$v->getFileName(),
				'rename_href' => 'index.php?route='.$controller.'/rnFile&ida='.$asig->idAsignatura.'&file='.$v->getFileName(),
			);
			$files[] = $file_data;
		}
		return $files;
	}
	/**
	 * Listado de pacticas
	 * 
	 * Recorre el directorio de practicas del profesor y forma el array para smarty
	 * 
	 * Optimizable
	 * 
	 * @param object Model_asignatura $asig
	 * @return array Array de elementos
	 */
	public function getPrac(Model_asignatura $asig)
	{
		$path = $this->path_teacher.$asig->nombreAsignatura.DIRSEP.'practicas';
		if (!file_exists($path)) throw new Exception('(!) MVC: getEnun(), ruta inexistente');
		
		$data = new IteratorIterator(new DirectoryIterator($path));
		$files = array();
		foreach($data as $v) {
			if ($v->isDot()) continue;
		
			$file_data = array(
				'file' => $v->getFileName(),
				'file_enl' => 'index.php?route=teacher/download&pr=1&ida='.$asig->idAsignatura.'&file='.$v->getFileName(),
				'size' => $v->getSize() / 1024,
				'date' => $v->getMTime(),
				'delete_href' => 'index.php?route=teacher/dlFile&ida='.$asig->idAsignatura.'&pr=1&file='.$v->getFileName(),
				'rename_href' => 'index.php?route=teacher/rnFile&ida='.$asig->idAsignatura.'&pr=1&file='.$v->getFileName(),
			);
			$files[] = $file_data;
		}
		return $files;
	}
	/**
	 * Elimina elemento del disco docente
	 * 
	 * @param boolean $pr Semaforo para enunciados o practicas
	 * @see model/Model_user#dlFile()
	 */
	public function dlFile($file,$path,$edit = null,$pr = false)
	{
		//adorado polimorfismo...
		if (isset($_GET['ida'])) {
			if (isset($_GET['pr'])) $pr = true;
			$this->setMasterPath($this->path_teacher);
			$asign = new Model_asignatura($_GET['ida']);
			$path = $asign->nombreAsignatura.DIRSEP.(($pr == false)?'enunciados':'practicas').DIRSEP;
		}
		parent::dlFile($file,$path,$edit);
	}

	/**
	 * Subir archivo a disco docente
	 * 
	 * Sube enunciados de manera directa con esta funcion y practicas 
	 * de manera indirecta con modelo estudiante
	 * 
	 * @param boolean $pr Semaforo para enunciados o practicas
	 * @see model/Model_user#upFile()
	 */
	public function upFile($file,$path,$mode,$pr = false)  
	{
		if (is_numeric($path)) {
			$this->setMasterPath($this->path_teacher);
			$asign = new Model_asignatura($path);
			$path = $asign->nombreAsignatura.DIRSEP.(($pr == false)?'enunciados':'practicas').DIRSEP;
			//$mode = false; si esta comentado permite sobrescribir practicas
		}
		parent::upFile($file,$path,$mode);
	}
}

?>