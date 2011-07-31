<?php
namespace Awesome\Xbox\Model\Api;

abstract class ApiAbstract extends \Base\Object
{
	/**
	 * Login data
	 * 
	 * @var array
	 */
	protected $_loginData = array();
	
	/**
	 * Locale to work in
	 * 
	 * @var array
	 */
	protected $_locale = 'en_GB';
	
	/**
	 * Path to request
	 * 
	 * @var array
	 */
	protected $_apiPath;
	
	/**
	 * API base URL
	 * 
	 * @var string
	 */
	protected $_apiBaseUrl;
	
	/**
	 * Port number to connect to.
	 * 
	 * @var int
	 */
	protected $_apiPort;
	
	/**
	 * The http client to play with
	 * 
	 * @var \Base\HttpClient
	 */
	protected $_httpClient;
	
	/**
	 * Local constructor that sets up the API stuffs
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		//first off the base URL and port
		$this->_apiBaseUrl	= \Base\Config::path('Xbox/XboxLiveApi/url');
		$this->_apiPort		= \Base\Config::path('Xbox/XboxLiveApi/port');
	}
	
	/**
	 * Set the login details for the job
	 * 
	 * @param string $email
	 * @param string $password
	 * @return \Awesome\Xbox\Model\Api\ApiAbstract
	 */
	public function setLoginDetails($email, $password)
	{
		$this->_loginData = array(
			'email'		=> $email,
			'password'	=> $password
		);
		return $this;
	}
	
	/**
	 * Set the locale to load
	 * 
	 * @param string $locale
	 * @return \Awesome\Xbox\Model\Api\ApiAbstract
	 */
	public function setLocale($locale)
	{
		$this->_locale = $locale;
		return $this;
	}
	
	/**
	 * Make the request to the desired URL
	 * 
	 * @param string $url
	 * @return array
	 */
	protected function _request($url)
	{
		//start the http client
		$url = $this->_apiBaseUrl.'/'.$this->_locale.'/'.$url;
		$this->_httpClient = new \Base\HttpClient($url, $this->_apiPort);
		
		//set the login data
		$this->_httpClient->setPost()->setData($this->_loginData);
		
		//perform the request
		try{
			$this->_httpClient->get();
		} catch ( \Base\Exception\HttpClient $e ) {}
		
		//send the data back, boooooooooyah
		$data = json_decode($this->_httpClient->getBody());
		return $data;
	}
	
	/**
	 * This is needed to work!
	 * 
	 * @return \Awesome\Xbox\Model\Api\ApiAbstract
	 */
	public function load($parameter = null)
	{
		//create the URL and request it
		$data = $this->_request(sprintf($this->_apiPath, $parameter));
		
		//if there's an error we want to return false to communicate this.
		if($data->response == 'error'){
			return false;
		}
		
		//set the data to the object and return true
		$this->setData($data->data);
		return true;
	}
}