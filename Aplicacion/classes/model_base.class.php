<?php
/**
 * model_base.class.php - Modelo base
 * 
 * Modelo del que heredan todos los modelos.
 * Clase abstracta donde se implemente el patron 'active record' y se realiza la conexion a la base de datos
 * 
 * Cada modelo tendra los datos de la tabla correspondiente en su base de datos. En esta clase se definen los
 * metodos necesarios para el dinamismo modelo-DB.
 * A su vez se instancia el objeto PDO, encargado de la conexion a la DB.
 * 
 * @author David Medina <aizmuth@gmail.com>
 * @author Fernando Pintado <alatul@gmail.com>
 * @package model
 */
abstract class Model_base
{
	/**
	 * PDO object
	 * 
	 * Encargado de los accesos a base de datos
	 * 
	 * @var object PDO
	 */
	protected $db;
	
	/**
	 * Modelo instanciado
	 * 
	 * @access private
	 * @var string $model_type
	 */
	private $model_type = '';
	
	/**
	 * Constructor
	 * 
	 * Instancia el objeto PDO y lo asigna en $db
	 * Asigna el tipo de modelo a $model_type
	 * 
	 * Efecuta diferentes operaciones en base al argumento que le llegue (variable) 
	 * rellenando los datos del modelo
	 * 
	 * @see $db
	 * @see $model_type
	 */
	public function __construct()
	{
		$this->db = new PDO(Config::$db_driver.":host=".Config::$db_host.";dbname=".Config::$db_db,
							Config::$db_user,Config::$db_pass);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->model_type = array_pop(explode('_',get_class($this)));
		if (isset($this->data)) $this->data = array_fill_keys($this->data,'');
		else throw new Exception('(!) ' . get_class($this) . ': Keys of data array needed');
		if (func_num_args() == 1) {
			if (is_array(func_get_arg(0))) $this->_fillValues(func_get_arg(0));
			elseif (is_numeric(func_get_arg(0))) $this->_fillModel(func_get_arg(0));
		}
	}
	public function __destruct() { $this->db = null; }
	protected function __get($prop) { return $this->data[$prop]; }
	/**
	 * Datos del modelo
	 * 
	 * @return array array asociativo con los datos del modelo.
	 */
	public function _getAll() { return $this->data; }
	
	/**
	 * Asigna valores a los datos del modelo (tupla)
	 * 
	 * @param string $prop Campo
	 * @param string $value Valor
	 */
	protected function __set($prop,$value)
	{
		if (array_key_exists($prop,$this->data)) $this->data[$prop] = $value;
		else throw new Exception('(!) ' . get_class($this) . ": Key '$prop' don't exists");
	}
	/**
	 * Rellena con los datos pasados.
	 * 
	 * Array mode
	 * 
	 * @param array $values
	 * @return unknown_type
	 */
	public function _fillValues($values)
	{
		if (!is_array($values)) return false;
		//Adapt $values for $this->data keys format to merge.
		foreach($values as $k => $v) if (!array_key_exists($k,$this->data)) unset($values[$k]);
		$this->data = array_merge($this->data,$values);
		return true;
	}
	/**
	 * Rellena datos desde DB
	 * 
	 * DB mode
	 * Con la ID y $model_type se rellena el modelo con los datos de la tupla correspondiente.
	 * 
	 * @see _fillValues()
	 * @param integer $id ID
	 */
	public function _fillModel($id)
	{
		if (!is_numeric($id)) throw new exception('(!) ' . get_class($this) . ': fillModel() need int');
		
		$keys = array_keys($this->data);
		$table_name = Config::$map_tables[$this->model_type];
		$id_n = $keys[0];
		$this->$id_n = $id;
		$stmt = $this->db->query("SELECT * FROM $table_name WHERE $id_n = $id LIMIT 0,1");
		
		$this->_fillValues($stmt->fetch(PDO::FETCH_ASSOC));
	}
	
	/**
	 * Inserta modelo en la DB
	 * 
	 * Si cumple con las reglas definidas en la DB se inserta una nueva tupla
	 * Recordemos que el ID esta marcado como 'auto_increment' en la DB
	 */
	public function _dbInsert()
	{
		$user_data = array_keys($this->data);
		$table_set = implode(',',$user_data);
		$table_values = implode(',:',$user_data); $table_values = ':'.$table_values;
		$table_name = Config::$map_tables[$this->model_type];
		
		$stmt = $this->db->prepare("INSERT INTO $table_name ($table_set) VALUES ($table_values)");
		foreach ($user_data as $v) $stmt->bindParam(':'.$v,$this->data[$v]);
		$stmt->execute();
		$id = $this->db->lastInsertId();
		return (is_numeric($id))?$id:false;
	}
	
	/**
	 * Elimina modelo de la DB
	 * 
	 * Si cumple con las reglas definidas en la DB y la tupla existe la elimina de la DB.
	 */
	public function _dbDelete()
	{
		$keys = array_keys($this->data);
		$id_n = $keys[0];
		$id_v = $this->$keys[0];
		$table_name = Config::$map_tables[$this->model_type];
		$sql = "DELETE FROM $table_name WHERE $id_n = $id_v";
		
		$this->db->exec($sql);
	}
	/**
	 * Actualiza el modelo en la DB
	 * 
	 * Actualiza los valores que se le indiquen en el argumento
	 * 
	 * @param array $data Array simple con los campos a actualizar
	 */
	public function _dbUpdate(array $data)
	{
		foreach($data as $k => $v) if (!array_key_exists($v,$this->data)) unset($data[$k]);
		$keys = array_keys($this->data);
		$sql = array();
		foreach ($data as $v) $sql[] = $v." = '".$this->data[$v]."'";
		$sql = implode(',',$sql);
		$table_name = Config::$map_tables[$this->model_type];
		$sql = "UPDATE $table_name SET $sql WHERE ".$keys[0].'='.$this->$keys[0];
		
		$this->db->exec($sql);
	}
}

?>
