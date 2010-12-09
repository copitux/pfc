<?php
/**
 * admin.php - Modelo de administrador
 * 
 * Modelo para el administrador
 * Singleton
 * 
 * Optimizable
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */

class Model_admin extends Model_base
{
	protected $data = array('admin');
	
	public function getDB() { return $this->db; }
}