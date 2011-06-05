<?php
namespace Base\Web\Action;

class Request extends Request\RequestAbstract
{
	/**
	 * We're storing the get var manager object
	 * 
	 * @var \Base\Web\Action\Request\Get
	 */
	protected $_get;
	
	/**
	 * We're storing the get var manager object
	 * 
	 * @var \Base\Web\Action\Request\Post
	 */
	protected $_post;
	
	/**
	 * We're storing the get var manager object
	 * 
	 * @var \Base\Web\Action\Request\Server
	 */
	protected $_server;
	
	/**
	 * This stores all the cookie management
	 * 
	 * @var array
	 */
	protected $_cookies = array();
	
	/**
	 * This stores all the session management
	 * 
	 * @var array
	 */
	protected $_sessions = array();
	
	/**
	 * Request path for the routing
	 * 
	 * @param array
	 */
	protected $_requestPath = array();
	
	/**
	 * Instantiate all the various data handlers, and load the request data into this object
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		//first store the request data :)
		parent::_construct($_REQUEST);
		
		//now we load up the static data
		$this->_get = new Request\Get();
		$this->_post = new Request\Post();
		$this->_server = new Request\Server();
		
		//parse the request
		$this->_parseRequest($this->getGet('q'));
	}
	
	/**
	 * Parse the request query to the appropriate arrays
	 * 
	 * @param string $requestString
	 * @return array
	 */
	protected function _parseRequest($requestPath)
	{
		$defaultPath = explode('/', \Base\Config::path('Base/Routing/default'));
		
		if(!empty($requestPath)){
			$requestPath = (array) explode('/', $requestPath);
			
			//fill any blanks with default values
			$path = array();
			$path[0] = array_key_exists(0, $requestPath) ? $requestPath[0] : $defaultPath[0];
			$path[1] = array_key_exists(1, $requestPath) ? $requestPath[1] : $defaultPath[1];
			$path[2] = array_key_exists(2, $requestPath) ? $requestPath[2] : $defaultPath[2];
			
			//now we set up the query
			$query = array();
			for($i=3; $i<count($requestPath);$i=$i+2){
				$query[$requestPath[$i]] = array_key_exists($i+1, $requestPath) ? $requestPath[$i+1] : true;
			}
		} else {
			$path = $defaultPath;
			$query = array();
		}
		
		//set the query to the _REQUEST handler
		$this->_forceSetData($query);
		$this->_requestPath = $path;
		
		return $this;
	}
	
	/**
	 * Get the get handler or data
	 * 
	 * @param string $key
	 * @return \Base\Web\Action\Request\Get
	 */
	public function getGet($key = null)
	{
		if(!is_null($key)){
			return $this->_get->getData($key);
		}
		return $this->_get;
	}
	
	/**
	 * Get the post handler or data
	 * 
	 * @param string $key
	 * @return \Base\Web\Action\Request\Post
	 */
	public function getPost($key = null)
	{
		if(!is_null($key)){
			return $this->_post->getData($key);
		}
		return $this->_post;
	}
	
	/**
	 * Get the server handler or data
	 * 
	 * @param string $key
	 * @return \Base\Web\Action\Request\Server
	 */
	public function getServer($key = null)
	{
		if(!is_null($key)){
			return $this->_server->getData($key);
		}
		return $this->_server;
	}
	
	/**
	 * Get the transport type for the request
	 * 
	 * @return string
	 */
	public function getTransport()
	{
		return $this->getServer()->getRequestMethod();
	}
	
	/**
	 * Get the request path
	 * 
	 * @return array
	 */
	public function getRequestPath()
	{
		return $this->_requestPath;
	}
	
	/**
	 * Get if this is a post request
	 * 
	 * @return bool
	 */
	public function isPost()
	{
		return $this->getTransport() == 'POST';
	}
	
	/**
	 * If this is an ajax request
	 * 
	 * @return bool
	 */
	public function isAjax()
	{
		return !is_null($this->getAjax());
	}
}