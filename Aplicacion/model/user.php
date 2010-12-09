<?php
/**
 * user.php - Modelo de usuario
 * 
 * Operaciones a realizar con los usuarios (disco local y compartidos)
 * 
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
class Model_user extends Model_base
{
	/**
	 * Datos de la tupla en orden
	 * 
	 * @var array Campos de tupla
	 */
	protected $data = array('idUsuario','nombre','apellido1','apellido2','correo','login','pass');
	//const DIR_ITERATOR_MODE = 'dir';
	
	/**
	 * Tipo de modelo (student, teacher)
	 * @var string $user_type
	 * @access private
	 */
	private $user_type = '';
	
	/**
	 * Ruta interna del disco local
	 * 
	 * @var string $path_user_files
	 * @access private
	 */
	private $path_user_files = '';
	
	/**
	 * Cuota de disco
	 * 
	 * Por desarrollar
	 * 
	 * @var integer $disk
	 * @access private
	 */
	private $disk = 30;
	
	public function __construct()
	{
		$this->user_type = array_pop(explode('_',get_class($this)));
		// Asignar mas llaves de clases student/teacher a $kdata
		if (isset($this->kdata_add)) foreach($this->kdata_add as $v) $this->data[] = $v;
		(func_num_args() == 1)?parent::__construct(func_get_arg(0)):parent::__construct();
		$this->path_user_files = 
			Config::$files_path . DIRSEP . $this->user_type . DIRSEP . $this->login . DIRSEP . 'local';
	}
	public function getPath() { return $this->path_user_files; }
	
	/**
	 * Inserta usuario
	 * 
	 * Override debido a lo implementacion de herencia a nivel ASI en DB.
	 * 
	 * Optimizable (JOIN)
	 * 
	 * @see classes/Model_base#_dbInsert()
	 */
	public function _dbInsert()
	{
		$user_data = $this->data;
		if (isset($this->kdata_add)) foreach($this->kdata_add as $v) unset($user_data[$v]);
		$user_data = array_keys($user_data);
		$table_set = implode(',',$user_data);
		$table_values = implode(',:',$user_data);
		$table_values = ':'.$table_values;
		
		$stmt = $this->db->prepare("INSERT INTO usuarios ($table_set) VALUES ($table_values)");
		foreach ($user_data as $v) $stmt->bindParam(':'.$v,$this->data[$v]);
		$stmt->execute();
	}
	
	/**
	 * Rellena objeto usuario
	 * 
	 * Sin ID's alumno,profesor
	 * 
	 * @see classes/Model_base#_fillModel()
	 */
	public function _fillModel($id)
	{
		if (!is_numeric($id)) throw new Exception('(!) ' . get_class($this) . ': fillModel() need int');
		
		$user_data = array_keys($this->data);
		$id_n = $user_data[0];
		$this->$id_n = $id;
		$stmt = $this->db->query("SELECT * FROM usuarios WHERE $id_n = $id LIMIT 0,1");
		$this->_fillValues($stmt->fetch(PDO::FETCH_ASSOC));
		// fill the idAlumno or idProfesor
		
	}
	/**
	 * Lista de asignaturas del usuario
	 * 
	 * Polimorfismo en base al modelo hijo que lo llame.
	 * 
	 * @see Model_asignatura::getAlumnos()
	 * @see Model_asignatura::getProfesor()
	 * @return array Array de modelos asignaturas en 'array mode' 
	 */
	public function getAsignaturas()
	{
		$sql = "SELECT asignaturas_idAsignatura FROM usuarios_has_asignaturas 
		WHERE usuarios_idUsuario = ".$this->idUsuario;
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_NUM);
		
		$array_asignaturas = array();
		foreach ($st as $v)
		{
			$asig = new Model_asignatura($v[0]);
			$carrera = new Model_carrera($asig->carreras_idCarrera);
			$curso = new Model_curso($asig->cursos_idCurso);
			//Se pasa a array para facilitar a smarty.
			$asigarr = $asig->_getAll();
			if ($this->user_type == 'student')
			{
				$t = new Model_teacher($asig->getProfesor());
				$asigarr['idProfesor'] = $t->_getAll();
			}
			$asigarr['carreras_idCarrera'] = $carrera->_getAll();
			$asigarr['cursos_idCurso'] = $curso->_getAll();
			if ($this->user_type == 'teacher') $asigarr['alumnos'] = $asig->getAlumnos('all');
			$array_asignaturas[] = $asigarr;
		}
		return $array_asignaturas;
	}
	
	/**
	 * Identificar usuario
	 * 
	 * SQL inyection prevention. Diferencia tipos de usuarios
	 * 
	 * @return unknown_type
	 */
	public function checkLogin()
	{
		$stmt = $this->db->prepare("SELECT * FROM usuarios 
									WHERE login = :login AND pass = :pass LIMIT 0,1");
		//$stmt = $this->db->prepare("CALL checkLogin(:login,:pass)");
		$stmt->bindParam(':login',$this->data['login']);
		$stmt->bindParam(':pass',$this->data['pass']);
		$stmt->execute();
		$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
		
		// Don't find User => leave
		if (empty($user_data)) return false;
		$this->_fillValues($user_data);
		$stmt = null;
		
		$table_name = Config::$map_tables[$this->user_type];
		$stmt = $this->db->prepare("SELECT * FROM $table_name 
									WHERE usuarios_idUsuario = :idUser LIMIT 0,1");
		$stmt->bindParam(':idUser',$this->data['idUsuario']);
		$stmt->execute();
		$user_type_data = $stmt->fetch(PDO::FETCH_ASSOC);
		// Don't find $user_type => leave
		if (empty($user_type_data)) return false;
		$this->_fillValues($user_type_data);
		return true;
	}

		
	/**
	 * Lista de elementos en disco local
	 * 
	 * funcion del modelo que devuelve un array de archivos del dir dnd se encuentra el usuario
	 * el array es asociativo pues tiene muchos valores, no solo los items del dir en cuestion
	 * sino todo lo necesario para smarty: hrefs de los enlaces, nombres de los enlaces, css classes ...
	 * Si la path esta mal (algo a tocado la url) devuelve false
	 * 
	 * @param string $path
	 * @return array Smarty array
	 */
	public function getLocalFiles($path)
	{

		//$path = trim($path,DIRSEP);
		$fullpath = $this->path_user_files . $path;
		if (!is_dir($fullpath)) return false;
				
		$dirs = array(); $files = array();
		$data = new IteratorIterator(new DirectoryIterator($fullpath));
		foreach($data as $v) {
			if ($v->isDot()) continue;
		
			$share = $this->is_share($v->getPathName());
			$p_share = $this->is_share(dirname($v->getPathName()));
			$file_data = array(
				'file' => $v->getFileName(),
				'file_enl' => ($v->isDir())
					?'index.php?route='.$this->user_type.'&path='.$path.$v->getFileName().DIRSEP
					:'index.php?route='.$this->user_type.'/download&path='.$path.$v->getFileName(),
				'size' => $v->getSize() / 1024,
				'date' => $v->getMTime(),
				'mime' => $v->getType(),
				'check_share' => ($share != false)?'share':'',
				'share_mode' => ($p_share != false)?'':(($share != false)?'Editar':'Compartir'),
				'share_href' => ($share != false)
					?'index.php?route='.$this->user_type.'/shareEdit&path='.$path.'&file='.$share
					:'index.php?route='.$this->user_type.'/share&path='.$path.'&file='.$v->getFileName(),
				'delete_href' => 'index.php?route='.$this->user_type.'/dlFile&path='.$path.'&file='.$v->getFileName(),
				'rename_href' => 'index.php?route='.$this->user_type.'/rnFile&path='.$path.'&file='.$v->getFileName(),
			);
			if ($v->isDir()) $dirs[] = $file_data;
			elseif ($v->isFile()) $files[] = $file_data;
		}
		return array_merge($dirs,$files);
	}
	
	/**
	 * Subir archivo
	 * 
	 * @param object $FILE['attachment']
	 * @param string path relativa
	 * @param boolean sobreescribir?
	 */
	public function upFile($file,$path,$mode)
	{
		$orig = $file['tmp_name'];
		$size = $file['size'];
		$dest = $this->path_user_files.$path.$file['name'];
		
		if (empty($file['name'])) throw new Exception('Debe escoger un archivo.');
		if ($size > (Config::$file_size * 1024 * 1024)) throw new Exception('El archivo supera el tama&ntilde;p permitido.');
		if (file_exists($dest) && ($mode === false)) throw new Exception('El archivo '.$file['name'].' ya existe. Si lo desea puede sobrescribirlo.');
		if (!is_uploaded_file($orig)) throw new Exception('Posiblemente el archivo supere el tamaño soportado.');
		if (!move_uploaded_file($orig,$dest)) throw new Exception('Fallo interno. Consulte con su administrador.');
		
		$id_padre = $this->is_share(trim($this->path_user_files.$path,DIRSEP));
		if (is_numeric($id_padre))
		{
			$patrn_f = new Model_file($id_padre);
			$patrn_f->padre = $patrn_f->idFichero;
			$patrn_f->ruta = $this->path_user_files.$path.$file['name'];
			$patrn_f->idFichero = null;
			$patrn_f->_dbInsert();
		}
	}
	
	/**
	 * Eliminar y renombrar elemento
	 * 
	 * @param mixed string (fisica), integer (DB)
	 * @param string Ruta relativa
	 * @param mixed Nuevo nombre
	 */
	public function dlFile($file,$path,$edit = null)
	{
		$id_prov = (is_numeric($file))?$file:false;
		if (!is_null($edit) && empty($edit)) throw new Exception ('Debes insertar un nombre.');
		if (!is_null($edit)) $edit = filterStr($edit); //regular pattern
		if ($id_prov == false) // renombrar y eliminar Dlocal sin compartir
		{
			// Al obligar a $file a no estar vacio nos aseguramos de no eliminar $path_user_files nunca.
			if (empty($file) && $edit == null) throw new Exception ('El archivo a eliminar no existe.');
			$fullpath_file = $this->path_user_files.$path.pathinfo($file,PATHINFO_BASENAME);
			if (!file_exists($fullpath_file)) throw new Exception('El archivo a eliminar no existe');
			$id_prov = $this->is_share($fullpath_file); //Owner user share this file?
			if ($id_prov == false) // Renombrar o eliminar solo en Dlocal y sin compartir.
			{
				if ($edit === null) {
					
					if (is_file($fullpath_file)) unlink($fullpath_file); //Delete Fie
					elseif (is_dir($fullpath_file)) Model_file::dlFolderPhy($fullpath_file);
				}
				else {
					$fullpath_edit = dirname($fullpath_file) . DIRSEP . $edit;	//Rename File
					if (file_exists($fullpath_edit)) throw new Exception('Ya hay un archivo con ese nombre');
					rename($fullpath_file,$fullpath_edit);
				}
			}
		}
		if ($id_prov != false) // Renombrar y eliminar tanto Dlocal (solo $this compartidos) como Dcompartido
		{
			$file = new Model_file($id_prov);
			if ($this->idUsuario == $file->usuarios_idUsuario) //$user shared that $file
			{
				if ($edit === null) $file->dlFile();
				else { $file->rnFile($edit); }
			}
			else
			{
				$grp = new Model_group($file->grupos_idGrupo);
				$users_in_grp = $grp->getAllData();
				$flag = false;
				foreach ($users_in_grp['users'] as $v) { // estas en el grupo?
					if ($this->idUsuario == $v['idUsuario']) $flag = true;
				}
				if ($flag == false) throw new Exception('No tienes relación con este elemento');
				$permit_flags = $grp->getPermission();
				if ($edit == null) { // tienes permisos para eliminar?
					if ($permit_flags['eliminar'] == 0) throw new Exception('Eliminar no permitido');
					$file->dlFile();
				}
				else // tienes permisos para editar?
				{
					if ($permit_flags['renombrar'] == 0) throw new Exception('Renombrar no permitido');
					$file->rnFile($edit);					
				}
			}
		}
	}

	/**
	 * Check de permisos de elemento
	 * 
	 * Puedo descargar yo este archivo?
	 * 
	 * @param integer ID file
	 * @return boolean
	 */
	public function downloadCheck($id)
	{
		$file = new Model_file($id);
		$grp = new Model_group($file->grupos_idGrupo);
		$users_in_grp = $grp->getAllData();
		$flag = false;
		foreach ($users_in_grp['users'] as $v) { // estas en el grupo?
			if ($this->idUsuario == $v['idUsuario']) $flag = true;
		}
		if ($flag && is_file($file->ruta)) return $file->ruta;
		else return false;
	
	}
	
	/**
	 * Check si la carpeta padre esta compartida
	 * 
	 * @param string Ruta relativa o elemento
	 * @return array (msn,true)
	 */
	public function paternHeredity($path) {
		$data = array('msg' => false);
		$id = $this->is_share(trim($this->path_user_files.$path,DIRSEP));
		if ($id != false)
		{
			$f = new Model_file($id);
			$grp = new Model_group($f->grupos_idGrupo);
			$data['msg'] = true;
			$data = array_merge($data,$grp->getAllData());
		}
		return $data;
	}
	
	/**
	 * Crea nueva carpeta
	 * 
	 * @param string $folder
	 * @param string $path
	 */
	public function newFolder($folder,$path)
	{
		$folder = pathinfo($folder,PATHINFO_BASENAME);
		$folder = filterStr($folder);
		if (empty($folder)) throw new Exception('Debes insertar un nombre');
		if (file_exists($this->path_user_files.$path.$folder)) throw new Exception($folder.' ya existe');
		if (!mkdir($this->path_user_files.$path.$folder)) throw new Exception('Fallo al crear la carpeta, consulte a su administrador');
		$id_padre = $this->is_share(trim($this->path_user_files.$path,DIRSEP));
		if (is_numeric($id_padre))
		{
			$patrn_f = new Model_file($id_padre);
			$patrn_f->padre = $patrn_f->idFichero;
			$patrn_f->ruta = $this->path_user_files.$path.$folder;
			$patrn_f->idFichero = null;
			$patrn_f->_dbInsert();
		}
	}
	
	/**
	 * Compartir elemento
	 * 
	 * @param string $file_name
	 * @param string $path
	 * @param array $users
	 * @param integer $profile
	 */
	public function share($file_name,$path,array $users,$profile)
	{
		$file_path = $this->path_user_files.$path.pathinfo($file_name,PATHINFO_BASENAME);
		if (!file_exists($file_path) || ($this->is_share($file_path) != false))
			throw new Exception('Intrusion en URL');
		if (empty($users)) throw new Exception('Necesario especificar algun amigo. Puede acceder a su lista de amigos en "Gestión de amigos"');
		
		$grp = new Model_group();
		$grp->perfiles_idPerfil = $profile;
		$grp->idGrupo = $grp->_dbInsert();
		$grp->assignUsers($users);
		
		$static_data = array('usuarios_idUsuario' => $this->idUsuario, 'grupos_idGrupo' => $grp->idGrupo);
		
		$patern = $this->is_share(trim($this->path_user_files.$path,DIRSEP));
		$file = new Model_file($static_data);
		$file->grupos_idGrupo = $grp->idGrupo;
		$file->ruta = $file_path;
		$file->padre = ($patern != false)?$patern:0;
		$file->idFichero = $file->_dbInsert();
		if (is_dir($file_path)) $this->_shareFolder($file);
		
	}

	/**
	 * Funcion para CSS, elige icono adecuado al tipo de usuario
	 * 
	 * Optimizable. Idiomas DB-modelo
	 * 
	 * @param $id css solution
	 * @return string (profesor,alumno)
	 */
	public function userShrType($id = false) {
		$flag = false;
		if ($id == false) { $id = $this->idUsuario; $flag = true; }
		$sql = "SELECT COUNT(idAlumno) FROM alumnos WHERE usuarios_idUsuario = $id";
		$stmt = $this->db->query($sql);
		$type = $stmt->fetch(PDO::FETCH_NUM);
		if ($flag == false)	return ($type[0] == 0)?'teacher':'student';
		else return ($type[0] == 0)?'profesor':'alumno';
	}
	
	/**
	 * Comparte carpeta recursivamente
	 * 
	 * @param string $folder_parent
	 * @access private
	 */
	private function _shareFolder(Model_file $folder_parent)
	{
		$data = new IteratorIterator(new DirectoryIterator($folder_parent->ruta));
		foreach ($data as $v)
		{
			if ($v->isDot()) continue;
			
			$is_share = $this->is_share($v->getPathName());
			$data = $folder_parent->_getAll(); unset($data['idFichero']);
			$child = new Model_file($data);
			$child->ruta = $v->getPathName();
			$child->padre = $folder_parent->idFichero;
			if ($is_share === false) $child->idFichero = $child->_dbInsert();
			else {
				$child->idFichero = $is_share;
				$child->grupos_idGrupo = $folder_parent->grupos_idGrupo;
				$child->_dbUpdate(array('padre','grupos_idGrupo'));
			}
			
			if ($v->isDir()) {
				$this->_shareFolder($child);
			}
		}
	}
	
	/**
	 * Check de elemento compartida
	 * 
	 * Chequea el campor ruta. Optimizable
	 * 
	 * @param string $rute
	 * @access private
	 * @return mixed false or ID file
	 */
	private function is_share($rute)
	{
		if (!file_exists($rute)) return false;
		
		$stmt = $this->db->prepare("SELECT idFichero FROM ficheros WHERE ruta = :rute AND usuarios_idUsuario = :id");
		$stmt->bindParam(':rute',$rute);
		$stmt->bindParam(':id',$this->data['idUsuario']);
		$stmt->execute();
		
		$a = $stmt->fetch(PDO::FETCH_ASSOC);
		return (is_numeric($a['idFichero']))?$a['idFichero']:false;
	}
	
	/**
	 * Usuarios que te comparten
	 * 
	 * @param boolean $all
	 * @return array
	 */
	public function getUsers($all = false)
	{
		$stmt = $this->db->query("SELECT * FROM usuarios WHERE idUsuario IN 
		(SELECT F.usuarios_idUsuario FROM usuarios_has_grupos UHG,ficheros F 
		WHERE UHG.usuarios_idUsuario = ".$this->idUsuario." AND UHG.grupos_idGrupo = F.grupos_idGrupo 
		GROUP BY F.usuarios_idUsuario)");
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$data = array();
		foreach ($st as $v) {
			$v['icon'] = Config::$map_css_clases[$this->userShrType($v['idUsuario'])];
			$data[] = $v;
		}
		return $data;
	}
	
	/**
	 * Ficheros que te comparten para un directorio en particular
	 * 
	 * @param integer ID usuario
	 * @param integer ID padre
	 * @param string Ruta
	 * @see getShrRute()
	 * @return array Smarty array
	 */
	public function getShrFiles($idu,$idp,&$rute)
	{
		$ff = new Model_file($idp);
		$rute = $ff->getShrRute($idu,$this->user_type);
		$sql = "SELECT * FROM ficheros WHERE padre = $idp AND usuarios_idUsuario = $idu AND grupos_idGrupo 
		IN (SELECT grupos_idGrupo FROM usuarios_has_grupos WHERE usuarios_idUsuario = ".$this->idUsuario.")";
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$dirs = array(); $files = array();
		foreach ($st as $v)
		{
			$g = new Model_group($v['grupos_idGrupo']);
			$profile = $g->getPermission();
			
			$file_type = '';
			if (is_dir($v['ruta'])) $file_type = 'dir';
			elseif (is_file($v['ruta'])) $file_type = 'file';
			$file_data = array(
				'href'	=> ($file_type == 'dir')
					?'index.php?route='.$this->user_type.'/dShare&id='.$idu.'&id_p='.$v['idFichero']
					:'index.php?route='.$this->user_type.'/download&file='.$v['idFichero'],
				'enl'	=> pathinfo($v['ruta'],PATHINFO_BASENAME), //Only name of File (format 'ruta')
				'icon'	=> $file_type,
				'rn'	=> ($profile['renombrar'] == 1)
							?"<a href=\"index.php?route=".$this->user_type."/rnFile&id=$idu&id_p=$idp&file=".$v['idFichero']."\" title=\"\">Renombrar</a>"
							:'Renombrar',
				'dl'	=> ($profile['eliminar'] == 1)
							?"<a href=\"index.php?route=".$this->user_type."/dlFile&id=$idu&id_p=$idp&file=".$v['idFichero']."\" title=\"\">Eliminar</a>"
							:'Eliminar',
			);
			if ($file_type == 'dir') $dirs[] = $file_data;
			elseif ($file_type == 'file') $files[] = $file_data;
		}
		return array_merge($dirs,$files);
	}
	protected function setMasterPath($path) { $this->path_user_files = $path; }
	
	/**
	 * Lista de amigos
	 * 
	 * @param boolean $all (semaforo para el return)
	 * @return array $all:true => array de Model_user; array de ID's
	 */
	public function getAmigos($all = true)
	{
		$sql = "SELECT idAmigo FROM amigos WHERE usuarios_idUsuario = ".$this->idUsuario;
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$users = array();
		foreach ($st as $v)
		{
			$user = new Model_user($v['idAmigo']);
			if ($all == true) $users[] = $user;
			else $users[] = $user->idUsuario;
		}
		return $users;
	}
	
	/**
	 * Sacar ID por campo
	 * 
	 * @param string$campo
	 * @param string $valor
	 * @return array
	 */
	public function getId($campo,$valor)
	{
		$sql = "SELECT idUsuario FROM usuarios WHERE $campo = ? LIMIT 0,1";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1,$valor);
		$stmt->execute();
		
		$st = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $st;
	}
	
	/**
	 * Eliminar amgios
	 * 
	 * @param array $amigos
	 */
	public function delAmigos($amigos)
	{
		foreach ($amigos as $v)
		{
			$sql = "DELETE FROM amigos WHERE idAmigo = $v AND usuarios_idUsuario = ".$this->idUsuario;
			$this->db->exec($sql);
		}
	}
	
	/**
	 * Insertar amigos
	 * 
	 * @param array $amigos
	 * @return array $amigos_added
	 */
	public function addAmigos($amigos)
	{
		$list_amigos = $this->getAmigos(false);
		$amigos_added = array();
		foreach ($amigos as $v)
		{
			$id = $this->getId('login',$v); $id = $id['idUsuario'];
			if ($id === null) continue;
			elseif (in_array($id,$list_amigos)) continue;
			elseif ($id == $this->idUsuario) continue;
			else
			{
				$sql = "INSERT INTO amigos (usuarios_idUsuario,idAmigo)  VALUES (".$this->idUsuario.",$id)";
				$this->db->exec($sql);
				$amigos_added[] = $v;
			}
		}
		return $amigos_added;
	}
}

?>
