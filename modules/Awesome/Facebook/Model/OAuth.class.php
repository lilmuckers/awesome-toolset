<?php
namespace Awesome\Facebook\Model;

class OAuth extends \Base\Object
{
	/**
	 * The initial authorisation URL
	 * 
	 * @var string
	 */
	protected $_authUrl = "https://www.facebook.com/dialog/oauth?%s";
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
		$namespace = $this->hasConfigNamespace() ? $this->getConfigNamespace() : 'Facebook';
		$this->_appId 			= \Base\Config::path($namespace.'/OAuth/application');
		$this->_appSecret 		= \Base\Config::path($namespace.'/OAuth/secret');
		$this->_redirectUri 	= \Base\Helper::get('web/url')->getUrl('facebook/oauth/recieve');
		parent::_construct();
	}
	
	/**
	 * Override the default redirect uri
	 * 
	 * @param string $url
	 * @return \Awesome\Facebook\Model\OAuth
	 */
	public function setRedirectUri($url){
		$this->_redirectUri 	= \Base\Helper::get('web/url')->getUrl($url);
		return $this;
	}
	
	/**
	 * Get the auth URL
	 * 
	 * @param array $_permissions
	 * @return string
	 */
	public function getAuthUrl($permissions = array()){
		$permissions = implode(',',$permissions);
		$data = array(
			'client_id'		=> $this->_appId,
			'scope'			=> $permissions,
			'redirect_uri'	=> $this->_redirectUri
		);
		$url = sprintf($this->_authUrl, http_build_query($data));
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
	
	/**
	 * Is this an authorised request?
	 * 
	 * @return bool
	 */
	public function isAuthed()
	{
		return $this->hasUserId();
	}
	
	/**
	 * Get the profile object
	 * 
	 * @param string $profileId
	 * @return \Awesome\Facebook\Model\Graph\Profile
	 */
	public function getProfile($profileId = null)
	{
		if(is_null($profileId) && $this->hasUserId()){
			$profileId = $this->getUserId();
		}
		if(!$this->hasProfiles() || !array_key_exists($profileId, $this->getProfiles())){
			$profile = new Graph\Profile();
			$profile->setId($profileId);
			$profile->setOAuth($this);
			$profiles = (array) $this->getProfiles();
			$profiles[$profileId] = $profile;
			$this->setProfiles($profiles);
		}
		$profiles = $this->getProfiles();
		return $profiles[$profileId];
	}
}