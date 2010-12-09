<?php
/**
 * file.php - Modelo de File
 * 
 * Operaciones a realizar con los elementos
 * 
 * Trata los elementos a nivel de base de datos (elementos compartidos).
 *
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */

class Model_file extends Model_base
{
	/**
	 * Datos de la tupla en orden
	 * 
	 * @var array Campos de tupla
	 */
	protected $data = array('idFichero','usuarios_idUsuario','grupos_idGrupo','padre','ruta');
	
	/**
	 * Semaforo para renombrar
	 * @var integer posicion
	 */
	protected $rn_edit_pos = 0;
	
	/**
	 * Menu de navegacion para Disco compartido
	 * 
	 * Recorre la base de datos formando un array para Smarty
	 * 
	 * @param integer ID usuario
	 * @param string Tipo de usuario
	 * @return array Array to smarty
	 */
	public function getShrRute($idu,$user_type)
	{
		$sdata = array();
		$sdata[] = array('href' => "index.php?route=$user_type/dShare&id=$idu&id_p=".$this->idFichero,'tit' => pathinfo($this->ruta,PATHINFO_BASENAME));
		while ($this->padre != 0)
		{
			$stmt = $this->db->query("SELECT idFichero,padre,ruta FROM ficheros WHERE idFichero = ".$this->padre);
			$file = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt = null;
			$sdata[] = array(
				'href'	=> "index.php?route=$user_type/dShare&id=$idu&id_p=".$file['idFichero'],
				'tit'	=> pathinfo($file['ruta'],PATHINFO_BASENAME),
			);
			
			$this->_fillModel($file['idFichero']);
		}
		return $sdata;
	}

	/**
	 * Elimina elemento
	 * 
	 * Elimina en Db y fisicamente si no recibe argumentos, y solo en DB en caso contrario (editShare)
	 * 
	 * @param boolean Por defecto true
	 */
	public function dlFile($all = true)
	{
		if (is_dir($this->ruta))
		{
			$this->_dbDelete();
			$this->_dlFolder();
			if ($all == true) self::dlFolderPhy($this->ruta);
		}
		elseif (is_file($this->ruta)) { $this->_dbDelete(); if ($all == true) unlink($this->ruta); }
	}
	
	/**
	 * Eliminar directorio en DB
	 * 
	 * Recorre la base de datos con los padres para eliminar las tuplas relacionadas
	 * 
	 * @access private
	 */
	private function _dlFolder()
	{
		$stmt = $this->db->query("SELECT idFichero,ruta FROM ficheros 
		WHERE padre = ".$this->idFichero." AND usuarios_idUsuario = ".$this->usuarios_idUsuario);
		
		foreach ($stmt as $v)
		{
			$f = new Model_file($v['idFichero']);
			$f->_dbDelete();
			if (is_dir($v['ruta'])) $f->_dlFolder();
		}
	}
	/**
	 * Elimina directorio fisicamente
	 * 
	 * Funcion static que se incluye en este modelo por relacion semantica
	 * 
	 *  Elimina un directorio recursivamente
	 *  
	 * @param string Ruta del directorio
	 */
	public static function dlFolderPhy($parent_folder)
	{
		$data_in_parent_folder = new IteratorIterator(new DirectoryIterator($parent_folder));
		
		foreach ($data_in_parent_folder as $v)
		{
			if ($v->isDot()) continue;
			elseif ($v->isFile()) unlink($v->getPathName());
			elseif ($v->isDir()) self::dlFolderPhy($v->getPathName());
		}
		rmdir($parent_folder);
	}
	/**
	 * Renombra un elemento
	 * 
	 * Actualiza la tupla correspondiente
	 * 
	 * En caso de ser directorio, lo hace recursivamente
	 * 
	 * @param string Nuevo nombre
	 * @see rnFolder()
	 */
	public function rnFile($new_string)
	{
			$ruta_ant = $this->ruta;
			$this->rename_set_pos();
			$this->rename_ruta($new_string);
			if (!rename($ruta_ant,$this->ruta)) throw new Exception('El archivo a editar no existe');
			$this->_dbUpdate(array('ruta'));
			if (is_dir($this->ruta)) $this->rnFolder($new_string);
	}
	/**
	 * Renombra todas las tuplas jerarquica y recursivamente por debajo.
	 * 
	 * @param string Nuevo nombre
	 * @see rename_ruta()
	 * @see set_pos()
	 */
	public function rnFolder($new_string)
	{
		$stmt = $this->db->query("SELECT idFichero,ruta FROM ficheros 
		WHERE padre = ".$this->idFichero." AND usuarios_idUsuario = ".$this->usuarios_idUsuario);
		
		foreach ($stmt as $v)
		{
			$f = new Model_file($v['idFichero']);
			$f->set_pos($this->rn_edit_pos);
			$f->rename_ruta($new_string);
			$f->_dbUpdate(array('ruta'));
			$f->rnFolder($new_string);
			
		}
	}
	/**
	 * Asigna posicion de edicion para poder renombrar tuplas hijas
	 * 
	 * @see rename_ruta() 
	 */
	public function rename_set_pos()
	{
		$v = explode(DIRSEP,$this->ruta);
		$this->rn_edit_pos = count($v)-1;
	}
	private function set_pos($id) { $this->rn_edit_pos = $id; }
	
	/**
	 * Script para renombrar en DB hijos de un directorio
	 * 
	 * 	Puesto que las rutas de los archivos se almacenan completas es necesario un script como este.
	 * 
	 * 	Ejemplo:
	 * 
	 * 		/files/student/fran689/local/dir_to_rename
	 * 		/files/student/fran689/local/dir_to_rename/file1.txt
	 * 		/files/student/fran689/local/dir_to_rename/another_dir
	 * 		/files/student/fran689/local/dir_to_rename/another_dir/file1.txt
	 *		/files/student/fran689/local/dir_to_rename/another_dir/file1.txt
	 *
	 *		/files/student/fran689/local/ |-- dir_to_rename -- Posicion 5 |
	 *
	 *		Gracias a la posicion editamos en consecuencia en sus hijos
	 * @param stirng Nuevo nombre
	 */
	public function rename_ruta($new_string)
	{
		$ruta_por_partes = explode(DIRSEP,$this->ruta);
		$ruta_por_partes[$this->rn_edit_pos] = $new_string;
		$this->ruta = implode(DIRSEP,$ruta_por_partes);
	}
}

?>
