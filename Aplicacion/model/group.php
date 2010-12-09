<?php
/**
 * group.php - Modelo de grupo
 * 
 * Operaciones a realizar con los grupos
 * 
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
class Model_group extends Model_base
{
	/**
	 * Datos de la tupla en orden
	 * 
	 * @var array Campos de tupla
	 */
	protected $data = array('idGrupo','perfiles_idPerfil','nombreGrupo');

	/**
	 * Asigna usuarios a un grupo
	 * 
	 * @param array Array de ID's usuario
	 */
	public function assignUsers(array $users)
	{
		foreach ($users as $v)
		{
			$this->db->exec("INSERT INTO usuarios_has_grupos (usuarios_idUsuario,grupos_idGrupo) 
			VALUES('$v','$this->idGrupo')");
		}
	}
	/**
	 * Retorna todos los datos posibles de un grupo
	 * 
	 * Usuarios, permisos, css_var ...
	 * 
	 * @return array Datos divididos en subarrays (users,profile)
	 */
	public function getAllData()
	{
		if (!is_numeric($this->idGrupo)) throw new Exception('(!) ' . get_class($this) . ': getValidData() need object fill');
		$data = array('users' => null,'profile' => $this->perfiles_idPerfil);
		
		$sql = "SELECT idUsuario,nombre,apellido1,apellido2 FROM usuarios U,usuarios_has_grupos UHG 
		WHERE UHG.grupos_idGrupo = ".$this->idGrupo." AND U.idUsuario = UHG.usuarios_idUsuario";
		$stmt = $this->db->query($sql);
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($users as $k => $v) //css icons
		{
			$u = new Model_user();
			$type = $u->userShrType($v['idUsuario']);
			$users[$k]['type'] = Config::$map_css_clases[$type];
		}
		$data['users'] = $users;
		return $data;
	}
	/**
	 * Permisos del perfil
	 * 
	 * Optimizable. OO concept, new model.
	 * 
	 * @return array $permisos
	 */
	public function getPermission()
	{
		// a modificar OO-concept
		$sql = "SELECT renombrar,eliminar FROM perfiles WHERE idPerfil = ".$this->perfiles_idPerfil." LIMIT 0,1";
		$stmt = $this->db->query($sql);
		$stmt = $stmt->fetch(PDO::FETCH_ASSOC);
		return $stmt;
	}
	
	/**
	 * Estado de la docencia
	 * 
	 * Si el nombre del grupo existe el profesor tiene activada la docencia.
	 * 
	 * @return mixed true si no encuentra nada, ID en caso contrario
	 */
	public function getGroupState()
	{
		$sql = "SELECT idGrupo FROM grupos WHERE nombreGrupo = '".$this->nombreGrupo."'";
		$stmt = $this->db->query($sql);
		$st = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($st)) return true; else return $st[0];
	}
}

?>
