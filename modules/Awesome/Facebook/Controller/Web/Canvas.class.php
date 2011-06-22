<?php
namespace Awesome\Facebook\Controller\Web;

class Canvas extends Oauth
{
	/**
	 * Do the initial page load, checking for authorisation
	 * 
	 * @return \Awesome\Facebook\Controller\Web\Canvas
	 */
	public function indexAction()
	{
		//process the signed request
		$signedRequest = $this->_getSignedRequest();
		
		//if we don't have enough data we need to oAuth it up
		if(!$signedRequest->getFlag('authed')){
			return $this->_canvasOauth();
		}
		
		return $this;
	}
	
	/**
	 * Handle the oAuth authentication
	 * 
	 * @return \Awesome\Facebook\Controller\Web\Canvas
	 */
	protected function _canvasOauth()
	{
		//we want to start oAuth =3
		$oAuth = $this->_initOAuth();
		$oAuth->setRedirectUri(\Base\Config::path($this->_configNamespace."/Facebook/canvas/url"));
		
		//format the layout to use a JS redirect for the canvas
		$this->getResponse()
			->setLayoutWrapper(\Base\Web\Action\Response\View::WRAPPER_AJAX)
			->removeLayout($this->getAction()->getRouteIdentifier())
			->addLayout('facebook_canvas-redirect');
		
		//set the redirect URL within the registry
		\Base\Registry::set(
			\Awesome\Facebook\View\Canvas\Redirect::FACEBOOK_OAUTH_REDIRECT_REGISTRY_KEY, 
			$oAuth->getAuthUrl($this->_facebookAuthScope)
		);
		return $this;
	}
	
	/**
	 * Format the signed request
	 * 
	 * @return \Awesome\Facebook\Model\Canvas\SignedRequest
	 */
	protected function _getSignedRequest()
	{
		$signedRequest = new \Awesome\Facebook\Model\Canvas\SignedRequest(array('config_namespace'=>$this->_configNamespace));
		$signedRequest->process($this->getRequest()->getSignedRequest());
		
		//if this is an authed request
		// we want to initiate the oAuth object with the token
		if($signedRequest->hasUserId() && $signedRequest->getUserId()){
			$signedRequest->setFlag('authed', true);
			$oAuth = $this->_initOauth();
			$oAuth->setData($signedRequest->getData());
		}
		
		return $signedRequest;
	}
}