<?php
namespace Base\OAuth;

class Response extends \Base\Object
{
	/**
	 * Prefix for the data keys
	 */
	const DATA_KEY_PREFIX = 'oauth_';
	
	/**
	 * Format the data array keys
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function _formatDataKey($key)
	{
		if(substr($key, 0 , strlen(self::DATA_KEY_PREFIX)) != self::DATA_KEY_PREFIX){
			$newKey = self::DATA_KEY_PREFIX.$key;
			$key = $this->hasData($newKey) ? $newKey : $key;
		}
		return $key;
	}
	
	/**
	 * Convert all the data into a query string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$data = array('oauth_token'=>$this->getToken());
		if($callback = $this->getOauthCallback()){
			$data['oauth_callback'] = $callback;
		}
		return http_build_query($data);
	}
}