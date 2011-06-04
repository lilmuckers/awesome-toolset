<?php
namespace Twitter\Model;

class Account extends \Base\DB\Object
{
	/**
	 * Store the twitter connection
	 * 
	 * @var \Twitter\Model\OAuth
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
	 * @return \Twitter\Model\Account
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
	 * @return \Twitter\Model\Account
	 */
	public function tweet($string)
	{
		$this->_getConnection()->tweet($string);
		return $this;
	}
	
	/**
	 * Get the OAuth connection
	 * 
	 * @return \Twitter\Model\OAuth
	 */
	protected function _getConnection()
	{
		if(!$this->_oauthConnection){
			$this->_oauthConnection = new OAuth();
			$this->_oauthConnection->setAccount($this);
		}
		return $this->_oauthConnection;
	}
}