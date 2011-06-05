<?php
namespace Twitter\Controller\Web;

class Oauth extends \Base\Web\Controller
{
	/**
	 * For recieving the response from twitter
	 * 
	 * @return \Twitter\Controller\Web\Oauth
	 */
	public function recieveAction()
	{
		echo "derp :)";
		
		return $this;
	}
}
