<?php

/**
 * config.php - Datos generales de configuracion
 * 
 * Contiene la gran mayoria de datos que necesita la aplicacion guardados
 * como variables estaticas para poder ser accedidas mas facilmente.
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package base
 */

class Config
{	

/**
 * Titulo de la pagina
 * 
 * @access public
 * @staticvar string
 */
	public static $title_page = 'UEMC - Disco duro virtual para la docencia';
	
/**
 * Driver de conexion a base de datos
 * 
 * @access public
 * @staticvar string
 */
	public static $db_driver = 'mysql';
	
/**
 * Direccion del servidor de base de datos
 * 
 * @access public
 * @staticvar string
 */
	public static $db_host = 'localhost';
	
/**
 * Nombre de usuario de la base de datos
 * 
 * @access public
 * @staticvar string
 */
	public static $db_user = '';
	
/**
 * Password para el usuario
 * 
 * @access public
 * @staticvar string
 */
	public static $db_pass = '';
	
/**
 * Base de datos a usar
 * 
 * @access public
 * @staticvar string
 */
	public static $db_db = 'proyecto';
	
/**
 * Traducciones de diferentes variables
 * 
 * Debido a la creacion de las tablas de la base de datos en 'spanish' y diferentes clases
 * del modelo en ingles se necesita la traduccion para mantener el dinamismo.
 * 
 * Optimizable
 *
 * @access public
 * @staticvar array
 */
	public static $map_tables = array(
		'user'		=> 'usuarios',
		'student'	=> 'alumnos',
		'teacher'	=> 'profesores',
		'file'		=> 'ficheros',
		'group'		=> 'grupos',
		'profile'	=> 'perfiles',
		'asignatura'=> 'asignaturas',
		'curso'		=> 'cursos',
		'carrera'	=> 'carreras',
		'amigo'		=> 'amigos',
	);
	public static $map_css_clases = array(
		'admin'	=> 'admin',
		'student'	=> 'alumno',
		'teacher'	=> 'profesor',
		'file'		=> 'ficheros',
		'group'		=> 'grupos',
		'profile'	=> 'perfiles',
		'asignatura'=> 'asignaturas',
		'curso'		=> 'cursos',
		'carrera'	=> 'carreras'
	);
	public static $map_ids = array(
		'user'		=> 'Usuario',
		'student'	=> 'Alumno',
		'teacher'	=> 'Profesor',
		'file'		=> 'Fichero'
	);
	
/**
 * Directorio donde guardar los archivos
 * 
 * Bajo este directorio esta toda la estructura donde estaran los archivos de los usuarios.
 * Debe tener permisos de escritura para el propietario de apache.
 * 
 * @access public
 * @staticvar string
 */	
	public static $files_path = 'files';

/**
 * Tamaño en megabyts permitidos en la subida de archivos
 * 
 * Debe ir en concordancia a las directivas de PHP.
 * 
 * @link http://es.php.net/manual/es/ini.core.php#ini.upload-max-filesize
 * @access public
 * @staticvar string
 */
	public static $file_size = 2;
	
/**
 * Estilo para la pagina.
 * 
 * @access public
 * @staticvar string
 */
	public static $css_theme = 'basic';
	
/**
 * Directorio donde se encuentra el core de smarty
 * 
 * @access public
 * @staticvar string
 */
	public static $smarty_libs = 'smartylibs';
	
/**
 * Directorio donde estaran las plantillas
 * 
 * @access public
 * @staticvar string
 */
	public static $smarty = 'smarty';
	
/**
 * Directorio para los controladores
 * 
 * @access public
 * @staticvar string
 */
	public static $controllers_path = 'controllers';

/**
 * Directorio para los modelos
 * 
 * @access public
 * @staticvar string
 */
	public static $model_path = 'model';
	
	public static $view_path = '';
	
/**
 * Directorio para las clases base
 * 
 * @access public
 * @staticvar string
 */
	public static $classes_path = 'classes';
	

	

/**
 * Busca los archivos .css dentro del theme escogido
 * 
 * @access public
 * @return array Array de archivos con formato .css
 */
	public static function getCssFiles()
	{
		$data = array();
		// '/' separator to work in Firefox/IE with Win/unix
		$theme_path = 'themes' . '/' . self::$css_theme . '/';
		
		$rcss = new DirectoryIterator($theme_path);
		foreach ($rcss as $css)
		{	
			//check .files
			$css_parts = pathinfo($css->getPathname());
			if ($css->isFile() && $css->isReadable() && ($css_parts['extension'] == 'css'))
				array_push($data,$theme_path.$css->getFilename());
		}
		return $data;
	}
}

?>
