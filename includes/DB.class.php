<?php

class DB {
	protected static $_db;
	
	/**
	 * Connect to the database using defined settings
	 * 
	 * @return void
	 */
	protected static function _connect(){
		self::$_db = new mysqli(XBL_DB_HOST, XBL_DB_USER, XBL_DB_PASSWORD, XBL_DB_DB);
		self::$_db->query("SET NAMES 'utf8'");
	}
	
	/**
	 * Return the results of a query without the middle man
	 * 
	 * @param string $sql
	 * @return array
	 */
	public static function query($sql){
		if(!self::$_db){
			self::_connect();
		}
		$result = self::$_db->query($sql);
		if(false === $result){
			throw new Exception('silly billy, query error: '.$sql);
		}
		if($result->num_rows > 0){
			$return = array();
			while($row = $result->fetch_object()){
				$return[] = $row;
			}
			return $return;
		}
		return true;
	}
	
	/**
	 * General select query performer
	 * 
	 * @param string $table
	 * @param array $conditions
	 * @param string|array $fields
	 * @param string|array $limit
	 * @param array $join
	 * @return array
	 */
	public static function select($table, Array $conditions, $fields = '*', $whereJoin = 'AND', $orderby = null, $limit = null){
		$tablePrefix = 'main_table';
		
		//format select fields
		if(!is_array($fields)) $fields = array($fields);
		foreach($fields as $key=>$field){
			$fields[$key] = $tablePrefix.'.'.$field;
		}
		
		$select = "SELECT ".implode(', ', $fields);
		$select .= " FROM {$table} {$tablePrefix}";
		
		//format conditions
		if(!empty($conditions)){
			$where = self::_where($conditions, $whereJoin);
			$select .= $where;
		}
		
		//format order by
		if(!is_null($orderby)){
			$order = self::_orderBy($orderby);
			$select .= $order;
		}
		
		//limit
		if(!is_null($limit)){
			$select .= " LIMIT ";
			if(is_array($limit)){
				$select .= implode(',', $limit);
			} else {
				$select .= $limit;
			}
		}
		
		//join
		
		return self::query($select);
	}
	
	/**
	 * Format a where statement
	 * 
	 * @param array $conditions
	 * @return string
	 */
	protected static function _where($conditions, $joiner = 'AND'){
		$where = array();
		foreach($conditions as $field => $expr){
			if(is_array($expr)){
				foreach($expr as $e=>$v){
					$e = strtolower($e);
					if($e == 'eq'){
						$where[] .= $field.' = \''.self::_escape($v).'\' ';
					} elseif ($e == 'neq') {
						$where[] .= $field.' != \''.self::_escape($v).'\' ';
					} elseif ($e == 'in'){
						if(!is_array($v)){
							$v = array($v);
						}
						array_walk($v, array('DB', '_escape'));
						$where[] .= $field.' IN (\''.implode("','", $v).'\') ';
					}
				}
			} else {
				$where[] .= $field.' = \''.self::_escape($expr).'\' ';
			}
		}
		return ' WHERE '.implode(' '.$joiner.' ', $where).' ';
	}
	
	/**
	 * Format order by statment
	 * 
	 * @param array $orderBy
	 * @return string
	 */
	protected static function _orderBy($orderBy){
		$order = array();
		if(!is_array($orderBy)){
			$orderBy = array($orderBy);
		}
		foreach($orderBy as $field => $direction){
			if(!in_array(strtoupper($direction), array('ASC', 'DESC'))){
				$direction = 'DESC';
			}
			$order[] = "{$field} {$direction}";
		}
		return ' ORDER BY '.implode(',',$order);
	}
	
	/**
	 * Escape the string for SQL usage
	 * 
	 * @param string $value
	 * @return string
	 */
	protected static function _escape($value){
		if(!self::$_db){
			self::_connect();
		}
		return self::$_db->real_escape_string($value);
	}
	
	/**
	 * Load one record
	 * 
	 * @param string $table
	 * @param string $id
	 * @param string $idfield
	 * @return stdClass
	 */
	public static function load($table, $id, $idField = 'id'){
		$load = self::select($table, array($idField=>$id));
		return true !== $load ? current($load) : false;
	}
	
	/**
	 * Insert a new row into the given table
	 * 
	 * @param string $table
	 * @param array $data
	 * @return bool
	 */
	public static function insert($table, $data = array()){
		$inserts = array();
		foreach($data as $key=>$value){
			if(strtoupper(substr($value, 0, 6)) == 'SELECT'){
				$inserts[$key] = "({$value})";
			} elseif(is_null($value)){
				$inserts[$key] = "NULL";
			} elseif(false !== $value) {
				$inserts[$key] = "'".self::_escape($value)."'";
			}
		}
		$sql = "INSERT INTO {$table} (".implode(',',array_keys($inserts)).") VALUES ";
		$sql .= "(".implode(',',$inserts).")";
		return (bool) DB::query($sql);
	}
	
	/**
	 * Inserts or Updates a row of the ID is specified
	 * will return the id
	 * 
	 * @param string $table
	 * @param array $data
	 * @param string $idField
	 * @return mixed
	 */
	public static function insertUpdate($table, $data, $idField = 'id'){
		if(array_key_exists($idField, $data) && !empty($data[$idField])){
			//update function
			$update = self::update($table, $data, array($idField=>$data[$idField]));
			return $update ? $data[$idField] : false;
		}
		//insert function
		$insert = self::insert($table, $data);
		if($insert){
			return current(self::query("SELECT LAST_INSERT_ID() as id"))->id;
		}
		return false;
	}
	
	/**
	 * Update rows based on conditions
	 * 
	 * @param string $table
	 * @param array $data
	 * @param array $conditions
	 * @return bool
	 */
	public static function update($table, $data, $conditions = array(), $whereJoin = 'AND'){
		$sql = "UPDATE {$table} SET ";
		$updates = array();
		foreach($data as $key=>$value){
			if(in_array($key, array_keys($conditions))){
				continue;
			}
			if(strtoupper(substr($value, 0, 6)) == 'SELECT'){
				$updates[] = "{$key} = ({$value})";
			} elseif(is_null($value)){
				$updates[] = "{$key} = NULL";
			} elseif(false !== $value) {
				$updates[] = "{$key} = '".self::_escape($value)."'";
			}
		}
		$sql .= implode(', ', $updates);
		
		//format conditions
		if(!empty($conditions)){
			$where = self::_where($conditions, $whereJoin);
			$sql .= $where;
		}
		return self::query($sql);
	}
}