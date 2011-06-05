<?php
namespace Base\Web\Action;

class Response extends ActionAbstract
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
		$this->_view = new Response\View();
		parent::_construct();
	}
	
	/**
	 * Prints the content of the page
	 * 
	 * @return void
	 */
	public function output()
	{
		if($this->getAction()->getRequest()->isAjax())
		{
			if($json = $this->getJson()){
				return json_encode($json);
			}
			return $this->_view->render(Response\View::WRAPPER_AJAX);
		}
		return $this->_view->render();
	}
}