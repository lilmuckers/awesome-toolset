<?php
namespace Awesome\Facebook\Model;

class OAuth extends \Base\Object
{
	/**
	 * The initial authorisation URL
	 * 
	 * @var string
	 */
	protected $_authUrl = "https://www.facebook.com/dialog/oauth?client_id=%s&redirect_uri=%s";
	protected $_accessUrl = "https://graph.facebook.com/oauth/access_token";
	
	/**
	 * Data for making the authorisation
	 * 
	 * @var string
	 */
	protected $_appId;
	protected $_appSecret;
	protected $_redirectUri;
	
	/**
	 * Set the consumer variables from the config
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_appId 			= \Base\Config::path('Facebook/OAuth/application');
		$this->_appSecret 		= \Base\Config::path('Facebook/OAuth/secret');
		$this->_redirectUri 	= \Base\Helper::get('web/url')->getUrl('facebook/oauth/recieve');
		parent::_construct();
	}
	
	/**
	 * Get the auth URL
	 * 
	 * @param array $_permissions
	 * @return string
	 */
	public function getAuthUrl($permissions = array()){
		$permissions = implode(',',$permissions);
		$url = sprintf($this->_authUrl, $this->_appId, $this->_redirectUri);
		if($permissions){
			$url .= 'scope='.$permissions;
		}
		return $url;
	}
	
	/**
	 * parse the response from facebook
	 * 
	 * @param array $data
	 * @return \Awesome\Facebook\Model\OAuth
	 */
	public function importWebResponse($data){
		if(array_key_exists('error_reason', $data)){
			$this->_error("Could not authenticate with Facebook");
		}
		$response = new \Base\Object($data);
		$this->setResponse($response);
		return $this;
	}
	
	/**
	 * Request teh access token from Facebook
	 * 
	 * @return string
	 */
	public function getAccessToken(){
		if(!$this->hasAccessToken()){
			
			$code = $this->getResponse()->getCode();
			$query = array(
				'client_id'		=> $this->_appId,
				'redirect_uri'	=> $this->_redirectUri,
				'client_secret'	=> $this->_appSecret,
				'code'			=> $code
			);
			
			//get the response
			$response = $this->_send($this->_accessUrl, $query)->getBody();
			
			//format the response
			$responseVars = array();
			parse_str($response, $responseVars);
			$response = new \Base\Object($responseVars);
			$this->setAccessToken($response);
		}
		return $this->getData('access_token');
	}
	
	/**
	 * Send the query to the oauth server
	 * 
	 * @return \Base\HttpClient
	 */
	protected function _send($url, $query)
	{
		//send it to the server
		$http = new \Base\HttpClient($url);
		$http->setGet();
		$http->setData($query);
		
		//set the verbose options
		if($this->getFlag('verbose')){
			$http->setFlag('verbose',true);
		}
		
		//a bit of error checking
		try{
			$http->get();
			return $http;
		} catch(\Base\Exception\HttpClient $e) {
			if($e->getCode() == 400){
				$this->_error("Access Denied to oAuth Server", 400, $e);
			}
			throw $e;
		}
	}
}