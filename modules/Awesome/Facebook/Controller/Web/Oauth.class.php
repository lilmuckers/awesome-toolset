<?php
namespace Awesome\Facebook\Controller\Web;

class Oauth extends \Base\Web\Controller
{
	/**
	 * Initialise the oAuth object
	 * 
	 * @return \Awesome\Facebook\Model\OAuth
	 */
	protected function _initOAuth()
	{
		//create oAuth object
		$oAuth = new \Awesome\Facebook\Model\OAuth();
		$this->setOAuth($oAuth);
		return $oAuth;
	}
	
	/**
	 * Start the oauth calls
	 * 
	 * @return \Awesome\Facebook\Controller\Web\Oauth
	 */
	public function startAction()
	{
		$oAuth = $this->_initOAuth();
		$accessUrl = $oAuth->getAuthUrl();
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

	}
}