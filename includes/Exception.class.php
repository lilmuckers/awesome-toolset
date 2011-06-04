<?php
namespace Base;

class Exception extends \Exception
{
	/**
	 * Store the object that threw the exception
	 * 
	 * @var \Base\Object
	 */
	protected $_object;
	
	/**
	 * Set the object that threw the exception
	 * 
	 * @param \Base\Object $object
	 * @return \Base\Exception
	 */
	public function setObject(\Base\Object $object)
	{
		$this->_object = $object;
		return $this;
	}
	
	/**
	 * Get the object that threw the exception
	 * 
	 * @return \Base\Object
	 */
	public function getObject()
	{
		return $this->_object;
	}
}