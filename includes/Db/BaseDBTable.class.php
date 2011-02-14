<?php

class BaseDBTable extends BaseObject
{
	/**
	 * The table name
	 * 
	 * @var string
	 */
	protected $_tableName;
	
	/**
	 * The primary key field
	 * 
	 * @var string
	 */
	protected $_primaryKey = 'id';
	
	/**
	 * Constructor - set the table name
	 * 
	 * @param string $tableName
	 * @param string $key
	 * @return void
	 */
	public function __construct($tableName, $key = 'id')
	{
		$this->_tableName = $tableName;
		$this->_primaryKey = $key;
	}
	
	/**
	 * Add a column
	 * 
	 * @param string $columnName
	 * @param string $definition
	 * @param array $position
	 * @return BaseDBTable
	 */
	public function addColumn($columnName, $definition, $position = array())
	{
		$this->_column[$columnName] = array(
			'name'			=> $columnName,
			'definition'	=> $definition,
			'position'		=>$position
		);
		return $this;
	}
	
	/**
	 * Setup the Table
	 * 
	 * @return BaseDBTable
	 */
	public function install()
	{
		try{
			$columns = DB::query(sprintf('DESCRIBE %s', $this->_tableName));
			
			//now we update the table
			foreach($this->_column as $column){
				$this->_updateRow($column, $columns);
			}
		} catch(Exception $e){
			//install a new table =3
			$query = "CREATE TABLE `{$this->_tableName}` (";
			$queries = array();
			foreach($this->_column as $column){
				$queries[] = "{$column['name']} {$column['definition']}";
			}
			if($this->_primaryKey) {
				$queries[] = "PRIMARY KEY  (`{$this->_primaryKey}`)";
			}
			$query .= implode(',',$queries);
			$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			DB::query($query);
		}
		
		$this->_postInstall();
		return $this;
	}
	
	/**
	 * Function that's run post install - used for defining indexes etc
	 * 
	 * @return BaseDBTable
	 */
	protected function _postInstall()
	{
		return $this;
	}
	
	/**
	 * Update a table row
	 * 
	 * @param array $defined
	 * @param array $definitions
	 " @return BaseDBTable
	 */
	protected function _updateRow($defined, $definitions)
	{
		foreach($definitions as $definition){
			if($definition->Field == $defined['name']){
				return $this;
			}
		}
		DB::query("ALTER TABLE {$this->_tableName} ADD COLUMN {$defined['name']} {$defined['definition']}");
		return $this;
	}
}