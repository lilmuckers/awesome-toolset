<?php
namespace Base\Web\Action\Request;

abstract class RequestAbstract extends \Base\Web\Action\ActionAbstract
{
	/**
	 * A flag as to if the data needs protected
	 * 
	 * @var bool
	 */
	protected $_protectData = true;
	
	/**
	 * Load the desired set of data into the parameters
	 * 
	 * @param array $data
	 * @return void
	 */
	protected function _construct($data)
	{
		//add the data to the internal array
		$this->_forceSetData($data);
		parent::_construct();
	}
	
	/**
	 * Force the data to be added to the internal array
	 * 
	 * @param array $data
	 * @return \Base\Web\Action\Request\ARequest
	 */
	protected function _forceSetData($data)
	{
		//temporarily unprotect data to allow initial setting
		$holder = $this->_protectData;
		$this->_protectData = false;
		
		//set the data
		$this->setData($data);
		
		//and then we restore protection
		$this->_protectData = $holder;
		
		return $this;
	}
	
	/**
	 * Overwrite the default setter to allow data protection
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function setData($key, $value = null)
	{
		if(!$this->_protectData){
			return parent::setData($key, $value);
		}
		$this->_error("The ${$this->_variable} data is protected", 102);
	}
	
	/**
	 * Overwrite the default unsetter to allow data protection
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function unsData($key = null)
	{
		if(!$this->_protectData){
			return parent::unsData($key);
		}
		$this->_error("The ${$this->_variable} data is protected", 102);
	}
	
	/**
	 * Format the data array keys
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function _formatDataKey($key)
	{
		return strtolower($key);
	}
}