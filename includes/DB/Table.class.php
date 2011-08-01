<?php
namespace Base\DB;

class Table extends \Base\Object
{
	/**
	 * Engine Types
	 */
	const ENGINE_MYIASM = 'MyISAM';
	const ENGINE_INNODB = 'InnoDB';
	
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
	 * The engine type
	 * 
	 * @var string
	 */
	protected $_engineType = 'MyISAM';
	
	/**
	 * The columns
	 * 
	 * @var array
	 */
	protected $_column = array();
	
	/**
	 * Foreign Keys
	 * 
	 * @var array
	 */
	protected $_foreignKeys = array();
	
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
	 * @return \Base\DB\Table
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
	 * @return \Base\DB\Table
	 */
	public function install()
	{
		try{
			$columns = \Base\DB::query(sprintf('DESCRIBE %s', $this->_tableName));
			
			//now we update the table
			foreach($this->_column as $column){
				$this->_updateRow($column, $columns);
			}
		} catch(\Exception $e){
			//install a new table =3
			$query = "CREATE TABLE `{$this->_tableName}` (";
			$queries = array();
			foreach($this->_column as $column){
				$queries[] = "{$column['name']} {$column['definition']}";
			}
			if($this->_primaryKey) {
				$queries[] = "PRIMARY KEY  (`{$this->_primaryKey}`)";
			}
			if($this->_foreignKeys){
				foreach($this->_foreignKeys as $col => $rel){
					$queries[] = "FOREIGN KEY ($col) REFERENCES {$rel['relation']['table']}({$rel['relation']['column']}) ".
						"ON UPDATE {$rel['on']['update']} ON DELETE {$rel['on']['delete']}";
				}
			}
			$query .= implode(',',$queries);
			$query .= ") ENGINE=".$this->_engineType." DEFAULT CHARSET=utf8;";
			\Base\DB::query($query);
		}
		
		$this->_postInstall();
		return $this;
	}
	
	/**
	 * Set the engine to use
	 * 
	 * @param string $engine
	 * @return \Base\DB\Table
	 */
	public function setEngine($engine)
	{
		$this->_engineType = $engine;
		return $this;
	}
	
	/**
	 * Add a foreign keys
	 * 
	 * @param string $column
	 * @param string $table
	 * @param string $tableColumn
	 * @param string $update
	 * @param string $delete
	 * @return \Base\DB\Table
	 */
	public function addForeignKey($column, $table, $tableColumn = 'id', $update = 'CASCADE', $delete = 'CASCADE')
	{
		$this->_foreignKeys[$column] = array(
			'relation'	=> array(
				'table'		=> $table,
				'column'	=> $tableColumn
			),
			'on'		=> array(
				'update'	=> $update,
				'delete'	=> $delete
			)
		);
		return $this;
	}
	
	/**
	 * Function that's run post install - used for defining indexes etc
	 * 
	 * @return \Base\DB\Table
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
	 " @return \Base\DB\Table
	 */
	protected function _updateRow($defined, $definitions)
	{
		foreach($definitions as $definition){
			if($definition->Field == $defined['name']){
				return $this;
			}
		}
		\Base\DB::query("ALTER TABLE {$this->_tableName} ADD COLUMN {$defined['name']} {$defined['definition']}");
		return $this;
	}
}