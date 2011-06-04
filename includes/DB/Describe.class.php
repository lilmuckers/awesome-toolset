<?php
namespace Base\DB;

class Describe extends \Base\Object
{
	/**
	 * Table Class
	 * 
	 * @var string
	 */
	protected $_tableClass = '\Base\DB\Table';
	
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
	 * @return \Base\DB\Table
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
	 * @return Base\DB\Describe
	 */
	public function install()
	{
		foreach($this->_tables as $table){
			$table->install();
		}
		return $this;
	}
}