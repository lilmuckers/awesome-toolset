<?php
namespace Base\Web\Action;

class Response extends \Base\Object
{
	/**
	 * The template object for this request
	 * 
	 * @var \Base\Web\Action\Response\View
	 */
	protected $_view;
	
	/**
	 * Set up the view layer and so forth
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_view = new Action\Response\View();
		parent::_construct();
	}
	
	/**
	 * Prints the content of the page
	 * 
	 * @return void
	 */
	public function output()
	{
		
	}
}