<?php
namespace Base\Web;

class Action extends \Base\Object
{
	/**
	 * Instance of this class for use by others
	 * 
	 * @var \Base\Web\Action
	 */
	protected static $_instance;
	
	/**
	 * The request object
	 * 
	 * @var \Base\Web\Action\Request
	 */
	protected $_request;
	
	/**
	 * The response object
	 * 
	 * @var \Base\Web\Action\Response
	 */
	protected $_response;
	
	/**
	 * Set up the request and response objects
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_request = new Action\Request();
		$this->_response = new Action\Response();
		parent::_construct();
	}
	
	/**
	 * Create and return an instance of this class
	 * 
	 * @return \Base\Web\Action
	 */
	public static function instance()
	{
		if(!(self::$_instance instanceof \Base\Web\Action)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Get the request object
	 * 
	 * @return \Base\Web\Action\Request
	 */
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * Get the response object
	 * 
	 * @return \Base\Web\Action\Response
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	/**
	 * Get the view object
	 * 
	 * @return \Base\Web\Action\Response\View
	 */
	public function getView()
	{
		return $this->_response->getView();
	}
}