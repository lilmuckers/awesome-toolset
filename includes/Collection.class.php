<?php
namespace Base;

abstract class Collection extends Object implements \IteratorAggregate, \Countable
{
	/**
	 * Constants for direction ordering.
	 */
	const SORT_ORDER_ASC	= 'ASC';
	const SORT_ORDER_DESC	= 'DESC';
	
	/**
	 * Array of \Base\Object to iterate through
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
	protected $_itemClass = '\Base\Object';
	
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
	 * apply the following limits
	 * 
	 * @var array
	 */
	protected $_limit = array();
	
	/**
	 * Set select order
	 *
	 * @param	string $field
	 * @param	string $direction
	 * @return	\Base\Collection
	 */
	public function setOrder($field, $direction = self::SORT_ORDER_DESC)
	{
		$this->_orders[$field] = $direction;
		return $this;
	}
	
	/**
	 * Implementation of IteratorAggregate::getIterator()
	 * 
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		$this->load();
		return new \ArrayIterator($this->_items);
	}
	
	/**
	 * Add collection filter
	 *
	 * @param string $field
	 * @param string $value
	 * @return \Base\Collection
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
	 * @param bool $flag
	 * @return \Base\Collection
	 */
	protected function _setIsLoaded($flag = true)
	{
		$this->_isCollectionLoaded = $flag;
		return $this;
	}
	
	/**
	 * Load!
	 * 
	 * @return \Base\Collection
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
	 * @return \Base\Collection
	 */
	protected function _beforeLoad()
	{
		return $this;
	}
	
	/**
	 * Anything that needs doing post-load
	 * 
	 * @return \Base\Collection
	 */
	protected function _afterLoad()
	{
		return $this;
	}
	
	/**
	 * Add an item onto the list
	 * 
	 * @param \Base\Object $object
	 * @return \Base\Collection
	 */
	public function addItem(\Base\Object $object)
	{
		$this->_items[] = $object;
		return $this;
	}
	
	/**
	 * Get an item by ID
	 * 
	 * @param int $id
	 * @return \Base\Object|bool
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
	 * @return \Base\Object|bool
	 */
	public function getItemByColumn($column, $value)
	{
		$this->load();
		foreach($this as $item){
			if($item->getData($column) == $value){
				return $item;
			}
		}
		return false;
	}
	
	/**
	 * Count the number of items
	 * 
	 * @return int
	 */
	public function count()
	{
		$this->load();
		return count($this->_items);
	}
	
	/**
	 * Apply the following function to all child items
	 * 
	 * @param string $function
	 * @param array $arguments
	 * @return \Base\Collection
	 */
	public function walk($function, $arguments = array())
	{
		foreach($this as $item){
			call_user_func_array(array($item, $function), $arguments);
		}
		return $this;
	}
	
	/**
	 * Calculate the sum of all values in given column
	 * 
	 * @param string $column
	 * @return float
	 */
	public function sumColumn($column)
	{
		return array_sum($this->getColumnValues($column, false));
	}
	
	/**
	 * Get all the column values
	 * 
	 * @param string $column
	 * @param bool $unique
	 * @return array
	 */
	public function getColumnValues($column, $unique = true)
	{
		$return = array();
		$this->load();
		foreach($this->_items as $item){
			if($item->hasData($column)){
				$value = $item->getData($column);
				$return[] = $value;
			}
		}
		return $unique ? array_unique($return) : $return;
	}
	
	
	/**
	 * Apply the following limit
	 * 
	 * @param int $length
	 * @param int $offset
	 * @return \Base\Collection
	 */
	public function setLimit($length, $offset = 0)
	{
		$this->_limit = array('start'=>$offset, 'length'=>$length);
		return $this;
	}
	
	/**
	 * Return just the first item
	 * 
	 * @return \Base\Object
	 */
	public function getFirstItem()
	{
		foreach($this->_items as $item){
			return $item;
		}
		return null;
	}
}