<?php

class BaseDBDescribe extends BaseObject
{
	/**
	 * Table Class
	 * 
	 * @var string
	 */
	protected $_tableClass = 'BaseDBTable';
	
	/**
	 * Array of tables
	 * 
	 * @var array
	 */
	protected $_tables = array();
	
	/**
	 * Create a new table object
	 * 
	 * @param string $tableName
	 * @return BaseDBTable
	 */
	protected function _addTable($tableName)
	{
		$tableClass = $this->_tableClass;
		$table = new $tableClass($tableName);
		
		$this->_tables[] = $table;
		
		return $table;
	}
	
	/**
	 * Install the tables
	 * 
	 * @return BaseDBDescribe
	 */
	public function install()
	{
		foreach($this->_tables as $table){
			$table->install();
		}
		return $this;
	}
}