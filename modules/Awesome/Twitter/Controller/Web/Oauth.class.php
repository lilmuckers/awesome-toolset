<?php
namespace Awesome\Twitter\Controller\Web;

class Oauth extends \Base\Web\Controller
{
	/**
	 * For recieving the response from twitter
	 * 
	 * @return \Twitter\Controller\Web\Oauth
	 */
	public function recieveAction()
	{
		return $this;
	}
}
