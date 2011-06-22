<?php
namespace Base\Web;


abstract class Controller extends \Base\Object
{
	/**
	 * Variable to store the action object
	 * 
	 * @var \Base\Web\Action
	 */
	protected $_action;
	
	/**
	 * Constructor to setup the action data
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_action = Action::instance();
		parent::_construct();
	}
	
	/**
	 * Get the request object
	 * 
	 * @return \Base\Web\Action\Request
	 */
	public function getRequest()
	{
		return $this->getAction()->getRequest();
	}
	
	/**
	 * Get the action object
	 * 
	 * @return \Base\Web\Action
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * Get the response object
	 * 
	 * @return \Base\Web\Action\Response
	 */
	public function getResponse()
	{
		return $this->getAction()->getResponse();
	}
	
	/**
	 * Contains the essential preDispatch functionality
	 * 
	 * @return \Base\Web\Controller
	 */
	final public function preDispatch()
	{
		$this->_preDispatch();
		return $this;
	}
	
	/**
	 * Placeholder for the controller specific preDispatch functionality
	 * 
	 * @return \Base\Web\Controller
	 */
	protected function _preDispatch()
	{
		$this->_action->getResponse()->setDefaultLayout();
		//we can only do this once we have the action, so we have access to the request object
		return $this;
	}
	
	/**
	 * Contains the essential postDispatch cleanup functionality
	 * 
	 * @return \Base\Web\Controller
	 */
	final public function postDispatch()
	{
		$this->_postDispatch();
		return $this;
	}
	
	/**
	 * Placeholder for the controller specific postDispatch functionality
	 * 
	 * @return \Base\Web\Controller
	 */
	protected function _postDispatch()
	{
		return $this;
	}
}