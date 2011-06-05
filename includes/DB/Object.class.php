<?php
namespace Base\DB;

/**
 * Base Model Class Abstract - all DB objects extend this one.
 */
abstract class Object extends \Base\Object
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
	 * We want to use a non-standard exception
	 * 
	 * @var string
	 */
	protected $_exceptionClass = '\Base\Exception\DB';
	
	/**
	 * Auto populate fields with associated function on row update
	 * 
	 * @var array
	 */
	protected $_autoUpdateFields = array(
		'updated_at'	=> '_dateTime'
	);
	
	/**
	 * Auto populate fields with associated function on row create
	 * 
	 * @var array
	 */
	protected $_autoInsertFields = array(
		'created_at'	=> '_dateTime'
	);
	
	/**
	 * Initialise the DB data
	 * 
	 * @param string $tableName
	 * @param string $idField
	 * @return \Base\DB\Object
	 */
	protected function _construct($tableName, $idField = 'id')
	{
		$this->_tableName = $tableName;
		$this->_idField = $idField;
		return parent::_construct();
	}
	
	/**
	 * Runs the save functionality - woohoo!
	 * 
	 * @return \Base\DB\Object
	 */
	public function save()
	{
		//we want to update, yes yes
		$this->setFlag('save', true);
		
		//before save functions
		$this->_beforeSave();
		
		//prepare the data for saving
		$data = $this->_prepareData();
		
		//is this a valid save call
		if(!empty($data) && $this->getFlag('save') === true){
			//do the actual save
			$id = \Base\DB::insertUpdate($this->_tableName, $data, $this->_idField);
			
			//what if something goes awry?
			if(false === $id){
				$this->_error('Could not write to database - unknown error');
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
			$fieldData = \Base\DB::query('DESCRIBE '.$this->_tableName);
			foreach($fieldData as $field){
				$this->_fields[$field->Field] = $field;
			}
		}
		
		//build write array of only the appropriate fields
		$fields = array_keys($this->_fields);
		$writeData = array();
		foreach($this->getData() as $key=>$value){
			if(in_array($key, $fields)){
				if($value instanceof \Base\Object){
					$writeData[$key] = $value->_toSql();
				} else {
					$writeData[$key] = $value;
				}
			}
		}
		
		//run the auto-population on row creation
		if(!$this->getId()){
			foreach($this->_autoInsertFields as $field=>$function){
				if(in_array($field, $fields)){
					$writeData[$field] = call_user_func(array($this, $function));
				}
			}
		}
		
		//run the autopopulation on row update - so everytime the row is saved in other words
		foreach($this->_autoUpdateFields as $field=>$function){
			if(in_array($field, $fields)){
				$writeData[$field] = call_user_func(array($this, $function));
			}
		}
		
		return $writeData;
	}
	
	/**
	 * Prepare the data for save
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _beforeSave()
	{
		return $this;
	}
	
	/**
	 * Anything that needs doing post-save
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _afterSave()
	{
		return $this;
	}
	
	/**
	 * Prepare the object for load
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _beforeLoad()
	{
		return $this;
	}
	
	/**
	 * Anything that needs doing post-load
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _afterLoad()
	{
		return $this;
	}
	
	/**
	 * Load data from the database. Woohoo!
	 * 
	 * @return \Base\DB\Object
	 */
	public function load($id, $field = null)
	{
		$this->_beforeLoad();
		
		//field to load by id
		if(is_null($field)) $field = $this->_idField;
		
		//load the data
		$data = \Base\DB::load($this->_tableName, $id, $field);
		if(!$data){
			$this->_error(sprintf("Unable to load by '%s' = '%s'", $field, $id));
		}
		$this->setData($data);
		
		$this->_afterLoad();
		return $this;
	}
	
	/**
	 * Delete this object
	 * 
	 * @return \Base\DB\Object
	 */
	public function delete()
	{
		//check if it's loaded before deleting
		if($this->getId()){
			$this->_beforeDelete();
			
			\Base\DB::delete($this->_tableName, $this->getId(), $this->_idField);
			
			$this->_afterDelete();
		}
		return $this;
	}
	
	/**
	 * Before delete we do these actions
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _beforeDelete()
	{
		return $this;
	}
	
	/**
	 * After delete we do a cleanup action
	 * 
	 * @return \Base\DB\Object
	 */
	protected function _afterDelete()
	{
		//unset all the data from the object
		$this->unsData();
		return $this;
	}
}
