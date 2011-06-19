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
	 * Set name of the layout wrapper
	 * 
	 * @var string
	 */
	protected $_layoutWrapper;
	
	/**
	 * Additional Layouts
	 * 
	 * @var array
	 */
	protected $_layouts = array();
	
	/**
	 * Regular expression for matching the route to layout code
	 * 
	 * @var string
	 */
	protected $_routeRegex = '/^\\\\([a-zA-Z0-9_]+)\\\\([a-zA-Z0-9_]+)\\\\Controller\\\\([a-z-A-Z]+)\\\\([a-zA-Z_0-9]+)::([a-zA-Z_0-9]+)Action$/';
	
	/**
	 * The replacement string in question
	 * 
	 * @var string
	 */
	protected $_routeRegexReplace = '$1_$2_$4-$5';
	
	/**
	 * Set up the view layer and so forth
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_view = new Response\View();
		$this->_layoutWrapper = Response\View::WRAPPER_NORMAL;
		parent::_construct();
	}
	
	/**
	 * Builds the string to identify the default layout node
	 * 
	 * @return \Base\Web\Action\Response
	 */
	public function setDefaultLayout()
	{
		$action = $this->getAction()->getRequest()->getRoute();
		$action = strtolower(preg_replace($this->_routeRegex, $this->_routeRegexReplace, $action));
		$this->addLayout($action);
		$this->getAction()->setRouteIdentifier($action);
		return $this;
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
			return $this->_view->render(Response\View::WRAPPER_AJAX, $this->_layouts);
		}
		
		return $this->_view->render($this->_layoutWrapper, $this->_layouts);
	}
	
	/**
	 * Set the layout wrapper to use
	 * 
	 * @param string $layout
	 * @return \Base\Web\Action\Response
	 */
	public function setLayoutWrapper($layout)
	{
		$this->_layoutWrapper = $layout;
		return $this;
	}
	
	/**
	 * Add a layout to the render
	 * 
	 * @param string $layout
	 * @return \Base\Web\Action\Response
	 */
	public function addLayout($layout)
	{
		//remove any duplicates
		$this->removeLayout($layout);
		$this->_layouts[] = $layout;
		return $this;
	}
	
	/**
	 * Remove a layout from the render
	 * 
	 * @param string $layout
	 * @return \Base\Web\Action\Response
	 */
	public function removeLayout($layout)
	{
		foreach($this->_layouts as $key=>$value){
			if($value == $layout){
				unset($this->_layouts[$key]);
			}
		}
		return $this;
	}
	
	public function redirect($url, $code = 302)
	{
		header("Location: $url");
		return $this;
	}
}