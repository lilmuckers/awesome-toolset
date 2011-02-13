<?php

abstract class BaseCollection extends BaseObject implements IteratorAggregate, Countable
{
	/**
	 * Constants for direction ordering.
	 */
	const SORT_ORDER_ASC	= 'ASC';
	const SORT_ORDER_DESC	= 'DESC';
	
	/**
	 * Array of Caffeine_Object to iterate through
	 * 
	 * @var array
	 */
	protected $_items = array();
	
	/**
	 * The id field to load by etc
	 * 
	 * @var string
	 */
	protected $_idField = 'id';
	
	/**
	 * Default object class for adding new items
	 * 
	 * @var string
	 */
	protected $_itemClass = 'BaseObject';
	
	/**
	 * Order configuration
	 *
	 * @var array
	 */
	protected $_orders = array();
	
	/**
	 * Filters configuration
	 *
	 * @var array
	 */
	protected $_filters = array();
	
	/**
	 * Filter rendered flag
	 *
	 * @var bool
	 */
	protected $_isFiltersRendered = false;
	
	/**
	 * Loading state flag
	 *
	 * @var bool
	 */
	protected $_isCollectionLoaded;
	
	/**
	 * Set select order
	 *
	 * @param	string $field
	 * @param	string $direction
	 * @return	BaseCollection
	 */
	public function setOrder($field, $direction = self::SORT_ORDER_DESC)
	{
		$this->_orders[$field] = $direction;
		return $this;
	}
	
	/**
	 * Implementation of IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		$this->load();
		return new ArrayIterator($this->_items);
	}
	
	/**
	 * Add collection filter
	 *
	 * @param string $field
	 * @param string $value
	 * @return BaseCollection
	 */
	public function addFilter($field, $value, $type = 'and')
	{
		$filter = array();
		$filter['field']	 = $field;
		$filter['value']	 = $value;
		$filter['type']		 = strtolower($type);

		$this->_filters[] = $filter;
		$this->_isFiltersRendered = false;
		return $this;
	}

	/**
	 * Retrieve collection loading status
	 *
	 * @return bool
	 */
	public function isLoaded()
	{
		return $this->_isCollectionLoaded;
	}

	/**
	 * Set collection loading status flag
	 *
	 * @param unknown_type $flag
	 * @return unknown
	 */
	protected function _setIsLoaded($flag = true)
	{
		$this->_isCollectionLoaded = $flag;
		return $this;
	}
	
	/**
	 * Load!
	 * 
	 * @return BaseCollection
	 */
	public function load()
	{
		$this->_beforeLoad();
		$this->_setIdLoaded(true);
		$this->_afterLoad();
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
	 * Add an item onto the list
	 * 
	 * @return BaseCollection
	 */
	public function addItem($object)
	{
		$this->_items[$object->getId()] = $object;
		return $this;
	}
	
	/**
	 * Get an item by ID
	 * 
	 * @return BaseObject|bool
	 */
	public function getItem($id)
	{
		return $this->getItemByColumn($this->_idField, $id);
	}
	
	/**
	 * Get an item by a column value
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @return BaseObject|bool
	 */
	public function getItemByColumn($column, $value)
	{
		foreach($this->_items as $item){
			if($item->getData($column) == $value){
				return $item;
			}
		}
		return false;
	}
}