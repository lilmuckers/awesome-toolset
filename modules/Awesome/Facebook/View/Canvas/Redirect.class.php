<?php
namespace Awesome\Facebook\View\Canvas;

class Redirect extends \Base\Web\View\Template
{
	const FACEBOOK_OAUTH_REDIRECT_REGISTRY_KEY = "FACEBOOK_OAUTH_REDIRECT_URL";
	/**
	 * Get the url that has been set in the registry
	 * 
	 * @return string
	 */
	public function getUrl(){
		return \Base\Registry::get(self::FACEBOOK_OAUTH_REDIRECT_REGISTRY_KEY);
	}
}