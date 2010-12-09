<?php
/**
 * student.php - Controlador de profesor
 * 
 * Controlador encargado de los profesores
 * Funcionalidad especifica de un profesor
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package controllers
 */

class Controller_teacher extends Controller_user
{
	/**
	 * Inserta funcionalidad al constructor de usuario
	 * 
	 * Chequeo de sesion y carga de la interfaz para Smarty
	 */
	public function __construct()
	{
		parent::__construct();
		if (!isset($_SESSION['user']['idProfesor'])) { $this->call_action = false; $this->login(); }
		
		// Teacher exists, create interface
		$subm_discos_duros = array(
				array('enl' => 'index.php?route=teacher','tit' => 'Dlocal',
				'act' => 'disco_local'),
				array('enl' => 'index.php?route=teacher/dDocnt','tit' => 'Ddocente',
				'act' => 'disco_docente'),
				array('enl' => 'index.php?route=teacher/dShare','tit' => 'Dcompartidos',
				'act' => 'disco_compartido')
			);
		$subm_panel_de_control = array(
				array('enl' => 'index.php?route=teacher/data','tit' => 'Datos',
				'act' => 'info'),
				array('enl' => 'index.php?route=teacher/amigos','tit' => 'Amigos',
				'act' => 'datos_usuario'),
				array('enl' => 'index.php','tit' => 'Activar Docencia',
				'act' => 'help'),
				array('enl' => 'index.php?route=teacher/unlog','tit' => 'Salir',
				'act' => 'unlog')
			);
		$menu = array(
			array('menu' => 'Discos Duros','class' => 'disco','submenu' => $subm_discos_duros),
			array('menu' => 'Panel de Control','class' => 'control_panel','submenu' => $subm_panel_de_control)
			
		);
		$this->smarty->assign('menu',$menu);
	}
	/**
	 * Disco docente
	 * 
	 * Muestra tanto las asignaturas a las que pertenece un profesor como las opciones en esa asignatura.
	 * 
	 * @see Model_user::getAsignaturas()
	 * @see Model_teacher::getEnun()
	 * @see Model_teacher::getPrac()
	 */
	function dDocnt()
	{
		if (!isset($_GET['id']))
		{
			$this->smarty->assign('asignaturas',$this->user->getAsignaturas());
			$this->smarty->display('teacher/dDocnt.tpl');
		}
		else
		{
			$mdl_asig = new Model_asignatura($_GET['id']);
			$this->smarty->assign('asig',$mdl_asig);
			$this->smarty->assign('files',$this->user->getEnun($mdl_asig));
			$this->smarty->assign('prac',$this->user->getPrac($mdl_asig));
			$this->smarty->display('teacher/dDocntfiles.tpl');
		}
	}
}


?>