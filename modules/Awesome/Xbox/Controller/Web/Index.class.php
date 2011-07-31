<?php
namespace Awesome\Xbox\Controller\Web;

class Index extends \Base\Web\Controller
{
	public function indexAction(){
		$api = new \Awesome\Xbox\Model\Api\GamerTag();
		$api->load($this->getRequest()->getData('tag'));
		var_dump($api->getData());
	}
}