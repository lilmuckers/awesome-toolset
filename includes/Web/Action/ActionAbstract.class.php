<?php
namespace Base\Web\Action;

abstract class ActionAbstract extends \Base\Object
{
	/**
	 * The Action class for access from multiple palces
	 * 
	 * @var \Base\Web\Action
	 */
	protected $_action;
	
	/**
	 * Set the action that is to be used
	 * 
	 * @param \Base\Web\Action $action
	 * @return \Base\Web\Action\AAction
	 */
	public function setAction(\Base\Web\Action $action)
	{
		$this->_action = $action;
		return $this;
	}
	
	/**
	 * Get the action
	 * 
	 * @return \Base\Web\Action
	 */
	public function getAction()
	{
		return $this->_action;
	}
}