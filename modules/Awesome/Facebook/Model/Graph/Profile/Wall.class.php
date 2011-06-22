<?php
namespace Awesome\Facebook\Model\Graph\Profile;

class Wall extends \Awesome\Facebook\Model\Graph\GraphAbstract
{
	/**
	 * Endpoint constants
	 */
	const USER_WALL_ENDPOINT = "%s/feed";
	
	/**
	 * Post on the wall
	 * 
	 * @param array $arguments
	 * @return string
	 */
	public function post($arguments){
		
		if(!array_key_exists('message', $arguments)){
			$this->_error("Must have at least a 'message' parameter to post on the wall");
		}
		$url = sprintf(self::USER_WALL_ENDPOINT, $this->getProfileId());
		$data = $this->_request($url, $arguments)
		return $data->id;
	}
}