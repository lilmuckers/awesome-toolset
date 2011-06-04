<?php
namespace Twitter\Model;

class OAuth extends \Base\OAuth
{
	/**
	 * Store the twitter account we'll be using for this session
	 * 
	 * @var \Twitter\Model\Account
	 */
	protected $_account;

	/**
	 * Variables for the consumer
	 * 
	 * @var string
	 */
	protected $_consumerKey		= 'OCRYsb16QEjgYICoGGAQBw';
	protected $_consumerSecret	= 'KolYlc4zuRvFLjMEMocmwMsrFIPAUH9W5rZD2WIlY';
	
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
	 * Set the twitter account we're using
	 * 
	 * @param \Twitter\Model\Account $account
	 * @return \Twitter\Model\TwitterOAuth
	 */
	public function setAccount(\Twitter\Model\Account $account)
	{
		$this->_account = $account;
		return $this;
	}
	
	/**
	 * Get the twitter account we're using
	 * 
	 * @return \Twitter\Model\Account
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