<?php

/**
 * controller_user.class.php - Controlador de usuario
 * 
 * Controlador encargado de los usuarios.
 * Contiene la gran mayoria de opciones de usuario comunes a ambos
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package controllers
 */

abstract class Controller_user extends Controller_base
{
	/**
	 * Tipo de usuario
	 * 
	 * @access private
	 * @var string $user_type (student or teacher)
	 */
	private $user_type = '';
	
	/**
	 * Modelo del usuario $user_type
	 * 
	 * @access protected
	 * @see $user_type
	 * @var object Model_*
	 */
	protected $user; //Object Model_$user_type
	
	/**
	 * Ruta relativa.
	 * 
	 * Forma la ruta que el usuario define en su disco.
	 * Usada en otras ocasiones sin caracter semantico.
	 * 
	 * @access private
	 * @var mixed $path
	 */
	private $path;
	
	
	/**
	 * Constructor que inserta funcion al base.
	 * 
	 * Generador de sesion si esta identificado.
	 * Instancia el modelo tipo.
	 * Asigna variables basicas de usuario a smarty.
	 */
	public function __construct()
	{
		parent::__construct(); //parent es la clase padre, llama a su constructor
		// Asigna student o teacher a la variable dependiendo del tipo de objeto instanciado (abstracion)
		$this->user_type = array_pop(explode('_',get_class($this)));
		//una var para el css (marca alumnos o profesores (.class) depende de dnd este logeado)
		$this->smarty->assign('user_type_css',Config::$map_css_clases[$this->user_type]);
		// No queremos que alguien modifique la URL para cargar una funcion de usuario sin estar logeado previamente
		// En los controladores que seran instanciados se manda a la pantalla de login si no hay sesion
		if (isset($_SESSION['user'])) {
			
			$this->smarty->assign('none','none');
			//$user en smarty (array) con los valores del usuario
			$this->smarty->assign('user',$_SESSION['user']);
			// instanciamos modelo
			$user_type_class = 'Model_'.$this->user_type;
			$this->user = new $user_type_class($_SESSION['user']);
			if (!file_exists($this->user->getPath()))
			{
				unset($_SESSION['user']);
				throw new Exception('(!) MVC: Ruta de usuario no encontrada, contacte con el administrador');
			}
			//cuota de disco (por desarrollar)
			//$this->smarty->assign('freeSpace',$this->user->space());
			//En que dir se encuentra el usuario (variable path de la url, la asigno a la variable del objeto)
			$this->path = (isset($_GET['path']))?$_GET['path']:DIRSEP;
		}
	}
	/**
	 * Fuerza la descarga del archivo.
	 * 
	 * Optimizable con mas chequeos y seguridad
	 * Rutas internas para no dar datos al usuario.
	 * 
	 * @see downloadfileclass
	 */
	public function download()
	{
		require(Config::$classes_path.DIRSEP.'downloadfileclass.inc');
		$path = '';
		
		if (isset($_GET['file']) && is_numeric($_GET['file'])) // COMPARTIDOS
		{
			//Descargando archivo que te comparten.
			$chk = $this->user->downloadCheck($_GET['file']);
			if ($chk != false) $path = $chk;
		}
		elseif (isset($_GET['ida']) && isset($_GET['file'])) // DOCENTE
		{
			$asig = new Model_asignatura($_GET['ida']);
			$prof = new Model_teacher($asig->getProfesor());
			
			$path = $prof->getPathTeacher();
			$path .= $asig->nombreAsignatura.DIRSEP;
			$path .= (isset($_GET['pr']))?'practicas':'enunciados';
			$path .= DIRSEP.$_GET['file'];	
		}
		else
		{
			//Locales
			$path = $this->user->getPath();
			$path .= $this->path;
		}
		
		$downloadfile = new DOWNLOADFILE($path);
		if (!$downloadfile->df_download()) {}

	}
	/**
	 * Elimina sesion
	 * 
	 * El controlador sin sesion no tiene funcionalidad.
	 */
	public function unlog() { unset($_SESSION['user']); header("Location: index.php"); }
	
	
	/**
	 * Muestra datos del usuario
	 */
	public function data() { $this->smarty->display($this->user_type.'/data.tpl'); }
	
	/**
	 * Identificacion de usuario.
	 * 
	 * Se encarga de mostrar o tramitar el formulario de identificacion.
	 * Si lo tramita llama al modelo para comprobar datos contra la DB.
	 * Encriptacion MD5 sin semillas.
	 * Genera sesion.
	 * 
	 * Optimizable con LDAP o similares.
	 * 
	 * @see Model_user::checkLogin()
	 */
	public function login()
	{
		//Vars del template.
		$menu = array(array('class' => 'login',"menu" => 'Login ('.Config::$map_tables[$this->user_type].')'));
		$this->smarty->assign('menu_info','Inserta los datos de acceso');
		$this->smarty->assign('menu',$menu);
		// Display formulario
		if (empty($_POST)) {
			$this->smarty->display('login.tpl');
		}
		//tramita formulario
		else {
			$_POST['pass'] = md5($_POST['pass']);
			$user_type_class = 'Model_'.$this->user_type;
			$user = new $user_type_class($_POST);
			// checkLogin (funcion del modelo): Devuelve true o false
			if ($user->checkLogin() === true) //Usuario logeado con su tipo
			{
				// Mete datos en la sesion y redirige a la pantalla de D.local (por defecto => $action = index)
				$data_to_ses = $user->_getAll(); unset($data_to_ses['pass']);
				$_SESSION['user'] = $data_to_ses;
				header("Location: index.php?route=".$this->user_type);
			}
			// Por si el login es incorrecto, decir algo al usuario.
			$this->smarty->assign('notlogin','Datos incorrectos');
			$this->smarty->display('login.tpl');
		}
	}
	/**
	 * Muestra Disco Local.
	 * 
	 * Muestra los datos que el modelo le proporciona y un menu de navegacion dinamico.
	 * Metodo por defecto si unicamente se indica el controlador.
	 * 
	 * @see Model_user::getLocalFiles()
	 * @see getRute()
	 */
	public function index()
	{

		$files = $this->user->getLocalFiles($this->path);
		if ($files === false) header("Location: index.php?route=".$this->user_type);
		$rute = $this->getRute(); // funcion de este controlador, doc mas abajo.
		$this->smarty->assign('files',$files);
		$this->smarty->assign('rute',$rute); //Array formateado ('tit','href','sep')
		$this->smarty->assign('rute_to_up',$this->path); // pasado a opciones para crear/subir en directorio correcto
		$this->smarty->display($this->user_type.'/basic.tpl');
	}

	/**
	 * Elimina elemento
	 * 
	 * Si se ha podido eliminar el elemento desaparece de la interfaz.
	 * Tramita errores del modelo bajo excepciones.
	 * 
	 * @see Model_user::dlFile()
	 */
	public function dlFile()
	{
		$file = (isset($_GET['file']))?$_GET['file']:'';
		try {
			//$file= nombre del archivo a borrar
			//$this->path= para poder formar la ruta completa del archivo en cuestion en la funcion del modelo
			// No devuelve nada, errores tramitados con excepciones
			$this->user->dlFile($file,$this->path);
			//Redirijo a donde estaba el usuario.
			if (isset($_GET['ida'])) header("Location: index.php?route=".$this->user_type.'/dDocnt&id='.$_GET['ida']);			
			else
			{
				$idu = (isset($_GET['id']))?'&id='.$_GET['id']:'';
				$idp = (isset($_GET['id_p']))?'&id_p='.$_GET['id_p']:'';
				header("Location: index.php?route=".$this->user_type.((is_numeric($file))?'/dShare'.$idu.$idp:'&path='.$this->path));	
			}
		} catch (Exception $e) {
			// Muestro excepciones como errores al usuario.
			$this->smarty->assign('errs',$e->getMessage());
			$this->smarty->display('err.tpl');
		}
	}
	
	/**
	 * Renombrar elemento
	 * 
	 * Muestra o tramita un formulario para renombrar un elemento..
	 * Errores captados con excepciones.
	 * 
	 * @see Model_user::dlFile()
	 */
	public function rnFile()
	{
		$file = (isset($_GET['file']))?$_GET['file']:'';

		if (empty($_POST))
		{
			if (is_numeric($file))
			{
				$f = new Model_file($file);
				$file = pathinfo($f->ruta,PATHINFO_BASENAME);
			}
			$this->smarty->assign('file',$file);
			$this->smarty->display('rename.tpl');
		}
		else
		{
			// se trata con el model dlFile debido a chequekeo de permisos iguales para ambas funciones
			try {
				$this->user->dlFile($file,$this->path,$_POST['newname']);
				if (isset($_GET['ida']))
				{
					header("Location: index.php?route=".$this->user_type."/dDocnt&id=".$_GET['ida']);
				}
				else
				{
					$idu = (isset($_GET['id']))?'&id='.$_GET['id']:'';
					$idp = (isset($_GET['id_p']))?'&id_p='.$_GET['id_p']:'';
					header("Location: index.php?route=".$this->user_type.((is_numeric($file))?'/dShare'.$idu.$idp:'&path='.$this->path));	
				}
			} catch (Exception $e) {
				if (is_numeric($file))
				{
					$f = new Model_file($file);
					$file = pathinfo($f->ruta,PATHINFO_BASENAME);
				}
				$this->smarty->assign('file',$file);
				$this->smarty->assign('fail_data',$e->getMessage());
				$this->smarty->display('rename.tpl');			
			}
		}
	}
	/**
	 * Subir archivo
	 * 
	 * Muestra o tramita un formulario para subir archivos.
	 * 
	 * Avisa, si el directorio donde se pretende subir el archivo ya esta compartido, de los usuarios y permisos.
	 * Errores captados con excepciones.
	 * 
	 * @see Model_user::upFile() 
	 */
	public function upFile()
	{
		$this->smarty->assign('size',(Config::$file_size *1024*1024));
		if (!is_numeric($_GET['path'])) { //upFile dDocnt check

			$advice_msg = 'El archivo estará compartido con permisos ';
			
			// Funcion del modelo que chequea si la carpeta padre donde subo el archivo esta compartida.
			// $advice es un array asociativo
			//	msg = semaforo para este controlador que indica si esta compartida o no
			//	valores_del_user = Son varios dependiendo del modelo.
			$advice = $this->user->paternHeredity($this->path);
			if ($advice['msg'])
			{
				$advice['msg'] = $advice_msg;
				$advice['profile'] = Model_profile::idToText($advice['profile']);
				$advice['msg'] .= '<acronym title="Acciones que podran llevar a cabo los usuarios a los que se le comparta">'.$advice['profile'].'</acronym> para los usuarios: ';

				// Muestro al usuario el aviso de que este archivo se compartira, y con quien y que permisos.
				$this->smarty->assign('advice',$advice);
			}
		}
		if (empty($_POST)) { $this->smarty->display('upfile.tpl'); }
		// tramito form
		else {
			try {
				$this->user->upFile($_FILES['file'],$this->path,isset($_POST['upwrite']));
				if (is_numeric($_GET['path']))
				{
					if ($this->user_type == 'student')
					{
						$this->smarty->assign('prsend',$_FILES['file']['name']);
						$this->smarty->display('upfile.tpl');
					}
					else header("Location: index.php?route=".$this->user_type.'/dDocnt&id='.$this->path);
				}
				else header("Location: index.php?route=".$this->user_type.'&path='.$this->path);
			} catch (Exception $e) {
				$this->smarty->assign('fail_data',$e->getMessage());
				$this->smarty->display('upfile.tpl');
			}
		}
	}
	
	/**
	 * Crear carpeta
	 * 
	 * Muestra o tramita el formulario de creación de carpeta.
	 * Avisa, si el directorio donde se pretende subir el archivo ya esta compartido, de los usuarios y permisos.
	 * 
	 * @see Mode_user::newFolder()
	 */
	public function newFolder()
	{
		$advice_msg = 'La carpeta que cree estará compartida con permisos ';
		$advice = $this->user->paternHeredity($this->path);
		if ($advice['msg'])
		{
			$advice['msg'] = $advice_msg;
			$advice['profile'] = Model_profile::idToText($advice['profile']);
			$advice['msg'] .= '<acronym title="Acciones que podran llevar a cabo los usuarios a los que se le comparta">'.$advice['profile'].'</acronym> para los usuarios: ';
			$this->smarty->assign('advice',$advice);
		}
		
		if (empty($_POST)) {
			$this->smarty->display('newfolder.tpl');
		}
		else {
			$folder = filter_var($_POST['folder'],FILTER_SANITIZE_STRING);
			try {
				$state = $this->user->newFolder($folder,$this->path);
				header("Location: index.php?route=".$this->user_type.'&path='.$this->path);
			} catch (Exception $e) {
				$this->smarty->assign('fail_data',$e->getMessage());
				$this->smarty->display('newfolder.tpl');
			}
		}
	}
	/**
	 * Compartir elemento.
	 * 
	 * Muestra o tramita el formulario para compartir un elemento.
	 * Errores tramitados con excepciones.
	 * 
	 * @see Model_user::share()
	 */
	public function share()
	{
		if (empty($_POST)) { 
			if (!isset($_GET['file'])) { header("Location: index.php?route=".$this->user_type); exit(); }
			
			$this->smarty->assign('file',$_GET['file']);
			$this->smarty->assign('user_type',$this->user_type);
			$this->smarty->assign('amigos',$this->user->getAmigos());
			$this->smarty->display('share.tpl');
		}
		else {
			$users = (isset($_POST['users']))?$_POST['users']:array();
			$profile = $_POST['profile'];
			$file = $_GET['file'];
			try {
				$this->user->share($file,$this->path,$users,$profile);
				header("Location: index.php?route=".$this->user_type.'&path='.$this->path);
			} catch (Exception $e) {
				$this->smarty->assign('user_type',$this->user_type);
				$this->smarty->assign('amigos',$this->user->getAmigos());
				$this->smarty->assign('fail_data',$e->getMessage());
				$this->smarty->display('share.tpl');
			}
		}
	}
	/**
	 * Editar permisos de compartir.
	 * 
	 * No realiza modificacion.
	 * Sencillamente elimina e inserta un nuevo grupo, reutilizacion de codigo.
	 * Optimizable.
	 * 
	 * @see Model_user::share()
	 */
	public function shareEdit()
	{
		if (empty($_POST)) {
			if (!isset($_GET['file']) || (!is_numeric($_GET['file']))) { header("Location: index.php?route=".$this->user_type); exit(); }
			
			$data = array('msg' => true);
			$id = $_GET['file'];

			$f = new Model_file($id);
			// debe ser archivo de $this
			if ($f->usuarios_idUsuario != $this->user->idUsuario) { header("Location: index.php?route=".$this->user_type); exit(); }
			$grp = new Model_group($f->grupos_idGrupo);
			$data = array_merge($data,$grp->getAllData());
			$data['profile'] = Model_profile::idToText($data['profile']);
			$data['msg'] = 'Compartido actualmente con permisos <acronym title="Acciones que podran llevar a cabo los usuarios a los que se le comparta">'.$data['profile'].'</acronym> para los usuarios:';
			
			$this->smarty->assign('advice',$data);
			
			$this->smarty->assign('file',basename($f->ruta,PATHINFO_BASENAME));
			$this->smarty->assign('file_id',$id);
			$this->smarty->assign('user_type',$this->user_type);
			$this->smarty->assign('amigos',$this->user->getAmigos());
			$this->smarty->display('shareEdit.tpl');
		}
		else {
			$mfile = new Model_file($_POST['file_id']);
			$grp = new Model_group($mfile->grupos_idGrupo);
			$grp->_dbDelete();
			$users = (isset($_POST['users']))?$_POST['users']:array();
			$profile = $_POST['profile'];
			$file = $_POST['file_name'];
			try {
				$this->user->share($file,$this->path,$users,$profile);
				header("Location: index.php?route=".$this->user_type.'&path='.$this->path);
			} catch (Exception $e) {
				header("Location: index.php?route=".$this->user_type.'&path='.$this->path);
			}
		}
	}
	/**
	 * Lista amigos del usuario.
	 * 
	 * Muestra una lista con los amigos del usuario que a su vez es un formulario.
	 * 
	 * @see Model_user::getAmigos()
	 */
	public function amigos()
	{
		$this->smarty->assign('user_type',$this->user_type);
		
		if (empty($_POST))
		{
			if (isset($_GET['amigos'])) $this->smarty->assign('del_amigos','Los usuarios <q>'.$_GET['amigos'].'</q> estan ahora en tu lista de amigos');
			$this->smarty->assign('amigos',$this->user->getAmigos());
			$this->smarty->display('amigos/list.tpl');
		}
		else //eliminar amigos seleccionados
		{
			if (!isset($_POST['users']))
			{
				$this->smarty->assign('amigos',$this->user->getAmigos());
				$this->smarty->assign('fail_data','Para eliminar usuarios primero debes seleccionarlos');
				$this->smarty->display('amigos/list.tpl');			
			}
			else
			{
				$this->user->delAmigos($_POST['users']);
				$this->smarty->assign('amigos',$this->user->getAmigos());
				$this->smarty->assign('del_amigos','Eliminados correctamente');
				$this->smarty->display('amigos/list.tpl');	
			}
		}
	}
	/**
	 * Inserta amigo.
	 * 
	 * Muestra o tramita el formulario para insertar amigos.
	 * 
	 * @see Model_user::addAmigos()
	 */
	public function amigosAdd()
	{
		if (empty($_POST)) $this->smarty->display('amigos/add.tpl');
		else
		{
			$amigos = (isset($_POST['amigos']))?$_POST['amigos']:'';
			$amigos = filterStr($amigos);
			$amigos = explode(' ',$amigos);
			try {
				$amigos_added = $this->user->addAmigos($amigos);
				$amigos_added = implode(' ',$amigos_added);
				header("Location: index.php?route=".$this->user_type."/amigos&amigos=$amigos_added");
			}
			catch (Exception $e){
				$this->smarty->assign('fail_data',$e->getMessage());
				$this->smarty->display('amigos/add.tpl');				
			}
		}
	}
	
	// DCOMPARTIDOS
	
	/**
	 * Muestra disco compartidos.
	 * 
	 * Muestra tanto los usuarios que comparten elementos al usuario
	 * como los elementos de un usuario.
	 *  
	 * @see Model_user::getUsers()
	 * @see Model_user::getShrFiles()
	 */
	public function dShare()
	{
		if (!isset($_GET['id'])) {
			$this->smarty->assign('u',$this->user->getUsers());
			$this->smarty->display($this->user_type.'/dShare.tpl');
		}
		else {
			$user = new Model_user($_GET['id']);
			$user_share = $user->_getAll();
			$user_share['type'] = Config::$map_css_clases[$user->userShrType($_GET['id'])];
			$this->smarty->assign('user_share',$user_share);
			$id_p = (isset($_GET['id_p']))?$_GET['id_p']:0;
			$this->smarty->assign('u',$this->user->getShrFiles($_GET['id'],$id_p,$rute));
			$this->smarty->assign('shr_rute',$rute);
			$this->smarty->display($this->user_type.'/dShareFiles.tpl');
		}
	}
	
	/**
	 * Menu de navegacion del disco local.
	 * 
	 * Forma el array con los datos necesarios para Smarty con la variable $path.
	 * Insertada funcionalidad para el separador.
	 * 
	 * @see $path
	 * @return array Datos para smarty
	 */
	private function getRute()
	{
		$rute = array(
			array('tit' => 'Dlocal','href' => 'index.php?route='.$this->user_type,'sep' => '&raquo;')
		);
		if (isset($_GET['path']))
		{
			$tits = explode(DIRSEP,trim($_GET['path'],DIRSEP));
			$path_format = '';
			for ($i = 0;$i<=count($tits)-1;$i++)
			{
				$path_format .= $tits[$i] . DIRSEP;
				$rute[] = array('tit' => $tits[$i],
				'href' => 'index.php?route='.$this->user_type.'&path='.DIRSEP.$path_format,
				'sep' => ($i == count($tits)-1)?'':'&raquo;'
				);
			}
		}
		return $rute;
	}
}

?>
