<?php
/**
 * Base Model Class Abstract - all DB objects extend this one.
 */
abstract class BaseDBObject extends BaseObject
{
	/**
	 * Tablename for all these interactions
	 * 
	 * @var string
	 */
	protected $_tableName = '';
	
	/**
	 * Default field to load on
	 * 
	 * @var string
	 */
	protected $_idField = 'id';
	
	/**
	 * Store the field data
	 * 
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * Initialise the DB data
	 * 
	 * @param string $tableName
	 * @param string $idField
	 * @return BaseDBObject
	 */
	protected function _construct($tableName, $idField = 'id')
	{
		$this->_tableName = $tableName;
		$this->_idField= $idField;
		return parent::_construct();
	}
	
	/**
	 * Runs the save functionality - woohoo!
	 * 
	 * @return BaseDBObject
	 */
	public function save()
	{
		$data = $this->_prepareData();
		
		$this->_beforeSave();
		
		//prepare the data for saving
		$data = $this->_prepareData();
		
		//is this a valid save call
		if(!empty($data)){
			//do the actual save
			$id = DB::insertUpdate($this->_tableName, $data, $this->_idField);
			
			//what if something goes awry?
			if(false === $id){
				throw new Exception('Could not write to database - unknown error');
			} else {
				$this->setData($this->_idField, $id);
			}
			
			$this->_afterSave();
		}
		return $this;
	}
	
	/**
	 * Get the id field name
	 * 
	 * @return mixed
	 */
	public function getId()
	{
		return $this->getData($this->_idField);
	}
	
	/**
	 * Get the table description so only the relevant fields get saved
	 * 
	 * @return array
	 */
	protected function _prepareData()
	{
		//get the table field data
		if(!$this->_fields){
			$fieldData = DB::query('DESCRIBE '.$this->_tableName);
			foreach($fieldData as $field){
				$this->_fields[$field->Field] = $field;
			}
		}
		
		//build write array of only the appropriate fields
		$fields = array_keys($this->_fields);
		$writeData = array();
		foreach($this->getData() as $key=>$value){
			if(in_array($key, $fields)){
				if($value instanceof BaseObject){
					$writeData[$key] = $value->_toSql();
				} else {
					$writeData[$key] = $value;
				}
			}
		}
		
		return $writeData;
	}
	
	/**
	 * Prepare the data for save
	 * 
	 * @return BaseDBObject
	 */
	protected function _beforeSave()
	{
		return $this;
	}
	
	/**
	 * Anything that needs doing post-save
	 * 
	 * @return BaseDBObject
	 */
	protected function _afterSave()
	{
		return $this;
	}
	
	/**
	 * Prepare the object for load
	 * 
	 * @return BaseDBObject
	 */
	protected function _beforeLoad()
	{
		return $this;
	}
	
	/**
	 * Anything that needs doing post-load
	 * 
	 * @return BaseDBObject
	 */
	protected function _afterLoad()
	{
		return $this;
	}
	
	/**
	 * Load data from the database. Woohoo!
	 * 
	 * @return BaseDBObject
	 */
	public function load($id, $field = null)
	{
		$this->_beforeLoad();
		
		//field to load by id
		if(is_null($field)) $field = $this->_idField;
		
		//load the data
		$data = DB::load($this->_tableName, $id, $field);
		if(!$data){
			throw new Exception(sprintf("Unable to load by '%s' = '%s'", $field, $id));
		}
		$this->setData($data);
		
		$this->_afterLoad();
		return $this;
	}
}