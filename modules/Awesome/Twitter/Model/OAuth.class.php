<?php
namespace Awesome\Twitter\Model;

class OAuth extends \Base\OAuth
{
	/**
	 * Store the twitter account we'll be using for this session
	 * 
	 * @var \Awesome\Twitter\Model\Account
	 */
	protected $_account;
	
	/**
	 * Variables for the OAuth urls
	 * 
	 * @var string
	 */
	protected $_requestTokenUrl	= 'https://api.twitter.com/oauth/request_token';
	protected $_accessTokenUrl	= 'https://api.twitter.com/oauth/access_token';
	protected $_authoriseUrl	= 'https://api.twitter.com/oauth/authorize';
	
	/**
	 * Twitter specific URLs
	 */
	const TWITTER_STATUS_URL = 'https://api.twitter.com/1/statuses/update.xml';
	
	/**
	 * Set the consumer variables from the config
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_consumerKey 	= \Base\Config::path('Twitter/OAuth/key');
		$this->_consumerSecret 	= \Base\Config::path('Twitter/OAuth/secret');
		parent::_construct();
	}
	
	/**
	 * Set the twitter account we're using
	 * 
	 * @param \Awesome\Twitter\Model\Account $account
	 * @return \Awesome\Twitter\Model\TwitterOAuth
	 */
	public function setAccount(\Twitter\Model\Account $account)
	{
		$this->_account = $account;
		return $this;
	}
	
	/**
	 * Get the twitter account we're using
	 * 
	 * @return \Awesome\Twitter\Model\Account
	 */
	public function getAccount()
	{
		return $this->_account;
	}
	
	/**
	 * Post a string to twitter as an update
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function tweet($string)
	{
		$params = array(
			'status'	=> $string
		);
		$response = $this->callResource(self::TWITTER_STATUS_URL, $params, $this->getAccount());
		return true;
	}
}