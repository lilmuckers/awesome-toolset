<?php
namespace Awesome\Facebook\Model\Graph;

abstract class GraphAbstract extends \Base\Object
{
	/**
	 * Constant to hold the base graph api url
	 */
	const BASE_FB_GRAPH_API_URL = "https://graph.facebook.com/%s";
	
	/**
	 * The oAuth instance to do the communication with
	 * 
	 * @var \Awesome\Facebook\Model\OAuth
	 */
	protected $_oAuth;
	
	/**
	 * Set the oAuth object
	 * 
	 * @param \Awesome\Facebook\Model\OAuth $oAuth
	 * @return \Awesome\Facebook\Model\Graph\GraphAbstract
	 */
	public function setOAuth($oAuth){
		$this->_oAuth = $oAuth;
		return $this;
	}
	
	/**
	 * Make a request to the URL, with the data, using oauth token
	 * 
	 * @param string $url
	 * @param array $data
	 * @return mixed
	 */
	protected function _request($url, $query = null)
	{
		$url = sprintf(self::BASE_FB_GRAPH_API_URL, $url);
		
		if($token = $this->_oAuth->getOauthToken()){
			$url .= '?'.http_build_query(array('access_token'=>$token));
		}
		
		//start the http client
		$http = new \Base\HttpClient($url);
		if(is_null($query)){
			$http->setGet();
		} else {
			$http->setPost();
			$http->setData($query);
		}
		//set the verbose options
		if($this->getFlag('verbose')){
			$http->setFlag('verbose',true);
		}
		//perform the request
		$http->get();
		$return = $http->getBody();
		
		//decode the response
		$data = json_decode($return);
		if(is_null($data)){
			return $return;
		}
		
		//as much as i hate stdClass - here it's better and easier than anything else.
		return $data;
	}
	
	/**
	 * Instantiate a new object of the supplied type
	 * 
	 * @param string $type
	 * @return \Awesome\Facebook\Model\Graph\GraphAbstract
	 */
	protected function _initiateGraphObject($type)
	{
		$object = new $type;
		$object->setOAuth($this->_oAuth);
		return $object;
	}
}