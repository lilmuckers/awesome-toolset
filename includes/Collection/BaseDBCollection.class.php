<?php

abstract class BaseDBCollection extends BaseCollection
{
	/**
	 * Default object class for adding new items
	 * 
	 * @var string
	 */
	protected $_itemClass = 'BaseDBObject';
	
	/**
	 * The fields to select
	 * 
	 * @var array
	 */
	protected $_select = array('*');
	
	/**
	 * store the tablename
	 * 
	 * @var string
	 */
	protected $_tableName;
	
	/**
	 * Setup the collection object for the DB stuff
	 * 
	 * @param string $tableName
	 * @param string $itemClass
	 * @param string $idField
	 * @return void
	 */
	protected function _construct($tableName, $itemClass = 'BaseDBObject', $idField = 'id')
	{
		$this->_tableName = $tableName;
		$this->_itemClass = $itemClass;
		$this->_idField = $idField;
		return parent::_construct();
	}
	
	/**
	 * load the data and push it into the right places
	 * 
	 * @return BaseDBCollection
	 */
	public function load()
	{
		$this->_beforeLoad();
		$data = DB::select($this->_tableName, $this->_renderWhere(), $this->_select, 'AND', $this->_orders);
		if(count((array)$data) > 0){
			foreach((array) $data as $row){
				$object = new $this->_itemClass($row);
				$this->addItem($object);
			}
		}
		$this->_afterLoad();
		return $this;
	}
	
	/**
	 * Add a field to select
	 * 
	 * @param string $fieldName
	 * @return BaseDBCollection
	 */
	public function addSelect($fieldName)
	{
		if(count($this->_select) == 0 && $this->_select[0] == '*'){
			if($fieldName != $this->_idField){
				$this->_select = array($this->_idField, $fieldName);
			} else {
				$this->_select = array($fieldName);
			}
		}
		$this->_select = array_merge($this->_select, $fieldName);
		return $this;
	}
	
	/**
	 * Render the where statements
	 * 
	 * @return array
	 */
	protected function _renderWhere()
	{
		$return = array();
		foreach($this->_filters as $filter){
			$return[$filter['field']] = $filter['value'];
		}
		return $return;
	}
}