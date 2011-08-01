<?php
namespace Awesome\Xbox\Controller\Web;

class Index extends \Base\Web\Controller
{
	public function indexAction(){
		$gamertag = new \Awesome\Xbox\Model\GamerTag();
		$gamertag->load('Lilmuckers', 'gamertag');
		var_dump($gamertag->getData());
	}
}