<?php
namespace Awesome\Facebook\Controller\Web;

class Oauth extends \Base\Web\Controller
{
	/**
	 * Namespace to draw the configuration from
	 * 
	 * @var string
	 */
	protected $_configNamespace = 'Facebook';
	
	/**
	 * Permissions to request from the user
	 * 
	 * @var array
	 */
	protected $_facebookAuthScope = array();
	
	/**
	 * Initialise the oAuth object
	 * 
	 * @return \Awesome\Facebook\Model\OAuth
	 */
	protected function _initOAuth()
	{
		//create oAuth object
		if(!$this->hasOAuth()){
			$oAuth = new \Awesome\Facebook\Model\OAuth(array('config_namespace'=>$this->_configNamespace));
			$this->setOAuth($oAuth);
			\Base\Registry::set('facebook_oauth', $oAuth);
		}
		return $this->getOAuth();
	}
	
	/**
	 * Start the oauth calls
	 * 
	 * @return \Awesome\Facebook\Controller\Web\Oauth
	 */
	public function startAction()
	{
		$oAuth = $this->_initOAuth();
		$accessUrl = $oAuth->getAuthUrl($this->_facebookAuthScope);
		$this->getResponse()->redirect($accessUrl);
		return $this;
	}
	
	/**
	 * Recieve the callback from facebook
	 * 
	 * @return \Awesome\Facebook\Controller\Web\Oauth
	 */
	public function recieveAction()
	{
		$oAuth = $this->_initOAuth();
		$oAuth->importWebResponse($this->getRequest()->getGet()->getData());
		$accessToken = $oAuth->getAccessToken();
		
		$account = new \Awesome\Facebook\Model\Account();
		$account->import($accessToken);
		$account->save();
		return $this;
	}
}