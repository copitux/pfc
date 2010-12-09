<?php
/**
 * profile.php - Modelo de permisos
 * 
 * Operaciones a realizar con los permisos
 * 
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
class Model_profile extends Model_base
{
	protected $data = array('idPerfil','renombrar','eliminar');
	
	/**
	 * Funcion para Smarty y css
	 * 
	 * @param integer ID
	 * @return string $text
	 */
	public static function idToText($id)
	{
		$text = null;
		switch ($id)
		{
			case 1:
				$text = 'renombrar y eliminar';
			break;
			case 2:
				$text = 'eliminar';
			break;
			case 3:
				$text = 'renombrar';
			break;
			case 4:
				$text = 'nulos';
			break;
			//no need default porque ya hubiera dado error en DB
		}
		return $text;
	}
}

?>