<?php
namespace Base;

abstract class OAuth extends Object
{
	/**
	 * Variables for the OAuth urls
	 * 
	 * @var string
	 */
	protected $_requestTokenUrl;
	protected $_accessTokenUrl;
	protected $_authoriseUrl;
	
	/**
	 * Variables for the consumer
	 * 
	 * @var string
	 */
	protected $_consumerKey;
	protected $_consumerSecret;
	
	/**
	 * We want to use a non-standard exception
	 * 
	 * @var string
	 */
	protected $_exceptionClass = '\Base\Exception\OAuth';
	
	/**
	 * OAuth information
	 * 
	 * @var string
	 */
	protected $_signatureMethod	= "HMAC-SHA1";
	protected $_version			= "1.0";
	
	/**
	 * Prefix for the parameters
	 */
	protected $_paramPrefix	= "oauth_";
	
	/**
	 * Default parameters
	 * 
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * Setup the base data
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->resetParams();
		return parent::_construct();
	}
	
	/**
	 * Get the request Token
	 * 
	 * @return \Base\OAuth\Response
	 */
	public function getRequestToken()
	{
		if(!$this->hasRequestToken()){
			//reset all the parameters
			$this->resetParams();
			
			// make signature and append to params
			//$this->setSecret($this->_consumerSecret);
			$this->setRequestUrl($this->_requestTokenUrl);
			$this->_buildSignature();
			
			//get the response
			$response = $this->_send($this->_requestTokenUrl)->getBody();
			
			//format the response
			$responseVars = array();
			parse_str($response, $responseVars);
			$response = new OAuth\Response($responseVars);
			$this->setRequestToken($response);
		}
		
		//send the query
		return $this->getData('request_token');
	}
	
	/**
	 * Get the authorise URL
	 * 
	 * @return string
	 */
	public function getAuthUrl()
	{
		$tokenData = $this->getRequestToken();
		return rtrim($this->_authoriseUrl,'/').'?'.$tokenData;
	}
	
	/**
	 * Get the access token
	 * 
	 * @param string $token
	 * @return string
	 */
	public function getAccessToken()
	{
		if(!$this->hasAccessToken()){
			//reset all the parameters
			$this->resetParams();
			
			//set the returned token
			$this->setParam('token', $this->getRequestToken()->getToken());
			
			//build the signiture
			$this->setSecret($this->getRequestToken()->getTokenSecret());
			$this->setRequestUrl($this->_accessTokenUrl);
			$this->_buildSignature();
			
			//get the response
			$response = $this->_send($this->_accessTokenUrl)->getBody();
			
			//format the response
			$responseVars = array();
			parse_str($response, $responseVars);
			$response = new OAuth\Response($responseVars);
			$this->setAccessToken($response);
		}
		return $this->getData('access_token');
	}
	
	/**
	 * Call the api resource
	 * 
	 * @param string $url
	 * @param array $params
	 * @param \Base\Object $accessToken
	 * @return string
	 */
	public function callResource($url, $params, $accessToken)
	{
		//reset all the parameters
		$this->resetParams();
		
		foreach($params as $key => $value){
			$this->setParam($key, $value, false);
		}
		
		//set the returned token
		$this->setParam('token', $accessToken->getToken());
		
		//build the signature
		$this->setRequestUrl($url);
		$this->setTransport(HttpClient::POST);
		$this->setSecret($accessToken->getTokenSecret());
		$this->_buildSignature();
		
		//get the response
		$response = $this->_send($url)->getBody();
		
		return $response;
	}
	
	/**
	 * Send the query to the oauth server
	 * 
	 * @return \Base\HttpClient
	 */
	protected function _send($url)
	{
		//sort the parameters again
		uksort($this->_params, 'strcmp');
		
		//build the query
		$query = $this->_buildQuery($this->_params);
		$header = $this->_buildHeader($this->_params);
		
		//send it to the server
		$http = new HttpClient($url);
		$http->addHeader("Authorization: {$header}");
		$http->setTransport($this->getTransport());
		
		//set the verbose options
		if($this->getFlag('verbose')){
			$http->setFlag('verbose',true);
		}
		
		$http->setQuery($query);
		
		//a bit of error checking
		try{
			$http->get();
			return $http;
		} catch(\Base\Exception\HttpClient $e) {
			if($e->getCode() == 401){
				$this->_error("Access Denied to oAuth Server", 401, $e);
			}
			throw $e;
		}
	}
	
	/**
	 * Build the signature
	 * 
	 * @return \Base\OAuth
	 */
	protected function _buildSignature()
	{
		//encode the parameters
		$keys	= $this->_urlencodeRfc3986(array_keys($this->_params));
		$values	= $this->_urlencodeRfc3986(array_values($this->_params));
		$this->_params	= array_combine($keys, $values);
		uksort($this->_params, 'strcmp');
		
		//put it into a query
		$params	= $this->_buildQuery($this->_params, true);
		
		//Form all the strings to work with
		$base	= $this->getTransport()."&".$this->_urlencodeRfc3986($this->getRequestUrl())."&".$this->_urlencodeRfc3986($params);
		$secret	= $this->_urlencodeRfc3986($this->_consumerSecret) ."&".$this->_urlencodeRfc3986($this->getSecret());
		
		//Encode it all and assign to the params
		$hash	= hash_hmac('sha1', $base, $secret, true);
		$base64	= base64_encode($hash);
		$this->setParam('signature', $this->_urlencodeRfc3986($base64));
		
		return $this;
	}
	
	/**
	 * Reset the parameters
	 * 
	 * @return \Base\OAuth
	 */
	public function resetParams()
	{
		$this->_params = array();
		$this->setParam('version', $this->_version);
		$this->setParam('nonce', time());
		$this->setParam('timestamp', time());
		$this->setParam('consumer_key', $this->_consumerKey);
		$this->setParam('signature_method', $this->_signatureMethod);
		$this->setTransport(HttpClient::GET);
		return $this;
	}
	
	/**
	 * Set a parameter for the header request
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param bool $prefix
	 * @return \Base\OAuth
	 */
	public function setParam($key, $value, $prefix = true)
	{
		$key = $prefix ? $this->_paramPrefix.$key : $key;
		$this->_params[$key] = $value;
		return $this;
	}
	
	/**
	 * Get a param value
	 * 
	 * @param string $key
	 * @param bool $prefix
	 * @return mixed
	 */
	public function getParam($key, $prefix = true)
	{
		$key = $prefix ? $this->_paramPrefix.$key : $key;
		return $this->_params[$key];
	}
	
	/**
	 * URL Encode to RFC3986 specification
	 * 
	 * @param mixed $input
	 * @return mixed
	 */
	protected function _urlencodeRfc3986($input)
	{
		if(is_array($input)) {
			return array_map(array($this, '_urlencodeRfc3986'), $input);
		} elseif (is_scalar($input)) {
			return str_replace('+',' ',str_replace('%7E', '~', rawurlencode($input)));
		} else{
			return '';
		}
	}
	
	/**
	 * Build the query
	 * 
	 * @param array $params
	 * @return string
	 */
	protected function _buildQuery($params, $includeoAuth = false)
	{
		$parts = array();
		foreach($params as $k=>$v){
			if($includeoAuth || strpos($k, $this->_paramPrefix) === false){
				$parts[] = "{$k}={$v}";
			}
		}
		return implode('&', $parts);
	}
	
	/**
	 * Build the OAuth header
	 * 
	 * @param array params
	 * @return string
	 */
	protected function _buildHeader($params)
	{
		$parts = array();
		foreach($params as $k=>$v)
		{
			if(strpos($k, $this->_paramPrefix) !== false){
				$parts[] = sprintf('%s="%s"', $k, $v);
			}
		}
		return "OAuth ".implode(', ', $parts);
	}
}