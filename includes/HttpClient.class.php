<?php

class HttpClient extends BaseObject
{
	/**
	 * Hostname
	 * 
	 * @var string
	 */
	protected $_url;
	
	/**
	 * Port
	 * 
	 * @var string
	 */
	protected $_port;
	
	/**
	 * HTTP username
	 * 
	 * @var string
	 */
	protected $_username;
	
	/**
	 * HTTP Password
	 * 
	 * @var string
	 */
	protected $_password;
	
	/**
	 * Transport Type
	 * 
	 * @var string
	 */
	protected $_transport;
	
	/**
	 * Request Headers
	 * 
	 * @var array
	 */
	protected $_headers = array();

	/**
	 * Transport types
	 */
	const POST		= 'POST';
	const GET		= 'GET';
	const DELETE	= 'DELETE';
	
	/**
	 * Small collection of response codes we want to deal with
	 */
	const HTTP_OK		= 200;
	const HTTP_CREATED	= 201;
	const HTTP_ACCEPTED	= 202;


	/**
	 * Create the URL
	 * 
	 * @param string $url
	 * @return void
	 */
	public function __construct($url, $port = 80, $user = null, $password = null)
	{
		$this->_url			= $url;
		$this->_port		= $port;
		$this->_username	= $user;
		$this->_password	= $password;
		$this->_transport	= self::GET;
	}
	
	/**
	 * Use Post
	 * 
	 * @return HttpClient
	 */
	public function setPost()
	{
		$this->_transport = self::POST;
		return $this;
	}
	
	/**
	 * Use Delete
	 * 
	 * @return HttpClient
	 */
	public function setDelete()
	{
		$this->_transport = self::DELETE;
		return $this;
	}
	
	/**
	 * Use GET
	 * 
	 * @return HttpClient
	 */
	public function setGet()
	{
		$this->_transport = self::GET;
		return $this;
	}
	
	/**
	 * Set something exotic for the transport
	 * 
	 * @param string $transport
	 * @return HttpClient
	 */
	public function setTransport($transport)
	{
		$this->_transport = $transport;
		return $this;
	}
	
	/**
	 * Add a header to be used
	 * 
	 * @param string
	 * @return HttpClient
	 */
	public function addHeader($header)
	{
		$this->_headers[] = $header;
		return $this;
	}
	
	/**
	 * Perform the actual request
	 * 
	 * @return HttpClient
	 */
	public function get()
	{
		//unset body to make sure we don't send it
		$this->unsBody();
		
		//start the curl connection
		$ch = curl_init();
		
		//build the query
		if($this->hasQuery()){
			$query = $this->getQuery();
		} else {
			$query = http_build_query($this->getData());
		}
		
		//if we need to log in, set the username/password
		if(!is_null($this->_username)){
			curl_setopt($s, CURLOPT_USERPWD, $this->_username.':'.$this->_password);
		}
		
		//we want the body to be returned.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		//display verbose data
		if($this->getFlag('verbose')){
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}
		
		//set the headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
		
		//little hack to get around SSL issues
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		//add the queries in the appropriate manner
		switch ($this->_transport) {
			case self::DELETE:
				curl_setopt($ch, CURLOPT_URL, $this->_url . '?' . $query);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::DELETE);
				break;
			case self::POST:
				curl_setopt($ch, CURLOPT_URL, $this->_url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
				break;
			case self::GET:
			default:
				curl_setopt($ch, CURLOPT_URL, $this->_url . '?' . $query);
				break;
		}
		
		//execute the query and format the response
		$contents = curl_exec ($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//error checking...
		switch ($status) {
			case self::HTTP_OK:
			case self::HTTP_CREATED:
			case self::HTTP_ACCEPTED:
				$this->setBody($contents);
				break;
			default:
				throw new HttpClientException("Error connecting to host: {$status}", $status);
		}
		
		return $this;
	}
}