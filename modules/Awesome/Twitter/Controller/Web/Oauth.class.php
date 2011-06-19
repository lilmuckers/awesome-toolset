<?php
namespace Awesome\Twitter\Controller\Web;

class Oauth extends \Base\Web\Controller
{
	/**
	 * Initialise the oAuth object
	 * 
	 * @return \Awesome\Twitter\Model\OAuth
	 */
	protected function _initOAuth()
	{
		//create oAuth object
		$oAuth = new \Awesome\Twitter\Model\OAuth();
		$this->setOAuth($oAuth);
		return $oAuth;
	}
	/**
	 * Start the oauth calls
	 * 
	 * @return \Awesome\Twitter\Controller\Web\Oauth
	 */
	public function startAction()
	{
		$oAuth = $this->_initOAuth();
		$accessUrl = $oAuth->getAuthUrl(\Base\Helper::get('web/url')->getUrl('twitter/oauth/recieve'));
		$this->getResponse()->redirect($accessUrl);
		return $this;
	}
	
	/**
	 * For recieving the response from twitter
	 * 
	 * @return \Awesome\Twitter\Controller\Web\Oauth
	 */
	public function recieveAction()
	{
		$oAuth = $this->_initOAuth();
		$oAuth->importWebResponse($this->getRequest()->getGet()->getData());
		$accessToken = $oAuth->getAccessToken();
		
		$account = new \Awesome\Twitter\Model\Account();
		$account->import($accessToken);
		$account->save();

		return $this;
	}
}
