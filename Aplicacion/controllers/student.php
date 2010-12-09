<?php
/**
 * student.php - Controlador de alumno
 * 
 * Controlador encargado de los alumnos
 * Funcionalidad especifica de un alumno
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package controllers
 */

class Controller_student extends Controller_user
{
	/**
	 * Inserta funcionalidad al constructor de usuario
	 * 
	 * Chequeo de sesion y carga de la interfaz para Smarty
	 */
	public function __construct()
	{
		parent::__construct();
		if (!isset($_SESSION['user']['idAlumno'])) { $this->call_action = false; $this->login(); }
		
		// Student exists, create interface
		$subm_discos_duros = array(
				array('enl' => 'index.php?route=student','tit' => 'Dlocal',
				'act' => 'disco_local'),
				array('enl' => 'index.php?route=student/dDocnt','tit' => 'Ddocente',
				'act' => 'disco_docente'),
				array('enl' => 'index.php?route=student/dShare','tit' => 'Dcompartidos',
				'act' => 'disco_compartido')
			);
		$subm_panel_de_control = array(
				array('enl' => 'index.php?route=student/data','tit' => 'Datos',
				'act' => 'info'),
				array('enl' => 'index.php?route=student/amigos','tit' => 'Amigos',
				'act' => 'datos_usuario'),
				array('enl' => 'index.php?route=student/unlog','tit' => 'Salir','act' => 'unlog')
			);
		$menu = array(
			array('menu' => 'Discos Duros','class' => 'disco','submenu' => $subm_discos_duros),
			array('menu' => 'Panel de Control','class' =>'control_panel','submenu' => $subm_panel_de_control)
			
		);
		$this->smarty->assign('menu',$menu);	
	}
	/**
	 * Disco docente
	 * 
	 * Muestra tanto las asignaturas a las que pertenece un alumno como las opciones en esa asignatura.
	 * 
	 * @see Model_user::getAsignaturas()
	 * @see Model_student::getEnun()
	 */
	function dDocnt()
	{
		if (!isset($_GET['id']) || !is_numeric($_GET['id']))
		{
			$this->smarty->assign('asignaturas',$this->user->getAsignaturas());
			$this->smarty->display('student/dDocnt.tpl');
		}
		else
		{
			try {
				$mdl_asig = new Model_asignatura($_GET['id']);
				$this->smarty->assign('files',$this->user->getEnun($mdl_asig));
				$this->smarty->assign('asig',$mdl_asig);
				$this->smarty->display('student/dDocntfiles.tpl');
			} catch (Exception $e) { 
				$this->smarty->assign('asd',$e->getMessage());
				//var_dumpa($e->getMessage());
				$this->smarty->display('student/dDocnt.tpl');
			}
			
		}
	}
}
?>