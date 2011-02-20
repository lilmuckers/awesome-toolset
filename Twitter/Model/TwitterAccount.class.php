<?php

class TwitterAccount extends BaseDBObject
{
	/**
	 * Store the twitter connection
	 * 
	 * @var TwitterOAuth
	 */
	protected $_oauthConnection;

	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('twitter');
	}
	
	/**
	 * Import from Access Token object
	 * 
	 * @param BaseOAuthResponse $accessToken
	 * @return TwitterAccount
	 */
	public function import($accessToken)
	{
		$data = $accessToken->getData();
		$this->setUsername($accessToken->getScreenName());
		$this->setTwitterUserId($accessToken->getUserId());
		$this->setToken($accessToken->getOauthToken());
		$this->setTokenSecret($accessToken->getOauthTokenSecret());
	}
	
	/**
	 * Tweet the given string
	 * 
	 * @param string $string
	 * @return TwitterAccount
	 */
	public function tweet($string)
	{
		$this->_getConnection()->tweet($string);
		return $this;
	}
	
	/**
	 * Get the OAuth connection
	 * 
	 * @return TwitterOAuth
	 */
	protected function _getConnection()
	{
		if(!$this->_oauthConnection){
			$this->_oauthConnection = new TwitterOAuth();
			$this->_oauthConnection->setAccount($this);
		}
		return $this->_oauthConnection;
	}
}